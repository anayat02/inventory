@extends('backend.layouts.app')
@section('content')
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Распределения ОС аудиторий по ответственным лицам</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form id="myform" role="form" action="{{route('synchronize')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="auditory">Аудитория</label>
                                            <select id="auditory" name="auditoryID" class="form-control">
                                                <option value="">Выберите аудиторию</option>
                                                @foreach($sortedAuditories as $auditories)
                                                    <option value="{{$auditories->auditoryID}}">{{$auditories->auditoryName}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tutor">Список ответственных</label>
                                            <select id="tutor" name="TutorID" class="form-control">
                                                <option value="">Выберите сотрудника</option>
                                                @foreach($user as $tutors)
                                                    <option value="{{$tutors->TutorID}}">{{$tutors->tutor_fullname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Синхронизация</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-2">
            </div>
        </div>
        <!-- /.row -->
    </div>
@endsection

<style>
    .select2-container--default{
        width: 100%;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        padding: 1px 7px !important;
        padding-left: 30px !important;
    }
    .select2-selection__choice__remove{
        padding: 1px 7px !important;
        margin: 0 !important;
    }
    .select2-selection__choice__remove:hover{
        background-color: #0e5b44; !important;
    }
</style>

