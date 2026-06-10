<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                FileUpload::make('image')
                    ->label('الصورة')
                    ->image()
                    ->imageEditor()
                    ->directory('posts')
                    ->visibility('public')
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($set, ?string $state) => $set(
                        'slug',
                        Str::slug($state ?? ''),
                    )),

                TextInput::make('slug')
                    ->label('الرابط')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Textarea::make('excerpt')
                    ->label('المقتطف')
                    ->maxLength(500)
                    ->rows(3),

                RichEditor::make('content')
                    ->label('المحتوى')
                    ->required()
                    ->columnSpanFull(),

                DatePicker::make('published_at')
                    ->label('تاريخ النشر')
                    ->displayFormat('Y/m/d')
                    ->helperText('اتركه فارغاً للحفظ كمسودة'),

                Toggle::make('is_published')
                    ->label('منشور')
                    ->default(false),
            ]);
    }
}
