
<?php $__env->startSection('content'); ?>

    <?php

        use Illuminate\Support\Facades\DB;

        $product_name= DB::table('in_product_name')->get();
        $tutor = DB::connection('mysql_platonus')->table('tutors')->get();

    ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Редактирование</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form id="myform" role="form" action="<?php echo e(route('updateAll', $edit->id_product)); ?>" method="post" enctype="multipart/form-data">
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php echo csrf_field(); ?>
                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="card-body">
                                        <?php
                                            // проверим в БД, есть ли у этого товара непомеченные характеристики
                                            $hasActiveChars = \App\Models\in_characteristics_for_product::where('id_product', $edit->id_product)
                                                               ->where('current_status', 0)
                                                               ->exists();
                                        ?>

                                        <?php if(!$edit->id_name || !$hasActiveChars): ?>
                                            <div class="form-group">
                                                <label for="category">Выберите инвентарь</label>
                                                <select id="id_name" name="id_name" class="form-control" required>
                                                    <option value="">Выбрать</option>
                                                    <?php $__currentLoopData = $product_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($prod->id_name); ?>"
                                                                <?php if(old('id_name',$edit->id_name)==$prod->id_name): ?> selected <?php endif; ?>>
                                                            <?php echo e($prod->name_product); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        <?php endif; ?>

                                    <?php if(empty($edit->auditoryID)): ?>
                                            <div class="form-group">
                                                <h5><strong>Местоположение</strong></h5>
                                                <label for="building">Корпус</label>
                                                <select id="building" name="buildingID" class="form-control">
                                                    <option value="">Выберите корпус</option>
                                                    <?php $__currentLoopData = $building; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $buildingID): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($buildingID->buildingID); ?>"><?php echo e($buildingID->buildingName); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <label for="auditory">Аудитория</label>
                                                <select id="auditory" name="auditoryID" class="form-control">
                                                    <option value="">Выберите аудиторию</option>
                                                    <?php $__currentLoopData = $sortedAuditories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auditory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($auditory->auditoryID); ?>"><?php echo e($auditory->auditoryName); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        <?php else: ?>
                                            <input type="text" name="buildingID"  class="form-control"
                                                   id="inv_number" required  hidden value="<?php echo e($edit->buildingID); ?>">
                                            <input type="text" name="auditoryID"  class="form-control"
                                                   id="inv_number" required readonly hidden value="<?php echo e($edit->auditoryID); ?>">
                                        <?php endif; ?>
                                        <?php if(empty($edit->TutorID)): ?>
                                            <div class="form-group">
                                                <label for="tutor">Выберите ответственное лицо</label>
                                                <select id="tutor" name="TutorID" class="form-control">
                                                    <option value="">Выберите ответственное лицо</option>
                                                    <?php $__currentLoopData = $tutor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tutors): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($tutors->TutorID); ?>"><?php echo e($tutors->lastname); ?> <?php echo e($tutors->firstname); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        <?php else: ?>
                                            <div class="form-group">
                                                <input type="text" name="TutorID"  class="form-control"
                                                       id="inv_number" required readonly hidden value="<?php echo e($edit->TutorID); ?>">
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-group">
                                            <label for="type">Назначение</label>
                                            <select id="type" name="type" class="form-control">
                                                <option value="">Выберите назначение</option>
                                                <option value="1">Личный</option>
                                                <option value="2">Аудиторный</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="inv_number">Инвертарный номер</label>
                                            <?php if(empty($edit->inv_number)): ?>
                                                <input type="text" name="inv_number"  class="form-control"
                                                       id="inv_number" placeholder="Введите номер" required>
                                            <?php else: ?>
                                                <input type="text" name="inv_number"  class="form-control"
                                                       id="inv_number" placeholder="Введите номер" required readonly value="<?php echo e($edit->inv_number); ?>">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="card-body">
                                        <?php if(!$edit->id_name || !$hasActiveChars): ?>
                                            <div id="product-form-container">
                                                
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="card-body">
                                        <ul>
                                            <?php
                                                // Сортируем характеристики по дате добавления и группируем по этой дате
                                                $groupedCharacteristics = $edit->characteristics
                                                    ->sortBy('created_at') // Сортируем по дате добавления
                                                    ->groupBy(function($characteristic) {
                                                        return \Carbon\Carbon::parse($characteristic->created_at)->format('d.m.y');
                                                    });
                                            ?>

                                            <?php $__currentLoopData = $groupedCharacteristics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $characteristics): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <!-- Выводим дату -->
                                                <h5><?php echo e($date); ?></h5>

                                                <?php $__currentLoopData = $characteristics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $characteristic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <ol>
                                                        <strong><?php echo e($characteristic->characteristic->name_characteristic); ?>:</strong> <?php echo e($characteristic->characteristic_value); ?>;
                                                        <br>
                                                    </ol>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <br>
                                            <a href="<?php echo e(route('editCharacteristic', $edit->id_product)); ?>" class="btn btn-block btn-info">Редактировать</a>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-2">
            </div>
        </div>
        <!-- /.row -->
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/invertory/redactor/move.blade.php ENDPATH**/ ?>