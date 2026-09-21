<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Акт приема-передачи оборудования</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; line-height: 1; color: #111; text-align:justify; margin:20px 32px 32px 32px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px;}
        .meta { font-size: 13px;}
        h1 { font-size:18px; text-align:center;}
        .center { text-align:center; }
        .section { font-size: 12px;}
        table { width:100%; border-collapse: collapse;}
        table th, table td { border:1px solid #444; font-size:12px; vertical-align:top; margin: 3px;}
        table th { background:#f2f2f2; text-align:left; }
        .small { font-size:14px; color:#333; }
        .contract-table {
            width: 100%;
            border-collapse: collapse;
        }
        .contract-table td {
            vertical-align: top;
            border: 1px solid #000;
        }
        .header-cell {
            text-align: center;
            font-weight: bold;
            padding: 15px 0;
        }
        .signature-line {
            margin-top: 30px;
        }
        .signature-area {
            margin-top: 40px;
        }
    </style>
</head>
<body>
<h1>АКТ</h1>
<p class="center small">приема-передачи оборудования с возможностью дальнейшего перехода права собственности</p>
<div class="header">
    <div class="meta">г. Алматы</div>
    <div class="meta">{{ $student->updated_at->locale('ru')->isoFormat('«D» MMMM YYYY г.') }}</div>
</div>
<div class="section">
    <p>Настоящим стороны,</p>
    <p>
        ТОО «Международный инженерно-технологический университет», именуемое в дальнейшем «Арендодатель», в лице Ректора Акпанбетова Д.Б., с одной стороны
        <br>
        и граждан-ин/ка Республики Казахстан <strong>{{$student->full_name}}</strong>,<br>
        ИИН <strong>{{$student->iinplt}}</strong>, именуем-ый/ая в дальнейшем «Арендатор/Обучающийся», подтверждают факт передачи Оборудования с возможностью дальнейшего перехода права собственности от Арендодателя к Арендатору/Обучающемуся, а именно:
    </p>
</div>
<div class="section">
    <p><strong>Арендодатель передал, а Арендатор/Обучающийся принял во временное безвозмездное пользование следующее Оборудование:</strong></p>

    <table>
        <thead>
        <tr>
            <th style="width:6%;">№</th>
            <th style="width:46%;">Наименование, Модель, Серийный номер</th>
            <th style="width:12%;">Кол-во, шт.</th>
            <th style="width:18%;">Стоимость за ед., тенге</th>
            <th style="width:18%;">Состояние</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>
                Ноутбук<br>
                <strong>{{$student->serialNumber}}</strong>
            </td>
            <td class="center">1</td>
            <td></td>
            <td>новый</td>
        </tr>
        </tbody>
    </table>

    <p>Оборудование передано Арендатору/Обучающемуся во временное безвозмездное пользование в новом состоянии, пригодном для его эксплуатации по его прямому назначению и находится в исправном состоянии.</p>

    <p>Оборудование принадлежит Арендодателю на праве собственности.</p>

    <p>
        Право собственности на Оборудование переходит к Арендатору/Обучающемуся при условии успешного окончания полного курса обучения и получения диплома собственного образца Международного инженерно-технологического университета.
        В случае несоблюдения условий, предусматривающих переход права собственности к Арендатору/Обучающемуся, Арендатор/Обучающемуся обязан вернуть оборудование Арендодателю в том же состоянии с учетом нормального износа либо компенсировать его стоимость.
    </p>

    <p>Настоящий акт составлен в двух экземплярах, имеющих одинаковую юридическую силу, по одному для каждой из сторон.</p>

    <p><strong>Дата передачи:</strong> {{ $student->updated_at->locale('ru')->isoFormat('«D» MMMM YYYY г.') }}</p>
    <p><strong>Место передачи:</strong> г. Алматы, пр. аль Фараби 93 А, здание Международного инженерно-технологического университета.</p>
</div>
<table class="contract-table">
    <tr>
        <td class="header-cell">«Арендодатель»</td>
        <td class="header-cell">«Арендатор»</td>
    </tr>
    <tr>
        <td>
            <strong>
                ТОО «Международный инженерно-технологический университет»<br>
                БИН: 080640010716<br>
                Ректор
            </strong>
            <p>&nbsp;</p>
        </td>
        <td>
            <strong>ФИО:</strong> <span>{{$student->full_name}}</span><br>
            <strong>ИИН:</strong> <span>{{$student->iinplt}}</span>
        </td>
    </tr>
    <tr>
        <td class="signature-area">
            <div class="signature-line">________________/ <strong>Акпанбетов Д.Б.<br>М.П.</strong></div>
        </td>
        <td class="signature-area">
            <div class="signature-line">________________/_______________</div>
        </td>
    </tr>
</table>

</body>
</html>
