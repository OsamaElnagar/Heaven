<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Enums\BookingChannel;
use App\Enums\BookingStatus;
use App\Enums\RoomType;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\Packages\Schemas\PackageForm;
use App\Filament\Resources\Rooms\RoomResource;
use App\Filament\Resources\Rooms\Schemas\RoomForm;
use App\Filament\Resources\Trips\Schemas\TripForm;
use App\Filament\Resources\Trips\TripResource;
use App\Models\Package;
use App\Models\Trip;
use App\Services\BookingService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

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
                            ->native(false)
                            ->hintActions([
                                Action::make('viewClient')
                                    ->label('عرض العميل')
                                    ->visible(fn (Get $get): bool => (bool) $get('client_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => ClientResource::getUrl('edit', ['record' => $get('client_id')]))
                                    ->openUrlInNewTab(),
                            ]),
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
                            })
                            ->hintActions([
                                Action::make('viewPackage')
                                    ->label('عرض الباقة')
                                    ->visible(fn (Get $get): bool => (bool) $get('package_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => PackageResource::getUrl('edit', ['record' => $get('package_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Select::make('trip_id')
                            ->label('الرحلة')
                            ->relationship('trip', 'name')
                            ->createOptionForm(fn ($schema) => TripForm::configure($schema))
                            ->editOptionForm(fn ($schema) => TripForm::configure($schema))
                            ->createOptionModalHeading('إضافة رحلة جديدة')
                            ->editOptionModalHeading('تعديل بيانات الرحلة')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if (! $state || ! $get('package_id')) {
                                    return;
                                }

                                $tripPackageId = Trip::where('id', $state)->value('package_id');

                                if ($tripPackageId !== $get('package_id')) {
                                    $set('trip_id', null);
                                }
                            })
                            ->hintActions([
                                Action::make('viewTrip')
                                    ->label('عرض الرحلة')
                                    ->visible(fn (Get $get): bool => (bool) $get('trip_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => TripResource::getUrl('edit', ['record' => $get('trip_id')]))
                                    ->openUrlInNewTab(),
                            ]),
                        Select::make('room_type')
                            ->label('نوع الغرفة')
                            ->options(RoomType::class)
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, RoomType|string|null $state) {
                                static::updatePricing($set, $get, $get('package_id'), $state);
                            }),
                        Select::make('channel')
                            ->label('مصدر العميل')
                            ->options(BookingChannel::class)
                            ->default(BookingChannel::DIRECT)
                            ->required()
                            ->native(false)
                            ->live(),
                        Select::make('branch_id')
                            ->label('الفرع')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('channel') === BookingChannel::BRANCH)
                            ->required(fn (Get $get): bool => $get('channel') === BookingChannel::BRANCH),
                        Select::make('agent_id')
                            ->label('الوكيل')
                            ->relationship('agent', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('channel') === BookingChannel::AGENT)
                            ->required(fn (Get $get): bool => $get('channel') === BookingChannel::AGENT),
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
                            ->native(false)
                            ->hintActions([
                                Action::make('viewRoom')
                                    ->label('عرض الغرفة')
                                    ->visible(fn (Get $get): bool => (bool) $get('room_id'))
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (Get $get) => RoomResource::getUrl('edit', ['record' => $get('room_id')]))
                                    ->openUrlInNewTab(),
                            ]),
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

        $pricing = app(BookingService::class)->calculatePricing(
            $package,
            $roomTypeEnum,
            (float) ($get('discount') ?? 0),
        );

        $set('total_price', $pricing['base_price'] + $pricing['room_surcharge']);
        $set('net_price', $pricing['net_price']);
    }
}
