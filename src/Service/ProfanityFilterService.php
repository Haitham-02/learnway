<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProfanityFilterService
{
    private HttpClientInterface $httpClient;
    private array $badWords = [
        'badword1', 'badword2', 'shit', 'fuck', 'damn', 'hell',
    ];

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function hasProfanity(string $text): bool
    {
        try {
            $response = $this->httpClient->request('POST', 'https://www.purgomalum.com/service/containsprofanity', [
                'json' => ['text' => $text],
            ]);

            $result = $response->toArray();
            return $result['containsProfanity'] ?? false;
        } catch (\Exception $e) {
            return $this->hasProfanityLocal($text);
        }
    }

    public function filter(string $text): string
    {
        try {
            $response = $this->httpClient->request('GET', 'https://www.purgomalum.com/service/replace', [
                'query' => ['text' => $text],
            ]);

            return $response->getContent();
        } catch (\Exception $e) {
            return $this->filterLocal($text);
        }
    }

    private function hasProfanityLocal(string $text): bool
    {
        $text = strtolower($text);
        foreach ($this->badWords as $word) {
            if (str_contains($text, $word)) {
                return true;
            }
        }
        return false;
    }

    private function filterLocal(string $text): string
    {
        foreach ($this->badWords as $word) {
            $replacement = str_repeat('*', strlen($word));
            $text = str_ireplace($word, $replacement, $text);
        }
        return $text;
    }
}
