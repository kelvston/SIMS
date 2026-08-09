<?php

namespace App\Models\Concerns;

use App\Services\AuditLogger;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLogger::record('created', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if ($changes === []) {
                return;
            }

            AuditLogger::record(
                'updated',
                $model,
                collect(array_keys($changes))->mapWithKeys(fn ($key) => [$key => $model->getOriginal($key)])->all(),
                $changes
            );
        });

        static::deleted(function ($model) {
            AuditLogger::record('deleted', $model, $model->getOriginal(), null);
        });
    }
}
