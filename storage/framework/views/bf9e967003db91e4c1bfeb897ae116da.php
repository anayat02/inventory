<style>
    li{
        font-size: 14px !important;
    }
    i{
        margin-right: 5px;
    }
</style>

<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
         with font-awesome or any other icon font library -->
    <li class="nav-item">
        <a href="/" class="nav-link">
            <i class="bi bi-house-fill"></i>
            <p>
                <?php echo e(__('Главная страница')); ?>

            </p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="bi bi-file-spreadsheet-fill"></i>
            <p>
                Добавление  инвентаря
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="<?php echo e(route('createDit')); ?>" class="nav-link">
                    <i class="bi bi-pc-display"></i>
                    <p>ДИТ</p>
                </a>
            </li>
            
            
        </ul>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="bi bi-database-fill"></i>
            <p>
                Просмотр базы
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview" style="margin-left: 12px;">
            <li class="nav-item">
                <a href="<?php echo e(route('all')); ?>" class="nav-link">
                    <i class="bi bi-globe"></i>
                    <p>Общая база</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('filter')); ?>" class="nav-link">
                    <i class="bi bi-funnel-fill"></i>
                    <p>Распределенные</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('noSorted')); ?>" class="nav-link">
                    <i class="bi bi-x-circle"></i>
                    <p>Не распределенные</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('noNumber')); ?>" class="nav-link">
                    <i class="bi bi-database-fill-dash"></i>
                    <p>Без инвентарного номера</p>
                </a>
            </li>
            <?php if(Auth::user()->hasAnyRole(['admin', 'super-admin'])): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route('export')); ?>" class="nav-link">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <p>Экспорт данных</p>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </li>
    <?php if(Auth::user()->hasAnyRole(['admin', 'super-admin'])): ?>
        <li class="nav-item">
            <a href="<?php echo e(route('change_tutor')); ?>" class="nav-link">
                <i class="bi bi-arrows-fullscreen"></i>
                <p>Перемещение</p>
            </a>
        </li>
    <?php endif; ?>
    <li class="nav-item">
        <a href="<?php echo e(route('transmission_send')); ?>" class="nav-link">
            <i class="bi bi-send-arrow-down-fill"></i>
            <p>Прием и передача ОС</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?php echo e(route('open_pdf')); ?>" class="nav-link">
            <i class="bi bi-pencil-square"></i>
            <p>Формирование описи</p>
        </a>
    </li>
    <?php if(Auth::user()->hasAnyRole(['admin', 'super-admin'])): ?>
        <li class="nav-item">
            <a href="<?php echo e(route('index_responsible')); ?>" class="nav-link">
                <i class="bi bi-person-add"></i>
                <p>Назначение ответственных по аудиториям</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-arrow-clockwise"></i>
                <p>
                    Генератор QR-кода
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="margin-left: 12px;">
                <li class="nav-item">
                    <a href="<?php echo e(route('for_qr_list_inv')); ?>" class="nav-link">
                        <i class="bi bi-qr-code"></i>
                        <p>Генератор QR-кода по инвентарному номеру</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('for_qr_list_auditory')); ?>" class="nav-link">
                        <i class="bi bi-qr-code"></i>
                        <p>Генератор QR-кода по аудиториям</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-folder-minus"></i>
                <p>
                    Списание
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="margin-left: 12px;">
                <li class="nav-item">
                    <a href="<?php echo e(route('write_off')); ?>" class="nav-link">
                        <i class="bi bi-x-octagon-fill"></i>
                        <p>Списание инвентаря</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('write_off_list')); ?>" class="nav-link">
                        <i class="bi bi-database-fill-x"></i>
                        <p>База списанного инвентаря</p>
                    </a>
                </li>
            </ul>
        </li>
        <?php if(Auth::user()->hasAnyRole(['admin', 'super-admin'])): ?>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-robot"></i>
                    <p>
                        ИИ-Аналитика
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="margin-left: 12px;">
                    <li class="nav-item">
                        <a href="<?php echo e(route('analytics.anomalies')); ?>" class="nav-link">
                            <i class="bi bi-exclamation-diamond"></i>
                            <p>Аномалии (DBSCAN)</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('analytics.predictive')); ?>" class="nav-link">
                            <i class="bi bi-graph-up-arrow"></i>
                            <p>Прогноз (Random Forest)</p>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php if(Auth::user()->hasAnyRole(['admin', 'super-admin'])): ?>
            <li class="nav-item">
                <a href="<?php echo e(route('classroom_checks.admin_history')); ?>" class="nav-link">
                    <i class="bi bi-shield-check text-warning"></i>
                    <p>Журнал проверок аудиторий</p>
                </a>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-gear-fill"></i>
                <p>
                    Настройки
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="margin-left: 12px;">
                <li class="nav-item">
                    <a href="<?php echo e(route('synchronize')); ?>" class="nav-link">
                        <i class="bi bi-arrow-repeat"></i>
                        <p>Синхронизация</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('properties')); ?>" class="nav-link">
                        <i class="bi bi-list-task"></i>
                        <p>
                            Список свойств
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('propertiesadd')); ?>" class="nav-link">
                        <i class="bi bi-download"></i>
                        <p>
                            Добавить свойства
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('category')); ?>" class="nav-link">
                        <i class="bi bi-card-list"></i>
                        <p>
                            Список наименований
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('categoryadd')); ?>" class="nav-link">
                        <i class="bi bi-plus-circle"></i>
                        <p>
                            Добавить наименования
                            <!-- <span class="right badge badge-danger">New</span> -->
                        </p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-laptop"></i>
                <p>
                    Грантовые ноутбуки
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="margin-left: 12px;">
                <li class="nav-item">
                    <a href="<?php echo e(route('student')); ?>" class="nav-link">
                        <i class="bi bi-arrow-bar-up"></i>
                        <p>Выдача на текущий год</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('laptop_list')); ?>" class="nav-link">
                        <i class="bi bi-card-list"></i>
                        <p>Общая база</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a href="https://ais.kazetu.kz" class="nav-link">
                <i class="bi bi-box-arrow-left"></i>
                <p>
                    Вернуться в АИС
                    <!-- <span class="right badge badge-danger">New</span> -->
                </p>
            </a>
        </li>

    <?php endif; ?>
</ul>
<?php /**PATH F:\OSPanel\domains\inv.metu\resources\views/backend/layouts/menu.blade.php ENDPATH**/ ?>