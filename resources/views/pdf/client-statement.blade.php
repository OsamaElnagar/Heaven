<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>كشف حساب</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
.header { text-align: center; border-bottom: 3px solid #2c3e50; padding-bottom: 10px; margin-bottom: 15px; }
h1 { font-size: 18px; margin: 0; }
h2 { font-size: 14px; margin: 5px 0 0; color: #7f8c8d; }
.section-title { background: #2c3e50; color: white; padding: 5px 10px; margin-top: 10px; font-size: 12px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
th { background: #3498db; color: white; padding: 5px; font-size: 10px; }
td { padding: 4px; border: 1px solid #ddd; font-size: 9px; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.summary { margin: 10px 0; }
.summary td { border: none; padding: 3px 10px; }
.footer { margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>{{ config('app.name') }}</h1>
<h2>كشف حساب - {{ $report['client']['name'] }}</h2>
</div>
<table class="summary">
<tr><td style="width:150px">الرقم القومي:</td><td>{{ $report['client']['national_id'] }}</td></tr>
<tr><td>الهاتف:</td><td>{{ $report['client']['phone'] }}</td></tr>
</table>
<div class="section-title">ملخص الحجوزات</div>
@foreach($report['bookings'] as $booking)
<table style="margin-bottom:8px;">
<tr><td style="width:120px;font-weight:bold;">المرجع</td><td>{{ $booking['reference'] }}</td><td style="width:100px;font-weight:bold;">الباقة</td><td>{{ $booking['package'] }}</td></tr>
<tr><td style="font-weight:bold;">الحالة</td><td>{{ $booking['status'] }}</td><td style="font-weight:bold;">التأشيرة</td><td>{{ $booking['visa_status'] ?? '-' }}</td></tr>
<tr><td style="font-weight:bold;">الإجمالي</td><td>{{ number_format($booking['total'], 2) }}</td><td style="font-weight:bold;">المدفوع</td><td>{{ number_format($booking['paid'], 2) }}</td></tr>
<tr><td style="font-weight:bold;">المتبقي</td><td>{{ number_format($booking['remaining'], 2) }}</td><td style="font-weight:bold;">المسترجع</td><td>{{ number_format($booking['refunded'], 2) }}</td></tr>
</table>
@if(!empty($booking['payments']))
<table>
<thead><tr><th>#</th><th>النوع</th><th>المبلغ</th><th>الطريقة</th><th>التاريخ</th><th>المرجع</th></tr></thead>
<tbody>
@foreach($booking['payments'] as $i => $p)
<tr>
<td class="text-center">{{ $i + 1 }}</td>
<td>{{ $p['type'] }}</td>
<td class="text-right">{{ number_format($p['amount'], 2) }}</td>
<td>{{ $p['method'] }}</td>
<td>{{ $p['paid_at'] }}</td>
<td>{{ $p['reference'] ?? '' }}</td>
</tr>
@endforeach
</tbody>
</table>
@endif
@endforeach
<div class="footer">تم إنشاء هذا التقرير في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }}</div>
</body>
</html>
