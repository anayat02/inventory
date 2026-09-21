<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Акт приёма-передачи</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 20px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header p {
            margin: 2px 0;
        }
        .section {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background: #f2f2f2;
            text-align: center;
        }
        table.signatures {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        table.signatures td {
            width: 50%;
            text-align: center;
            border: none;
            vertical-align: top;
        }

        table.signatures .signature-line {
            margin-top: 40px;
        }

    </style>
</head>
<body>

<div class="title">Акт приёма – передачи</div>

<div class="header">
    <p>ТОО «Международный инженерно – технологический университет»</p>
    <p>г. Алматы, {{ \Carbon\Carbon::now()->locale('ru')->isoFormat('«D» MMMM YYYY г.') }}</p>
</div>

<div class="section">
    <p>
        <strong>{{ $sender }} – {{ $sender_position ?? 'Ответственный сотрудник' }} </strong>, в дальнейшем «Сторона 1», с одной стороны, и
        <br>
        <strong>{{ $receiver }} – {{ $receiver_position ?? ' Новый ответственный сотрудник' }} </strong>, в дальнейшем «Сторона 2», с другой стороны,
        вместе именуемые как «Стороны», подписали настоящий Акт приёма-передачи
        ТОО «Международный инженерно – технологический университет» (далее – ТОО «МИТУ») о нижеследующем:
    </p>
</div>

<div class="section">
    <p><strong>Сторона 1 передает Стороне 2 следующее:</strong></p>

    <table>
        <thead>
        <tr>
            <th>№</th>
            <th>Наименование атрибута</th>
            <th>Инв. номер</th>
            <th>Количество (шт.)</th>
            <th>Аудитория</th>
        </tr>
        </thead>
        <tbody>
        @foreach($pdfData as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    {{ $item->product_name }}:

                    @if($item->characteristics->where('id_characteristic', 4)->count() > 0)
                        @foreach($item->characteristics->where('id_characteristic', 4)->where('current_status', 0) as $characteristic)
                            <strong>{{ $characteristic->characteristic_value }}</strong>;
                            <br>
                        @endforeach
                    @else
                        @foreach($item->characteristics->where('id_characteristic', 2)->where('current_status', 0) as $characteristic)
                            <strong>{{ $characteristic->characteristic_value }}</strong>;
                            <br>
                        @endforeach
                    @endif
                </td>
                <td style="text-align: center;">{{ $item->inv_number }}</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: center;">{{ $item->auditoryName ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<table class="signatures">
    <tr>
        <td>
            <p><strong>Передала документы:</strong></p>
            <p>Сторона 1</p>
            <p>{{ $sender }}</p>
            <div class="signature-line">______________________</div>
        </td>
        <td>
            <p><strong>Приняла документы:</strong></p>
            <p>Сторона 2</p>
            <p>{{ $receiver }}</p>
            <div class="signature-line">______________________</div>
        </td>
    </tr>
</table>
</body>
</html>
