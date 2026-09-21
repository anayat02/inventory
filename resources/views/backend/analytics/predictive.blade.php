@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4" style="color: #6f42c1;"><i class="fas fa-brain"></i> Предиктивный анализ поломок (Random Forest)</h2>
        
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h3 class="card-title m-0">Прогноз износа оборудования</h3>
                <span class="badge bg-danger ml-auto">Алгоритм: Случайный лес</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover m-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID ОС</th>
                            <th>Инв. номер</th>
                            <th>Год выпуска</th>
                            <th>Ремонты</th>
                            <th>Перемещения</th>
                            <th width="20%">Вероятность поломки</th>
                            <th>Статус ИИ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($predictions as $prediction)
                            <tr>
                                <td>{{ $prediction->id_product }}</td>
                                <td><strong>{{ $prediction->inv_number ?? 'Нет номера' }}</strong></td>
                                <td>{{ $prediction->manufacture_year }}</td>
                                <td>{{ $prediction->repair_count }}</td>
                                <td>{{ $prediction->movement_count }}</td>
                                <td>
                                    <div class="progress" style="height: 10px; margin-bottom: 5px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $prediction->risk_score }}%;" aria-valuenow="{{ $prediction->risk_score }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">{{ $prediction->risk_score }}% вероятность выхода из строя</small>
                                </td>
                                <td>
                                    @if($prediction->risk_score > 90)
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Критично (Замена)</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> В зоне риска</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                                    <p class="mt-2 text-muted">Модель Random Forest не предсказала скорых поломок в текущем пуле оборудования.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
