<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Client;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الشخصية')
                    ->components([
                        TextEntry::make('name')
                            ->label('الاسم'),
                        TextEntry::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->placeholder('-'),
                        TextEntry::make('national_id')
                            ->label('الرقم القومي'),
                        TextEntry::make('passport_number')
                            ->label('رقم جواز السفر')
                            ->placeholder('-'),
                        TextEntry::make('passport_expiry')
                            ->label('انتهاء الجواز')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('date_of_birth')
                            ->label('تاريخ الميلاد')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('gender')
                            ->label('الجنس')
                            ->badge(),
                        TextEntry::make('marital_status')
                            ->label('الحالة الاجتماعية')
                            ->badge(),
                        TextEntry::make('blood_type')
                            ->label('فصيلة الدم')
                            ->placeholder('-'),
                        TextEntry::make('governorate')
                            ->label('المحافظة')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('معلومات الاتصال')
                    ->components([
                        TextEntry::make('phone')
                            ->label('رقم الهاتف'),
                        TextEntry::make('phone_alt')
                            ->label('رقم هاتف بديل')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label('البريد الإلكتروني')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('العنوان')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('بيانات المحرم')
                    ->components([
                        TextEntry::make('mahram_name')
                            ->label('اسم المحرم')
                            ->placeholder('-'),
                        TextEntry::make('mahram_relation')
                            ->label('صلة القرابة')
                            ->placeholder('-'),
                        TextEntry::make('mahram_phone')
                            ->label('هاتف المحرم')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('ملاحظات طبية')
                    ->components([
                        TextEntry::make('medical_notes')
                            ->label('ملاحظات طبية')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->hidden(fn (Client $record): bool => blank($record->medical_notes)),
            ]);
    }
}
