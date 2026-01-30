<?php

namespace App\Application\GoogleCloud;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Support\Facades\Http;

class GeminiService
{
	public function summarizeMultimedia(string $gcsPath, string $mimeType): array
	{
		$projectId = env('GOOGLE_CLOUD_PROJECT_ID');
		$bucketCloud = env('GOOGLE_CLOUD_STORAGE_BUCKET');
		$gcsUri = "gs://{$bucketCloud}/{$gcsPath}";
		$location = 'us-central1';
		$accessToken = $this->getAccessToken();

		$modelId = 'gemini-2.0-flash-lite-001';
		$url = "https://{$location}-aiplatform.googleapis.com/v1/projects/{$projectId}/locations/{$location}/publishers/google/models/{$modelId}:generateContent";

		$prompt = "以下の動画／音声を解析し、内容の要点のみを日本語で簡潔に出力してください。説明や前置きは不要です。";

		$response = Http::withToken($accessToken)
			->timeout(300)
			->connectTimeout(30)
			->retry(0, 0)
			->post($url, [
				'contents' => [
					[
						'role' => 'user',
						'parts' => [
							['text' => $prompt],
							[
								'fileData' => [
									'mimeType' => $mimeType,
									'fileUri' => $gcsUri,
								],
							],
						],
					],
				],
				'generationConfig' => [
					'maxOutputTokens' => 800,
					'temperature' => 0.2,
				],
			]);
		return $response->json();
	}

	private function getAccessToken(): string
	{
		$keyFilePath = env('GOOGLE_CLOUD_KEY_FILE');
		$credentials = new ServiceAccountCredentials(
			'https://www.googleapis.com/auth/cloud-platform',
			json_decode(file_get_contents($keyFilePath), true)
		);
		$token = $credentials->fetchAuthToken(HttpHandlerFactory::build());
		return $token['access_token'];
	}

	public function summarizeRagAnswer(
		string $question,
		string $rawAnswer,
		array $documents = []
	): string {
		$projectId = env('GOOGLE_CLOUD_PROJECT_ID');
		$location = 'us-central1';
		$accessToken = $this->getAccessToken();

		$modelId = 'gemini-2.0-flash-lite-001';
		$url = "https://{$location}-aiplatform.googleapis.com/v1/projects/{$projectId}/locations/{$location}/publishers/google/models/{$modelId}:generateContent";

		// Build simple reference text from documents (titles + sections only)
		$refs = [];
		foreach ($documents as $doc) {
			$title = $doc['file_name'] ?? '';
			$section = $doc['section'] ?? '';
			if ($title || $section) {
				$refs[] = trim("{$title} {$section}");
			}
			if (count($refs) >= 5) {
				break; // limit to save tokens
			}
		}

		$referenceText = implode("\n", $refs);

		$prompt = <<<TEXT
            以下は資料検索から抽出された情報です。
            あなたの役割は「文章の整理」のみです。
            
            重要なルール：
            - 抽出された情報に含まれていない内容は一切追加しないでください
            - 推測、一般知識、背景説明は禁止です
            - データベース名、テーブル名、システム内部情報は回答に含めないでください
            - 資料に明確な記載がない場合は、必ず次の文で回答してください：
            「申し訳ございませんが、資料の中に該当する情報は見つかりませんでした。」
            
            回答条件：
            - 日本語（です・ます調）
            - グループチャット向けに簡潔に
            - 箇条書き（「・」形式）
            - 最大5項目
            - 引用番号（[1], [2], [3] など）は一切出力しないでください
            - 資料に記載がある場合のみ内容を整理して回答してください
            - 出力形式は必ず箇条書き（「・」）にしてください
            
            質問：
            {$question}
            
            抽出された回答案：
            {$rawAnswer}
            
            参考情報（必要な場合のみ）：
            {$referenceText}
            TEXT;

		$response = Http::withToken($accessToken)
			->timeout(60)
			->connectTimeout(10)
			->retry(0, 0)
			->post($url, [
				'contents' => [
					[
						'role' => 'user',
						'parts' => [
							['text' => $prompt],
						],
					],
				],
				'generationConfig' => [
					'maxOutputTokens' => 400,
					'temperature' => 0.2,
					'topP' => 0.8,
				],
			]);

		$json = $response->json();

		return trim(
			$json['candidates'][0]['content']['parts'][0]['text']
			?? '申し訳ございませんが、資料の中に該当する情報は見つかりませんでした。'
		);
	}

	public function isMeaningfulQuestion(string $question): bool
    {
        $projectId = env('GOOGLE_CLOUD_PROJECT_ID');
        $location = 'us-central1';
        $accessToken = $this->getAccessToken();

        $modelId = 'gemini-2.0-flash-lite-001';
        $url = "https://{$location}-aiplatform.googleapis.com/v1/projects/{$projectId}/locations/{$location}/publishers/google/models/{$modelId}:generateContent";

        $prompt = <<<TEXT
            あなたは「質問の妥当性判定」専用AIです。

            以下の質問が次の条件を満たしているかを判定してください。

            【YES の条件】
            - 意味のある質問である
            - 情報を求めている、または明確な意図がある
            - 業務・知識・説明・確認などに関係している

            【NO の条件】
            - 無意味な文字列（例：aaaa、123123、？？？）
            - スパム、テスト入力
            - 単語の羅列のみ
            - 明確な質問や意図が存在しない

            【重要】
            - 出力は必ず次のどちらか一語のみ
            YES
            NO

            質問：
            {$question}
            TEXT;

        $response = Http::withToken($accessToken)
            ->timeout(30)
            ->connectTimeout(10)
            ->retry(0, 0)
            ->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 10,
                    'temperature' => 0.0,
                ],
            ]);

        $json = $response->json();
        $result = strtoupper(trim(
            $json['candidates'][0]['content']['parts'][0]['text'] ?? 'NO'
        ));

        return $result === 'YES';
    }
}