
<?php $__env->startSection('content'); ?>
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
                    <form id="myform"
                          role="form"
                          action="<?php echo e(route('characteristics.store', $id_product)); ?>"
                          method="POST"
                          enctype="multipart/form-data"
                    >
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
                                        <?php if($characteristics->isEmpty()): ?>
                                            <div class="alert alert-warning">
                                                У этого товара нет активных характеристик для редактирования.
                                            </div>
                                        <?php else: ?>
                                            <?php $__currentLoopData = $characteristics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $char): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <input type="text" name="id_product"  class="form-control"
                                                       id="id_product" value="<?php echo e($char->id_product); ?>" required hidden>
                                                <input type="text" name="id_characteristic[]"  class="form-control"
                                                       id="id_characteristic" value="<?php echo e($char->id_characteristic); ?>" required hidden>
                                                <?php
                                                    $html = $char->input_characteristic;
                                                    $val = $char->characteristic_value;
                                                    
                                                    if (strpos($html, '<select') !== false) {
                                                        $html = str_replace('value="' . $val . '"', 'value="' . $val . '" selected', $html);
                                                    } elseif (strpos($html, '<input') !== false) {
                                                        if (strpos($html, 'value=') === false) {
                                                            $html = str_replace('<input', '<input value="' . e($val) . '"', $html);
                                                        } else {
                                                            $html = preg_replace('/value="[^"]*"/', 'value="' . e($val) . '"', $html);
                                                        }
                                                    }
                                                ?>
                                                <?php echo $html; ?>

                                                <br>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <?php if(!$characteristics->isEmpty()): ?>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        <?php endif; ?>
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

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/invertory/redactor/charEdit.blade.php ENDPATH**/ ?>