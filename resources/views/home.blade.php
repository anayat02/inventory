@extends('backend.layouts.app')

@section('page-class', 'home-page')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h4>{{ __('Информация о пользователе') }}</h4></div>
            <div class="card-body">
                <div class="col">
                    <div class="row">
                        @foreach($info as $data)
                            <div class="col-md-4">
                                <img class="profile-user-img img-responsive img-circle" src="https://ais.kazetu.kz/dist/users/{{$data->avatar_url}}" alt="User profile picture">
                                <br>
                                <h6 style="text-align: center">{{ $data->fio_rus }}</h6>
                                <h6 style="text-align: center; color: #777">{{ $data->name_job_rus }}</h6>
                                <h6 style="text-align: center; color: #777">{{ $data->division_name_rus }}</h6>
                            </div>
                        @endforeach
                        <div class="col">
                            @foreach($info as $data)
                                <h6>Логин: <b>{{ Auth::user()->Login }}</b></h6>
                                <hr>
                                <h6>Почта: <b>{{ $data->email }}</b></h6>
                                <hr>
                                <h6>Моб-тел.: <b>{{ $data->mobile_phone }}</b></h6>
                            @endforeach
                        </div>
                        <div class="col">
                            @if(!empty($messages))
                                @foreach($messages as $message)
                                    <div class="col-md-12">
                                        <div class="card collapsed-card" style="background-color: #0073b7 !important">
                                            <div class="card-header">
                                                <h3 style="color: white" class="card-title">Ошибка при редактировании</h3>

                                                <div class="card-tools">
                                                    <button style="margin-top: 3%;" type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <!-- /.card-tools -->
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body" style="background-color: #D8D8D8;">
                                                <p><b>Инвентарный номер: </b> {{$message->inv_number}}</p>
                                                <p><b>Название инвертаря: </b> {{$message->name_product}}</p>
                                                <p><b>Данные редактора: </b> {{$message->tutor_fullname}}</p>
                                                <p style="color: red;"><b>Сообщение об ошибке: </b> {{$message->message}}</p>
                                                <p style="color: #0d6efd"><a href="https://inv.metu.kz/all/{{$message->id_product}}" class="form-control" style="text-align: center; background-color: #0073B7; color: white;">Редактировать</a></p>
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                        <!-- /.card -->
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">
                                    <div class="card collapsed-card" style="background-color: #0073b7 !important">
                                        <div class="card-header">
                                            <h6 style="color: white" class="card-title">Здесь будут отображаться ошибки при редактировании</h6>
                                        </div>
                                        <!-- /.card-header -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            @if($hasPendingTransmissions)
                                <div class="text-center mt-4">
                                    <button id="openTransmissionsBtn" class="btn btn-success btn-lg">
                                        <i class="bi bi-box-seam me-2"></i> У вас есть заявки на приём ОС
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-shield-check mr-1"></i> Сегодняшние занятия и контроль сохранности аудиторий
                </h3>
                <div class="card-tools">
                    @if(Auth::user()->hasAnyRole(['admin', 'super-admin']))
                        <a href="{{ route('classroom_checks.admin_history') }}" class="btn btn-tool btn-sm" title="Журнал проверок">
                            <i class="bi bi-journal-text"></i> Журнал проверок
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body {{ empty($todaySchedule) || count($todaySchedule) == 0 ? 'p-3' : 'p-0' }}">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible m-3">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible m-3">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                    </div>
                @endif

                @if(empty($todaySchedule) || count($todaySchedule) == 0)
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                        <small>На сегодня запланированных занятий в компьютерных аудиториях нет.</small>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm mb-0" style="font-size: 13px;">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th class="text-left">Аудитория</th>
                                    <th>Тип</th>
                                    <th class="text-left">Кафедра</th>
                                    <th>Время</th>
                                    <th>Пары</th>
                                    <th style="width: 160px;">Вход</th>
                                    <th style="width: 160px;">Выход</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todaySchedule as $slot)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $slot->auditoryName }}</strong>
                                            <div class="small text-muted">{{ $slot->buildingName }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info">{{ $slot->auditoryTypeName }}</span>
                                        </td>
                                        <td>{{ $slot->cafedraNameRU }}</td>
                                        <td class="text-center text-muted">
                                            {{ $slot->start }} — {{ $slot->finish }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary">{{ $slot->lesson_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($slot->entrance_status == 'completed')
                                                <span class="badge badge-success p-2 d-block">
                                                    <i class="fas fa-check mr-1"></i> Пройдено
                                                </span>
                                                @if($slot->entrance_check && $slot->entrance_check->status == 'discrepancy')
                                                    <small class="text-danger d-block mt-1">Есть замечания</small>
                                                @endif
                                            @elseif($slot->can_check_entrance)
                                                <a href="{{ route('classroom_checks.form', ['auditoryID' => $slot->auditoryID, 'checkType' => 'entrance', 'start' => $slot->start, 'finish' => $slot->finish]) }}"
                                                   class="btn btn-sm btn-primary btn-block">
                                                    <i class="bi bi-box-arrow-in-right mr-1"></i> Вход
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-default btn-block" disabled title="{{ $slot->entrance_unlock_hint ?? '' }}">
                                                    <i class="fas fa-lock mr-1"></i> Недоступно
                                                </button>
                                                @if(isset($slot->entrance_unlock_hint))
                                                    <small class="text-muted d-block mt-1">{{ $slot->entrance_unlock_hint }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($slot->exit_status == 'completed')
                                                <span class="badge badge-success p-2 d-block">
                                                    <i class="fas fa-check mr-1"></i> Пройдено
                                                </span>
                                                @if($slot->exit_check && $slot->exit_check->status == 'discrepancy')
                                                    <small class="text-danger d-block mt-1">Есть замечания</small>
                                                @endif
                                            @elseif($slot->can_check_exit)
                                                <a href="{{ route('classroom_checks.form', ['auditoryID' => $slot->auditoryID, 'checkType' => 'exit', 'start' => $slot->start, 'finish' => $slot->finish]) }}"
                                                   class="btn btn-sm btn-warning btn-block">
                                                    <i class="bi bi-box-arrow-right mr-1"></i> Выход
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-default btn-block" disabled title="{{ $slot->exit_unlock_hint ?? '' }}">
                                                    <i class="fas fa-lock mr-1"></i> Недоступно
                                                </button>
                                                @if(isset($slot->exit_unlock_hint))
                                                    <small class="text-muted d-block mt-1">{{ $slot->exit_unlock_hint }}</small>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <!-- /.card -->
        <div class="card card-primary">
            <div class="card-header info">
                <h3 class="card-title">Список ОС, закреплённых за вами</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                @if($inventory->isEmpty())
                    <p class="text-center text-muted">Нет закрелённых за вами основных средств.</p>
                @else
                    <form action="{{ route('store_trans') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="tutor">Передать ответственному лицу:</label>
                            <select id="tutor" name="TutorID" class="form-control" required>
                                <option value="">Выберите сотрудника</option>
                                @foreach($tutorList as $tut)
                                    <option value="{{ $tut->TutorID }}">
                                        {{ $tut->lastname }} {{ $tut->firstname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <table id="example1" class="table table-bordered">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAllInventory"></th>
                                <th>Наименование</th>
                                <th>Инв. номер</th>
                                <th>Аудитория</th>
                                <th>Статус</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($inventory as $inv)
                                <tr>
                                    <td><input type="checkbox" name="id_products[]" value="{{ $inv->id_product }}"></td>
                                    <td>
                                        {{ $inv->name_product }}:
                                        @if($inv->characteristics->where('id_characteristic', 4)->count() > 0)
                                            @foreach($inv->characteristics->where('id_characteristic', 4)->where('current_status', 0) as $characteristic)
                                                <strong>{{ $characteristic->characteristic_value }}</strong>;
                                                <br>
                                            @endforeach
                                        @else
                                            @foreach($inv->characteristics->where('id_characteristic', 2)->where('current_status', 0) as $characteristic)
                                                <strong>{{ $characteristic->characteristic_value }}</strong>;
                                                <br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $inv->inv_number }}</td>
                                    <td>{{ $inv->auditoryName ?? '—' }}</td>
                                    <td>
                                        @php
                                            $statusLabels = [
                                                1 => ['label' => 'Ответственный', 'class' => 'bg-success'],
                                                2 => ['label' => 'Отправлен на передачу', 'class' => 'bg-info'],
                                                3 => ['label' => 'Отклонён', 'class' => 'bg-danger'],
                                            ];

                                            $status = $statusLabels[$inv->transmission_status] ?? ['label' => 'Неизвестно', 'class' => 'bg-secondary'];
                                        @endphp

                                        <span class="badge {{ $status['class'] }}">
                                                    {{ $status['label'] }}
                                                </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                Отправить выбранные ОС на передачу
                            </button>
                        </div>
                    </form>
                @endif
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- 🧩 Модальное окно -->
<div class="modal fade" id="transmissionModal" tabindex="-1" aria-labelledby="transmissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i> Заявки на приём ОС</h5>
            </div>
            <div class="modal-body" id="transmissionModalBody">
                <div class="text-center text-muted py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-2">Загрузка данных...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // === Глобальный чекбокс на главной таблице (передача ОС) ===
        const globalCheck = document.getElementById('checkAllInventory');
        if (globalCheck) {
            globalCheck.addEventListener('change', function () {
                document.querySelectorAll('input[name="id_products[]"]').forEach(cb => cb.checked = this.checked);
            });
        }

        // === Открытие модалки с заявками на приём ===
        document.getElementById('openTransmissionsBtn')?.addEventListener('click', async function () {
            const modalBody = document.getElementById('transmissionModalBody');
            const modal = new bootstrap.Modal(document.getElementById('transmissionModal'));

            modalBody.innerHTML = `
            <div class="text-center text-muted py-4">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-2">Загрузка...</p>
            </div>
        `;
            modal.show();

            try {
                const response = await fetch('{{ route('transmissions.confirm') }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                modalBody.innerHTML = html;

                // === Привязка чекбоксов "Выбрать все" внутри модалки ===
                const modalElement = document.getElementById('transmissionModal');
                modalElement.addEventListener('change', function (e) {
                    if (e.target.classList.contains('checkAll')) {
                        const table = e.target.closest('table');
                        if (!table) return;

                        const boxes = table.querySelectorAll('input[name="id_products[]"]');
                        boxes.forEach(cb => cb.checked = e.target.checked);
                    }
                });

            } catch (error) {
                modalBody.innerHTML = `<p class="text-danger text-center mt-4">Ошибка загрузки: ${error.message}</p>`;
            }
        });
    });
</script>
@endsection

<style>
    .profile-user-img {
        display: block;
        margin: 0 auto;
    }
    .select2-container {
        display: block !important;
        width: 100% !important;
    }

    .select2-container .select2-selection--multiple {
        min-height: 38px;
        overflow-y: auto;
        max-height: 200px; /* можно увеличить если нужно */
        white-space: normal !important;
    }

    .select2-selection__rendered {
        white-space: normal !important;
    }

    .card-body {
        overflow: visible !important;
        height: auto !important;
        min-height: auto !important;
    }

    .home-page {
        overflow: visible !important;
        height: auto !important;
        min-height: auto !important;
    }

</style>