@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">

        {{-- Фильтр --}}
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Фильтр проверок</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('classroom_checks.admin_history') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted small mb-1">Дата проверки</label>
                                <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ request('date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted small mb-1">Преподаватель</label>
                                <select name="tutor_id" id="tutor_id" class="form-control form-control-sm select2">
                                    <option value="">Все преподаватели</option>
                                    @foreach($allTutors ?? [] as $tut)
                                        <option value="{{ $tut->TutorID }}" {{ request('tutor_id') == $tut->TutorID ? 'selected' : '' }}>
                                            {{ $tut->lastname }} {{ $tut->firstname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted small mb-1">Аудитория</label>
                                <select name="auditory_id" id="auditory_id" class="form-control form-control-sm select2">
                                    <option value="">Все аудитории</option>
                                    @foreach($allAuditories ?? [] as $aud)
                                        <option value="{{ $aud->auditoryID }}" {{ request('auditory_id') == $aud->auditoryID ? 'selected' : '' }}>
                                            {{ $aud->auditoryName }} ({{ $aud->buildingName }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted small mb-1">Тип проверки</label>
                                <select name="check_type" id="check_type" class="form-control form-control-sm">
                                    <option value="">Все типы</option>
                                    <option value="entrance" {{ request('check_type') == 'entrance' ? 'selected' : '' }}>Вход</option>
                                    <option value="exit" {{ request('check_type') == 'exit' ? 'selected' : '' }}>Выход</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted small mb-1">Статус</label>
                                <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">Все статусы</option>
                                    <option value="ok" {{ request('status') == 'ok' ? 'selected' : '' }}>Без замечаний</option>
                                    <option value="discrepancy" {{ request('status') == 'discrepancy' ? 'selected' : '' }}>С замечаниями</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end mb-3">
                            <button type="submit" class="btn btn-sm btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i> Применить
                            </button>
                            <a href="{{ route('classroom_checks.admin_history') }}" class="btn btn-sm btn-default">
                                <i class="fas fa-undo mr-1"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Таблица --}}
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-journal-check mr-1"></i> Журнал проверок сохранности аудиторий</h3>
            </div>
            <div class="card-body p-0">
                <table id="example2" class="table table-bordered table-striped table-sm mb-0" style="font-size: 13px;">
                    <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Дата и время</th>
                        <th>Преподаватель</th>
                        <th>Аудитория</th>
                        <th>Время пар</th>
                        <th>Тип</th>
                        <th>Статус</th>
                        <th>Периферия</th>
                        <th>Замечания</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($checks as $check)
                        <tr class="text-center {{ $check->status == 'discrepancy' ? 'table-warning' : '' }}">
                            <td class="text-muted">{{ $check->id }}</td>
                            <td>{{ $check->created_at ? $check->created_at->format('d.m.Y H:i') : $check->check_date }}</td>
                            <td class="text-left"><strong>{{ $check->tutor_fullname }}</strong></td>
                            <td class="text-left">{{ $check->auditory_name }}</td>
                            <td class="text-muted">{{ $check->lesson_start }} — {{ $check->lesson_finish }}</td>
                            <td>
                                @if($check->check_type == 'entrance')
                                    <span class="badge badge-info">Вход</span>
                                @else
                                    <span class="badge badge-secondary">Выход</span>
                                @endif
                            </td>
                            <td>
                                @if($check->status == 'discrepancy')
                                    <span class="badge badge-danger">Замечания</span>
                                @else
                                    <span class="badge badge-success">OK</span>
                                @endif
                            </td>
                            <td>
                                @if($check->keyboard_count !== null || $check->mouse_count !== null)
                                    <small class="text-muted">
                                        Кл: <strong>{{ $check->keyboard_count ?? '—' }}</strong>&nbsp;
                                        Мышь: <strong>{{ $check->mouse_count ?? '—' }}</strong>
                                    </small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-left" style="max-width: 200px;">
                                @if(!empty($check->comment))
                                    <div class="small text-muted mb-1"><i class="fas fa-comment-alt mr-1"></i>{{ Str::limit($check->comment, 60) }}</div>
                                @endif
                                @if($check->items->where('fact_count', '<', 'db_count')->count() > 0 || $check->items->whereNotNull('note')->count() > 0)
                                    <ul class="mb-0 pl-3 small text-danger">
                                        @foreach($check->items as $dItem)
                                            @if($dItem->fact_count < $dItem->db_count || !empty($dItem->note))
                                                <li>
                                                    {{ $dItem->product_name ?? ('Кат. #' . $dItem->id_product) }}:
                                                    <strong>{{ $dItem->db_count }}</strong> → <strong>{{ $dItem->fact_count }}</strong>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @elseif(empty($check->comment))
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info btn-show-check-details"
                                        data-check='@json($check)'
                                        title="Подробности">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Дата и время</th>
                        <th>Преподаватель</th>
                        <th>Аудитория</th>
                        <th>Время пар</th>
                        <th>Тип</th>
                        <th>Статус</th>
                        <th>Периферия</th>
                        <th>Замечания</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Модальное окно подробностей --}}
<div class="modal fade" id="checkDetailsModal" tabindex="-1" role="dialog" aria-labelledby="checkDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkDetailsModalLabel">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Проверка аудитории <span class="text-muted font-weight-normal">№ <span id="detailCheckId"></span></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" onclick="$('#checkDetailsModal').modal('hide')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-2">

                {{-- Инфо-строки --}}
                <table class="table table-sm table-borderless mb-3" style="font-size: 13px;">
                    <tbody>
                        <tr>
                            <td class="text-muted pl-0" style="width: 140px;"><i class="fas fa-user mr-1"></i> Преподаватель</td>
                            <td><strong id="detailTutor"></strong></td>
                            <td class="text-muted" style="width: 130px;"><i class="fas fa-calendar-alt mr-1"></i> Дата</td>
                            <td id="detailDateTime"></td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-0"><i class="fas fa-door-open mr-1"></i> Аудитория</td>
                            <td id="detailAuditory"></td>
                            <td class="text-muted"><i class="fas fa-clock mr-1"></i> Время пар</td>
                            <td id="detailLessonTime"></td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-0"><i class="fas fa-exchange-alt mr-1"></i> Тип</td>
                            <td id="detailCheckType"></td>
                            <td class="text-muted"><i class="fas fa-check-circle mr-1"></i> Статус</td>
                            <td id="detailStatus"></td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-0"><i class="bi bi-keyboard mr-1"></i> Клавиатуры</td>
                            <td><strong id="detailKeyboardCount"></strong></td>
                            <td class="text-muted"><i class="bi bi-mouse mr-1"></i> Мыши</td>
                            <td><strong id="detailMouseCount"></strong></td>
                        </tr>
                    </tbody>
                </table>

                {{-- Комментарий --}}
                <div id="detailCommentContainer" class="alert alert-warning py-2 d-none mb-3" style="font-size: 13px;">
                    <i class="fas fa-comment-alt mr-1"></i> <strong>Комментарий:</strong>
                    <span id="detailCommentText"></span>
                </div>

                {{-- Таблица оборудования --}}
                <div class="card card-outline card-secondary mb-0">
                    <div class="card-header py-2">
                        <h6 class="card-title mb-0"><i class="fas fa-boxes mr-1"></i> Оборудование по категориям</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm table-hover mb-0" id="detailItemsTable" style="font-size: 13px;">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th class="text-left">Категория</th>
                                    <th style="width: 90px;">По базе</th>
                                    <th style="width: 90px;">По факту</th>
                                    <th style="width: 90px;">Разница</th>
                                    <th class="text-left">Замечание</th>
                                </tr>
                            </thead>
                            <tbody id="detailItemsBody">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" data-bs-dismiss="modal" onclick="$('#checkDetailsModal').modal('hide')">
                    <i class="fas fa-times mr-1"></i> Закрыть
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).on('click', '.btn-show-check-details', function() {
        var check = $(this).data('check');
        if (!check) return;

        $('#detailCheckId').text(check.id);
        $('#detailTutor').text(check.tutor_fullname || ('ID: ' + check.tutor_id));
        $('#detailAuditory').text(check.auditory_name || ('ID: ' + check.auditory_id));

        var dateFormatted = check.created_at_formatted || (check.created_at ? check.created_at : (check.check_date || '—'));
        $('#detailDateTime').text(dateFormatted);
        $('#detailLessonTime').text((check.lesson_start || '—') + ' — ' + (check.lesson_finish || '—'));

        if (check.check_type === 'entrance') {
            $('#detailCheckType').html('<span class="badge badge-info">Вход в аудиторию</span>');
        } else {
            $('#detailCheckType').html('<span class="badge badge-secondary">Выход из аудитории</span>');
        }

        if (check.status === 'discrepancy') {
            $('#detailStatus').html('<span class="badge badge-danger">С замечаниями</span>');
        } else {
            $('#detailStatus').html('<span class="badge badge-success">Без замечаний</span>');
        }

        $('#detailKeyboardCount').text(check.keyboard_count !== null && check.keyboard_count !== undefined ? check.keyboard_count + ' шт.' : 'не указано');
        $('#detailMouseCount').text(check.mouse_count !== null && check.mouse_count !== undefined ? check.mouse_count + ' шт.' : 'не указано');

        if (check.comment && check.comment.trim() !== '') {
            $('#detailCommentText').text(check.comment);
            $('#detailCommentContainer').removeClass('d-none');
        } else {
            $('#detailCommentContainer').addClass('d-none');
        }

        var tbody = $('#detailItemsBody');
        tbody.empty();

        if (check.items && check.items.length > 0) {
            $.each(check.items, function(idx, item) {
                var pName = item.product_name;
                if (!pName && item.product) pName = item.product.name_product;
                if (!pName) pName = 'Категория #' + item.id_product;

                var dbCount  = parseInt(item.db_count  || 0);
                var factCount = parseInt(item.fact_count || 0);
                var diff = factCount - dbCount;

                var diffHtml = '', rowClass = '';
                if (diff < 0) {
                    diffHtml = '<span class="badge badge-danger">' + diff + ' шт.</span>';
                    rowClass = 'table-danger';
                } else if (diff > 0) {
                    diffHtml = '<span class="badge badge-warning">+' + diff + ' шт.</span>';
                    rowClass = 'table-warning';
                } else {
                    diffHtml = '<span class="badge badge-success">0</span>';
                }

                var noteText = item.note ? item.note : (diff < 0 ? '<span class="text-danger">Недостача</span>' : '<span class="text-muted">—</span>');

                tbody.append(
                    '<tr class="' + rowClass + ' text-center">' +
                        '<td>' + (idx + 1) + '</td>' +
                        '<td class="text-left"><strong>' + pName + '</strong></td>' +
                        '<td>' + dbCount + '</td>' +
                        '<td>' + factCount + '</td>' +
                        '<td>' + diffHtml + '</td>' +
                        '<td class="text-left">' + noteText + '</td>' +
                    '</tr>'
                );
            });
        } else {
            tbody.append('<tr><td colspan="6" class="text-center text-muted py-3">Нет данных по оборудованию</td></tr>');
        }

        $('#checkDetailsModal').modal('show');
    });
});
</script>
@endsection
