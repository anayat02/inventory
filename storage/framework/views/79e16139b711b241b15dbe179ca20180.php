
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
                        <h3 class="card-title">Добавить инвентарь</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form id="myform" role="form" action="<?php echo e(route('addAll')); ?>" method="post" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="category">Выберите товар</label>
                                            <select id="id_name" name="id_name" class="form-control">
                                                <option value="">Выберите товар</option>
                                                <?php $__currentLoopData = $product_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($name->id_name); ?>"><?php echo e($name->name_product); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <br>
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
                                                <?php $__currentLoopData = $auditories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auditory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($auditory->auditoryID); ?>"><?php echo e($auditory->auditoryName); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="tutor">Выберите ответственное лицо</label>
                                            <select id="tutor" name="TutorID" class="form-control">
                                                <option value="">Выберите ответственное лицо</option>
                                                <?php $__currentLoopData = $tutor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tutors): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($tutors->TutorID); ?>"><?php echo e($tutors->lastname); ?> <?php echo e($tutors->firstname); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="type">Назначение</label>
                                            <select id="type" name="type" class="form-control">
                                                <option value="">Выберите назначение</option>
                                                <option value="1">Личный</option>
                                                <option value="2">Аудиторный</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="inv_number">Инвентарный номер</label>
                                            <input type="text" name="inv_number"  class="form-control"
                                                   id="inv_number" placeholder="Введите номер">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="quantity">Количество</label>
                                            <br>
                                            <input id="quantity" class="form-control" type="number" name="quantity" placeholder="Введите количество">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="card-body">
                                        <div id="product-form-container">
                                            <!-- Сюда будет загружена форма соответствующего продукта -->
                                        </div>
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

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/invertory/create_invertory/dit_create.blade.php ENDPATH**/ ?>