<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class VisioMailClient
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function send(string $toEmail, string $toName, string $subject, string $html, string $text, array $metadata = []): void
    {
        $key = config('services.visio.key');
        $url = rtrim((string) config('services.visio.url', 'https://mail.visio.md'), '/');

        if (! is_string($key) || $key === '') {
            if (! app()->runningUnitTests()) {
                Log::warning('Visio API key is missing. The order email was not sent.');
            }

            return;
        }

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $key,
                'Accept' => 'application/json',
            ])->timeout(8)->post($url.'/api/v1/send', [
                'to' => [
                    'email' => $toEmail,
                    'name' => $toName,
                ],
                'subject' => $subject,
                'html' => $html,
                'text' => $text,
                'replyTo' => config('company.email'),
                'metadata' => $metadata,
            ]);

            if ($response->failed()) {
                Log::warning('Visio mail was not accepted.', [
                    'status' => $response->status(),
                ]);
            }
        } catch (Throwable $exception) {
            Log::warning('Visio mail could not be sent.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
