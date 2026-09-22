<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <!-- 1. Секция Импорта -->
            <div class="card card-cyan collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Импорт инвентаря</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
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
            </div>

            <!-- 2. Секция Фильтрации (между импортом и списком наименований) -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-2"></i> Фильтр данных</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(url()->current()); ?>" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="buildingID">Учебный корпус:</label>
                                    <select name="buildingID" id="buildingID" class="form-control select2">
                                        <option value="">Все корпусы</option>
                                        <?php $__currentLoopData = $buildings ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($b->buildingID); ?>" <?php echo e(request('buildingID') == $b->buildingID ? 'selected' : ''); ?>>
                                                <?php echo e($b->buildingName); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="auditoryID">Аудитория:</label>
                                    <select name="auditoryID" id="auditoryID" class="form-control select2">
                                        <option value="">Все аудитории</option>
                                        <?php $__currentLoopData = $auditories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($a->auditoryID); ?>" <?php echo e(request('auditoryID') == $a->auditoryID ? 'selected' : ''); ?>>
                                                <?php echo e($a->auditoryName); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="id_name">Наименование ОС:</label>
                                    <select name="id_name" id="id_name" class="form-control select2">
                                        <option value="">Все наименования</option>
                                        <?php $__currentLoopData = $productNames ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($pn->id_name); ?>" <?php echo e(request('id_name') == $pn->id_name ? 'selected' : ''); ?>>
                                                <?php echo e($pn->name_product); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="type">Назначение:</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="">Все назначения</option>
                                        <option value="1" <?php echo e(request('type') == '1' ? 'selected' : ''); ?>>Личный</option>
                                        <option value="2" <?php echo e(request('type') == '2' ? 'selected' : ''); ?>>Аудиторный</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="verification_status">Статус проверки:</label>
                                    <select name="verification_status" id="verification_status" class="form-control">
                                        <option value="">Все статусы</option>
                                        <option value="1" <?php echo e(request('verification_status') == '1' ? 'selected' : ''); ?>>Отправлено. На проверке</option>
                                        <option value="2" <?php echo e(request('verification_status') == '2' ? 'selected' : ''); ?>>Подтверждено</option>
                                        <option value="3" <?php echo e(request('verification_status') == '3' ? 'selected' : ''); ?>>На доработке</option>
                                    </select>
                                </div>
                            </div>
                            <?php if(Auth::user()->hasAnyRole(['admin', 'super-admin'])): ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tutorID">Ответственное лицо:</label>
                                        <select name="tutorID" id="tutorID" class="form-control select2">
                                            <option value="">Все сотрудники</option>
                                            <?php $__currentLoopData = $tutors ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($t->TutorID); ?>" <?php echo e(request('tutorID') == $t->TutorID ? 'selected' : ''); ?>>
                                                    <?php echo e($t->lastname); ?> <?php echo e($t->firstname); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-3 d-flex align-items-end mb-3">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search mr-1"></i> Применить
                                </button>
                                <a href="<?php echo e(url()->current()); ?>" class="btn btn-default">
                                    <i class="fas fa-undo mr-1"></i> Сбросить
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 3. Секция Таблицы с инвентарем -->
            <div class="card card-primary">
                <div class="card-header info">
                    <h3 class="card-title">Список наименований</h3>
                </div>
                <div class="card-body">
                    <table id="example2" class="table table-bordered table-striped" style="font-size: 13px !important">
                        <?php
                            $adminTutorID = [646, 359];
                            $isAdminUser = Auth::user()->hasAnyRole(['admin', 'super-admin']) || in_array(Auth::user()->TutorID, $adminTutorID);
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
                            <?php if($isAdminUser): ?>
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
                                            <strong><?php echo e($characteristic->characteristic->name_characteristic ?? ''); ?>:</strong> <?php echo e($characteristic->characteristic_value); ?>;
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
                                        <a href="<?php echo e(route('editAll', $item->id_product)); ?>" class="btn-sm btn-danger">Редактировать</a>
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
                                    <?php if($isAdminUser): ?>
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
                            <th>Инвентарный номер</th>
                            <th>Назначение</th>
                            <th>Характеристика</th>
                            <th>Дата редактирования</th>
                            <th>Последний редактор</th>
                            <th>Редактирование</th>
                            <th>Статус</th>
                            <?php if($isAdminUser): ?>
                                <th>Подтверждение</th>
                            <?php endif; ?>
                            <th>Примечание</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
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

                    <input class="form-control" type="hidden" name="inv_number" id="inv_number">
                    <input class="form-control" type="hidden" name="redactor_id" id="redactor_id">
                    <input class="form-control" type="hidden" name="id_product" id="id_product">
                    <input class="form-control" type="hidden" name="id_name" id="id_name">

                    <div class="card-body">
                        <button class="btn btn-primary" type="submit">Отправить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function fillModal(invNumber, redactorId, id_name, id_product) {
            document.getElementById('inv_number').value = invNumber;
            document.getElementById('redactor_id').value = redactorId;
            document.getElementById('id_name').value = id_name;
            document.getElementById('id_product').value = id_product;
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/invertory/create_invertory/all_db.blade.php ENDPATH**/ ?>