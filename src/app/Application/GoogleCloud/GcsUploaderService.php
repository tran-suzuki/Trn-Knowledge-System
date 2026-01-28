<?php
namespace App\Application\GoogleCloud;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\UploadedFile;
use RuntimeException;

final class GcsUploaderService
{
	private $bucket;

	private const SIMPLE_UPLOAD_LIMIT = 100 * 1024 * 1024; // 100MB
	private const CHUNK_SIZE = 8 * 1024 * 1024; // 8MB

	public function __construct()
	{
		$storage = new StorageClient([
			'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
			'keyFile' => json_decode(
				file_get_contents(env('GOOGLE_CLOUD_KEY_FILE')),
				true
			),
		]);

		$this->bucket = $storage->bucket(
			env('GOOGLE_CLOUD_STORAGE_BUCKET')
		);
	}

	public function uploadFile(array $gcsItem): array
	{
		try {
			$file = $gcsItem['file'];
			$gcsPath = $gcsItem['gcsPath'];

			if (!$file instanceof UploadedFile || !$file->isValid()) {
				throw new \RuntimeException('Uploaded file is not valid');
			}

			$size = $file->getSize();

			if ($size < self::SIMPLE_UPLOAD_LIMIT) {
				$this->simpleUpload($file, $gcsPath);
			} else {
				$this->resumableUploadFromUploadedFile($file, $gcsPath);
			}

			$this->uploadMetadata($gcsItem);

			return [
				'document_id' => $gcsItem['documentId'],
				'gcs_path' => $gcsPath,
				'status' => 'UPLOADED',
				'size' => $file->getSize(),
				'mime_type' => $file->getMimeType(),
			];
		} catch (\Throwable $th) {
			\Log::error('[GcsUploaderService][uploadFile] ', [
				'error' => $th->getMessage(),
				'gcs_path' => $gcsItem['gcsPath'] ?? null,
			]);
			throw $th;
		}
	}

	private function simpleUpload(UploadedFile $file, string $gcsPath): void
	{
		$stream = fopen($file->getRealPath(), 'r');

		if ($stream === false) {
			throw new RuntimeException('Cannot open file stream');
		}

		try {
			$this->bucket->upload($stream, [
				'name' => $gcsPath,
				'metadata' => [
					'contentType' => $file->getMimeType() ?? 'application/octet-stream',
				],
			]);
		} finally {
			if (is_resource($stream)) {
				fclose($stream);
			}
		}
	}

	private function resumableUploadFromUploadedFile(UploadedFile $file, string $gcsPath): void
	{
		$handle = fopen($file->getRealPath(), 'r');

		if ($handle === false) {
			throw new RuntimeException('Cannot open file stream');
		}

		try {
			$uploader = $this->bucket->getResumableUploader(
				$handle,
				[
					'name' => $gcsPath,
					'metadata' => [
						'contentType' => $file->getMimeType() ?? 'application/octet-stream',
					],
					'chunkSize' => self::CHUNK_SIZE,
				]
			);

			$uploader->upload();

		} finally {
			fclose($handle);
		}

	}

