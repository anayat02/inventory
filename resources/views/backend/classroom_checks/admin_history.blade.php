@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- 1. Секция Фильтрации -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i> Фильтр проверок аудиторий</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('classroom_checks.admin_history') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date">Дата проверки:</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tutor_id">Преподаватель:</label>
                                <select name="tutor_id" id="tutor_id" class="form-control select2">
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
                                <label for="auditory_id">Аудитория:</label>
                                <select name="auditory_id" id="auditory_id" class="form-control select2">
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
                                <label for="check_type">Тип проверки:</label>
                                <select name="check_type" id="check_type" class="form-control">
                                    <option value="">Все типы</option>
                                    <option value="entrance" {{ request('check_type') == 'entrance' ? 'selected' : '' }}>Вход в аудиторию</option>
                                    <option value="exit" {{ request('check_type') == 'exit' ? 'selected' : '' }}>Выход из аудитории</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Статус проверки:</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Все статусы</option>
                                    <option value="ok" {{ request('status') == 'ok' ? 'selected' : '' }}> Без замечаний</option>
                                    <option value="discrepancy" {{ request('status') == 'discrepancy' ? 'selected' : '' }}>С замечаниями (Аномалии)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end mb-3">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i> Применить
                            </button>
                            <a href="{{ route('classroom_checks.admin_history') }}" class="btn btn-default">
                                <i class="fas fa-undo mr-1"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Таблица записей с использованием DataTables (id="example2") -->
        <div class="card card-primary">
            <div class="card-header info">
                <h3 class="card-title">Журнал проверок сохранности аудиторий (Администрирование)</h3>
            </div>
            <div class="card-body">
                <table id="example2" class="table table-bordered table-striped" style="font-size: 13px !important">
                    <thead>
                    <tr style="text-align: center !important">
                        <th>ID</th>
                        <th>Дата и время</th>
                        <th>Преподаватель</th>
                        <th>Аудитория</th>
                        <th>Время пар</th>
                        <th>Тип проверки</th>
                        <th>Статус</th>
                        <th>Учет периферии</th>
                        <th>Замечания / Разногласия</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($checks as $check)
                        <tr style="text-align: center !important" class="{{ $check->status == 'discrepancy' ? 'table-warning' : '' }}">
                            <td>{{ $check->id }}</td>
                            <td>{{ $check->created_at ? $check->created_at->format('d.m.Y H:i') : $check->check_date }}</td>
                            <td><strong>{{ $check->tutor_fullname }}</strong></td>
                            <td>{{ $check->auditory_name }}</td>
                            <td>{{ $check->lesson_start }} — {{ $check->lesson_finish }}</td>
                            <td>
                                @if($check->check_type == 'entrance')
                                    <span class="badge bg-info">Вход</span>
                                @else
                                    <span class="badge bg-secondary">Выход</span>
                                @endif
                            </td>
                            <td>
                                @if($check->status == 'discrepancy')
                                    <span class="badge bg-danger">⚠️ Замечания</span>
                                @else
                                    <span class="badge bg-success"> Без замечаний</span>
                                @endif
                            </td>
                            <td>
                                @if($check->keyboard_count !== null || $check->mouse_count !== null)
                                    <div>
                                        <i class="bi bi-keyboard me-1"></i> Клавиатуры: <strong>{{ $check->keyboard_count ?? '—' }}</strong>
                                    </div>
                                    <div>
                                        <i class="bi bi-mouse me-1"></i> Мыши: <strong>{{ $check->mouse_count ?? '—' }}</strong>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-start">
                                @if(!empty($check->comment))
                                    <div class="small mb-1"><strong>Коммент:</strong> {{ $check->comment }}</div>
                                @endif

                                @if($check->items->count() > 0)
                                    <ul class="mb-0 ps-3 small text-danger">
                                        @foreach($check->items as $dItem)
                                            @if($dItem->fact_count < $dItem->db_count || !empty($dItem->note))
                                                <li>
                                                    <strong>{{ $dItem->product_name ?? ($dItem->product->name_product ?? ('Категория #' . $dItem->id_product)) }}</strong>: 
                                                    По базе: <strong>{{ $dItem->db_count }}</strong>, по факту: <strong>{{ $dItem->fact_count }}</strong>
                                                    @if($dItem->note) — <em>{{ $dItem->note }}</em> @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @elseif(empty($check->comment))
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info btn-show-check-details" 
                                        data-check='@json($check)' 
                                        title="Просмотреть подробности отчета">
                                    <i class="fas fa-eye mr-1"></i> Подробнее
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr style="text-align: center !important">
                        <th>ID</th>
                        <th>Дата и время</th>
                        <th>Преподаватель</th>
                        <th>Аудитория</th>
                        <th>Время пар</th>
                        <th>Тип проверки</th>
                        <th>Статус</th>
                        <th>Учет периферии</th>
                        <th>Замечания / Разногласия</th>
                        <th>Действия</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра подробностей проверки -->
