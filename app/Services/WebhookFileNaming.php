<?php

namespace App\Services;

use Illuminate\Support\Str;

class WebhookFileNaming
{
    public function __construct(private readonly NanonetsPayloadUtils $payloadUtils)
    {
    }

    public function sanitizeSourceType(?string $rawValue): ?string
    {
        if ($rawValue === null || $rawValue === '') {
            return null;
        }

        if (str_contains($rawValue, '..') || str_contains($rawValue, '/') || str_contains($rawValue, '\\')) {
            return null;
        }

        return $rawValue;
    }

    public function extractInvoiceNumberOcr(array $payload): ?string
    {
        [, $fieldValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Invoice_Number');

        return $fieldValue !== '' ? $fieldValue : null;
    }

    public function normalizeInvoiceNumber(?string $rawValue): ?string
    {
        if ($rawValue === null) {
            return null;
        }

        $withoutSpaces = preg_replace('/\s+/', '', $rawValue);
        $withDots = str_replace(['-', '/', '\\'], '.', $withoutSpaces ?? '');
        $normalized = preg_replace('/[^A-Za-z0-9._]/', '_', $withDots);
        $normalized = preg_replace('/_+/', '_', $normalized ?? '');
        $normalized = preg_replace('/\.+/', '.', $normalized ?? '');
        $normalized = trim($normalized ?? '', '._');

        return $normalized === '' ? null : $normalized;
    }

    public function makeFallbackFileStem(): string
    {
        return now()->format('Ymd_His') . '_' . substr(Str::uuid()->toString(), 0, 8);
    }

    public function reserveUniqueFileStem(string $storageBase, string $baseFileStem, bool $includePdf): string
    {
        $candidateName = $baseFileStem;
        $suffix = 0;

        while (true) {
            $jsonPath = $storageBase . DIRECTORY_SEPARATOR . $candidateName . '.json';
            $pdfPath = $storageBase . DIRECTORY_SEPARATOR . $candidateName . '.pdf';

            if (! file_exists($jsonPath) && (! $includePdf || ! file_exists($pdfPath))) {
                return $candidateName;
            }

            $suffix++;
            $candidateName = $baseFileStem . '_' . $suffix;
        }
    }
}
