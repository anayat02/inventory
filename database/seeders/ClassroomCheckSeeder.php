<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassroomCheck;
use App\Models\ClassroomCheckItem;
use Carbon\Carbon;

class ClassroomCheckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем демонстрационную запись успешной проверки
        $check1 = ClassroomCheck::create([
            'tutor_id'       => 521,
            'auditory_id'    => 101,
            'check_date'     => Carbon::today()->format('Y-m-d'),
            'lesson_start'   => '10:50:00',
            'lesson_finish'  => '13:35:00',
            'check_type'     => 'entrance',
            'status'         => 'ok',
            'comment'        => 'Вход в аудиторию 305(И), техника исправна.',
            'keyboard_count' => 15,
            'mouse_count'    => 15,
        ]);

        ClassroomCheckItem::create([
            'check_id'     => $check1->id,
            'id_product'   => 0,
            'product_name' => 'Системный блок',
            'db_count'     => 15,
            'fact_count'   => 15,
            'is_present'   => true,
            'condition'    => 'ok',
            'note'         => null,
        ]);

        ClassroomCheckItem::create([
            'check_id'     => $check1->id,
            'id_product'   => 0,
            'product_name' => 'Монитор',
            'db_count'     => 15,
            'fact_count'   => 15,
            'is_present'   => true,
            'condition'    => 'ok',
            'note'         => null,
        ]);

        // Создаем демонстрационную запись проверки с замечанием (аномалией)
        $check2 = ClassroomCheck::create([
            'tutor_id'       => 521,
            'auditory_id'    => 73,
            'check_date'     => Carbon::today()->subDay()->format('Y-m-d'),
            'lesson_start'   => '09:00:00',
            'lesson_finish'  => '10:35:00',
            'check_type'     => 'exit',
            'status'         => 'discrepancy',
            'comment'        => 'Обнаружено расхождение по количеству системных блоков.',
            'keyboard_count' => 14,
            'mouse_count'    => 15,
        ]);

        ClassroomCheckItem::create([
            'check_id'     => $check2->id,
            'id_product'   => 0,
            'product_name' => 'Системный блок',
            'db_count'     => 15,
            'fact_count'   => 14,
            'is_present'   => false,
            'condition'    => 'missing',
            'note'         => '1 шт. не работает / в сервисе',
        ]);

        ClassroomCheckItem::create([
            'check_id'     => $check2->id,
            'id_product'   => 0,
            'product_name' => 'Клавиатуры (Периферия)',
            'db_count'     => 15,
            'fact_count'   => 14,
            'is_present'   => false,
            'condition'    => 'damaged',
            'note'         => 'Количество клавиатур по факту (14 шт.) не совпадает с системными блоками по базе (15 шт.)',
        ]);
    }
}
