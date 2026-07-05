<?php

namespace App\Jobs;

use App\Services\WebhookPayloadProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhookPayload implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly array $body,
        private readonly ?string $sourceType,
        private readonly bool $includePdf = false,
    ) {
    }

    public function handle(WebhookPayloadProcessor $processor): void
    {
        $processor->process($this->body, $this->sourceType, $this->includePdf);
    }
}
