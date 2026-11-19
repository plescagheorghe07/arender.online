@php
    $accent = $accent ?? '#047857';
    $wash = $wash ?? '#ecfdf5';
    $ink = $ink ?? '#064e3b';
    $kicker = $kicker ?? '';
    $addons = is_array($order->addons) ? $order->addons : [];
    $addonTotal = collect($addons)->sum(fn (array $addon): int => (int) ($addon['price'] ?? 0));
    $rental = (int) $order->price - $addonTotal;
    $money = fn (int $amount): string => number_format($amount, 0, ',', ' ').' MDL';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
    <body style="margin:0;background:#f6f3ec;padding:24px;font-family:Arial, Helvetica, sans-serif;color:#1c1917;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:24px;overflow:hidden;">
            <tr>
                <td style="height:6px;background:{{ $accent }};font-size:0;line-height:0;">&nbsp;</td>
            </tr>
            <tr>
                <td style="padding:28px 32px 8px;background:{{ $wash }};">
                    <p style="margin:0;font-size:12px;letter-spacing:0.14em;text-transform:uppercase;color:{{ $ink }};">{{ config('app.name') }}</p>
                    <p style="margin:12px 0 0;font-size:26px;line-height:1.25;font-weight:700;color:#1c1917;">{{ $kicker }}</p>
                    <p style="margin:8px 0 0;font-size:14px;color:#57534e;">{{ __('mail.order_no', ['id' => $order->id], $locale) }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding:8px 32px 0;background:{{ $wash }};">
                    <p style="margin:0;font-size:16px;line-height:1.6;color:#1c1917;">{{ $intro }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding:20px 32px 8px;background:{{ $wash }};">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:18px;">
                        <tr>
                            <td style="padding:18px 20px 6px;font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:{{ $ink }};">{{ __('mail.total', [], $locale) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:0 20px 18px;font-size:32px;line-height:1.1;font-weight:700;color:#1c1917;">{{ $money((int) $order->price) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding:12px 32px 28px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5;">
                        <tr>
                            <td style="padding:8px 16px 8px 0;color:#78716c;vertical-align:top;width:132px;">{{ __('mail.product', [], $locale) }}</td>
                            <td style="padding:8px 0;font-weight:600;">{{ $order->product_name }}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px 16px 8px 0;color:#78716c;vertical-align:top;">{{ __('mail.period', [], $locale) }}</td>
                            <td style="padding:8px 0;">{{ $order->days_label }} · {{ $money($rental) }}</td>
                        </tr>
                        @foreach ($addons as $addon)
                            <tr>
                                <td style="padding:8px 16px 8px 0;color:#78716c;vertical-align:top;">{{ __('mail.addons', [], $locale) }}</td>
                                <td style="padding:8px 0;">{{ $addon['title'] ?? '' }} · {{ $money((int) ($addon['price'] ?? 0)) }}</td>
                            </tr>
                        @endforeach
                        @foreach ([
                            __('mail.name', [], $locale) => $order->customer_name,
                            __('mail.phone', [], $locale) => $order->phone,
                            __('mail.email', [], $locale) => $order->email,
                            __('mail.address', [], $locale) => $order->address,
                        ] as $label => $value)
                            @if (filled($value))
                                <tr>
                                    <td style="padding:8px 16px 8px 0;color:#78716c;vertical-align:top;">{{ $label }}</td>
                                    <td style="padding:8px 0;">{{ $value }}</td>
                                </tr>
                            @endif
                        @endforeach
                        @if (filled($order->note))
                            <tr>
                                <td style="padding:8px 16px 8px 0;color:#78716c;vertical-align:top;">{{ __('mail.note', [], $locale) }}</td>
                                <td style="padding:8px 0;">{{ $order->note }}</td>
                            </tr>
                        @endif
                    </table>

                    @if ($changes !== [])
                        <p style="margin:22px 0 8px;font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#78716c;">{{ __('mail.changes', [], $locale) }}</p>
                        <ul style="margin:0;padding-left:18px;font-size:14px;line-height:1.6;">
                            @foreach ($changes as $change)
                                <li>{{ $change }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <p style="margin:28px 0 8px;font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:{{ $ink }};">{{ __('mail.reach', [], $locale) }}</p>
                    <p style="margin:0;font-size:14px;line-height:1.6;color:#44403c;">
                        {{ __('mail.phone', [], $locale) }}: <a href="tel:{{ config('company.phone') }}" style="color:{{ $ink }};text-decoration:none;">{{ config('company.phone_display') }}</a><br>
                        {{ __('mail.email', [], $locale) }}: <a href="mailto:{{ config('company.email') }}" style="color:{{ $ink }};text-decoration:none;">{{ config('company.email') }}</a><br>
                        {{ config('company.address') }}<br>
                        {{ __('mail.hours', [], $locale) }}: {{ config('company.hours') }}
                    </p>
                </td>
            </tr>
        </table>
    </body>
</html>
