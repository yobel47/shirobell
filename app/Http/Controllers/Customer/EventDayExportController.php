<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\EventGift;
use App\Models\Invitation;
use App\Policies\InvitationPolicy;
use App\Services\Admin\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Event-day CSV: attendance (?jenis=kehadiran) or gifts (?jenis=hadiah). */
class EventDayExportController extends Controller
{
    public function __invoke(Request $request, Invitation $invitation): StreamedResponse
    {
        Gate::authorize('update', $invitation);
        $gifts = $request->query('jenis') === 'hadiah';

        if (InvitationPolicy::actingAsAdmin(auth()->user(), $invitation)) {
            app(AuditLogger::class)->log('invitation.admin_export', $invitation, [
                'owner_id' => $invitation->user_id,
                'export' => $gifts ? 'event_gifts_csv' : 'attendance_csv',
            ]);
        }

        $filename = ($gifts ? 'hadiah-' : 'kehadiran-').$invitation->slug.'-'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($invitation, $gifts) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            $row = fn (array $cells) => fputcsv($out, array_map([WishExportController::class, 'safe'], $cells));

            if ($gifts) {
                $row(['Waktu', 'Pemberi', 'Jenis', 'Nominal', 'Catatan', 'Grup']);
                $invitation->eventGifts()->with('guest:id,group_name')->orderBy('id')->chunk(500, function ($items) use ($row) {
                    foreach ($items as $gift) {
                        $row([$gift->created_at->format('Y-m-d H:i'), $gift->giver_name, EventGift::KINDS[$gift->kind] ?? $gift->kind, $gift->amount ?? '', $gift->note ?? '', $gift->guest?->group_name ?? '']);
                    }
                });
            } else {
                $row(['Nama', 'Grup', 'Meja', 'Hadir', 'Waktu check-in', 'Jumlah orang', 'Souvenir', 'Tamu dadakan']);
                $invitation->guests()->orderBy('name')->chunk(500, function ($guests) use ($row) {
                    foreach ($guests as $guest) {
                        $row([
                            $guest->name, $guest->group_name ?? '', $guest->table_number ?? '',
                            $guest->checked_in_at ? 'Ya' : 'Tidak', $guest->checked_in_at?->format('Y-m-d H:i') ?? '',
                            $guest->checked_in_pax ?? '', $guest->souvenir_at ? 'Ya' : 'Tidak', $guest->is_walk_in ? 'Ya' : 'Tidak',
                        ]);
                    }
                });
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
