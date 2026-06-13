<?php

namespace App\Filament\Resources\Branches\Schemas;

use App\Filament\Resources\Cities\CityResource;
use App\Filament\Resources\Cities\Schemas\CityForm;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الفرع')
                    ->components([
                        TextInput::make('code')
                            ->label('الكود')
                            ->disabled()
                            ->hint('يتم إنشاؤه تلقائياً'),
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('اسم الفرع'),
                        TextInput::make('phone')
                            ->label('الهاتف')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('05xxxxxxxxx'),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('example@domain.com'),
                        Select::make('city_id')
                            ->label('المدينة')
                            ->relationship('city', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->createOptionForm(fn ($schema) => CityForm::configure($schema))
                            ->editOptionForm(fn ($schema) => CityForm::configure($schema))
                            ->createOptionModalHeading('إضافة مدينة جديدة')
                            ->editOptionModalHeading('تعديل المدينة')
                            ->hintActions([
                                Action::make('viewCity')
                                    ->label('عرض المدينة')
                                    ->visible(fn (Get $get): bool => (bool) $get('city_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => CityResource::getUrl('edit', ['record' => $get('city_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Textarea::make('address')
                            ->label('العنوان')
                            ->columnSpanFull()
                            ->placeholder('العنوان بالكامل...'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('معلومات الإدارة والعمولات')
                    ->components([
                        TextInput::make('manager_name')
                            ->label('اسم المدير')
                            ->maxLength(255)
                            ->placeholder('اسم مدير الفرع'),
                        TextInput::make('manager_phone')
                            ->label('هاتف المدير')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('05xxxxxxxxx'),
                        TextInput::make('commission_percentage')
                            ->label('نسبة العمولة (%)')
                            ->numeric()
                            ->default(0)
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->placeholder('0.00'),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->inline(false)
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
