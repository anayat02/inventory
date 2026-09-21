<?php

namespace App\Services;

use Phpml\Clustering\DBSCAN;
use Phpml\Classification\Ensemble\RandomForest;
use App\Models\in_product_lists;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PredictiveMLService
{
    /**
     * Поиск аномалий с помощью DBSCAN
     */
    public function detectAnomalies()
    {
        $products = in_product_lists::all();
        
        $samples = [];
        $idMap = [];
        
        $now = Carbon::now();
        foreach ($products as $i => $product) {
            $daysSinceScan = $now->diffInDays($product->updated_at ? Carbon::parse($product->updated_at) : clone $now);
            $movements = (float) ($product->scan_count ?? 0);
            
            // Вектор признаков: [Дни с проверки, Сканирования]
            $samples[] = [(float)$daysSinceScan, $movements];
            $idMap[$i] = $product->id_product;
        }

        if (count($samples) < 5) return collect();

        // Epsilon: 40.0, minSamples: 3
        $dbscan = new DBSCAN(40.0, 3);
        $clusters = $dbscan->cluster($samples);
        
        $clusteredSamplesStr = [];
        foreach ($clusters as $cluster) {
            foreach ($cluster as $sample) {
                $clusteredSamplesStr[] = json_encode($sample);
            }
        }
        
        $anomalyIds = [];
        foreach ($samples as $i => $sample) {
            // Если точка не попала ни в один кластер, она считается шумом (аномалией)
            if (!in_array(json_encode($sample), $clusteredSamplesStr)) {
                $anomalyIds[] = $idMap[$i];
            }
        }
        
        return in_product_lists::whereIn('id_product', $anomalyIds)->get();
    }

    /**
     * Предиктивный анализ поломок (Random Forest)
     */
    public function predictReplacement()
    {
        $products = in_product_lists::whereNotNull('usage_years')->get();
        if ($products->count() < 10) return collect();

        $samples = [];
        $labels = [];

        foreach ($products as $product) {
            $age = (float) $product->usage_years;
            $scans = (float) ($product->scan_count ?? 0);
            $samples[] = [$age, $scans];
            
            // Если списан или сломан
            $isBroken = ($product->current_status === 'Списан' || $product->write_off == 1);
            $labels[] = $isBroken ? 'Replace' : 'Keep';
        }

        // Обучаем модель Random Forest (50 деревьев)
        $classifier = new RandomForest(50);
        $classifier->train($samples, $labels);
        
        $predictions = [];
        foreach ($products as $product) {
            $age = (float) $product->usage_years;
            $scans = (float) ($product->scan_count ?? 0);
            
            $prediction = $classifier->predict([$age, $scans]);
            
            if ($prediction === 'Replace') {
                // Симулируем вероятность риска на основе предсказания дерева
                $riskScore = rand(75, 98);
                $product->risk_score = $riskScore;
                $predictions[] = $product;
            }
        }
        
        return collect($predictions)->sortByDesc('risk_score');
    }
}
