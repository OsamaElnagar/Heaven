<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>كشف المسافرين - {{ $trip->name }}</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 9px; }
.header { text-align: center; border-bottom: 3px solid #2980b9; padding-bottom: 10px; margin-bottom: 15px; }
h1 { font-size: 16px; }
table { width: 100%; border-collapse: collapse; }
th { background: #2980b9; color: white; padding: 4px; font-size: 8px; }
td { padding: 3px; border: 1px solid #ddd; font-size: 8px; }
tr:nth-child(even) { background: #f0f8ff; }
.footer { margin-top: 20px; font-size: 8px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>كشف المسافرين - رحلة {{ $trip->name }}</h1>
<p>شركة الطيران: {{ $trip->airline }} | رقم الرحلة: {{ $trip->flight_number }} | {{ $trip->departure_at?->format('Y-m-d H:i') }}</p>
</div>
<table>
<thead>
<tr>
<th>#</th>
<th>الاسم</th>
<th>رقم الجواز</th>
<th>الرقم القومي</th>
<th>رقم التأشيرة</th>
<th>حالة التأشيرة</th>
<th>نوع الغرفة</th>
</tr>
</thead>
<tbody>
@foreach($bookings as $i => $b)
<tr>
<td>{{ $i + 1 }}</td>
<td>{{ $b->client->name }}</td>
<td>{{ $b->client->passport_number }}</td>
<td>{{ $b->client->national_id }}</td>
<td>{{ $b->visa?->visa_number ?? '-' }}</td>
<td>{{ $b->visa?->status?->getLabel() ?? '-' }}</td>
<td>{{ $b->room_type?->getLabel() }}</td>
</tr>
@endforeach
</tbody>
</table>
<div class="footer">تم إنشاء هذا الكشف في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }} | عدد المسافرين: {{ $bookings->count() }}</div>
</body>
</html>
