<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuditLogger
{
    public static function record(
        string $action,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $metadata = []
    ): void {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        $request = request();

        AuditLog::query()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable ? $auditable::class : null,
            'auditable_id' => $auditable?->getKey(),
            'old_values' => self::clean($oldValues),
            'new_values' => self::clean($newValues),
            'metadata' => self::clean($metadata),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
        ]);
    }

    private static function clean(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return collect($values)
            ->except(['password', 'remember_token', 'current_password', 'password_confirmation'])
            ->all();
    }
}
