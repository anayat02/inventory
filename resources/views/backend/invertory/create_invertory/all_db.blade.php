@extends('backend.layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- 1. Секция Импорта -->
            <div class="card card-cyan collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Импорт инвентаря</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Выберите Excel файл для импорта. <b style="color: red;">Перед этим посмотрите <a href="/backend/assets/шаблон_импорта.xlsx">ШАБЛОН</a> импорта.</b></label>
                            <br>
                            <input type="file" name="excel_file" class="form" id="file" accept=".xls, .xlsx">
                        </div>
                        <button class="btn btn-primary btn-sm">Загрузить</button>
                    </form>
                </div>
            </div>

            <!-- 2. Секция Фильтрации (между импортом и списком наименований) -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-2"></i> Фильтр данных</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="buildingID">Учебный корпус:</label>
                                    <select name="buildingID" id="buildingID" class="form-control select2">
                                        <option value="">Все корпусы</option>
                                        @foreach($buildings ?? [] as $b)
                                            <option value="{{ $b->buildingID }}" {{ request('buildingID') == $b->buildingID ? 'selected' : '' }}>
                                                {{ $b->buildingName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="auditoryID">Аудитория:</label>
                                    <select name="auditoryID" id="auditoryID" class="form-control select2">
                                        <option value="">Все аудитории</option>
                                        @foreach($auditories ?? [] as $a)
                                            <option value="{{ $a->auditoryID }}" {{ request('auditoryID') == $a->auditoryID ? 'selected' : '' }}>
                                                {{ $a->auditoryName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="id_name">Наименование ОС:</label>
                                    <select name="id_name" id="id_name" class="form-control select2">
                                        <option value="">Все наименования</option>
                                        @foreach($productNames ?? [] as $pn)
                                            <option value="{{ $pn->id_name }}" {{ request('id_name') == $pn->id_name ? 'selected' : '' }}>
                                                {{ $pn->name_product }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="type">Назначение:</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="">Все назначения</option>
                                        <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Личный</option>
                                        <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Аудиторный</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="verification_status">Статус проверки:</label>
                                    <select name="verification_status" id="verification_status" class="form-control">
                                        <option value="">Все статусы</option>
                                        <option value="1" {{ request('verification_status') == '1' ? 'selected' : '' }}>Отправлено. На проверке</option>
                                        <option value="2" {{ request('verification_status') == '2' ? 'selected' : '' }}>Подтверждено</option>
                                        <option value="3" {{ request('verification_status') == '3' ? 'selected' : '' }}>На доработке</option>
                                    </select>
                                </div>
                            </div>
                            @if(Auth::user()->hasAnyRole(['admin', 'super-admin']))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tutorID">Ответственное лицо:</label>
                                        <select name="tutorID" id="tutorID" class="form-control select2">
                                            <option value="">Все сотрудники</option>
                                            @foreach($tutors ?? [] as $t)
                                                <option value="{{ $t->TutorID }}" {{ request('tutorID') == $t->TutorID ? 'selected' : '' }}>
                                                    {{ $t->lastname }} {{ $t->firstname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3 d-flex align-items-end mb-3">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search mr-1"></i> Применить
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-default">
                                    <i class="fas fa-undo mr-1"></i> Сбросить
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 3. Секция Таблицы с инвентарем -->
            <div class="card card-primary">
                <div class="card-header info">
                    <h3 class="card-title">Список наименований</h3>
                </div>
                <div class="card-body">
                    <table id="example2" class="table table-bordered table-striped" style="font-size: 13px !important">
                        @php
                            $adminTutorID = [646, 359];
                            $isAdminUser = Auth::user()->hasAnyRole(['admin', 'super-admin']) || in_array(Auth::user()->TutorID, $adminTutorID);
                        @endphp
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название инвентаря</th>
                            <th>Учебный корпус</th>
                            <th>Аудитория</th>
                            <th>Ответственное лицо</th>
                            <th>Инвентарный номер</th>
                            <th>Назначение</th>
                            <th>Характеристика</th>
                            <th>Дата редактирования</th>
                            <th>Последний редактор</th>
                            <th>Редактирование</th>
                            <th>Статус</th>
                            @if ($isAdminUser)
                                <th>Подтверждение</th>
                            @endif
                            <th>Примечание</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            @if(!empty($item))
                                @php
                                    $updated_at = \Carbon\Carbon::parse($item->updated_at);
                                    $formattedDate = $updated_at->format('d.m.Y');
                                @endphp
                                <tr style="text-align: center !important">
                                    <td>{{ $item->id_product }}</td>
                                    <td>{{ $item->name_product }}</td>
                                    <td>{{ $item->buildingName }}</td>
                                    <td>{{ $item->auditoryName }}</td>
                                    <td>{{ $item->tutor_fullname }}</td>
                                    <td>{{ $item->inv_number }}</td>
                                    <td>
                                        @if($item->type == 1)
                                            Личный
                                        @elseif($item->type == 2)
                                            Аудиторный
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($item->characteristics->where('current_status', 0) as $characteristic)
                                            <strong>{{ $characteristic->characteristic->name_characteristic ?? '' }}:</strong> {{ $characteristic->characteristic_value }};
                                            <br>
                                        @endforeach
                                    </td>
                                    <td align="center"><p >{{$formattedDate}}</p></td>
                                    <td>
                                        @if($item->redactor_fullname)
                                            {{ $item->redactor_fullname }}
                                        @else
                                            <h6 style="color: #7f8c8d">Нет данных</h6>
                                        @endif
                                    </td>
                                    <td align="center">
                                        <br>
                                        <a href="{{route('editAll', $item->id_product)}}" class="btn-sm btn-danger">Редактировать</a>
                                    </td>
                                    <td>
                                        @if($item->verification_status == 1)
                                            <span class="badge bg-warning">Отправлено.<br>На проверке</span>
                                        @elseif($item->verification_status == 2)
                                            <span class="badge bg-success">Подтверждено</span>
                                        @elseif($item->verification_status == 3)
                                            <span class="badge bg-danger">На доработке</span>
                                        @endif
                                    </td>
                                    @if ($isAdminUser)
                                        <td>
                                            <form action="{{ route('confirmStatus', ['id' => $item->id_product]) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-block btn-info" type="submit">Подтвердить</button>
                                            </form>
                                            <br>
                                            <button class="btn btn-block btn-danger" data-toggle="modal" data-target="#modal-lg"
                                                    onclick="fillModal('{{ $item->inv_number }}',
                                                                       '{{ $item->redactor_id }}',
                                                                       '{{ $item->id_name }}',
                                                                       '{{ $item->id_product }}')">
                                                Отказать
                                            </button>
                                        </td>
                                    @endif
                                    <td>
                                        @if(!empty($item->note))
                                            <span class="badge bg-info">{{$item->note}}</span>
                                        @else
                                            <p style="color: #7f8c8d">Нет данных</p>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Название инвентаря</th>
                            <th>Учебный корпус</th>
                            <th>Аудитория</th>
                            <th>Ответственное лицо</th>
                            <th>Инвентарный номер</th>
                            <th>Назначение</th>
                            <th>Характеристика</th>
                            <th>Дата редактирования</th>
                            <th>Последний редактор</th>
                            <th>Редактирование</th>
                            <th>Статус</th>
                            @if ($isAdminUser)
                                <th>Подтверждение</th>
                            @endif
                            <th>Примечание</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно -->
    <div class="modal fade" id="modal-lg">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Причина</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="refuseForm" action="{{ route('refuseStatus', ['id' => 0]) }}" method="POST">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="modal-body">
                        <label for="message">Укажите причину отказа</label>
                        <textarea class="form-control" name="message" id="message" placeholder="Напишите причину..."></textarea>
                    </div>

                    <input class="form-control" type="hidden" name="inv_number" id="inv_number">
                    <input class="form-control" type="hidden" name="redactor_id" id="redactor_id">
                    <input class="form-control" type="hidden" name="id_name" id="modal_id_name">
                    <div class="card-body">
                        <button class="btn btn-primary" type="submit">Отправить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function fillModal(invNumber, redactorId, id_name, id_product) {
            document.getElementById('inv_number').value = invNumber;
            document.getElementById('redactor_id').value = redactorId;
            document.getElementById('modal_id_name').value = id_name;

            const form = document.getElementById('refuseForm');
            form.action = form.action.replace(/\/\d+$/, '/' + id_product);
        }
    </script>
@endsection
