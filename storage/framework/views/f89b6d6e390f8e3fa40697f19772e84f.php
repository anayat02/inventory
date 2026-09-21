<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4" style="color: #6f42c1;"><i class="fas fa-brain"></i> Предиктивный анализ поломок (Random Forest)</h2>
        
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h3 class="card-title m-0">Прогноз износа оборудования</h3>
                <span class="badge bg-danger ml-auto">Алгоритм: Случайный лес</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover m-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID ОС</th>
                            <th>Инв. номер</th>
                            <th>Год выпуска</th>
                            <th>Ремонты</th>
                            <th>Перемещения</th>
                            <th width="20%">Вероятность поломки</th>
                            <th>Статус ИИ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $predictions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prediction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($prediction->id_product); ?></td>
                                <td><strong><?php echo e($prediction->inv_number ?? 'Нет номера'); ?></strong></td>
                                <td><?php echo e($prediction->manufacture_year); ?></td>
                                <td><?php echo e($prediction->repair_count); ?></td>
                                <td><?php echo e($prediction->movement_count); ?></td>
                                <td>
                                    <div class="progress" style="height: 10px; margin-bottom: 5px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo e($prediction->risk_score); ?>%;" aria-valuenow="<?php echo e($prediction->risk_score); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted"><?php echo e($prediction->risk_score); ?>% вероятность выхода из строя</small>
                                </td>
                                <td>
                                    <?php if($prediction->risk_score > 90): ?>
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Критично (Замена)</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> В зоне риска</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                                    <p class="mt-2 text-muted">Модель Random Forest не предсказала скорых поломок в текущем пуле оборудования.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/analytics/predictive.blade.php ENDPATH**/ ?>