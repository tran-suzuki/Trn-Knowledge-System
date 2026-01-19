<?php

namespace App\Application\GoogleCloud;
use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class VertexAIService
{
    protected $projectId;
    protected $dataStoreId;
    protected $keyFilePath;
    protected $bucketName;

    public function __construct()
    {
        $this->projectId = env('GOOGLE_CLOUD_PROJECT_ID');
        $this->dataStoreId = env('VERTEX_AI_DATA_STORE_ID');
        $this->keyFilePath = env('GOOGLE_CLOUD_KEY_FILE');
        $this->bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
    }

    private function getAccessToken()
    {
        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/cloud-platform',
            json_decode(file_get_contents($this->keyFilePath), true)
        );
        $token = $credentials->fetchAuthToken(HttpHandlerFactory::build());
        return $token['access_token'];
    }

    public function triggerImport(int $groupId)
    {
        $accessToken = $this->getAccessToken();
        $url = "https://discoveryengine.googleapis.com/v1alpha/projects/{$this->projectId}/locations/global/dataStores/{$this->dataStoreId}/branches/0/documents:import";

        $response = Http::withToken($accessToken)->post($url, [
            'gcsSource' => [
                'inputUris' => ["gs://{$this->bucketName}/metadata_{$groupId}/*.jsonl"]
            ],
            'reconciliationMode' => 'INCREMENTAL',
        ]);

        return $response;
    }
}