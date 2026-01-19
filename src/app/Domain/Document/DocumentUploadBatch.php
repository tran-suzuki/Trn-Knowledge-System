<?php

namespace App\Domain\Document;
use DomainException;
use App\Domain\Document\View\Document;
final class DocumentUploadBatch
{
    /** @var Document[] */
    private array $documents;

    private function __construct(
        array $documents,
        private?int $rootParentId
    ) {
        if (count($documents) === 0) {
            throw new DomainException('No documents');
        }

        if (count($documents) > 20) {
            throw new DomainException('Too many documents');
        }

        $this->documents = $documents;
    }

    /** @return Document[] */
    public function documents(): array
    {
        return $this->documents;
    }

    public static function fromUploadedMeta(
        int $userId,
        int $groupId,
        ?int $parentId,
        string $parentFolder,
        array $filesMeta,
        array $paths
    ): self {
        $documents = [];
        $folders = [];

        foreach ($filesMeta as $key => $meta) {
            $jsonPath = json_decode($paths[$key], true);
            /**
             * 1️⃣ Folder (only 1)
             */
            if ($jsonPath['source'] === 'folder') {
                $folderName = dirname($jsonPath['path']);

                if (!in_array($folderName, $folders, true)) {
                    $folders[] = $folderName;

                    $documents[] = Document::fromUploaded(
                        userId: $userId,
                        groupId: $groupId,
                        meta: $meta,
                        relativePath: $parentFolder .$jsonPath['path'],
                        source: 'folder'
                    );
                }
            }

            /**
             * 2️⃣ File
             */
            $documents[] = Document::fromUploaded(
                userId: $userId,
                groupId: $groupId,
                meta: $meta,
                relativePath: $parentFolder . $jsonPath['path'],
                source: 'file'
            );
        }

        return new self(
            documents: $documents,
            rootParentId: $parentId
        );
    }

    public function rootParentId(): ?int
    {
        return $this->rootParentId;
    }
}