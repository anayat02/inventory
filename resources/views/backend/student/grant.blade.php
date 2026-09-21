@extends('backend.layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header info">
                    <h3 class="card-title">Список студентов</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example2" class="table table-bordered table-striped" style="font-size: 13px !important">
                        @php
                            $adminTutorID = [646, 359, 521, 463, 738, 503, 817, 398, 786, 796, 782, 347, 772];
                        @endphp
                        <thead>
                        <tr>
                            <th>Кафедра</th>
                            <th>ОП</th>
                            <th>Группа</th>
                            <th>ID</th>
                            <th>ФИО студента</th>
                            <th>ИИН</th>
                            <th>Форма оплаты</th>
                            <th>Статус</th>
                            <th style="text-align: center">Серийный номер</th>
                            <th>Выдал</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($merged as $student)
                            <tr>
                                <td>{{$student->cafedraNameRU}}</td>
                                <td>{{$student->Spec}}</td>
                                <td>{{$student->group}}</td>
                                <td>{{$student->StudentID}}</td>
                                <td>{{$student->FioStudent}}</td>
                                <td>{{$student->iinplt}}</td>
                                <td>{{$student->PaymentForm}}</td>
                                <td>
                                    @php $s = $student->status; @endphp

                                    @if($s === 1 || $s === '1')
                                        <span class="badge bg-success">Выпущен</span>
                                    @elseif($s === 0 || $s === '0')
                                        <span class="badge bg-warning">Вернул</span>
                                    @elseif($s === null || $s === '' )
                                        <span class="badge bg-info">Не принял</span>
                                    @else
                                        <span class="badge bg-secondary">Неизвестно</span>
                                    @endif
                                    @if($student->status == 1)
                                        <a class="btn btn-primary" href="{{route('act', $student->StudentID)}}" target="_blank">Распечатать акт</a>
                                    @endif
                                </td>
                                <td style="text-align: center"><b style="color: @if($student->status == 0) red @endif;">{{isset($student->serialNumber) ? $student->serialNumber : 'Нет данных'}}</b></td>
                                <td style="text-align: center;"><b>{!! $student->login ?? '<p style="color: grey;">Нет данных</p>' !!}</b></td>
                                <td>
                                    <form action="{{ route('release', ['id' => $student->StudentID]) }}" method="POST">
                                        @csrf
                                        <input style="margin: 20px; width: 30%" class="form-control" readonly hidden name="StudentID" id="StudentID" value="{{ $student->StudentID }}">
                                        <button class="btn btn-block btn-success" type="submit">Выдать</button>
                                    </form>
                                    <br>
                                    @if(in_array(\Illuminate\Support\Facades\Auth::user()->TutorID, [646, 359, 521]))
                                        <form action="{{ route('confirm', ['id' => $student->StudentID]) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-block btn-warning" type="submit">Вернуть</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Кафедра</th>
                            <th>ОП</th>
                            <th>Группа</th>
                            <th>ID</th>
                            <th>ФИО студента</th>
                            <th>ИИН</th>
                            <th>Форма оплаты</th>
                            <th>Статус</th>
                            <th style="text-align: center">Серийный номер</th>
                            <th>Выдал</th>
                            <th>Действие</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
{{--    <!-- Модальное окно -->
    <div class="modal fade" id="modal-lg">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Серийный номер</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @if(!empty($student))
                    <form action="{{ route('release', ['id' => $student->StudentID]) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <label for="message">Укажите серийный номер ноутбука</label>
                            <input class="form-control" name="serialNumber" id="serialNumber" placeholder="Серийный номер ноутбука">
                        </div>
                        <!-- Скрытые поля для передачи данных строки -->
                        <input style="margin: 20px; width: 30%" class="form-control" readonly name="StudentID" id="StudentID" value="{{ $student->StudentID }}">
                        <div class="card-body">
                            <button class="btn btn-primary" type="submit">Сохранить</button>
                        </div>
                    </form>
                @endif
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <script>

        function fillModal(StudentID,) {
            document.getElementById('StudentID').value = StudentID;
        }
    </script>--}}
@endsection
