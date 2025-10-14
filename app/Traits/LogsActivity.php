<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;
use Spatie\Activitylog\LogOptions;

trait LogsActivity
{
    use SpatieLogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getLogName())
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName));
    }

    protected function getLogName(): string
    {
        return class_basename($this);
    }

    protected function getDescriptionForEvent(string $eventName): string
    {
        $modelName = class_basename($this);
        $displayName = $this->getAttribute('name')
            ?? $this->getAttribute('title')
            ?? $this->getAttribute('label')
            ?? null;

        $prefix = $displayName ? "{$modelName} '{$displayName}'" : $modelName;

        return match($eventName) {
            'created'      => "{$prefix} was created",
            'updated'      => "{$prefix} was updated",
            'deleted'      => "{$prefix} was deleted",
            'restored'     => "{$prefix} was restored",
            'forceDeleted' => "{$prefix} was permanently deleted",
            default        => "{$prefix} was {$eventName}",
        };
    }
}
