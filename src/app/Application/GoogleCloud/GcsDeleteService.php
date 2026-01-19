<?php

namespace App\Application\GoogleCloud;

use Google\Cloud\Storage\StorageClient;
use RuntimeException;

final class GcsDeleteService
{
    private $bucket;

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

    public function deleteFile(string $gcsPath): void
    {
        $object = $this->bucket->object($gcsPath);

        if ($object->exists()) {
            $object->delete();
        }
    }

    public function deleteFolder(string $prefix): void
    {
        $prefix = rtrim($prefix, '/') . '/';

        foreach ($this->bucket->objects(['prefix' => $prefix]) as $object) {
            $object->delete();
        }
    }
}
