<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>قسيمة حجز - {{ $booking->reference }}</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
.header { text-align: center; border-bottom: 3px solid #8e44ad; padding-bottom: 10px; margin-bottom: 15px; }
h1 { font-size: 18px; margin: 0; color: #8e44ad; }
.section-title { background: #8e44ad; color: white; padding: 5px 10px; margin: 10px 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.info td:first-child { width: 150px; font-weight: bold; background: #f5f0ff; }
td { padding: 5px; border: 1px solid #ddd; }
.footer { margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>قسيمة حجز</h1>
<h3>{{ $booking->reference }}</h3>
</div>
<div class="section-title">بيانات الحاج</div>
<table class="info">
<tr><td>الاسم</td><td>{{ $booking->client->name }}</td></tr>
<tr><td>الرقم القومي</td><td>{{ $booking->client->national_id }}</td></tr>
<tr><td>جواز السفر</td><td>{{ $booking->client->passport_number }}</td></tr>
<tr><td>الهاتف</td><td>{{ $booking->client->phone }}</td></tr>
</table>
<div class="section-title">تفاصيل الحجز</div>
<table class="info">
<tr><td>الباقة</td><td>{{ $booking->package?->name }} - {{ $booking->package?->type?->getLabel() }}</td></tr>
<tr><td>الرحلة</td><td>{{ $booking->trip?->name ?? '-' }}</td></tr>
<tr><td>تاريخ المغادرة</td><td>{{ $booking->trip?->departure_at?->format('Y-m-d') ?? $booking->package?->departure_date?->format('Y-m-d') }}</td></tr>
<tr><td>تاريخ العودة</td><td>{{ $booking->trip?->return_at?->format('Y-m-d') ?? $booking->package?->return_date?->format('Y-m-d') }}</td></tr>
<tr><td>نوع الغرفة</td><td>{{ $booking->room_type?->getLabel() }}</td></tr>
<tr><td>رقم الغرفة</td><td>{{ $booking->room?->room_number ?? '-' }}</td></tr>
<tr><td>التأشيرة</td><td>{{ $booking->visa?->status?->getLabel() ?? 'لم تتقدم' }} {{ $booking->visa?->visa_number ?? '' }}</td></tr>
</table>
<div class="section-title">التسعير</div>
<table class="info">
<tr><td>السعر الإجمالي</td><td>{{ number_format($booking->total_price, 2) }} ج.م</td></tr>
<tr><td>الخصم</td><td>{{ number_format($booking->discount, 2) }} ج.م</td></tr>
<tr><td>صافي السعر</td><td style="font-weight:bold;">{{ number_format($booking->net_price, 2) }} ج.م</td></tr>
<tr><td>المدفوع</td><td>{{ number_format($booking->paid_amount, 2) }} ج.م</td></tr>
<tr><td>المتبقي</td><td style="font-weight:bold; color: #e74c3c;">{{ number_format(max($booking->net_price - $booking->paid_amount, 0), 2) }} ج.م</td></tr>
</table>
<div class="footer">تم إنشاء هذه القسيمة في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }}</div>
</body>
</html>
