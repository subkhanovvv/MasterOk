@extends('layouts.admin')

@section('title', 'Отчет')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">📊 Отчет по кассе</h2>

    <!-- Filter Form -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Дата начала</label>
            <input type="date" id="start_date" name="start_date" class="form-control"
                value="{{ request('start_date', $startDate->toDateString()) }}">
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">Дата окончания</label>
            <input type="date" id="end_date" name="end_date" class="form-control"
                value="{{ request('end_date', $endDate->toDateString()) }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Фильтровать</button>
        </div>
    </form>

    <!-- Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <h5>💰 Касса за период: <strong>{{ $startDate->format('d.m.Y') }} — {{ $endDate->format('d.m.Y') }}</strong></h5>
            <p class="fs-4 mt-2">Итоговая сумма: 
                <strong class="{{ $netCash >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($netCash, 0, ',', ' ') }} сум
                </strong>
            </p>
        </div>
    </div>
<div class="card mt-4">
    <div class="card-body">
        <h5 class="mb-3">💳 Непогашенные займы ({{ $startDate->format('d.m.Y') }} — {{ $endDate->format('d.m.Y') }})</h5>

        <div class="d-flex justify-content-between">
            <div class="text-start">
                <h6>⬅️ Взятые займы</h6>
                <p class="text-danger fs-5">
                    {{ number_format($takenLoanTotal, 0, ',', ' ') }} сум
                </p>
            </div>

            <div class="text-end">
                <h6>➡️ Выданные займы</h6>
                <p class="text-success fs-5">
                    {{ number_format($givenLoanTotal, 0, ',', ' ') }} сум
                </p>
            </div>
        </div>
    </div>
</div>

    <!-- Activity Table -->
    <div class="card">
        <div class="card-header">
            Типы активностей
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped m-0">
                <thead class="table-light">
                    <tr>
                        <th>Тип</th>
                        <th>Количество</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activityTypeCounts as $type => $count)
                        <tr>
                            <td>{{ __("Тип: $type") }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
