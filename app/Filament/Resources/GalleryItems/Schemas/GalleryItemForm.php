<?php

namespace App\Filament\Resources\GalleryItems\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GalleryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                SpatieMediaLibraryFileUpload::make('gallery')
                    ->label('الصورة')
                    ->collection('gallery')
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255),

                Textarea::make('caption')
                    ->label('الوصف')
                    ->maxLength(500)
                    ->rows(3),

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
