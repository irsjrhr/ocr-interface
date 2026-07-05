<?php

return [
    'master_api_key' => env('MASTER_API_KEY', 'changeme_master_key'),
    'storage_path' => env('WEBHOOK_STORAGE_PATH') ?: storage_path('app/webhook-data'),
];
