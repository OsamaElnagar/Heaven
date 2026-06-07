<?php

namespace App\Models\Concerns;

use App\Services\Accounting\DocumentSequenceService;

trait HasEntityCode
{
    public static function bootHasEntityCode(): void
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = app(DocumentSequenceService::class)
                    ->getNextNumberWithYear($model::entityCodeType());
            }
        });
    }

    abstract public static function entityCodeType(): string;
}
