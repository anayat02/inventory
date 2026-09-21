<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4"><i class="fas fa-exclamation-triangle text-warning"></i> ИИ-Анализ: Аномалии и Ошибки базы (DBSCAN)</h2>
        
        <div class="row">
            <!-- Дубликаты -->
            <div class="col-md-4">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Дубликаты инв. номеров</h3>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <?php if($duplicateProducts->count() > 0): ?>
                            <ul class="list-group">
                            <?php $__currentLoopData = $duplicateProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo e($product->inv_number); ?>

                                    <span class="badge bg-danger rounded-pill">ID: <?php echo e($product->id_product); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-success"><i class="fas fa-check-circle"></i> Дубликатов не найдено</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Без ответственного -->
            <div class="col-md-4">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">ОС без ответственного лица</h3>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <?php if($noTutorProducts->count() > 0): ?>
                            <ul class="list-group">
                            <?php $__currentLoopData = $noTutorProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo e($product->inv_number ?? 'Нет номера'); ?>

                                    <span class="badge bg-warning text-dark rounded-pill">Нет ответственного</span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-success"><i class="fas fa-check-circle"></i> Все ОС распределены</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- DBSCAN Аномалии -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Аномалии поведения (DBSCAN)</h3>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <?php if($dbscanAnomalies->count() > 0): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Алгоритм выявил объекты-выбросы.
                            </div>
                            <ul class="list-group">
                            <?php $__currentLoopData = $dbscanAnomalies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item">
                                    <strong><?php echo e($product->inv_number ?? 'ID: '.$product->id_product); ?></strong>
                                    <p class="mb-0 text-muted small">
                                        Аномальное поведение (потерянные сканирования или частые перемещения).
                                    </p>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-success"><i class="fas fa-check-circle"></i> Аномалий кластеризации не найдено</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/analytics/anomalies.blade.php ENDPATH**/ ?>