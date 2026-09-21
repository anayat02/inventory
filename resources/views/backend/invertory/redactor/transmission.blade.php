@extends('backend.layouts.app')
@section('content')

    @php

        use Illuminate\Support\Facades\DB;

        $tutor = DB::connection('mysql_platonus')->table('tutors')->get();
        $building = DB::table('buildings')->get();
        $auditories = DB::table('auditories')->get();
        $sortedAuditories = $auditories->sortBy('auditoryName');

    @endphp
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Перемещение</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form id="myform" role="form" action="{{route('store_trans')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="card-body">
                                        <div class="form-group">
                                            @php
                                                $auditoryTypes = $inventory->pluck('auditoryType')->unique();
                                                $uniqueAuditories = $inventory->whereNotNull('auditoryName')->unique('auditoryName');

                                                $auditoryType = $auditoryTypes->first();
                                            @endphp
                                            @if($auditoryType == 2)
                                                <label for="recordSelect">Выберите инвентарь</label>
                                                <select id="recordSelect" multiple class="form-control" name="id_products[]">
                                                    @foreach($inventory as $inv)
                                                        <option value="{{ $inv->id_product }}">
                                                            {{ $inv->name }} ({{ $inv->inv_number }}):

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

                                                        </option>
                                                    @endforeach
                                                </select>

                                            @endif
                                            @if($auditoryType == 1)
                                                <div class="mt-3">
                                                    <label for="auditorySelect">Выберите аудиторию:</label>
                                                    <select id="auditorySelect" class="form-control" name="auditoryID">
                                                        @foreach($uniqueAuditories as $inv)
                                                            <option value="{{ $inv->auditoryID }}">
                                                                {{ $inv->auditoryName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="tutor">Передаваемое лицо</label>
                                            <select id="tutor" name="TutorID" class="form-control">
                                                <option value="">Выберите ответственное лицо</option>
                                                @foreach($tutor as $tutors)
                                                    <option value="{{$tutors->TutorID}}">{{$tutors->lastname}} {{$tutors->firstname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
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

<script>
    // script.js
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const fileUpload = document.getElementById('fileUpload');

        // Обработчик выбора файлов
        fileInput.addEventListener('change', displayFiles);

        // Обработка события перетаскивания файлов
        fileUpload.addEventListener('dragover', function (e) {
            e.preventDefault();
            fileUpload.classList.add('dragging');
        });

        fileUpload.addEventListener('dragleave', function () {
            fileUpload.classList.remove('dragging');
        });

        fileUpload.addEventListener('drop', function (e) {
            e.preventDefault();
            fileUpload.classList.remove('dragging');
            const files = e.dataTransfer.files;
            displayFiles({ target: { files } });
        });

        // Функция для отображения списка загруженных файлов
        function displayFiles(event) {
            fileList.innerHTML = ''; // Очистка списка перед добавлением новых файлов
            const files = event.target.files;

            for (const file of files) {
                const fileItem = document.createElement('p');
                fileItem.textContent = `Имя файла: ${file.name}, Размер: ${(file.size / 1024).toFixed(2)} KB`;
                fileList.appendChild(fileItem);
            }
        }
    });

</script>
