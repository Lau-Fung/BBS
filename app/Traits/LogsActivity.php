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
        
        return match($eventName) {
            'created' => "{$modelName} was created",
            'updated' => "{$modelName} was updated",
            'deleted' => "{$modelName} was deleted",
            'restored' => "{$modelName} was restored",
            'forceDeleted' => "{$modelName} was permanently deleted",
            default => "{$modelName} was {$eventName}",
        };
    }
}
