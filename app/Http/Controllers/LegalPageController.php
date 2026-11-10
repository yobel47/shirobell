<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Terms, privacy, refund and contact pages. Business identity and support
 * contacts come from admin settings so the owner can update them without a
 * deploy (required by Midtrans production review).
 */
class LegalPageController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    public function terms(): View
    {
        return $this->page('legal.terms');
    }

    public function privacy(): View
    {
        return $this->page('legal.privacy');
    }

    public function refund(): View
    {
        return $this->page('legal.refund');
    }

    public function contact(): View
    {
        return $this->page('legal.contact');
    }

    private function page(string $view): View
    {
        $siteName = (string) $this->settings->get('site_name');
        $whatsapp = (string) $this->settings->get('support_whatsapp');

        return view($view, [
            'legal' => [
                'site' => $siteName,
                'business' => trim((string) $this->settings->get('business_name')) ?: $siteName,
                'address' => trim((string) $this->settings->get('business_address')),
                'email' => trim((string) $this->settings->get('support_email')),
                'whatsapp' => $whatsapp,
                'whatsapp_url' => $whatsapp !== '' ? 'https://wa.me/'.self::waNumber($whatsapp) : null,
                'domain' => config('app.domain'),
                'price' => $this->settings->packagePrice(),
                'days' => $this->settings->packageDurationDays(),
                'updated_at' => Carbon::parse(config('shirobell.legal_updated_at'))->translatedFormat('j F Y'),
            ],
        ]);
    }

    /** 0812... or +62812... becomes 62812... for wa.me links. */
    private static function waNumber(string $number): string
    {
        $digits = preg_replace('/\D/', '', $number);

        return str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits;
    }
}
