<?php

namespace App\Orders\Services;

use App\Mail\VisioMailClient;
use App\Orders\Models\Order;
use Illuminate\Support\Facades\Lang;

class OrderNotifier
{
    public function __construct(private readonly VisioMailClient $mail) {}

    public function created(Order $order): void
    {
        $this->send($order, 'created', []);
    }

    /**
     * @param  list<string>  $changes
     */
    public function updated(Order $order, array $changes): void
    {
        if ($changes === []) {
            return;
        }

        $this->send($order, 'updated', $changes);
    }

    public function statusChanged(Order $order): void
    {
        $event = in_array($order->status, ['confirmed', 'done', 'cancelled'], true)
            ? $order->status
            : 'status';

        $this->send($order, $event, []);
    }

    /**
     * @param  list<string>  $changes
     */
    private function send(Order $order, string $event, array $changes): void
    {
        $email = trim((string) $order->email);

        if ($email === '') {
            return;
        }

        $locale = $order->locale ?: (string) config('app.locale');
        $status = Lang::get('admin.status_'.$order->status, [], $locale);
        $subject = Lang::get('mail.'.$event.'_subject', [
            'id' => $order->id,
            'status' => $status,
        ], $locale);
        $intro = Lang::get('mail.'.$event.'_intro', [
            'name' => $order->customer_name,
            'status' => $status,
        ], $locale);

        $introText = is_string($intro) ? $intro : '';
        $statusText = is_string($status) ? $status : '';

        $template = view()->exists('mail.events.'.$event) ? $event : 'status';

        $this->mail->send(
            toEmail: $email,
            toName: $order->customer_name,
            subject: is_string($subject) ? $subject : '',
            html: view('mail.events.'.$template, [
                'intro' => $introText,
                'order' => $order,
                'changes' => $changes,
                'locale' => $locale,
                'status' => $statusText,
            ])->render(),
            text: $this->text($introText, $locale, $order, $changes, $statusText),
            metadata: [
                'orderId' => $order->id,
                'event' => $event,
            ],
        );
    }

    /**
     * @param  list<string>  $changes
     */
    private function text(string $intro, string $locale, Order $order, array $changes, string $status): string
    {
        $lines = [
            $intro,
            '',
            Lang::get('mail.total', [], $locale).': '.number_format((int) $order->price, 0, ',', ' ').' MDL',
            Lang::get('mail.product', [], $locale).': '.$order->product_name,
            Lang::get('mail.period', [], $locale).': '.$order->days_label,
            Lang::get('mail.status', [], $locale).': '.$status,
            Lang::get('mail.address', [], $locale).': '.$order->address,
        ];

        foreach (is_array($order->addons) ? $order->addons : [] as $addon) {
            $title = is_array($addon) ? (string) ($addon['title'] ?? '') : '';
            $price = is_array($addon) ? (int) ($addon['price'] ?? 0) : 0;
            $lines[] = Lang::get('mail.addons', [], $locale).': '.$title.' · '.number_format($price, 0, ',', ' ').' MDL';
        }

        if (is_string($order->note) && trim($order->note) !== '') {
            $lines[] = Lang::get('mail.note', [], $locale).': '.$order->note;
        }

        foreach ($changes as $change) {
            $lines[] = $change;
        }

        $lines[] = '';
        $lines[] = Lang::get('mail.reach', [], $locale);
        $lines[] = Lang::get('mail.phone', [], $locale).': '.config('company.phone_display');
        $lines[] = Lang::get('mail.email', [], $locale).': '.config('company.email');
        $lines[] = (string) config('company.address');
        $lines[] = Lang::get('mail.hours', [], $locale).': '.config('company.hours');

        return implode("\n", $lines);
    }
}
