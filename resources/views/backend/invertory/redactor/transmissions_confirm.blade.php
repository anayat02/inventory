@extends('backend.layouts.app')

@section('content')
    <div class="custom-transmission-container">
        <div class="custom-card">
            <div class="custom-card-header">
                <h4><i class="bi bi-box-seam me-2"></i> Заявки на приём основных средств</h4>
            </div>

            <div class="custom-card-body">
                @if($groupedTransmissions->isEmpty())
                    <div class="custom-empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Нет заявок на приём.</p>
                    </div>
                @else
                    <div class="custom-accordion" id="transmissionAccordion">
                        @foreach($groupedTransmissions as $senderName => $items)
                            <div class="custom-accordion-item">
                                <button class="custom-accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $loop->index }}"
                                        aria-expanded="false"
                                        aria-controls="collapse{{ $loop->index }}">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2"></i>
                                        <span class="fw-semibold">{{ $senderName }}</span>
                                    </div>
                                    <span class="custom-badge">{{ $items->count() }}</span>
                                </button>

                                <div id="collapse{{ $loop->index }}"
                                     class="collapse custom-accordion-collapse"
                                     data-bs-parent="#transmissionAccordion">
                                    <div class="custom-accordion-body">
                                        <form action="{{ route('transmissions.accept') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="senderTutorID" value="{{ $items->first()->TutorID }}">

                                            <div class="custom-table-wrapper">
                                                <table class="custom-table">
                                                    <thead>
                                                    <tr>
                                                        <th><input type="checkbox" class="checkAll"></th>
                                                        <th>Наименование</th>
                                                        <th>Инвентарный №</th>
                                                        <th>Аудитория</th>
                                                        <th>Дата передачи</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($items as $item)
                                                        <tr>
                                                            <td><input type="checkbox" name="id_products[]" value="{{ $item->id_product }}"></td>
                                                            <td>{{ $item->name_product }}</td>
                                                            <td>{{ $item->inv_number }}</td>
                                                            <td>{{ $item->auditoryName }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i') }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="custom-action">
                                                <button type="submit" class="custom-btn">
                                                    <i class="bi bi-check2-circle me-1"></i>
                                                    Принять ОС от {{ $senderName }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.checkAll').forEach(box => {
            box.addEventListener('change', function(){
                const table = this.closest('table');
                const checkboxes = table.querySelectorAll('input[name="id_products[]"]');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        });
    </script>

    <style>
        /* ========== СВОИ СТИЛИ — НЕ ЗАВИСЯТ ОТ APP.CSS ========== */

        .custom-transmission-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .custom-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .custom-card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 16px 24px;
        }

        .custom-card-header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #222;
        }

        .custom-card-body {
            padding: 20px 24px;
        }

        /* Аккордеон */
        .custom-accordion-item {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
            background: #fff;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .custom-accordion-item:hover {
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .custom-accordion-button {
            background: #f8f9fa;
            color: #212529;
            border: none;
            width: 100%;
            padding: 14px 20px;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: left;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .custom-accordion-button:hover {
            background: #e9ecef;
        }

        .custom-accordion-collapse {
            border-top: 1px solid #dee2e6;
            background: #fff;
        }

        .custom-accordion-body {
            padding: 20px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .custom-badge {
            background: #0d6efd;
            color: white;
            border-radius: 12px;
            padding: 3px 10px;
            font-size: 13px;
        }

        /* Таблица */
        .custom-table-wrapper {
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }

        .custom-table th {
            background: #f1f3f5;
            text-align: left;
            padding: 10px;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .custom-table td {
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .custom-table tr:hover {
            background: #f8f9fa;
        }

        .custom-table input[type="checkbox"] {
            transform: scale(1.2);
            cursor: pointer;
        }

        /* Кнопка */
        .custom-action {
            text-align: right;
            margin-top: 15px;
        }

        .custom-btn {
            background: #198754;
            border: none;
            color: #fff;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .custom-btn:hover {
            background: #157347;
            transform: translateY(-1px);
        }

        /* Пустое состояние */
        .custom-empty-state {
            text-align: center;
            padding: 50px 0;
            color: #6c757d;
        }

        .custom-empty-state i {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        /* Анимация */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

    </style>

    {{-- Подключаем Bootstrap JS и иконки, если не подключены --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
