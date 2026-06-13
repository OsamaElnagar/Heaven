<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الشخصية')
                    ->components([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required(),
                        TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية'),
                        TextInput::make('national_id')
                            ->label('الرقم القومي')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('passport_number')
                            ->label('رقم جواز السفر')
                            ->unique(ignoreRecord: true),
                        DatePicker::make('passport_expiry')
                            ->label('انتهاء الجواز')
                            ->native(false),
                        DatePicker::make('date_of_birth')
                            ->label('تاريخ الميلاد')
                            ->native(false),
                        Select::make('gender')
                            ->label('الجنس')
                            ->options(Gender::class)
                            ->required()
                            ->default(Gender::MALE)
                            ->native(false),
                        Select::make('marital_status')
                            ->label('الحالة الاجتماعية')
                            ->options(MaritalStatus::class)
                            ->required()
                            ->default(MaritalStatus::SINGLE)
                            ->native(false),
                        TextInput::make('blood_type')
                            ->label('فصيلة الدم'),
                        TextInput::make('governorate')
                            ->label('المحافظة'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('معلومات الاتصال')
                    ->components([
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required(),
                        TextInput::make('phone_alt')
                            ->label('رقم هاتف بديل')
                            ->tel(),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email(),
                        Textarea::make('address')
                            ->label('العنوان')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('بيانات المحرم')
                    ->components([
                        TextInput::make('mahram_name')
                            ->label('اسم المحرم'),
                        TextInput::make('mahram_relation')
                            ->label('صلة القرابة'),
                        TextInput::make('mahram_phone')
                            ->label('هاتف المحرم')
                            ->tel(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('ملاحظات طبية')
                    ->components([
                        Textarea::make('medical_notes')
                            ->label('ملاحظات طبية')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
