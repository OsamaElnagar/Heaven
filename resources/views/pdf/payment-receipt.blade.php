<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8"/>
<title>إيصال دفع</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
.header { text-align: center; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 15px; }
h1 { font-size: 16px; margin: 0; }
table { width: 100%; border-collapse: collapse; }
.info td:first-child { width: 150px; font-weight: bold; background: #f8f9fa; }
td { padding: 6px; border: 1px solid #ddd; }
.total { font-size: 16px; font-weight: bold; color: #27ae60; }
.footer { margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
<h1>{{ config('app.name') }}</h1>
<h3>إيصال دفع</h3>
</div>
<table class="info">
<tr><td>الحجز</td><td>{{ $payment->booking?->reference }}</td></tr>
<tr><td>العميل</td><td>{{ $payment->booking?->client?->name }}</td></tr>
<tr><td>الباقة</td><td>{{ $payment->booking?->package?->name }}</td></tr>
<tr><td>نوع الدفعة</td><td>{{ $payment->type?->getLabel() }}</td></tr>
<tr><td>طريقة الدفع</td><td>{{ $payment->method?->getLabel() }}</td></tr>
<tr><td>المبلغ</td><td class="total">{{ number_format($payment->amount, 2) }} ج.م</td></tr>
<tr><td>تاريخ الدفع</td><td>{{ $payment->paid_at?->format('Y-m-d') }}</td></tr>
<tr><td>رقم مرجعي</td><td>{{ $payment->reference ?? '-' }}</td></tr>
</table>
<div class="footer">تم إنشاء هذا الإيصال في {{ $generatedAt ?? now()->format('Y-m-d h:i A') }}</div>
</body>
</html>
