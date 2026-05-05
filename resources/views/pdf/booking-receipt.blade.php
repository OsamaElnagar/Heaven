<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>إيصال دفع - {{ $booking->reference }}</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
.header { text-align: center; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 15px; }
h1 { font-size: 16px; margin: 0; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.info td:first-child { width: 150px; font-weight: bold; background: #f8f9fa; }
td { padding: 5px; border: 1px solid #ddd; }
.section-title { background: #2c3e50; color: white; padding: 5px 10px; margin: 10px 0; }
th { background: #34495e; color: white; padding: 4px; font-size: 10px; }
.footer { margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>{{ config('app.name') }}</h1>
<h3>إيصال دفع</h3>
</div>
<table class="info">
<tr><td>الحجز</td><td>{{ $booking->reference }}</td></tr>
<tr><td>العميل</td><td>{{ $booking->client->name }}</td></tr>
<tr><td>الباقة</td><td>{{ $booking->package?->name }}</td></tr>
<tr><td>الإجمالي</td><td>{{ number_format($booking->net_price, 2) }} ج.م</td></tr>
<tr><td>المدفوع</td><td>{{ number_format($booking->paid_amount, 2) }} ج.م</td></tr>
<tr><td>المتبقي</td><td>{{ number_format(max($booking->net_price - $booking->paid_amount, 0), 2) }} ج.م</td></tr>
</table>
@if($booking->payments->isNotEmpty())
<div class="section-title">سجل المدفوعات</div>
<table>
<thead><tr><th>#</th><th>النوع</th><th>المبلغ</th><th>الطريقة</th><th>التاريخ</th></tr></thead>
<tbody>
@foreach($booking->payments as $i => $p)
<tr>
<td>{{ $i + 1 }}</td>
<td>{{ $p->type?->getLabel() }}</td>
<td>{{ number_format($p->amount, 2) }}</td>
<td>{{ $p->method?->getLabel() }}</td>
<td>{{ $p->paid_at?->format('Y-m-d') }}</td>
</tr>
@endforeach
</tbody>
</table>
@endif
<div class="footer">تم إنشاء هذا الإيصال في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }}</div>
</body>
</html>
