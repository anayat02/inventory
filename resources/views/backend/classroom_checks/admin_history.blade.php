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
                                    <option value="discrepancy" {{ request('status') == 'discrepancy' ? 'selected' : '' }}>⚠️ С замечаниями (Аномалии)</option>
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
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
