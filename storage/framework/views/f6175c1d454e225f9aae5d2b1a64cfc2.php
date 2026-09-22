<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title">
                    Проверка инвентаря: <?php echo e($auditory->auditoryName); ?> (<?php echo e($auditory->buildingName); ?>)
                </h3>
                <div>
                    <span class="badge <?php echo e($checkType == 'entrance' ? 'bg-primary' : 'bg-warning text-dark'); ?> fs-6">
                        Тип проверки: <?php echo e($checkType == 'entrance' ? 'Вход на занятие' : 'Выход с занятия'); ?>

                    </span>
                    <span class="badge bg-info text-dark fs-6 ms-2">
                        Время пар: <?php echo e($start); ?> — <?php echo e($finish); ?>

                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Укажите количество рабочей техники каждой категории оборудования в аудитории. 
                    Если фактическое количество отличается от базы данных, появится поле для ввода примечания.
                </div>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
                <?php endif; ?>

                <form action="<?php echo e(route('classroom_checks.store')); ?>" method="POST" id="classroomCheckForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="auditory_id" value="<?php echo e($auditory->auditoryID); ?>">
                    <input type="hidden" name="check_type" value="<?php echo e($checkType); ?>">
                    <input type="hidden" name="lesson_start" value="<?php echo e($start); ?>">
                    <input type="hidden" name="lesson_finish" value="<?php echo e($finish); ?>">

                    <!-- 1. ТАБЛИЦА СВЕРКИ КОЛИЧЕСТВА ОС ПО КАТЕГОРИЯМ -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light font-weight-bold">
                            Учет оборудования по категориям:
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0" id="categoriesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;" class="text-center">#</th>
                                            <th>Наименование ОС</th>
                                            <th style="width: 140px;" class="text-center">По базе (шт.)</th>
                                            <th style="width: 160px;" class="text-center">По факту (шт.)</th>
                                            <th>Примечание</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $idx = 0; ?>
                                        <?php $__empty_1 = true; $__currentLoopData = $groupedSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catName => $catData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <?php $idx++; ?>
                                            <tr data-is-sysblock="<?php echo e($catData['is_system_block'] ? '1' : '0'); ?>">
                                                <input type="hidden" name="categories[<?php echo e($idx); ?>][name]" value="<?php echo e($catData['name']); ?>">
                                                <input type="hidden" name="categories[<?php echo e($idx); ?>][db_count]" value="<?php echo e($catData['db_count']); ?>" class="db-count-input">
                                                
                                                <td class="text-center"><?php echo e($idx); ?></td>
                                                <td>
                                                    <strong class="fs-6"><?php echo e($catData['name']); ?></strong>
                                                    <?php if($catData['is_system_block']): ?>
                                                        <span class="badge bg-primary ms-1">ПК / Системный блок</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary fs-6"><?php echo e($catData['db_count']); ?> шт.</span>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" min="0" name="categories[<?php echo e($idx); ?>][fact_count]" 
                                                           value="<?php echo e($catData['db_count']); ?>" 
                                                           class="form-control form-control-lg text-center font-weight-bold fact-count-input"
                                                           style="width: 110px; margin: 0 auto;">
                                                </td>
                                                <td>
                                                    <div class="note-container d-none">
                                                        <input type="text" name="categories[<?php echo e($idx); ?>][note]" 
                                                               class="form-control border-warning note-input" 
                                                               placeholder="Опишите причину (например, 1 шт. в ремонте или не работает)...">
                                                    </div>
                                                    <div class="match-badge text-success small font-weight-bold">
                                                         Совпадает с базой
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    В базе данных за этой аудиторией не числится оборудования.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 2. УЧЕТ ПЕРИФЕРИИ (КЛАВИАТУРЫ И МЫШИ) -->
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white font-weight-bold">
                            <i class="bi bi-keyboard me-2"></i> Учет периферии (Клавиатуры и Мыши)
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="keyboard_count" class="form-label font-weight-bold">
                                        <i class="bi bi-keyboard me-1 text-primary"></i> Количество клавиатур (по факту):
                                    </label>
                                    <input type="number" min="0" name="keyboard_count" id="keyboard_count" 
                                           class="form-control form-control-lg text-center font-weight-bold" 
                                           value="<?php echo e($systemBlockCount); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="mouse_count" class="form-label font-weight-bold">
                                        <i class="bi bi-mouse me-1 text-primary"></i> Количество мышек (по факту):
                                    </label>
                                    <input type="number" min="0" name="mouse_count" id="mouse_count" 
                                           class="form-control form-control-lg text-center font-weight-bold" 
                                           value="<?php echo e($systemBlockCount); ?>">
                                </div>
                            </div>

                            <!-- Динамический алерт соответствия периферии с системными блоками -->
                            <div id="peripheralAlert" class="alert alert-success mt-3 mb-0 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                                <span id="peripheralAlertText">
                                    Количество клавиатур и мышей соответствует количеству системных блоков (<?php echo e($systemBlockCount); ?> шт.).
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. ОБЩИЙ КОММЕНТАРИЙ -->
                    <div class="mb-3">
                        <label for="comment" class="form-label font-weight-bold">Общий комментарий к аудитории (необязательно):</label>
                        <textarea name="comment" id="comment" rows="2" class="form-control" placeholder="Укажите дополнительные замечания по аудитории..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                        <a href="<?php echo e(route('home')); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Отмена
                        </a>
                        <button type="submit" class="btn btn-success btn-lg px-4">
                            <i class="bi bi-check-circle-fill me-1"></i> Сохранить результаты проверки
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
        const mouseInput = document.getElementById('mouse_count');

        function updateFormState() {
            let totalFactSysBlocks = 0;
            let hasSysBlocksInRoom = false;

            document.querySelectorAll('#categoriesTable tbody tr').forEach(function(row) {
                let dbInput = row.querySelector('.db-count-input');
                let factInput = row.querySelector('.fact-count-input');
                let noteContainer = row.querySelector('.note-container');
                let noteInput = row.querySelector('.note-input');
                let matchBadge = row.querySelector('.match-badge');
                let isSysBlock = row.getAttribute('data-is-sysblock') === '1';

                if (!dbInput || !factInput) return;

                let dbVal = parseInt(dbInput.value) || 0;
                let factVal = parseInt(factInput.value);

                if (isNaN(factVal)) factVal = 0;

                if (isSysBlock) {
                    hasSysBlocksInRoom = true;
                    totalFactSysBlocks += factVal;
                }

                // Показываем поле примечания ТОЛЬКО при расхождении по факту
                if (factVal !== dbVal) {
                    if (noteContainer) noteContainer.classList.remove('d-none');
                    if (matchBadge) matchBadge.classList.add('d-none');
                    if (noteInput) {
                        noteInput.setAttribute('required', 'required');
                    }
                    factInput.classList.add('is-invalid');
                    factInput.classList.remove('is-valid');
                } else {
                    if (noteContainer) noteContainer.classList.add('d-none');
                    if (matchBadge) matchBadge.classList.remove('d-none');
                    if (noteInput) {
                        noteInput.removeAttribute('required');
                        noteInput.value = '';
                    }
                    factInput.classList.remove('is-invalid');
                    factInput.classList.add('is-valid');
                }
            });

            // Сверка периферии
            let keyboards = parseInt(keyboardInput?.value) || 0;
            let mice = parseInt(mouseInput?.value) || 0;

            const alertBox = document.getElementById('peripheralAlert');
            const alertText = document.getElementById('peripheralAlertText');

            if (!alertBox || !alertText) return;

            if (!hasSysBlocksInRoom) {
                alertBox.className = 'alert alert-info mt-3 mb-0 d-flex align-items-center';
                alertText.innerHTML = `В этой аудитории системных блоков по базе не зафиксировано.`;
            } else if (keyboards === totalFactSysBlocks && mice === totalFactSysBlocks) {
                alertBox.className = 'alert alert-success mt-3 mb-0 d-flex align-items-center';
                alertText.innerHTML = `<i class="bi bi-check-circle-fill me-2 fs-5"></i> <strong>Отлично!</strong> Количество клавиатур (${keyboards}) и мышей (${mice}) совпадает с количеством системных блоков по факту (${totalFactSysBlocks} шт.).`;
            } else {
                alertBox.className = 'alert alert-warning mt-3 mb-0 d-flex align-items-center';
                alertText.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-warning"></i> <strong>Внимание!</strong> Системных блоков по факту: <strong>${totalFactSysBlocks} шт.</strong>, а клавиатур введено: <strong>${keyboards} шт.</strong>, мышек: <strong>${mice} шт.</strong>`;
            }
        }

        document.querySelectorAll('.fact-count-input').forEach(function(input) {
            input.addEventListener('input', updateFormState);
        });

        if (keyboardInput) keyboardInput.addEventListener('input', updateFormState);
        if (mouseInput) mouseInput.addEventListener('input', updateFormState);

        // Инициализация при загрузке
        updateFormState();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/classroom_checks/check_form.blade.php ENDPATH**/ ?>