<div class="modal fade" id="checkDetailsModal" tabindex="-1" role="dialog" aria-labelledby="checkDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="checkDetailsModalLabel">
                    <i class="fas fa-clipboard-check mr-2"></i> Подробности отчета проверки аудитории № <span id="detailCheckId"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Панель статуса и общей информации -->
                <div class="card card-outline card-info mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong><i class="fas fa-user mr-1 text-primary"></i> Преподаватель:</strong> 
                                <span id="detailTutor"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong><i class="fas fa-door-open mr-1 text-primary"></i> Аудитория:</strong> 
                                <span id="detailAuditory"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong><i class="fas fa-calendar-alt mr-1 text-primary"></i> Дата и время:</strong> 
                                <span id="detailDateTime"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong><i class="fas fa-clock mr-1 text-primary"></i> Время пар:</strong> 
                                <span id="detailLessonTime"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong><i class="fas fa-sign-in-alt mr-1 text-primary"></i> Тип проверки:</strong> 
                                <span id="detailCheckType"></span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong><i class="fas fa-exclamation-triangle mr-1 text-primary"></i> Общий статус:</strong> 
                                <span id="detailStatus"></span>
                            </div>
                        </div>

                        <!-- Секция учета периферии -->
                        <hr class="my-2">
                        <div class="row bg-light p-2 rounded">
                            <div class="col-md-6">
                                <i class="bi bi-keyboard mr-1 text-secondary"></i> Клавиатуры (по факту): <strong id="detailKeyboardCount"></strong>
                            </div>
                            <div class="col-md-6">
                                <i class="bi bi-mouse mr-1 text-secondary"></i> Мыши (по факту): <strong id="detailMouseCount"></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Блок общего комментария, если есть -->
                <div id="detailCommentContainer" class="alert alert-warning d-none mb-3">
                    <strong><i class="fas fa-comment-alt mr-1"></i> Комментарий преподавателя:</strong>
                    <div id="detailCommentText" class="mt-1"></div>
                </div>

                <!-- Таблица попредметного инвентаря -->
                <h6 class="font-weight-bold mb-2">
                    <i class="fas fa-boxes mr-1 text-primary"></i> Результаты проверки оборудования по категориям
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm mb-0" id="detailItemsTable" style="font-size: 13px;">
                        <thead class="bg-light text-center">
                            <tr>
                                <th>#</th>
                                <th>Категория / Инвентарь</th>
                                <th>По базе (шт.)</th>
                                <th>По факту (шт.)</th>
                                <th>Разница</th>
                                <th>Состояние / Замечание</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsBody">
                            <!-- Заполняется динамически через JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
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
            $('#detailCheckType').html('<span>Вход в аудиторию</span>');
        } else {
            $('#detailCheckType').html('<span>Выход из аудитории</span>');
        }

        if (check.status === 'discrepancy') {
            $('#detailStatus').html('<span class="badge bg-danger">С замечаниями</span>');
        } else {
            $('#detailStatus').html('<span class="badge bg-success"> Без замечаний</span>');
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
                if (!pName && item.product) {
                    pName = item.product.name_product;
                }
                if (!pName) {
                    pName = 'Категория #' + item.id_product;
                }

                var dbCount = parseInt(item.db_count || 0);
                var factCount = parseInt(item.fact_count || 0);
                var diff = factCount - dbCount;

                var diffBadge = '';
                var rowClass = '';

                if (diff < 0) {
                    diffBadge = '<span class="badge bg-danger">' + diff + ' шт.</span>';
                    rowClass = 'table-danger';
                } else if (diff > 0) {
                    diffBadge = '<span class="badge bg-warning">+' + diff + ' шт.</span>';
                    rowClass = 'table-warning';
                } else {
                    diffBadge = '<span class="badge bg-success">0 (Совпадает)</span>';
                }

                var noteText = item.note || '—';
                if (diff < 0 && (!item.note || item.note === '')) {
                    noteText = '<span class="text-danger font-italic">Недостача оборудования</span>';
                }

                var tr = $('<tr class="' + rowClass + ' text-center">' +
                    '<td>' + (idx + 1) + '</td>' +
                    '<td class="text-start"><strong>' + pName + '</strong></td>' +
                    '<td>' + dbCount + '</td>' +
                    '<td>' + factCount + '</td>' +
                    '<td>' + diffBadge + '</td>' +
                    '<td class="text-start">' + noteText + '</td>' +
                '</tr>');

                tbody.append(tr);
            });
        } else {
            tbody.append('<tr><td colspan="6" class="text-center text-muted p-3">Нет данных по категориям оборудования</td></tr>');
        }

        $('#checkDetailsModal').modal('show');
    });
});
</script>
@endsection
