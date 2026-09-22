<?php $__env->startSection('content'); ?>
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
                <form method="GET" action="<?php echo e(route('classroom_checks.admin_history')); ?>" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date">Дата проверки:</label>
                                <input type="date" name="date" id="date" class="form-control" value="<?php echo e(request('date')); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tutor_id">Преподаватель:</label>
                                <select name="tutor_id" id="tutor_id" class="form-control select2">
                                    <option value="">Все преподаватели</option>
                                    <?php $__currentLoopData = $allTutors ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tut): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($tut->TutorID); ?>" <?php echo e(request('tutor_id') == $tut->TutorID ? 'selected' : ''); ?>>
                                            <?php echo e($tut->lastname); ?> <?php echo e($tut->firstname); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="auditory_id">Аудитория:</label>
                                <select name="auditory_id" id="auditory_id" class="form-control select2">
                                    <option value="">Все аудитории</option>
                                    <?php $__currentLoopData = $allAuditories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($aud->auditoryID); ?>" <?php echo e(request('auditory_id') == $aud->auditoryID ? 'selected' : ''); ?>>
                                            <?php echo e($aud->auditoryName); ?> (<?php echo e($aud->buildingName); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="check_type">Тип проверки:</label>
                                <select name="check_type" id="check_type" class="form-control">
                                    <option value="">Все типы</option>
                                    <option value="entrance" <?php echo e(request('check_type') == 'entrance' ? 'selected' : ''); ?>>Вход в аудиторию</option>
                                    <option value="exit" <?php echo e(request('check_type') == 'exit' ? 'selected' : ''); ?>>Выход из аудитории</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Статус проверки:</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Все статусы</option>
                                    <option value="ok" <?php echo e(request('status') == 'ok' ? 'selected' : ''); ?>> Без замечаний</option>
                                    <option value="discrepancy" <?php echo e(request('status') == 'discrepancy' ? 'selected' : ''); ?>>⚠️ С замечаниями (Аномалии)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end mb-3">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i> Применить
                            </button>
                            <a href="<?php echo e(route('classroom_checks.admin_history')); ?>" class="btn btn-default">
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
                    <?php $__currentLoopData = $checks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $check): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="text-align: center !important" class="<?php echo e($check->status == 'discrepancy' ? 'table-warning' : ''); ?>">
                            <td><?php echo e($check->id); ?></td>
                            <td><?php echo e($check->created_at ? $check->created_at->format('d.m.Y H:i') : $check->check_date); ?></td>
                            <td><strong><?php echo e($check->tutor_fullname); ?></strong></td>
                            <td><?php echo e($check->auditory_name); ?></td>
                            <td><?php echo e($check->lesson_start); ?> — <?php echo e($check->lesson_finish); ?></td>
                            <td>
                                <?php if($check->check_type == 'entrance'): ?>
                                    <span class="badge bg-info">Вход</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Выход</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($check->status == 'discrepancy'): ?>
                                    <span class="badge bg-danger">⚠️ Замечания</span>
                                <?php else: ?>
                                    <span class="badge bg-success"> Без замечаний</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($check->keyboard_count !== null || $check->mouse_count !== null): ?>
                                    <div>
                                        <i class="bi bi-keyboard me-1"></i> Клавиатуры: <strong><?php echo e($check->keyboard_count ?? '—'); ?></strong>
                                    </div>
                                    <div>
                                        <i class="bi bi-mouse me-1"></i> Мыши: <strong><?php echo e($check->mouse_count ?? '—'); ?></strong>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-start">
                                <?php if(!empty($check->comment)): ?>
                                    <div class="small mb-1"><strong>Коммент:</strong> <?php echo e($check->comment); ?></div>
                                <?php endif; ?>

                                <?php if($check->items->count() > 0): ?>
                                    <ul class="mb-0 ps-3 small text-danger">
                                        <?php $__currentLoopData = $check->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($dItem->fact_count < $dItem->db_count || !empty($dItem->note)): ?>
                                                <li>
                                                    <strong><?php echo e($dItem->product_name ?? ($dItem->product->name_product ?? ('Категория #' . $dItem->id_product))); ?></strong>: 
                                                    По базе: <strong><?php echo e($dItem->db_count); ?></strong>, по факту: <strong><?php echo e($dItem->fact_count); ?></strong>
                                                    <?php if($dItem->note): ?> — <em><?php echo e($dItem->note); ?></em> <?php endif; ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php elseif(empty($check->comment)): ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/classroom_checks/admin_history.blade.php ENDPATH**/ ?>