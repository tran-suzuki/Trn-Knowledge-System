<?php

namespace App\Application\GoogleCloud;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Support\Facades\Http;

class GeminiService {
	public function summarizeMultimedia(string $gcsPath, string $mimeType): array {
		$projectId   = env('GOOGLE_CLOUD_PROJECT_ID');
		$bucketCloud = env('GOOGLE_CLOUD_STORAGE_BUCKET');
		$gcsUri      = "gs://{$bucketCloud}/{$gcsPath}";
		$location    = 'us-central1';
		$accessToken = $this->getAccessToken();

		$modelId = 'gemini-2.0-flash-lite-001';
		$url     = "https://{$location}-aiplatform.googleapis.com/v1/projects/{$projectId}/locations/{$location}/publishers/google/models/{$modelId}:generateContent";

		$prompt = "以下の動画／音声を解析し、内容の要点のみを日本語で簡潔に出力してください。説明や前置きは不要です。";

		$response = Http::withToken($accessToken)
			->timeout(300)
			->connectTimeout(30)
			->retry(0, 0)
			->post($url, [
				'contents'         => [
					[
						'role'  => 'user',
						'parts' => [
							['text' => $prompt],
							[
								'fileData' => [
									'mimeType' => $mimeType,
									'fileUri'  => $gcsUri,
								],
							],
						],
					],
				],
				'generationConfig' => [
					'maxOutputTokens' => 800,
					'temperature'     => 0.2,
				],
			]);
		return $response->json();
	}

	private function getAccessToken(): string {
		$keyFilePath = env('GOOGLE_CLOUD_KEY_FILE');
		$credentials = new ServiceAccountCredentials(
			'https://www.googleapis.com/auth/cloud-platform',
			json_decode(file_get_contents($keyFilePath), true)
		);
		$token = $credentials->fetchAuthToken(HttpHandlerFactory::build());
		return $token['access_token'];
	}
}