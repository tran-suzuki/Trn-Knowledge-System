<?php

namespace App\Application\GoogleCloud;

use App\Application\Chat\Dto\In\MessageListInputDto;
use Google\Cloud\DiscoveryEngine\V1\Client\SearchServiceClient;
use Google\Cloud\DiscoveryEngine\V1\SearchRequest;
use Google\Cloud\DiscoveryEngine\V1\SearchRequest\ContentSearchSpec;
use Google\Cloud\DiscoveryEngine\V1\SearchRequest\ContentSearchSpec\SummarySpec;
use Google\Cloud\DiscoveryEngine\V1\SearchRequest\ContentSearchSpec\SnippetSpec;
use App\Application\GoogleCloud\GeminiService;
use App\Domain\ChatSession\ChatSessionRepositoryInterface;
use App\Application\Chat\Dto\In\RAGInputDto;
use App\Application\Chat\Dto\View\ChatMessageListItemDto;
use App\Application\Chat\MessageListService;
use App\Application\GoogleCloud\Dto\RagAskResultDto;
use App\Domain\ChatSession\In\ChatMessageStoreInput;
use App\Domain\ChatSession\In\ChatSessionStoreInput;
use App\Domain\ChatSession\View\ChatMessage;
use App\Domain\ChatSession\View\ChatSession;
use App\Domain\Group\GroupRepositoryInterface;
use App\Models\DtChatSession;
use Illuminate\Support\Str;

final class RagService
{
    private string $projectId;
    private string $location;
    private string $servingConfigId = 'default_config';

    public function __construct(
        private GeminiService $geminiService,
        private ChatSessionRepositoryInterface $chatSessionRepository,
        private GroupRepositoryInterface $groupRepository,  
        private MessageListService $messageListService,
    ) {
        $this->projectId = env('GOOGLE_CLOUD_PROJECT_ID');
        $this->dataStoreId = env('VERTEX_AI_ENGINE_ID');
        $this->location = env('VERTEX_AI_LOCATION', 'global');
    }

    private function getOptions(): array
    {
        return [
            'credentials' => env(key: 'GOOGLE_APPLICATION_CREDENTIALS'),
            'projectId' => $this->projectId,
        ];
    }

    public function handle(RAGInputDto $input): RagAskResultDto
    {
        try {
            $group = $this->groupRepository->getByDisplayId($input->groupDisplayId);
            $sessionId = $this->newChatMessage($input, $group->id, 'user', $input->question);

            $messageResult = $this->messageListService->handle(new MessageListInputDto(sessionId: $sessionId));
            $sessionDisplayId = $this->getSessionDisplayId($sessionId);

              if (!$this->geminiService->isMeaningfulQuestion($input->question)) {
                $errorMessage = '申し訳ありませんが、ご質問の内容を判別できませんでした。もう少し具体的に入力してください。';
                $sessionDisplayId = $this->getSessionDisplayId($sessionId);
                $newArray =[];
                $messageResult = $this->messageListService->handle(new MessageListInputDto(sessionId: $sessionId));
                $newArray = array_merge($messageResult->items, [new ChatMessageListItemDto(role: 'ai', content: $errorMessage, metadata: null)]);
                return new RagAskResultDto(
                    sessionDisplayId: $sessionDisplayId,
                    messages: $newArray,
                );
            }

            $context = '';
            $lastMessages = array_slice($input->history, -5);
            foreach ($lastMessages as $msg) {
                $context .= "Q: {$msg['content']}\n";
            }

            $fullQuery = <<<TEXT
                                【チャットのテーマ】
                                {$input->titleChat}
                                以下は参考用の直近の会話履歴です。
                                質問に指示語や省略表現が含まれる場合のみ参照してください。
                                {$context}
                                【現在の質問】
                                {$input->question}
                                TEXT;

            $result = $this->search($fullQuery, $group->id);
            $aiChatAnswer = '';
            if (empty($result['answer']) && empty($result['documents'])) {
                
                $aiChatAnswer = '申し訳ありませんが、該当する情報が見つかりませんでした。';
            } else {
                $aiChatAnswer = $this->geminiService->summarizeRagAnswer(
                    question: $input->question,
                    rawAnswer: $result['answer'] ?? '',
                    documents: $result['documents'] ?? []
                );
            }
           
            $this->newChatMessage($input, $group->id, 'ai', $aiChatAnswer, $sessionId);

            $messageResult = $this->messageListService->handle(new MessageListInputDto(sessionId: $sessionId));

            return new RagAskResultDto(
                sessionDisplayId: $sessionDisplayId,
                messages: $messageResult->items,
            );
        } catch (\Throwable $th) {
            dd(77);
           throw $th;
        }
    }

