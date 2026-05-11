<?php

namespace App\Service\Ai;

use Gemini\Client;
use Qdrant\Config;
use Qdrant\Qdrant;
use Qdrant\Models\PointsStruct;
use Qdrant\Models\PointStruct;
use Qdrant\Models\VectorStruct;
use Qdrant\Models\Request\SearchRequest;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

class VectorSearchService
{
    private ?Qdrant $qdrant = null;
    private ?Client $gemini = null;
    private string $collectionName = 'learnway_knowledge';

    public function __construct(
        private ParameterBagInterface $params,
        private LoggerInterface $logger
    ) {
        $qdrantHost = $this->params->get('qdrant_host');
        $geminiKey = $this->params->get('gemini_api_key');

        if ($qdrantHost) {
            try {
                $config = new Config($qdrantHost);
                $this->qdrant = new Qdrant($config);
            } catch (\Exception $e) {
                $this->logger->warning('Qdrant initialization failed: ' . $e->getMessage());
            }
        }

        if ($geminiKey) {
            $this->gemini = \Gemini::client($geminiKey);
        }
    }

    public function generateEmbedding(string $text): array
    {
        if (!$this->gemini) {
            return array_fill(0, 768, 0.0);
        }

        try {
            $response = $this->gemini->embeddings()->embedContent([
                'model' => 'models/text-embedding-004',
                'content' => ['parts' => [['text' => $text]]]
            ]);
            return $response->embedding->values;
        } catch (\Exception $e) {
            $this->logger->error('Embedding Generation Error: ' . $e->getMessage());
            return array_fill(0, 768, 0.0);
        }
    }

    public function indexChunk(string $id, string $text, array $metadata): void
    {
        if (!$this->qdrant) return;

        try {
            $vector = $this->generateEmbedding($text);
            $point = new PointStruct(
                $id,
                new VectorStruct($vector),
                array_merge($metadata, ['content' => $text])
            );
            $this->qdrant->collections($this->collectionName)->points()->upsert(new PointsStruct([$point]));
        } catch (\Exception $e) {
            $this->logger->error('Qdrant Indexing Error: ' . $e->getMessage());
        }
    }

    public function search(string $query, array $filters = [], int $limit = 5): array
    {
        if (!$this->qdrant) return [];

        try {
            $vector = $this->generateEmbedding($query);
            $searchRequest = new SearchRequest(new VectorStruct($vector));
            $searchRequest->setLimit($limit);
            $searchRequest->setWithPayload(true);

            // Apply Filters (Qdrant Filter API)
            if (!empty($filters)) {
                $qdrantFilter = new \Qdrant\Models\Filter\Filter();
                
                // If user_id is provided, prioritize content uploaded by this user 
                // OR generic content (placeholder for class-shared content if implemented)
                if (isset($filters['user_id'])) {
                    $qdrantFilter->addMust(
                        new \Qdrant\Models\Filter\Condition\MatchString('user_id', $filters['user_id'])
                    );
                }

                $searchRequest->setFilter($qdrantFilter);
            }

            $response = $this->qdrant->collections($this->collectionName)->points()->search($searchRequest);
            return $response['result'] ?? [];
        } catch (\Exception $e) {
            $this->logger->warning('Qdrant Search Error (Possible offline): ' . $e->getMessage());
            return [];
        }
    }
}
