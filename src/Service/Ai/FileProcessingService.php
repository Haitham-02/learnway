<?php

namespace App\Service\Ai;

use Smalot\PdfParser\Parser;

class FileProcessingService
{
    public function __construct(
        private VectorSearchService $vectorSearch
    ) {}

    /**
     * Process an uploaded file and index its content.
     * Returns the first portion of the text for summarization.
     */
    public function processFile(string $filePath, string $originalFilename, string $sourceType, int $sourceId, array $metadata): string
    {
        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $text = "";

        if ($extension === 'pdf') {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
        } elseif ($extension === 'txt') {
            $text = file_get_contents($filePath);
        }

        if (empty($text)) return "";

        // Chunking
        $chunks = $this->chunkText($text);

        foreach ($chunks as $index => $chunk) {
            $id = md5($sourceType . $sourceId . $index);
            $this->vectorSearch->indexChunk($id, $chunk, array_merge($metadata, [
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'chunk_index' => $index
            ]));
        }

        // Return up to the first 10,000 characters for immediate summarization
        return mb_substr($text, 0, 10000);
    }

    private function chunkText(string $text, int $size = 1000, int $overlap = 100): array
    {
        $chunks = [];
        $length = mb_strlen($text);
        
        for ($i = 0; $i < $length; $i += ($size - $overlap)) {
            $chunks[] = mb_substr($text, $i, $size);
            if ($i + $size >= $length) break;
        }
        
        return $chunks;
    }
}
