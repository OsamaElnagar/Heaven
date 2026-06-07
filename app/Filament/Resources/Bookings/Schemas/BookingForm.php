<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Enums\BookingStatus;
use App\Enums\RoomType;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Packages\Schemas\PackageForm;
use App\Filament\Resources\Rooms\Schemas\RoomForm;
use App\Filament\Resources\Trips\Schemas\TripForm;
use App\Models\Package;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الحجز')
                    ->components([
                        Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->required()
                            ->createOptionForm(fn ($schema) => ClientForm::configure($schema))
                            ->editOptionForm(fn ($schema) => ClientForm::configure($schema))
                            ->createOptionModalHeading('إضافة عميل جديد')
                            ->editOptionModalHeading('تعديل بيانات العميل')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('package_id')
                            ->label('الباقة')
                            ->relationship('package', 'name')
                            ->required()
                            ->createOptionForm(fn ($schema) => PackageForm::configure($schema))
                            ->editOptionForm(fn ($schema) => PackageForm::configure($schema))
                            ->createOptionModalHeading('إضافة باقة جديده')
                            ->editOptionModalHeading('تعديل بيانات الباقة')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                static::updatePricing($set, $get, $state, $get('room_type'));
                            }),
                        Select::make('trip_id')
                            ->label('الرحلة')
                            ->relationship('trip', 'name')
                            ->createOptionForm(fn ($schema) => TripForm::configure($schema))
                            ->editOptionForm(fn ($schema) => TripForm::configure($schema))
                            ->createOptionModalHeading('إضافة رحلة جديدة')
                            ->editOptionModalHeading('تعديل بيانات الرحلة')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('room_type')
                            ->label('نوع الغرفة')
                            ->options(RoomType::class)
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, RoomType|string|null $state) {
                                static::updatePricing($set, $get, $get('package_id'), $state);
                            }),
                        Select::make('status')
                            ->label('الحالة')
                            ->options(BookingStatus::class)
                            ->required()
                            ->default(BookingStatus::PENDING)
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('التسعير')
                    ->components([
                        TextInput::make('total_price')
                            ->label('السعر الإجمالي')
                            ->required()
                            ->numeric()
                            ->prefix('ج.م')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?float $state) {
                                $discount = (float) ($get('discount') ?? 0);
                                $set('net_price', max(($state ?? 0) - $discount, 0));
                            }),
                        TextInput::make('discount')
                            ->label('الخصم')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?float $state) {
                                $total = (float) ($get('total_price') ?? 0);
                                $set('net_price', max($total - ($state ?? 0), 0));
                            }),
                        TextInput::make('net_price')
                            ->label('صافي السعر')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('paid_amount')
                            ->label('المبلغ المدفوع')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->components([
                        Select::make('room_id')
                            ->label('الغرفة')
                            ->relationship('room', 'room_number')
                            ->createOptionForm(fn ($schema) => RoomForm::configure($schema))
                            ->editOptionForm(fn ($schema) => RoomForm::configure($schema))
                            ->createOptionModalHeading('إضافة غرفة جديدة')
                            ->editOptionModalHeading('تعديل بيانات الغرفة')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        DatePicker::make('due_date')
                            ->label('تاريخ الاستحقاق')
                            ->native(false),
                        RichEditor::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    protected static function updatePricing(Set $set, Get $get, ?string $packageId, RoomType|string|null $roomType): void
    {
        if (! $packageId || ! $roomType) {
            return;
        }

        $package = Package::find($packageId);
        $roomTypeEnum = $roomType instanceof RoomType ? $roomType : RoomType::tryFrom($roomType);

        if (! $package || ! $roomTypeEnum) {
            return;
        }

        $basePrice = (float) $package->base_price;

        $surcharge = match ($roomTypeEnum) {
            RoomType::SINGLE => $basePrice * 0.5,
            RoomType::DOUBLE => 0,
            RoomType::TRIPLE => -$basePrice * 0.1,
            RoomType::QUAD => -$basePrice * 0.2,
            RoomType::QUINT => -$basePrice * 0.3,
            RoomType::SEXTUPLE => -$basePrice * 0.4,
        };

        $totalPrice = $basePrice + $surcharge;
        $discount = (float) ($get('discount') ?? 0);

        $set('total_price', $totalPrice);
        $set('net_price', max($totalPrice - $discount, 0));
    }
}
