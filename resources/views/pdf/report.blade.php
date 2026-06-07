<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.4; color: #2c3e50; }
        .header { text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 3px solid #1a5f7a; padding: 15px; }
        .header h1 { font-size: 22px; margin-bottom: 3px; color: #1a5f7a; }
        .header h2 { font-size: 15px; font-weight: normal; color: #2980b9; }
        .filters { margin-bottom: 10px; padding: 8px 12px; background: #f0f4f8; border: 1px solid #d0d8e0; border-radius: 4px; font-size: 10px; color: #555; }
        .filters strong { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #3498db; padding: 5px 7px; text-align: right; font-size: 10px; }
        th { background: #2980b9; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f0f8ff; }
        tr:nth-child(odd) { background-color: #ffffff; }
        .summary-row td { background: #e8f4f8 !important; font-weight: bold; border-top: 2px solid #1a5f7a; }
        .amount { text-align: left; direction: ltr; display: inline-block; width: 100%; }
        .footer { margin-top: 25px; text-align: center; font-size: 10px; color: #7f8c8d; border-top: 2px solid #3498db; padding-top: 10px; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 9px; }
        .badge-success { background: #27ae60; color: white; }
        .badge-danger { background: #e74c3c; color: white; }
        .badge-warning { background: #f39c12; color: white; }
        .badge-info { background: #3498db; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $storeName }}</h1>
        <h2>{{ $title }}</h2>
    </div>

    @if($filters)
        <div class="filters"><strong>بحث:</strong> {{ $filters }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                @foreach($columns as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
        @empty
            <tr><td colspan="{{ count($columns) + 1 }}" style="text-align: center;">لا توجد بيانات</td></tr>
        @endforelse
        </tbody>
        @if(!empty($summaries))
            <tfoot>
                @foreach($summaries as $summary)
                    <tr class="summary-row">
                        <td colspan="{{ count($columns) + 1 - count($summary) }}"></td>
                        @foreach($summary as $cell)
                            <td>{!! $cell !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tfoot>
        @endif
    </table>

    <p class="footer">تم إنشاء هذا التقرير في {{ $generatedAt }} | {{ $storeName }}</p>
</body>
</html>
