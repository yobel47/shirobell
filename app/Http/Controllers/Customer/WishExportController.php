<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WishExportController extends Controller
{
    public function __invoke(Invitation $invitation): StreamedResponse
    {
        Gate::authorize('view', $invitation);

        $filename = 'rsvp-ucapan-'.$invitation->slug.'-'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($invitation) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows Indonesian text correctly.
            fputcsv($out, ['Waktu', 'Nama', 'Kehadiran', 'Jumlah orang', 'Ucapan', 'Tamu terdaftar', 'Grup', 'Disembunyikan']);

            $invitation->wishes()->with('guest:id,name,group_name')->orderBy('id')->chunk(500, function ($wishes) use ($out) {
                foreach ($wishes as $wish) {
                    fputcsv($out, array_map([self::class, 'safe'], [
                        $wish->created_at->format('Y-m-d H:i'),
                        $wish->name,
                        $wish->attendance?->label() ?? '',
                        $wish->pax ?? '',
                        $wish->message ?? '',
                        $wish->guest ? 'Ya' : 'Tidak',
                        $wish->guest?->group_name ?? '',
                        $wish->is_hidden ? 'Ya' : 'Tidak',
                    ]));
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Neutralise spreadsheet formula injection (cells starting with = + - @ tab CR). */
    public static function safe(mixed $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
