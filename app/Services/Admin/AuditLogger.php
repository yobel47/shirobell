<?php

namespace App\Services\Admin;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public function log(string $action, ?Model $subject = null, array $meta = []): AdminActivityLog
    {
        $log = new AdminActivityLog;
        $log->forceFill([
            'admin_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'meta' => $meta ?: null,
            'ip_hash' => request()->ip() ? hash_hmac('sha256', (string) request()->ip(), (string) config('app.key')) : null,
            'created_at' => now(),
        ])->save();

        return $log;
    }
}
