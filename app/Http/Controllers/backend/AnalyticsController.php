<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PredictiveMLService;
use App\Models\in_product_lists;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    protected $mlService;

    public function __construct(PredictiveMLService $mlService)
    {
        $this->mlService = $mlService;
    }

    public function anomalies()
    {
        // 1. Одинаковые инвентарные номера (базовые правила)
        $duplicates = in_product_lists::select('inv_number', DB::raw('count(*) as total'))
            ->groupBy('inv_number')
            ->having('total', '>', 1)
            ->get();
            
        $duplicateProducts = in_product_lists::whereIn('inv_number', $duplicates->pluck('inv_number'))->get();

        // 2. Оборудование без ответственного
        $noTutorProducts = in_product_lists::whereNull('TutorID')->orWhere('TutorID', 0)->get();

        // 3. Аномалии по версии DBSCAN (нестандартное поведение: давно не сканировали + часто перемещали и т.д.)
        $dbscanAnomalies = $this->mlService->detectAnomalies();

        return view('backend.analytics.anomalies', compact('duplicateProducts', 'noTutorProducts', 'dbscanAnomalies'));
    }

    public function predictive()
    {
        // Предиктивный анализ (Random Forest)
        $predictions = $this->mlService->predictReplacement();

        return view('backend.analytics.predictive', compact('predictions'));
    }
}
