<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MLDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = \App\Models\in_product_lists::all();
        $currentYear = (int) date('Y');

        if ($products->count() === 0) {
            $this->command->warn('Таблица in_product_lists пуста. Данные для ML не сгенерированы.');
            return;
        }

        foreach ($products as $product) {
            $year = rand(2010, $currentYear);
            $age = $currentYear - $year;
            
            $repairs = rand(0, max(1, floor($age / 2)));
            $movements = rand(0, 10);
            
            $needsReplacement = 0;
            if ($age > 7 || $repairs > 3 || ($age > 5 && $movements > 7)) {
                if (rand(1, 10) > 2) { 
                    $needsReplacement = 1;
                }
            } else {
                if (rand(1, 10) > 9) { 
                    $needsReplacement = 1;
                }
            }
            
            if (rand(1, 100) <= 5) {
                $updatedAt = \Carbon\Carbon::now()->subDays(rand(200, 400));
            } else {
                $updatedAt = \Carbon\Carbon::now()->subDays(rand(1, 100));
            }

            $product->update([
                'manufacture_year' => $year,
                'repair_count' => $repairs,
                'movement_count' => $movements,
                'needs_replacement' => $needsReplacement,
                'updated_at' => $updatedAt
            ]);
        }
        
        $this->command->info('Сгенерированы тестовые данные ML для ' . $products->count() . ' ОС.');
    }
}
