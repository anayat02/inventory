<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Замечание по оборудованию аудитории</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; color: #333; padding: 20px; }
        .container { background-color: #ffffff; padding: 25px; border-radius: 8px; border: 1px solid #e1e4e8; max-width: 650px; margin: 0 auto; }
        .header { border-bottom: 2px solid #dc3545; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { color: #dc3545; margin: 0; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 8px; border-bottom: 1px solid #eee; }
        .info-table td.label { font-weight: bold; width: 35%; color: #555; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f8f9fa; font-size: 13px; }
        .badge-danger { background-color: #dc3545; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
        .badge-warning { background-color: #ffc107; color: #212529; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Внимание: Замечания по оборудованию в {{ $auditoryName }}</h2>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Преподаватель:</td>
                <td>{{ $tutorName }}</td>
            </tr>
            <tr>
                <td class="label">Аудитория:</td>
                <td>{{ $auditoryName }}</td>
            </tr>
            <tr>
                <td class="label">Тип проверки:</td>
                <td>{{ $check->check_type == 'entrance' ? 'Вход на занятие' : 'Выход с занятия' }}</td>
            </tr>
            <tr>
                <td class="label">Дата и время проверки:</td>
                <td>{{ $check->created_at ? $check->created_at->format('d.m.Y H:i') : date('d.m.Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Время проведения пар:</td>
                <td>{{ $check->lesson_start }} — {{ $check->lesson_finish }}</td>
            </tr>
            @if($check->keyboard_count !== null || $check->mouse_count !== null)
            <tr>
                <td class="label">Учет периферии:</td>
                <td>Клавиатуры: <strong>{{ $check->keyboard_count ?? '—' }} шт.</strong>, Мыши: <strong>{{ $check->mouse_count ?? '—' }} шт.</strong></td>
            </tr>
            @endif
            @if(!empty($check->comment))
            <tr>
                <td class="label">Общий комментарий:</td>
                <td>{{ $check->comment }}</td>
            </tr>
            @endif
        </table>

        <h4>Список предметов с замечаниями:</h4>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Наименование предмета</th>
                    <th>Инв. №</th>
                    <th>Состояние</th>
                    <th>Примечание</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discrepancies as $item)
                <tr>
                    <td>{{ $item['product_name'] }}</td>
                    <td>{{ $item['inv_number'] ?? '—' }}</td>
                    <td>
                        @if($item['condition'] == 'missing')
                            <span class="badge-danger">Отсутствует</span>
                        @elseif($item['condition'] == 'damaged')
                            <span class="badge-warning">Поврежден</span>
                        @else
                            <span>{{ $item['condition'] }}</span>
                        @endif
                    </td>
                    <td>{{ $item['note'] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 25px; font-size: 12px; color: #777;">
            Это автоматическое уведомление системы инвентаризации METU.
        </p>
    </div>
</body>
</html>
