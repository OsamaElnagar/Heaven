<?php

namespace App\Models\Concerns;

use App\Services\Accounting\DocumentSequenceService;
use Illuminate\Database\Eloquent\Model;

trait HasDocumentNumber
{
    public static function bootHasDocumentNumber(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->number)) {
                $model->number = app(DocumentSequenceService::class)
                    ->getNextNumberWithYear($model->documentType());
            }
        });
    }

    abstract public static function documentType(): string;
}
