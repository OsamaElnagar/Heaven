<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('question')
                    ->label('السؤال')
                    ->required()
                    ->maxLength(500),

                Textarea::make('answer')
                    ->label('الإجابة')
                    ->required()
                    ->rows(5),

                TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->integer()
                    ->default(0)
                    ->helperText('الأقل يظهر أولاً'),

                Toggle::make('is_published')
                    ->label('منشور')
                    ->default(true),
            ]);
    }
}
