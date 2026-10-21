@extends('backend.layouts.app')
@section('content')

    <div class="card-body">
        <div class="row">

            {{-- Форма --}}
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Отпуск ноутбука сотруднику</h3>
                    </div>

                    <form action="{{ route('laptop_for_teachers') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">

                            <div class="form-group">
                                <label for="tutor_name">ФИО принимающего лица</label>
                                <input type="text"
                                       name="tutor_name"
                                       id="tutor_name"
                                       list="tutorsList"
                                       value="{{ old('tutor_name') }}"
                                       class="form-control @error('tutor_name') is-invalid @enderror"
                                       placeholder="Начните вводить ФИО"
                                       autocomplete="off"
                                       required>
                                <datalist id="tutorsList">
                                    @foreach($tutors as $tutor)
                                        <option value="{{ $tutor->full_name }}"></option>
                                    @endforeach
                                </datalist>
                                @error('tutor_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="model">Модель ноутбука</label>
                                    <input type="text"
                                           name="model"
                                           id="model"
                                           value="{{ old('model') }}"
                                           class="form-control @error('model') is-invalid @enderror"
                                           placeholder="Введите модель"
                                           required>
                                    @error('model')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="serial_number">Серийный номер</label>
                                    <input type="text"
                                           name="serial_number"
                                           id="serial_number"
                                           value="{{ old('serial_number') }}"
                                           class="form-control @error('serial_number') is-invalid @enderror"
                                           placeholder="Введите серийный номер"
                                           required>
                                    @error('serial_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label for="fileInput">Скан-копия документа</label>
                                <div class="file-upload" id="fileUpload">
                                    <input type="file" id="fileInput" name="file" accept=".pdf,.doc,.docx">
                                    <span id="fileUploadText">Перетащите файл сюда или нажмите для выбора</span>
                                </div>
                                <button type="button" id="fileClear" class="btn btn-link btn-sm text-danger p-0 mt-1 d-none">Убрать файл</button>
                                @error('file')
                                <span class="text-danger small d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Список --}}
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Список отпущенных ноутбуков</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>ФИО ответственного</th>
                                <th>Модель</th>
                                <th>Серийный номер</th>
                                <th>Акт ПП</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($laptop_list as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ $row->full_name }}</td>
                                    <td>{{ $row->model }}</td>
                                    <td>{{ $row->serial_number }}</td>
                                    <td>
                                        @if($issuance->file_act)
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-toggle="modal"
                                                    data-target="#pdfModal"
                                                    data-pdf="{{ Storage::url('act_pp/' . rawurlencode($row->file_act)) }}"
                                                    data-title="Акт приёма-передачи">
                                                Открыть
                                            </button>
                                        @else
                                            АКТ отсутствует
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Модальное окно PDF --}}
    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalTitle">PDF</h5>
                    <a href="#" id="pdfModalDownload" class="btn btn-sm btn-outline-secondary ml-auto mr-2" target="_blank" download>Скачать</a>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="pdfFrame" src="" style="width:100%; height:80vh; border:0;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <style>
        .file-upload {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 110px;
            padding: 0 15px;
            border: 1px dashed #adb5bd;
            border-radius: 4px;
            background: #fafafa;
            color: #6c757d;
            text-align: center;
            transition: border-color .2s, background-color .2s;
        }
        .file-upload:hover,
        .file-upload.dragging {
            border-color: #007bff;
            background: #f3f8ff;
        }
        .file-upload.has-file {
            border: 1px solid #28a745;
            background: #f1faf3;
            color: #155724;
            font-weight: 500;
        }
        .file-upload input[type="file"] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
    </style>

    {{-- Зона загрузки: чистый JS, не зависит от jQuery --}}
    <script>
        (function () {
            function init() {
                var input = document.getElementById('fileInput');
                var zone  = document.getElementById('fileUpload');
                var text  = document.getElementById('fileUploadText');
                var clear = document.getElementById('fileClear');
                if (!input || !zone || !text || !clear) return;

                var hint = text.textContent;

                function render() {
                    var file = input.files && input.files[0];
                    zone.classList.toggle('has-file', !!file);
                    clear.classList.toggle('d-none', !file);
                    text.textContent = file
                        ? 'Файл выбран: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' КБ)'
                        : hint;
                }

                input.addEventListener('change', render);
                input.addEventListener('input', render);

                ['dragenter', 'dragover'].forEach(function (ev) {
                    zone.addEventListener(ev, function () { zone.classList.add('dragging'); });
                });
                ['dragleave', 'drop'].forEach(function (ev) {
                    zone.addEventListener(ev, function () { zone.classList.remove('dragging'); });
                });

                // Подстраховка: если браузер не положил файл в input, назначаем вручную
                zone.addEventListener('drop', function (e) {
                    if (e.dataTransfer && e.dataTransfer.files.length && !input.files.length) {
                        input.files = e.dataTransfer.files;
                    }
                    setTimeout(render, 0);
                });

                clear.addEventListener('click', function () {
                    input.value = '';
                    render();
                });

                render();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>

    {{-- Модальное окно PDF: jQuery подключается после контента, поэтому ждём загрузки страницы --}}
    <script>
        window.addEventListener('load', function () {
            if (typeof window.jQuery === 'undefined') return;
            var $ = window.jQuery;

            $('#pdfModal').on('show.bs.modal', function (e) {
                var btn = $(e.relatedTarget);
                var url = btn.data('pdf');
                $('#pdfModalTitle').text(btn.data('title') || 'PDF');
                $('#pdfModalDownload').attr('href', url);
                $('#pdfFrame').attr('src', url);
            }).on('hidden.bs.modal', function () {
                $('#pdfFrame').attr('src', '');
            });
        });
    </script>
@endsection