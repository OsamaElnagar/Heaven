<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>توزيع الغرف - {{ $trip->name }}</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
.header { text-align: center; border-bottom: 3px solid #27ae60; padding-bottom: 10px; margin-bottom: 15px; }
h1 { font-size: 16px; margin: 0; }
.hotel-title { background: #27ae60; color: white; padding: 6px 10px; margin: 10px 0; }
.room { border: 1px solid #bdc3c7; margin: 5px 0; padding: 8px; border-radius: 4px; }
.room-header { font-weight: bold; margin-bottom: 5px; }
table { width: 100%; border-collapse: collapse; }
th { background: #2ecc71; color: white; padding: 3px 5px; font-size: 9px; }
td { padding: 2px 5px; border: 1px solid #ddd; font-size: 9px; }
.footer { margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>توزيع الغرف - رحلة {{ $trip->name }}</h1>
</div>
@foreach($rooms as $hotelName => $hotelRooms)
<div class="hotel-title">{{ $hotelName }}</div>
@foreach($hotelRooms as $room)
<div class="room">
<div class="room-header">{{ $room->room_number ?? 'غرفة '.$room->id }} ({{ $room->type?->getLabel() }} - {{ $room->occupied }}/{{ $room->capacity }})</div>
<table>
<thead><tr><th>#</th><th>الاسم</th><th>المرجع</th><th>جواز السفر</th></tr></thead>
<tbody>
@foreach($room->bookings as $i => $b)
<tr>
<td>{{ $i + 1 }}</td>
<td>{{ $b->client->name }}</td>
<td>{{ $b->reference }}</td>
<td>{{ $b->client->passport_number }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endforeach
@endforeach
<div class="footer">تم إنشاء هذا التقرير في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }}</div>
</body>
</html>
