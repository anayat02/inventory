@extends('backend.layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">1. Откуда копировать</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Аудитория</label>
                        <select id="source_auditoryID" class="form-control">
                            <option value="">Выберите аудиторию</option>
                            @foreach($auditories as $a)
                                <option value="{{ $a->auditoryID }}">{{ $a->auditoryName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Товар-донор</label>
                        <select id="source_id_product" class="form-control" disabled>
                            <option value="">Сначала выберите аудиторию</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Характеристики выбранного товара</label>
                        <ul id="source_characteristics_list" class="list-group">
                            <li class="list-group-item text-muted">Товар не выбран</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header"><h3 class="card-title">2. Куда копировать</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Аудитория</label>
                        <select id="target_auditoryID" class="form-control">
                            <option value="">Выберите аудиторию</option>
                            @foreach($auditories as $a)
                                <option value="{{ $a->auditoryID }}">{{ $a->auditoryName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Тип товара (все товары этого типа получат характеристики)</label>
                        <select id="target_id_name" class="form-control" disabled>
                            <option value="">Сначала выберите аудиторию</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center">
            <form id="syncForm" method="POST" action="{{ route('copyCharacteristics.sync') }}">
                @csrf
                <input type="hidden" name="source_id_product" id="hidden_source_id_product">
                <input type="hidden" name="target_auditoryID" id="hidden_target_auditoryID">
                <input type="hidden" name="target_id_name" id="hidden_target_id_name">
                <button type="submit" id="syncBtn" class="btn btn-lg btn-primary" disabled>
                    <i class="fas fa-sync mr-2"></i> Синхронизировать характеристики
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sourceAuditorySelect = document.getElementById('source_auditoryID');
            const sourceProductSelect  = document.getElementById('source_id_product');
            const sourceCharList       = document.getElementById('source_characteristics_list');

            const targetAuditorySelect = document.getElementById('target_auditoryID');
            const targetNameSelect     = document.getElementById('target_id_name');

            const syncBtn = document.getElementById('syncBtn');

            function checkReady() {
                syncBtn.disabled = !(sourceProductSelect.value && targetAuditorySelect.value && targetNameSelect.value);
            }

            // Шаг 1: выбор аудитории-источника -> подгружаем товары
            sourceAuditorySelect.addEventListener('change', function () {
                sourceProductSelect.innerHTML = '<option value="">Загрузка...</option>';
                sourceProductSelect.disabled = true;
                sourceCharList.innerHTML = '<li class="list-group-item text-muted">Товар не выбран</li>';
                checkReady();

                if (!this.value) {
                    sourceProductSelect.innerHTML = '<option value="">Сначала выберите аудиторию</option>';
                    return;
                }

                fetch('/copy-characteristics/products/' + this.value)
                    .then(r => r.json())
                    .then(data => {
                        sourceProductSelect.innerHTML = '<option value="">Выберите товар</option>';
                        data.forEach(p => {
                            const opt = document.createElement('option');
                            opt.value = p.id_product;
                            opt.textContent = (p.name_product || 'Без названия') + ' — ' + (p.inv_number || 'без номера');
                            sourceProductSelect.appendChild(opt);
                        });
                        sourceProductSelect.disabled = false;
                    });
            });

            // Выбор товара-донора -> подгружаем его характеристики
            sourceProductSelect.addEventListener('change', function () {
                document.getElementById('hidden_source_id_product').value = this.value;
                checkReady();

                if (!this.value) {
                    sourceCharList.innerHTML = '<li class="list-group-item text-muted">Товар не выбран</li>';
                    return;
                }

                sourceCharList.innerHTML = '<li class="list-group-item text-muted">Загрузка...</li>';

                fetch('/copy-characteristics/characteristics/' + this.value)
                    .then(r => r.json())
                    .then(data => {
                        if (data.length === 0) {
                            sourceCharList.innerHTML = '<li class="list-group-item text-danger">У товара нет активных характеристик</li>';
                            return;
                        }
                        sourceCharList.innerHTML = '';
                        data.forEach(c => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item';
                            li.innerHTML = '<strong>' + (c.name_characteristic || '') + ':</strong> ' + c.characteristic_value;
                            sourceCharList.appendChild(li);
                        });
                    });
            });

            // Шаг 2: выбор аудитории-получателя -> подгружаем типы товаров
            targetAuditorySelect.addEventListener('change', function () {
                document.getElementById('hidden_target_auditoryID').value = this.value;
                targetNameSelect.innerHTML = '<option value="">Загрузка...</option>';
                targetNameSelect.disabled = true;
                checkReady();

                if (!this.value) {
                    targetNameSelect.innerHTML = '<option value="">Сначала выберите аудиторию</option>';
                    return;
                }

                fetch('/copy-characteristics/names/' + this.value)
                    .then(r => r.json())
                    .then(data => {
                        targetNameSelect.innerHTML = '<option value="">Выберите тип товара</option>';
                        data.forEach(n => {
                            const opt = document.createElement('option');
                            opt.value = n.id_name;
                            opt.textContent = n.name_product || 'Без названия';
                            targetNameSelect.appendChild(opt);
                        });
                        targetNameSelect.disabled = false;
                    });
            });

            targetNameSelect.addEventListener('change', function () {
                document.getElementById('hidden_target_id_name').value = this.value;
                checkReady();
            });
        });
    </script>

@endsection