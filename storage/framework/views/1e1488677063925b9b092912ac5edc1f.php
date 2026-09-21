
<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-cyan collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Импорт инвентаря</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="<?php echo e(route('import')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="file">Выберите Excel файл для импорта. <b style="color: red;">Перед этим посмотрите <a href="/backend/assets/шаблон_импорта.xlsx">ШАБЛОН</a> импорта.</b></label>
                            <br>
                            <input type="file" name="excel_file" class="form" id="file" accept=".xls, .xlsx">
                        </div>
                        <button class="btn btn-primary btn-sm">Загрузить</button>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
            <div class="card card-primary">
                <div class="card-header info">
                    <h3 class="card-title">Список наименований</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example2" class="table table-bordered table-striped" style="font-size: 13px !important">
                        <?php

                            $adminTutorID = [646, 359];

                        ?>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название инвентаря</th>
                            <th>Учебный корпус</th>
                            <th>Аудитория</th>
                            <th>Ответственное лицо</th>
                            <th>Инвентарный номер</th>
                            <th>Назначение</th>
                            <th>Характеристика</th>
                            <th>Дата редактирования</th>
                            <th>Последний редактор</th>
                            <th>Редактирование</th>
                            <th>Статус</th>
                            <?php if(in_array(Auth::user()->TutorID, $adminTutorID)): ?>
                                <th>Подтверждение</th>
                            <?php endif; ?>
                            <th>Примечание</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!empty($item)): ?>
                                <?php
                                    $updated_at = \Carbon\Carbon::parse($item->updated_at);

                                    $formattedDate = $updated_at->format('d.m.Y');
                                ?>
                                <tr style="text-align: center !important">
                                    <td><?php echo e($item->id_product); ?></td>
                                    <td><?php echo e($item->name_product); ?></td>
                                    <td><?php echo e($item->buildingName); ?></td>
                                    <td><?php echo e($item->auditoryName); ?></td>
                                    <td><?php echo e($item->tutor_fullname); ?></td>
                                    <td><?php echo e($item->inv_number); ?></td>
                                    <td>
                                        <?php if($item->type == 1): ?>
                                            Личный
                                        <?php elseif($item->type == 2): ?>
                                            Аудиторный
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $__currentLoopData = $item->characteristics->where('current_status', 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $characteristic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <strong><?php echo e($characteristic->characteristic->name_characteristic); ?>:</strong> <?php echo e($characteristic->characteristic_value); ?>;
                                            <br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td align="center"><p ><?php echo e($formattedDate); ?></p></td>
                                    <td>
                                        <?php if($item->redactor_fullname): ?>
                                            <?php echo e($item->redactor_fullname); ?>

                                        <?php else: ?>
                                            <h6 style="color: #7f8c8d">Нет данных</h6>
                                        <?php endif; ?>
                                    </td>
                                    <td align="center">
                                        <br>
                                        <a href="<?php echo e(route('editAll', $item->id_product)); ?>" class="btn-sm  btn-danger">Редактировать</a>
                                    </td>
                                    <td>

                                        <?php if($item->verification_status == 1): ?>
                                            <span class="badge bg-warning">Отправлено.<br>На проверке</span>
                                        <?php elseif($item->verification_status == 2): ?>
                                            <span class="badge bg-success">Подтверждено</span>
                                        <?php elseif($item->verification_status == 3): ?>
                                            <span class="badge bg-danger">На доработке</span>
                                        <?php endif; ?>

                                    </td>
                                    <?php if(in_array(Auth::user()->TutorID, $adminTutorID)): ?>
                                        <td>
                                            <form action="<?php echo e(route('confirmStatus', ['id' => $item->id_product])); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button class="btn btn-block btn-info" type="submit">Подтвердить</button>
                                            </form>
                                            <br>
                                            <button class="btn btn-block btn-danger" data-toggle="modal" data-target="#modal-lg"
                                                    onclick="fillModal('<?php echo e($item->inv_number); ?>',
                                                                       '<?php echo e($item->redactor_id); ?>',
                                                                       '<?php echo e($item->id_name); ?>',
                                                                       '<?php echo e($item->id_product); ?>')">
                                                Отказать
                                            </button>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php if(!empty($item->note)): ?>
                                            <span class="badge bg-info"><?php echo e($item->note); ?></span>
                                        <?php else: ?>
                                            <p style="color: #7f8c8d">Нет данных</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Название инвентаря</th>
                            <th>Учебный корпус</th>
                            <th>Аудитория</th>
                            <th>Ответственное лицо</th>
                            <th>Инвертанный номер</th>
                            <th>Назначение</th>
                            <th>Характеристика</th>
                            <th>Дата редактирования</th>
                            <th>Последний редактор</th>
                            <th>Редактирование</th>
                            <th>Статус</th>
                            <?php if(in_array(Auth::user()->TutorID, $adminTutorID)): ?>
                                <th>Подтверждение</th>
                            <?php endif; ?>
                            <th>Примечание</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
    <!-- Модальное окно -->
    <div class="modal fade" id="modal-lg">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Причина</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php if(!empty($item)): ?>
                    <form action="<?php echo e(route('refuseStatus', ['id' => 0])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="modal-body">
                            <label for="message">Укажите причину отказа</label>
                            <textarea class="form-control" name="message" id="message" placeholder="Напишите причину..."></textarea>
                        </div>

                        <!-- Скрытые поля -->
                        <input class="form-control" readonly name="inv_number" id="inv_number">
                        <input class="form-control" readonly name="redactor_id" id="redactor_id">
                        <input class="form-control" readonly name="id_product" id="id_product">
                        <input class="form-control" readonly name="id_name" id="id_name">

                        <div class="card-body">
                            <button class="btn btn-primary" type="submit">Отправить</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <script>

        function fillModal(invNumber, redactorId, id_name, id_product) {
            // Находим скрытые поля в модальном окне и устанавливаем им значения
            document.getElementById('inv_number').value = invNumber;
            document.getElementById('redactor_id').value = redactorId;
            document.getElementById('id_name').value = id_name;
            document.getElementById('id_product').value = id_product;
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/invertory/create_invertory/all_db.blade.php ENDPATH**/ ?>