<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logModelEvent($model, 'created');
        });

        static::updated(function ($model) {
            // Compute before/after for changed attributes only
            $changed = array_keys($model->getChanges());
            $before = [];
            $after = [];
            foreach ($changed as $attr) {
                $before[$attr] = $model->getOriginal($attr);
                $after[$attr] = $model->{$attr};
            }
            self::logModelEvent($model, 'updated', $before, $after);
        });

        static::deleted(function ($model) {
            self::logModelEvent($model, 'deleted');
        });
    }

    protected static function logModelEvent($model, string $event, array $before = null, array $after = null): void
    {
        try {
            $user = auth()->user();
            AuditLog::create([
                'user_id'        => $user?->id,
                'user_email'     => $user?->email,
                'method'         => request()->method(),
                'route'          => optional(request()->route())->getName(),
                'url'            => request()->fullUrl(),
                'action'         => $event,
                'ip_address'     => request()->ip(),
                'user_agent'     => (string) request()->userAgent(),
                'payload'        => collect(request()->all())->except(['password','password_confirmation','_token'])->toArray(),
                'model_type'     => get_class($model),
                'model_id'       => $model->getKey(),
                'event'          => $event,
                'changes_before' => $before,
                'changes_after'  => $after,
            ]);
        } catch (\Throwable $e) {
            // do not interrupt flow
        }
    }
}
