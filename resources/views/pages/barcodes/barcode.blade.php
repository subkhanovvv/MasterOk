@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                    <h4 class="card-title card-title-dash">Barcodes</h4>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Название producta</th>
                            <th>Склад</th>
                            <th>Штрих-код</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barcodes as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                    <div>{{$b->product->name}} - {{ $b->product->qty }}</div>
                                    <svg style="height:70px" >{!! file_get_contents(storage_path('app/public/' . $b->barcode_path)) !!}</svg>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <div class="pagination mb-0">
                    {{ $barcodes->links('pagination::bootstrap-4') }}
                </div>
                <p class="text-muted mb-0">
                    Показаны с {{ $barcodes->firstItem() }} по {{ $barcodes->lastItem() }} из
                    {{ $barcodes->total() }} результатов
                </p>
            </div>
        </div>
    </div>
@endsection
