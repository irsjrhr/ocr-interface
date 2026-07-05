<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Throwable;

class WebhookPayloadProcessor
{
    public function __construct(
        private readonly NanonetsPayloadUtils $payloadUtils,
        private readonly WebhookFileNaming $fileNaming,
        private readonly NanonetsPayloadTransformer $transformer,
        private readonly UsageStatsService $usageStats,
    ) {
    }

    public function process(array $body, ?string $sourceTypeHeader, bool $includePdf = false): void
    {
        $storagePath = config('webhook.storage_path');
        File::ensureDirectoryExists($storagePath);

        $sourceType = $this->fileNaming->sanitizeSourceType($sourceTypeHeader);
        $storageBase = $sourceType ? $storagePath . DIRECTORY_SEPARATOR . $sourceType : $storagePath;
        File::ensureDirectoryExists($storageBase);

        $invoiceNumberRaw = $this->fileNaming->extractInvoiceNumberOcr($body);
        $baseFileStem = $this->fileNaming->normalizeInvoiceNumber($invoiceNumberRaw)
            ?: $this->fileNaming->makeFallbackFileStem();

        $signedUrls = $this->payloadUtils->getSignedUrls($body);
        [$originalUrl] = $this->payloadUtils->getLastOriginalUrl($signedUrls);

        $shouldWritePdf = $includePdf && is_string($originalUrl) && $originalUrl !== '';
        $fileStem = $this->fileNaming->reserveUniqueFileStem($storageBase, $baseFileStem, $shouldWritePdf);

        $pdfFilename = $fileStem . '.pdf';
        $jsonFilename = $fileStem . '.json';
        $pdfPath = $storageBase . DIRECTORY_SEPARATOR . $pdfFilename;

        if ($shouldWritePdf) {
            try {
                Http::timeout(60)->sink($pdfPath)->get($originalUrl)->throw();
            } catch (Throwable) {
                // Keep the webhook accepted even when the source PDF is temporarily unavailable.
            }
        }

        $documentFileName = $shouldWritePdf
            ? $pdfFilename
            : (string) ($this->payloadUtils->getResultValue($body, 'input') ?: $pdfFilename);
        $documentFileUrl = is_string($originalUrl) && $originalUrl !== ''
            ? $originalUrl
            : (string) ($this->payloadUtils->getResultValue($body, 'file_url') ?: '');

        $transformedPayload = $this->transformer->buildTransformedExportPayload(
            $body,
            $documentFileName,
            $documentFileUrl,
        );

        // Download File Response JSON
        $file_response_nanonets = $storageBase . DIRECTORY_SEPARATOR . $jsonFilename;
        File::put(
            $file_response_nanonets,
            json_encode($transformedPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        );

        // Membuat Bucket dan Upload Berdasarkan File Response Nanonets Di Cloud Server MINIO Dengan Protkol
        /*  
        - Membuat bucket dengan nama file json nya
        - Upload file json nya ke bucket
        */


    
        $this->usageStats->incrementWebhookUsage();
    }
}