	public function uploadMetadata(
		array $gcsItem,
	): void {
		try {
			$groupId = $gcsItem['groupId'];
			$gcsFilePath = $gcsItem['gcsPath'];
			$file = $gcsItem['file'];
			$fileType = $file->getType();
			$mimeType = $file->getMimeType();
			$fileSize = $file->getSize();

			$documentId = $gcsItem['metadataName'];
			$metadataPath = "metadata_{$groupId}/{$documentId}.jsonl";
			$isMedia = in_array(explode('/', $mimeType)[0], ['video', 'audio']);
			$fullGcsUri = sprintf('gs://%s/%s', env('GOOGLE_CLOUD_STORAGE_BUCKET'), $gcsFilePath);
			$contentData = [];

			if ($isMedia) {
				//GeminiService summari video/audio
				$geminiService = new GeminiService();
				$geminiResult = $geminiService->summarizeMultimedia($gcsFilePath, $mimeType);
				$summariResult = data_get($geminiResult, 'candidates.0.content.parts.0.text') ?? '要約なし';
				$contentData = [
					'mimeType' => 'text/plain',
					'rawBytes' => base64_encode($summariResult),
				];

			} else {
				$contentData = [
					'mimeType' => $mimeType,
					'uri' => $fullGcsUri,
				];
			}

			$metadata = [
				'id' => $documentId,
				'structData' => [
					'group_id' => (string)$groupId,
					'document_id' => $documentId,
					'file_path' => $gcsFilePath,
					'file_name' => $gcsItem['documentName'],
					'file_type' => $fileType,
					'mime_type' => $mimeType,
					'size' => $fileSize,
					'created_at' => now()->toISOString(),
					'summary' => $isMedia ? $summariResult ?? '要約なし' : '',
					'original_uri' => $fullGcsUri,
				],
				'content' => $contentData,
			];

			$jsonLine = json_encode(
				$metadata,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			) . "\n";

			$a = $this->bucket->upload($jsonLine, [
				'name' => $metadataPath,
				'metadata' => [
					'contentType' => 'application/x-jsonlines',
				],
			]);

		} catch (\Throwable $e) {
			\Log::error('[GcsUploaderService] uploadMetadata failed', [
				'file' => $gcsFilePath,
				'groupId' => $groupId,
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
			]);
			throw $e;
		}
	}

	public function copyFile(
		string $sourceDisplayId,
		string $sourcePath,
		string $destinationDisplayId,
		string $destinationPath
	): void {
		try {
			$sourceObject = $this->bucket->object($sourcePath);
			if (!$sourceObject->exists()) {
				throw new RuntimeException("Source file does not exist: {$sourcePath}");
			}
			$sourceObject->copy($this->bucket, [
				'name' => $destinationPath
			]);
			$sourceMetadataPath = "metadata_" . (string) $sourceDisplayId[0] . "/{$sourceDisplayId}.jsonl";
			$destinationMetadataPath = "metadata_" . (string) $destinationDisplayId[0] . "/{$destinationDisplayId}.jsonl";
			$oldMetadata = $this->readMetadata($sourceMetadataPath);

			$newMetadata = $this->transformMetadataForCopy(
				$oldMetadata,
				$destinationDisplayId,
				$destinationMetadataPath
			);

			$this->uploadCopiedMetadata(
				$newMetadata,
				$destinationMetadataPath
			);


		} catch (\Throwable $e) {
			\Log::error('[GcsUploaderService] copyFile failed', [
				'from' => $sourcePath,
				'to' => $destinationPath,
				'error' => $e->getMessage()
			]);
			throw $e;
		}
	}

	public function readMetadata(string $metadataPath): array
	{
		$object = $this->bucket->object($metadataPath);

		if (!$object->exists()) {
			throw new RuntimeException("Metadata not found: {$metadataPath}");
		}

		return json_decode(
			trim($object->downloadAsString()),
			true
		);
	}

	public function transformMetadataForCopy(
		array $oldMetadata,
		string $newDocumentId,
		string $newGcsPath
	): array {
		$newUri = sprintf(
			'gs://%s/%s',
			env('GOOGLE_CLOUD_STORAGE_BUCKET'),
			$newGcsPath
		);

		// root id
		$oldMetadata['id'] = $newDocumentId;

		// structData
		$oldMetadata['structData'] = array_merge(
			$oldMetadata['structData'],
			[
				'group_id' => (string) $newDocumentId[0],
				'document_id' => $newDocumentId,
				'file_path' => $newGcsPath,
				'original_uri' => $newUri,
				'created_at' => now()->toISOString(),
			]
		);

		// content
		if (isset($oldMetadata['content']['uri'])) {
			// non-media
			$oldMetadata['content']['uri'] = $newUri;
		}
		return $oldMetadata;
	}

	public function uploadCopiedMetadata(
		array $metadata,
		string $path
	): void {
		$jsonLine = json_encode(
			$metadata,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		) . "\n";

		$this->bucket->upload($jsonLine, [
			'name' => $path,
			'metadata' => [
				'contentType' => 'application/x-jsonlines',
			],
		]);
	}
}
