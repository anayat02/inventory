@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-clipboard-check mr-1"></i>
                    Проверка инвентаря: <strong>{{ $auditory->auditoryName }}</strong>
                    <small class="text-muted">({{ $auditory->buildingName }})</small>
                </h3>
                <div class="card-tools">
                    <span class="badge {{ $checkType == 'entrance' ? 'badge-primary' : 'badge-warning' }} mr-1">
                        {{ $checkType == 'entrance' ? 'Вход на занятие' : 'Выход с занятия' }}
                    </span>
                    <span class="badge badge-secondary">
                        {{ $start }} — {{ $finish }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="callout callout-info py-2 mb-3" style="font-size: 13px;">
                    <i class="fas fa-info-circle mr-1"></i>
                    Укажите фактическое количество каждой категории оборудования.
                    При расхождении с базой появится поле для примечания.
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('classroom_checks.store') }}" method="POST" id="classroomCheckForm">
                    @csrf
                    <input type="hidden" name="auditory_id"   value="{{ $auditory->auditoryID }}">
                    <input type="hidden" name="check_type"    value="{{ $checkType }}">
                    <input type="hidden" name="lesson_start"  value="{{ $start }}">
                    <input type="hidden" name="lesson_finish" value="{{ $finish }}">

                    {{-- 1. Таблица оборудования по категориям --}}
                    <div class="card card-outline card-secondary mb-3">
                        <div class="card-header py-2">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-boxes mr-1"></i> Оборудование по категориям
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-hover align-middle mb-0" id="categoriesTable" style="font-size: 13px;">
                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th class="text-left">Наименование ОС</th>
                                            <th style="width: 120px;">По базе</th>
                                            <th style="width: 150px;">По факту</th>
                                            <th class="text-left">Примечание</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $idx = 0; @endphp
                                        @forelse($groupedSummary as $catName => $catData)
                                            @php $idx++; @endphp
                                            <tr data-is-sysblock="{{ $catData['is_system_block'] ? '1' : '0' }}">
                                                <input type="hidden" name="categories[{{ $idx }}][name]"     value="{{ $catData['name'] }}">
                                                <input type="hidden" name="categories[{{ $idx }}][db_count]" value="{{ $catData['db_count'] }}" class="db-count-input">

                                                <td class="text-center text-muted">{{ $idx }}</td>
                                                <td>
                                                    {{ $catData['name'] }}
                                                    @if($catData['is_system_block'])
                                                        <span class="badge badge-primary ml-1">ПК</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-secondary">{{ $catData['db_count'] }} шт.</span>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" min="0"
                                                           name="categories[{{ $idx }}][fact_count]"
                                                           value="{{ $catData['db_count'] }}"
                                                           class="form-control form-control-sm text-center font-weight-bold fact-count-input"
                                                           style="width: 90px; margin: 0 auto;">
                                                </td>
                                                <td>
                                                    <div class="note-container d-none">
                                                        <input type="text"
                                                               name="categories[{{ $idx }}][note]"
                                                               class="form-control form-control-sm border-warning note-input"
                                                               placeholder="Укажите причину расхождения...">
                                                    </div>
                                                    <small class="match-badge text-success"><i class="fas fa-check mr-1"></i>Совпадает</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    В базе данных за этой аудиторией не числится оборудования.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Периферия --}}
                    <div class="card card-outline card-info mb-3">
                        <div class="card-header py-2">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-keyboard mr-1"></i> Учет периферии
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label for="keyboard_count" class="text-muted small mb-1">
                                            <i class="bi bi-keyboard mr-1"></i> Клавиатуры (по факту):
                                        </label>
                                        <input type="number" min="0" name="keyboard_count" id="keyboard_count"
                                               class="form-control text-center font-weight-bold"
                                               value="{{ $systemBlockCount }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label for="mouse_count" class="text-muted small mb-1">
                                            <i class="bi bi-mouse mr-1"></i> Мышки (по факту):
                                        </label>
                                        <input type="number" min="0" name="mouse_count" id="mouse_count"
                                               class="form-control text-center font-weight-bold"
                                               value="{{ $systemBlockCount }}">
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end mb-2">
                                    <div id="peripheralAlert" class="callout callout-success py-2 w-100 mb-0" style="font-size: 12px;">
                                        <span id="peripheralAlertText">
                                            Соответствует числу рабочих мест ({{ $systemBlockCount }} шт.)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Комментарий --}}
                    <div class="form-group">
                        <label for="comment" class="text-muted small mb-1">
                            <i class="fas fa-comment-alt mr-1"></i> Общий комментарий (необязательно):
                        </label>
                        <textarea name="comment" id="comment" rows="2"
                                  class="form-control form-control-sm"
                                  placeholder="Дополнительные замечания..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Отмена
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Сохранить проверку
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const keyboardInput = document.getElementById('keyboard_count');
        const mouseInput    = document.getElementById('mouse_count');
        const dbSysBlockCount = {{ $systemBlockCount }};

        function updateFormState() {
            let hasSysBlocks = (dbSysBlockCount > 0);

            document.querySelectorAll('#categoriesTable tbody tr').forEach(function(row) {
                let dbInput       = row.querySelector('.db-count-input');
                let factInput     = row.querySelector('.fact-count-input');
                let noteContainer = row.querySelector('.note-container');
                let noteInput     = row.querySelector('.note-input');
                let matchBadge    = row.querySelector('.match-badge');

                if (!dbInput || !factInput) return;

                let dbVal   = parseInt(dbInput.value)   || 0;
                let factVal = parseInt(factInput.value);
                if (isNaN(factVal)) factVal = 0;

                if (factVal !== dbVal) {
                    noteContainer?.classList.remove('d-none');
                    matchBadge?.classList.add('d-none');
                    noteInput?.setAttribute('required', 'required');
                    factInput.classList.add('is-invalid');
                    factInput.classList.remove('is-valid');
                } else {
                    noteContainer?.classList.add('d-none');
                    matchBadge?.classList.remove('d-none');
                    noteInput?.removeAttribute('required');
                    if (noteInput) noteInput.value = '';
                    factInput.classList.remove('is-invalid');
                    factInput.classList.add('is-valid');
                }
            });

            let keyboards = parseInt(keyboardInput?.value) || 0;
            let mice      = parseInt(mouseInput?.value)    || 0;
            const alertBox  = document.getElementById('peripheralAlert');
            const alertText = document.getElementById('peripheralAlertText');
            if (!alertBox || !alertText) return;

            if (!hasSysBlocks) {
                alertBox.className = 'callout callout-info py-2 w-100 mb-0';
                alertText.innerHTML = 'Системных блоков в базе нет.';
            } else if (keyboards === dbSysBlockCount && mice === dbSysBlockCount) {
                alertBox.className = 'callout callout-success py-2 w-100 mb-0';
                alertText.innerHTML = `<i class="fas fa-check mr-1"></i> Клав: ${keyboards}, мышей: ${mice} — совпадает (${dbSysBlockCount} р/м).`;
            } else {
                alertBox.className = 'callout callout-warning py-2 w-100 mb-0';
                alertText.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i> Р/мест: <strong>${dbSysBlockCount}</strong>, клав: <strong>${keyboards}</strong>, мышей: <strong>${mice}</strong>.`;
            }
        }

        document.querySelectorAll('.fact-count-input').forEach(i => i.addEventListener('input', updateFormState));
        keyboardInput?.addEventListener('input', updateFormState);
        mouseInput?.addEventListener('input', updateFormState);

        updateFormState();
    });
</script>
@endsection
