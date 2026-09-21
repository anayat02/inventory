

<?php $__env->startSection('page-class', 'home-page'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h4><?php echo e(__('Информация о пользователе')); ?></h4></div>
            <div class="card-body">
                <div class="col">
                    <div class="row">
                        <?php $__currentLoopData = $info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4">
                                <img class="profile-user-img img-responsive img-circle" src="https://ais.kazetu.kz/dist/users/<?php echo e($data->avatar_url); ?>" alt="User profile picture">
                                <br>
                                <h6 style="text-align: center"><?php echo e($data->fio_rus); ?></h6>
                                <h6 style="text-align: center; color: #777"><?php echo e($data->name_job_rus); ?></h6>
                                <h6 style="text-align: center; color: #777"><?php echo e($data->division_name_rus); ?></h6>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="col">
                            <?php $__currentLoopData = $info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <h6>Логин: <b><?php echo e(Auth::user()->Login); ?></b></h6>
                                <hr>
                                <h6>Почта: <b><?php echo e($data->email); ?></b></h6>
                                <hr>
                                <h6>Моб-тел.: <b><?php echo e($data->mobile_phone); ?></b></h6>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="col">
                            <?php if(!empty($messages)): ?>
                                <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-12">
                                        <div class="card collapsed-card" style="background-color: #0073b7 !important">
                                            <div class="card-header">
                                                <h3 style="color: white" class="card-title">Ошибка при редактировании</h3>

                                                <div class="card-tools">
                                                    <button style="margin-top: 3%;" type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <!-- /.card-tools -->
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body" style="background-color: #D8D8D8;">
                                                <p><b>Инвентарный номер: </b> <?php echo e($message->inv_number); ?></p>
                                                <p><b>Название инвертаря: </b> <?php echo e($message->name_product); ?></p>
                                                <p><b>Данные редактора: </b> <?php echo e($message->tutor_fullname); ?></p>
                                                <p style="color: red;"><b>Сообщение об ошибке: </b> <?php echo e($message->message); ?></p>
                                                <p style="color: #0d6efd"><a href="https://inv.metu.kz/all/<?php echo e($message->id_product); ?>" class="form-control" style="text-align: center; background-color: #0073B7; color: white;">Редактировать</a></p>
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                        <!-- /.card -->
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <div class="col-md-12">
                                    <div class="card collapsed-card" style="background-color: #0073b7 !important">
                                        <div class="card-header">
                                            <h6 style="color: white" class="card-title">Здесь будут отображаться ошибки при редактировании</h6>
                                        </div>
                                        <!-- /.card-header -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <?php if($hasPendingTransmissions): ?>
                                <div class="text-center mt-4">
                                    <button id="openTransmissionsBtn" class="btn btn-success btn-lg">
                                        <i class="bi bi-box-seam me-2"></i> У вас есть заявки на приём ОС
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <!-- /.card -->
        <div class="card card-primary">
            <div class="card-header info">
                <h3 class="card-title">Список ОС, закреплённых за вами</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <?php if($inventory->isEmpty()): ?>
                    <p class="text-center text-muted">Нет закрелённых за вами основных средств.</p>
                <?php else: ?>
                    <form action="<?php echo e(route('store_trans')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label for="tutor">Передать ответственному лицу:</label>
                            <select id="tutor" name="TutorID" class="form-control" required>
                                <option value="">Выберите сотрудника</option>
                                <?php $__currentLoopData = $tutorList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tut): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tut->TutorID); ?>">
                                        <?php echo e($tut->lastname); ?> <?php echo e($tut->firstname); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <table id="example1" class="table table-bordered">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAllInventory"></th>
                                <th>Наименование</th>
                                <th>Инв. номер</th>
                                <th>Аудитория</th>
                                <th>Статус</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $inventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><input type="checkbox" name="id_products[]" value="<?php echo e($inv->id_product); ?>"></td>
                                    <td>
                                        <?php echo e($inv->name_product); ?>:
                                        <?php if($inv->characteristics->where('id_characteristic', 4)->count() > 0): ?>
                                            <?php $__currentLoopData = $inv->characteristics->where('id_characteristic', 4)->where('current_status', 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $characteristic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <strong><?php echo e($characteristic->characteristic_value); ?></strong>;
                                                <br>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <?php $__currentLoopData = $inv->characteristics->where('id_characteristic', 2)->where('current_status', 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $characteristic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <strong><?php echo e($characteristic->characteristic_value); ?></strong>;
                                                <br>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($inv->inv_number); ?></td>
                                    <td><?php echo e($inv->auditoryName ?? '—'); ?></td>
                                    <td>
                                        <?php
                                            $statusLabels = [
                                                1 => ['label' => 'Ответственный', 'class' => 'bg-success'],
                                                2 => ['label' => 'Отправлен на передачу', 'class' => 'bg-info'],
                                                3 => ['label' => 'Отклонён', 'class' => 'bg-danger'],
                                            ];

                                            $status = $statusLabels[$inv->transmission_status] ?? ['label' => 'Неизвестно', 'class' => 'bg-secondary'];
                                        ?>

                                        <span class="badge <?php echo e($status['class']); ?>">
                                                    <?php echo e($status['label']); ?>

                                                </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                Отправить выбранные ОС на передачу
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- 🧩 Модальное окно -->
<div class="modal fade" id="transmissionModal" tabindex="-1" aria-labelledby="transmissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i> Заявки на приём ОС</h5>
            </div>
            <div class="modal-body" id="transmissionModalBody">
                <div class="text-center text-muted py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-2">Загрузка данных...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // === Глобальный чекбокс на главной таблице (передача ОС) ===
        const globalCheck = document.getElementById('checkAllInventory');
        if (globalCheck) {
            globalCheck.addEventListener('change', function () {
                document.querySelectorAll('input[name="id_products[]"]').forEach(cb => cb.checked = this.checked);
            });
        }

        // === Открытие модалки с заявками на приём ===
        document.getElementById('openTransmissionsBtn')?.addEventListener('click', async function () {
            const modalBody = document.getElementById('transmissionModalBody');
            const modal = new bootstrap.Modal(document.getElementById('transmissionModal'));

            modalBody.innerHTML = `
            <div class="text-center text-muted py-4">
                <div class="spinner-border text-success" role="status"></div>
                <p class="mt-2">Загрузка...</p>
            </div>
        `;
            modal.show();

            try {
                const response = await fetch('<?php echo e(route('transmissions.confirm')); ?>', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                modalBody.innerHTML = html;

                // === Привязка чекбоксов "Выбрать все" внутри модалки ===
                const modalElement = document.getElementById('transmissionModal');
                modalElement.addEventListener('change', function (e) {
                    if (e.target.classList.contains('checkAll')) {
                        const table = e.target.closest('table');
                        if (!table) return;

                        const boxes = table.querySelectorAll('input[name="id_products[]"]');
                        boxes.forEach(cb => cb.checked = e.target.checked);
                    }
                });

            } catch (error) {
                modalBody.innerHTML = `<p class="text-danger text-center mt-4">Ошибка загрузки: ${error.message}</p>`;
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<style>
    .profile-user-img {
        display: block;
        margin: 0 auto;
    }
    .select2-container {
        display: block !important;
        width: 100% !important;
    }

    .select2-container .select2-selection--multiple {
        min-height: 38px;
        overflow-y: auto;
        max-height: 200px; /* можно увеличить если нужно */
        white-space: normal !important;
    }

    .select2-selection__rendered {
        white-space: normal !important;
    }

    .card-body {
        overflow: visible !important;
        height: auto !important;
        min-height: auto !important;
    }

    .home-page {
        overflow: visible !important;
        height: auto !important;
        min-height: auto !important;
    }

</style>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/home.blade.php ENDPATH**/ ?>