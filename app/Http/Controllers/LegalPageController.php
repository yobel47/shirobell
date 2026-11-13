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
        $contacts = $this->settings->supportContacts();

        return view($view, [
            'legal' => [
                'site' => $siteName,
                'business' => trim((string) $this->settings->get('business_name')) ?: $siteName,
                'address' => trim((string) $this->settings->get('business_address')),
                'email' => $contacts['email'] ?? '',
                'whatsapp' => $contacts['whatsapp'] ?? '',
                'whatsapp_url' => $contacts['whatsapp_url'],
                'instagram' => $contacts['instagram'],
                'instagram_url' => $contacts['instagram_url'],
                'domain' => config('app.domain'),
                'price' => $this->settings->packagePrice(),
                'days' => $this->settings->packageDurationDays(),
                'updated_at' => Carbon::parse(config('shirobell.legal_updated_at'))->translatedFormat('j F Y'),
            ],
        ]);
    }
}
