<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Application\GoogleCloud\VertexAIService;

class VertexAIImportJob implements ShouldQueue
{
    use Queueable;

    protected $groupId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * Execute the job.
     */
    public function handle(VertexAIService $vertexAIService): void
    {
        $vertexAIService->triggerImport($this->groupId);
    }
}
