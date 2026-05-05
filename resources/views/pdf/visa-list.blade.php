<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>قائمة التأشيرات</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 9px; }
.header { text-align: center; border-bottom: 3px solid #2c3e50; padding-bottom: 10px; margin-bottom: 15px; }
h1 { font-size: 16px; }
table { width: 100%; border-collapse: collapse; }
th { background: #2c3e50; color: white; padding: 4px; font-size: 8px; }
td { padding: 3px; border: 1px solid #ddd; font-size: 8px; }
tr:nth-child(even) { background: #f5f6fa; }
.footer { margin-top: 20px; font-size: 8px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>{{ config('app.name') }} - قائمة التأشيرات</h1>
</div>
<table>
<thead>
<tr>
<th>#</th>
<th>الحجز</th>
<th>العميل</th>
<th>جواز السفر</th>
<th>رقم التأشيرة</th>
<th>الحالة</th>
<th>تاريخ التقديم</th>
<th>تاريخ الموافقة</th>
<th>تاريخ الانتهاء</th>
<th>الرحلة</th>
</tr>
</thead>
<tbody>
@foreach($visas as $i => $visa)
<tr>
<td>{{ $i + 1 }}</td>
<td>{{ $visa->booking?->reference }}</td>
<td>{{ $visa->booking?->client?->name }}</td>
<td>{{ $visa->booking?->client?->passport_number }}</td>
<td>{{ $visa->visa_number ?? '-' }}</td>
<td>{{ $visa->status?->getLabel() }}</td>
<td>{{ $visa->applied_at?->format('Y-m-d') ?? '-' }}</td>
<td>{{ $visa->approved_at?->format('Y-m-d') ?? '-' }}</td>
<td>{{ $visa->expiry_date?->format('Y-m-d') ?? '-' }}</td>
<td>{{ $visa->booking?->trip?->name ?? '-' }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">تم إنشاء هذا التقرير في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }} | عدد التأشيرات: {{ $visas->count() }}</div>
</body>
</html>
