<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>تقرير الإيرادات - {{ $year }}</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
.header { text-align: center; border-bottom: 3px solid #2c3e50; padding-bottom: 10px; margin-bottom: 15px; }
h1 { font-size: 18px; margin: 0; }
h2 { font-size: 14px; margin: 5px 0 0; color: #7f8c8d; }
.summary-cards { margin-bottom: 15px; }
.summary-cards table { width: 100%; }
.summary-cards td { padding: 10px; text-align: center; border: 1px solid #ddd; width: 33%; }
.summary-cards .number { font-size: 20px; font-weight: bold; }
.summary-cards .label { font-size: 10px; color: #666; }
.section-title { background: #2c3e50; color: white; padding: 5px 10px; margin: 15px 0 10px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
th { background: #3498db; color: white; padding: 5px; font-size: 9px; }
td { padding: 4px; border: 1px solid #ddd; font-size: 9px; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.collected { color: #27ae60; font-weight: bold; }
.outstanding { color: #e74c3c; }
.footer { margin-top: 20px; font-size: 8px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>{{ config('app.name') }} - تقرير الإيرادات</h1>
<h2>السنة: {{ $year }}</h2>
</div>
<div class="summary-cards">
<table>
<tr>
<td><div class="number">{{ $report['total_bookings'] }}</div><div class="label">إجمالي الحجوزات</div></td>
<td><div class="number collected">{{ number_format($report['total_collected'], 0) }} ج.م</div><div class="label">إجمالي المحصل</div></td>
<td><div class="number outstanding">{{ number_format($report['total_outstanding'], 0) }} ج.م</div><div class="label">المستحق</div></td>
</tr>
</table>
</div>
<div class="section-title">تفاصيل الباقات</div>
<table>
<thead>
<tr>
<th>الباقة</th>
<th>النوع</th>
<th>عدد الحجوزات</th>
<th>المحصل</th>
<th>المستحق</th>
<th>نسبة التحصيل</th>
</tr>
</thead>
<tbody>
@foreach($report['packages'] as $pkg)
@php
$totalOps = $pkg['collected'] + $pkg['outstanding'];
$pct = $totalOps > 0 ? round(($pkg['collected'] / $totalOps) * 100) : 0;
@endphp
<tr>
<td>{{ $pkg['package_name'] }}</td>
<td>{{ $pkg['type']->getLabel() }}</td>
<td class="text-center">{{ $pkg['bookings'] }}</td>
<td class="text-right collected">{{ number_format($pkg['collected'], 0) }}</td>
<td class="text-right outstanding">{{ number_format($pkg['outstanding'], 0) }}</td>
<td class="text-center">{{ $pct }}%</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">تم إنشاء هذا التقرير في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }}</div>
</body>
</html>
