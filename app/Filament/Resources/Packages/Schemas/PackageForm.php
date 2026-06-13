<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Enums\PackageGrade;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الباقة')
                    ->components([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required(),
                        Select::make('type_id')
                            ->label('النوع')
                            ->relationship('type', 'name_ar')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('grade')
                            ->label('الدرجة')
                            ->options(PackageGrade::class)
                            ->default(PackageGrade::STANDARD)
                            ->required()
                            ->native(false),
                        TextInput::make('season_year')
                            ->label('سنة الموسم')
                            ->required()
                            ->numeric(),
                        TextInput::make('duration_nights')
                            ->label('عدد الليالي')
                            ->required()
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateDates($set, $get);
                            }),
                        TextInput::make('base_price')
                            ->label('السعر الأساسي')
                            ->required()
                            ->numeric()
                            ->prefix('ج.م'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('المقاعد والتواريخ')
                    ->components([
                        DatePicker::make('departure_date')
                            ->label('تاريخ المغادرة')
                            ->native(false)
                            ->rules(fn (Get $get): array => [
                                function ($attribute, $value, $fail) use ($get) {
                                    $return = $get('return_date');
                                    if ($value && $return && Carbon::parse($value)->isAfter(Carbon::parse($return))) {
                                        $fail('تاريخ المغادرة يجب أن يكون قبل تاريخ العودة.');
                                    }
                                },
                            ])
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateDuration($set, $get);
                            }),
                        DatePicker::make('return_date')
                            ->label('تاريخ العودة')
                            ->native(false)
                            ->rules(fn (Get $get): array => [
                                function ($attribute, $value, $fail) use ($get) {
                                    $departure = $get('departure_date');
                                    if ($value && $departure && Carbon::parse($value)->isBefore(Carbon::parse($departure))) {
                                        $fail('تاريخ العودة يجب أن يكون بعد تاريخ المغادرة.');
                                    }
                                },
                            ])
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateDuration($set, $get);
                            }),
                        TextInput::make('total_seats')
                            ->label('إجمالي المقاعد')
                            ->required()
                            ->numeric()
                            ->rules(fn (Get $get): array => [
                                function ($attribute, $value, $fail) use ($get) {
                                    $reserved = (int) ($get('reserved_seats') ?? 0);
                                    if ((int) $value < $reserved) {
                                        $fail('إجمالي المقاعد لا يمكن أن يكون أقل من المقاعد المحجوزة.');
                                    }
                                },
                            ])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                static::updateAvailableSeats($set, $get);
                            }),
                        TextInput::make('reserved_seats')
                            ->label('المقاعد المحجوزة')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('available_seats')
                            ->label('المقاعد المتاحة')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                        Toggle::make('front_office_visible')
                            ->label('ظاهرة للمكتب الأمامي')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('تفاصيل إضافية')
                    ->components([
                        RichEditor::make('includes')
                            ->label('يشمل')
                            ->columnSpanFull(),
                        RichEditor::make('excludes')
                            ->label('لا يشمل')
                            ->columnSpanFull(),
                        RichEditor::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected static function updateDuration(Set $set, Get $get): void
    {
        $departure = $get('departure_date');
        $return = $get('return_date');

        if ($departure && $return) {
            $diff = Carbon::parse($departure)->diffInDays($return);
            $set('duration_nights', max($diff, 0));

            return;
        }

        static::updateDates($set, $get);
    }

    protected static function updateDates(Set $set, Get $get): void
    {
        $departure = $get('departure_date');
        $duration = (int) ($get('duration_nights') ?? 0);

        if ($departure && $duration) {
            $set('return_date', Carbon::parse($departure)->addDays($duration));

            return;
        }

        $return = $get('return_date');

        if ($return && $duration) {
            $set('departure_date', Carbon::parse($return)->subDays($duration));
        }
    }

    protected static function updateAvailableSeats(Set $set, Get $get): void
    {
        $total = (int) ($get('total_seats') ?? 0);
        $reserved = (int) ($get('reserved_seats') ?? 0);

        $set('available_seats', max($total - $reserved, 0));
    }
}
