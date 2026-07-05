<?php

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;

class NanonetsPayloadTransformer
{
    public function __construct(private readonly NanonetsPayloadUtils $payloadUtils)
    {
    }

    public function buildTransformedExportPayload(array $payload, string $documentFileName, string $documentFileUrl): array
    {
        [$invoiceConf, $invoiceValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Invoice_Number');
        [$poConf, $poValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'PO_Number');
        [$invoiceDateConf, $invoiceDateValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Invoice_Date');
        [$dueDateConf, $dueDateValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Due_Date');
        [$subtotalConf, $subtotalValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Sub_Total');
        [$totalConf, $totalValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Total_Amount');
        [$dueAmountConf, $dueAmountValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Due_Amount');
        [$currencyConf, $currencyValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Currency');
        [$vendorNameConf, $vendorNameValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Vendor_Name');
        [$vendorAddressConf, $vendorAddressValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Vendor_Address');
        [$vendorTaxConf, $vendorTaxValue] = $this->payloadUtils->getFieldPredictionValue($payload, 'Vendor_Tax_ID');

        $nowUtc = $this->isoUtcNow();

        return [
            'pagination' => [
                'total' => 1,
                'total_pages' => 1,
                'next' => null,
                'previous' => null,
            ],
            'results' => [
                [
                    'url' => '',
                    'status' => 'exporting',
                    'arrived_at' => $nowUtc,
                    'exported_at' => null,
                    'export_failed_at' => null,
                    'document' => [
                        'url' => '',
                        'file_name' => $documentFileName,
                        'file' => $documentFileUrl,
                    ],
                    'modifier' => [
                        'url' => '',
                        'username' => '',
                    ],
                    'schema' => [
                        'url' => '',
                    ],
                    'metadata' => [],
                    'content' => [
                        [
                            'category' => 'section',
                            'schema_id' => 'invoice_details_section',
                            'children' => [
                                $this->makeDatapoint('invoice_id', $this->normalizeInvoiceIdValue($invoiceValue), 'string', $invoiceConf),
                                $this->makeDatapoint('order_id', $poValue, 'string', $poConf),
                                $this->makeDatapoint('date_issue', $this->toIsoDate($invoiceDateValue), 'date', $invoiceDateConf),
                                $this->makeDatapoint('date_due', $this->toIsoDate($dueDateValue), 'date', $dueDateConf),
                            ],
                        ],
                        [
                            'category' => 'section',
                            'schema_id' => 'payment_info_section',
                            'children' => [],
                        ],
                        [
                            'category' => 'section',
                            'schema_id' => 'totals_section',
                            'children' => [
                                $this->makeDatapoint('amount_total_base', $this->toCompactNumber($subtotalValue), 'number', $subtotalConf),
                                $this->makeDatapoint('amount_total_tax', '', 'number', null),
                                $this->makeDatapoint('amount_total', $this->toCompactNumber($totalValue), 'number', $totalConf),
                                $this->makeDatapoint('amount_due', $this->toCompactNumber($dueAmountValue), 'number', $dueAmountConf),
                                $this->makeDatapoint('currency', $this->toCurrencyEnum($currencyValue), 'enum', $currencyConf),
                                [
                                    'category' => 'multivalue',
                                    'schema_id' => 'tax_details',
                                    'children' => [],
                                ],
                            ],
                        ],
                        [
                            'category' => 'section',
                            'schema_id' => 'vendor_section',
                            'children' => [
                                $this->makeDatapoint('sender_name', $this->normalizeVendorName($vendorNameValue), 'string', $vendorNameConf),
                                $this->makeDatapoint('sender_address', $vendorAddressValue, 'string', $vendorAddressConf),
                                $this->makeDatapoint('sender_vat_id', $vendorTaxValue, 'string', $vendorTaxConf),
                            ],
                        ],
                        [
                            'category' => 'section',
                            'schema_id' => 'Other_section',
                            'children' => [],
                        ],
                        [
                            'category' => 'section',
                            'schema_id' => 'line_items_section',
                            'children' => [
                                [
                                    'category' => 'multivalue',
                                    'schema_id' => 'line_items',
                                    'children' => $this->buildLineItemsChildren($payload),
                                ],
                            ],
                        ],
                    ],
                    'automated' => false,
                    'modified_at' => $nowUtc,
                    'assigned_at' => $nowUtc,
                ],
            ],
        ];
    }

    private function isoUtcNow(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s.u\Z');
    }

    private function normalizeInvoiceIdValue(string $rawValue): string
    {
        return preg_replace('/\s+/', '', $rawValue) ?? '';
    }

    private function normalizeVendorName(string $rawValue): string
    {
        $compact = preg_replace('/\s*\.\s*/', '.', trim($rawValue)) ?? '';

        return preg_replace('/\s{2,}/', ' ', $compact) ?? '';
    }

    private function toIsoDate(string $rawValue): string
    {
        $value = trim($rawValue);

        if ($value === '') {
            return '';
        }

        foreach (['Y-m-d', 'd.m.Y', 'm/d/Y', 'd/m/Y'] as $format) {
            $parsed = DateTimeImmutable::createFromFormat('!' . $format, $value);

            if ($parsed !== false && $parsed->format($format) === $value) {
                return $parsed->format('Y-m-d');
            }
        }

        return $value;
    }

    private function toCompactNumber(string $rawValue): string
    {
        $value = trim($rawValue);

        if ($value === '') {
            return '';
        }

        $negative = str_starts_with($value, '-');
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if ($digits === '') {
            return '';
        }

        return $negative ? '-' . $digits : $digits;
    }

    private function toQuantityNumber(string $rawValue): string
    {
        $value = preg_replace('/\s+/', '', trim($rawValue)) ?? '';

        if ($value === '') {
            return '';
        }

        $value = str_replace(',', '.', $value);
        $value = preg_replace('/[^0-9.\-]/', '', $value) ?? '';

        return preg_replace('/\.{2,}/', '.', $value) ?? '';
    }

    private function toCurrencyEnum(string $rawValue): string
    {
        $value = trim($rawValue);

        if ($value === '') {
            return '';
        }

        if (strtoupper($value) === 'IDR') {
            return 'other';
        }

        return strtolower($value);
    }

    private function makeDatapoint(string $schemaId, string $value, string $valueType, ?float $confidence): array
    {
        return [
            'category' => 'datapoint',
            'schema_id' => $schemaId,
            'rir_confidence' => $confidence,
            'value' => $value,
            'type' => $valueType,
        ];
    }

    private function buildLineItemsChildren(array $payload): array
    {
        $tableCells = [];

        foreach ($this->payloadUtils->getPredictions($payload) as $item) {
            if (($item['type'] ?? null) === 'table' && isset($item['cells']) && is_array($item['cells'])) {
                $tableCells = array_values(array_filter($item['cells'], 'is_array'));
                break;
            }
        }

        $rows = [];

        foreach ($tableCells as $cell) {
            $row = $cell['row'] ?? null;
            $label = $cell['label'] ?? null;
            $text = $cell['text'] ?? '';

            if (! is_int($row) || $row < 1) {
                continue;
            }

            if (! is_string($label) || $label === '') {
                continue;
            }

            if (! is_string($text)) {
                $text = '';
            }

            $rows[$row] ??= [];
            $rows[$row][$label] = trim($text);
        }

        ksort($rows);

        $lineItems = [];

        foreach ($rows as $row) {
            $lineItems[] = [
                'category' => 'tuple',
                'schema_id' => 'line_item',
                'children' => [
                    $this->makeDatapoint('item_code', $row['Code'] ?? '', 'string', null),
                    $this->makeDatapoint('item_description', $row['Description'] ?? '', 'string', null),
                    $this->makeDatapoint('item_quantity', $this->toQuantityNumber($row['Quantity'] ?? ''), 'number', null),
                    $this->makeDatapoint('item_uom', $row['UOM'] ?? '', 'string', null),
                    $this->makeDatapoint('item_amount_base', '', 'number', null),
                    $this->makeDatapoint('item_amount', $this->toCompactNumber($row['Unit_Price'] ?? ''), 'number', null),
                    $this->makeDatapoint('item_total_base', '', 'number', null),
                    $this->makeDatapoint('item_amount_total', $this->toCompactNumber($row['Total_Amount'] ?? ''), 'number', null),
                ],
            ];
        }

        return $lineItems;
    }
}
