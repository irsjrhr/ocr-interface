<?php

namespace App\Services;

class NanonetsPayloadUtils
{
    public function getResultContainer(array $payload): array
    {
        return isset($payload['result']) && is_array($payload['result'])
            ? $payload['result']
            : $payload;
    }

    public function getResultValue(array $payload, string $key): mixed
    {
        $result = $this->getResultContainer($payload);

        return $result[$key] ?? null;
    }

    public function getSignedUrls(array $payload): mixed
    {
        return $this->getResultValue($payload, 'signed_urls');
    }

    public function getLastOriginalUrl(mixed $signedUrls): array
    {
        if (is_array($signedUrls) && $signedUrls !== []) {
            $lastKey = array_key_last($signedUrls);
            $lastValue = $signedUrls[$lastKey];

            if (is_array($lastValue) && isset($lastValue['original']) && is_string($lastValue['original']) && $lastValue['original'] !== '') {
                return [$lastValue['original'], is_string($lastKey) ? $lastKey : null];
            }

            if (is_string($lastValue) && $lastValue !== '') {
                return [$lastValue, is_string($lastKey) ? $lastKey : null];
            }
        }

        return [null, null];
    }

    public function getPredictions(array $payload): array
    {
        $prediction = $this->getResultValue($payload, 'prediction');

        if (! is_array($prediction)) {
            return [];
        }

        return array_values(array_filter($prediction, 'is_array'));
    }

    public function getFieldPredictionValue(array $payload, string $label): array
    {
        foreach ($this->getPredictions($payload) as $item) {
            if (($item['type'] ?? null) !== 'field') {
                continue;
            }

            if (($item['label'] ?? null) !== $label) {
                continue;
            }

            $score = $item['score'] ?? null;
            $confidence = is_int($score) || is_float($score) ? (float) $score : null;
            $ocrText = $item['ocr_text'] ?? '';

            return [$confidence, is_string($ocrText) ? trim($ocrText) : ''];
        }

        return [null, ''];
    }
}
