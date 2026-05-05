<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>ملخص الباقة - {{ $package->name }}</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
.header { text-align: center; border-bottom: 3px solid #e67e22; padding-bottom: 10px; margin-bottom: 15px; }
.section-title { background: #e67e22; color: white; padding: 6px 10px; margin: 15px 0 10px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
th { background: #f39c12; color: white; padding: 5px; font-size: 10px; }
td { padding: 4px; border: 1px solid #ddd; font-size: 10px; }
.info td:first-child { width: 150px; font-weight: bold; background: #fef9e7; }
.footer { margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>{{ $package->name }}</h1>
<h3>{{ $package->type?->getLabel() }} - {{ $package->grade?->getLabel() }}</h3>
</div>
<div class="section-title">معلومات الباقة</div>
<table class="info">
<tr><td>الموسم</td><td>{{ $package->season_year }}</td><td>المدة</td><td>{{ $package->duration_nights }} ليلة</td></tr>
<tr><td>السعر الأساسي</td><td>{{ number_format($package->base_price, 2) }} ج.م</td><td>المقاعد</td><td>{{ $package->total_seats }} (المحجوز: {{ $package->reserved_seats }})</td></tr>
<tr><td>تاريخ المغادرة</td><td>{{ $package->departure_date?->format('Y-m-d') }}</td><td>تاريخ العودة</td><td>{{ $package->return_date?->format('Y-m-d') }}</td></tr>
</table>
@if($package->includes)
<div class="section-title">يشمل</div><p>{{ $package->includes }}</p>
@endif
@if($package->excludes)
<div class="section-title">لا يشمل</div><p>{{ $package->excludes }}</p>
@endif
@if($package->hotels->isNotEmpty())
<div class="section-title">الفنادق</div>
<table>
<thead><tr><th>الفندق</th><th>المدينة</th><th>الليالي</th><th>تكلفة الفرد</th></tr></thead>
<tbody>
@foreach($package->hotels as $hotel)
<tr>
<td>{{ $hotel->name }}</td>
<td>{{ $hotel->pivot->city }}</td>
<td>{{ $hotel->pivot->nights }}</td>
<td>{{ number_format($hotel->pivot->cost_per_person, 2) }} ج.م</td>
</tr>
@endforeach
</tbody>
</table>
@endif
<div class="footer">تم إنشاء هذا الملخص في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }}</div>
</body>
</html>
