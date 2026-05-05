<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>بطاقة الحاج - {{ $client->name }}</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
.header { text-align: center; border-bottom: 3px solid #1a5f7a; padding-bottom: 10px; margin-bottom: 15px; }
.label { color: #666; }
.value { font-weight: bold; }
.section-title { background: #1a5f7a; color: white; padding: 6px 10px; margin-top: 15px; margin-bottom: 10px; }
table { width: 100%; border-collapse: collapse; }
td { padding: 5px; border: 1px solid #ddd; }
.footer { margin-top: 20px; font-size: 10px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>بطاقة الحاج</h1>
</div>
<div class="section-title">البيانات الشخصية</div>
<table>
<tr><td class="label">الاسم</td><td class="value">{{ $client->name }}</td><td class="label">الاسم بالإنجليزية</td><td class="value">{{ $client->name_en }}</td></tr>
<tr><td class="label">الرقم القومي</td><td class="value">{{ $client->national_id }}</td><td class="label">جواز السفر</td><td class="value">{{ $client->passport_number }}</td></tr>
<tr><td class="label">انتهاء الجواز</td><td class="value">{{ $client->passport_expiry?->format('Y-m-d') }}</td><td class="label">تاريخ الميلاد</td><td class="value">{{ $client->date_of_birth?->format('Y-m-d') }}</td></tr>
<tr><td class="label">الجنس</td><td class="value">{{ $client->gender?->getLabel() }}</td><td class="label">فصيلة الدم</td><td class="value">{{ $client->blood_type }}</td></tr>
<tr><td class="label">الهاتف</td><td class="value">{{ $client->phone }}</td><td class="label">المحافظة</td><td class="value">{{ $client->governorate }}</td></tr>
</table>
<div class="section-title">الحجوزات</div>
@foreach($client->bookings as $booking)
<table>
<tr><td class="label">المرجع</td><td class="value">{{ $booking->reference }}</td><td class="label">الباقة</td><td class="value">{{ $booking->package?->name }}</td></tr>
<tr><td class="label">الحالة</td><td class="value">{{ $booking->status?->getLabel() }}</td><td class="label">الرحلة</td><td class="value">{{ $booking->trip?->name }}</td></tr>
<tr><td class="label">نوع الغرفة</td><td class="value">{{ $booking->room_type?->getLabel() }}</td><td class="label">التأشيرة</td><td class="value">{{ $booking->visa?->status?->getLabel() ?? '-' }} {{ $booking->visa?->visa_number }}</td></tr>
</table>
@endforeach
<div class="footer">تم إنشاء هذه البطاقة في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }}</div>
</body>
</html>