    private function getSessionDisplayId(int $sessionId): string
    {
        $session = DtChatSession::query()->find($sessionId);

        return $session?->display_id ?? '';
    }

    private function search(string $question, int $groupId): array
    {
        $client = new SearchServiceClient($this->getOptions());
        $servingConfig = sprintf(
            'projects/%s/locations/%s/collections/default_collection/engines/%s/servingConfigs/%s',
            $this->projectId,
            $this->location,
            env('VERTEX_AI_ENGINE_ID'),
            $this->servingConfigId
        );

        $summarySpec = (new SummarySpec())
            ->setSummaryResultCount(5)
            ->setIncludeCitations(true)
            ->setLanguageCode('ja');

        $snippetSpec = (new SnippetSpec())
            ->setReturnSnippet(true);

        $contentSearchSpec = (new ContentSearchSpec())
            ->setSummarySpec($summarySpec)
            ->setSnippetSpec($snippetSpec);

        $request = (new SearchRequest())
            ->setServingConfig($servingConfig)
            ->setQuery($question)
            ->setFilter("group_id: ANY(\"" . (string) $groupId . "\")")
            ->setContentSearchSpec($contentSearchSpec)
            ->setPageSize(5);

        $response = $client->search($request);
        $searchResponse = $response->getPage()->getResponseObject();
        $answerText = $searchResponse->getSummary() ? $searchResponse->getSummary()->getSummaryText() : '';

        $docs = [];
        foreach ($response->iterateAllElements() as $result) {
            $doc = $result->getDocument();
            $data = json_decode($doc->getStructData()->serializeToJsonString(), true);
            $content = '';

            $derivedStructData = $doc->getDerivedStructData();
            if ($derivedStructData) {
                $derivedData = json_decode($derivedStructData->serializeToJsonString(), true);

                if (isset($derivedData['extractive_segments'])) {
                    foreach ($derivedData['extractive_segments'] as $segment) {
                        $content .= ($segment['content'] ?? '') . "\n";
                    }
                }

                if (empty($content) && isset($derivedData['snippets'][0]['snippet'])) {
                    $content = $derivedData['snippets'][0]['snippet'];
                }
            }

            $docs[] = [
                'file_name' => $data['file_name'] ?? 'Unknown',
                'summary' => $content ?: ($data['summary'] ?? ''),
            ];
        }

        return [
            'answer' => $answerText,
            'documents' => $docs
        ];
    }

    private function newChatMessage(
        RAGInputDto $input,
        int $groupId,
        string $role,
        string $content,
        ?int $sessionId = null
    ): int {
        try {
            $resultSessionId = 0;
            if ($sessionId == null) {
                if (is_null($input->sessionDisplayId)) {
                    $inputChatSession = new ChatSessionStoreInput(
                        displayId: $this->generateUniqueDisplayId(),
                        fkUserId: $input->userId,
                        fkCompanyId: $input->companyId,
                        fkGroupId: $groupId,
                        title: $input->titleChat,
                        fkCreatedBy: $input->userId,
                    );
                    $chatSessionDomain = ChatSession::create($inputChatSession);
                    $resultSessionId = $this->chatSessionRepository->storeChatSession($chatSessionDomain);
                } else {
                    $resultSessionId = $this->chatSessionRepository->getByDisplayId($input->sessionDisplayId);
                }
            } else {
                $resultSessionId = $sessionId;
            }

            $inputChatMessage = new ChatMessageStoreInput(
                sessionId: $resultSessionId,
                role: $role,
                content: $content,
                metadata: null
            );
           
            $chatMessage = ChatMessage::create($inputChatMessage);
            $this->chatSessionRepository->storeChatMessage($chatMessage);
            return $resultSessionId;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function generateUniqueDisplayId(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $displayId = Str::random(8);

            if (!$this->chatSessionRepository->existsByDisplayId($displayId)) {
                return $displayId;
            }
        }

        throw new \RuntimeException(__('group.display_id_exist'));
    }
}