@extends('layouts.admin')

@section('content')
    <style>
        .barcode-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 20px;
        }

        .barcode-item {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .barcode-item svg {
            max-width: 100%;
            height: 70px;
        }

        .barcode-item strong {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .barcode-item p {
            font-size: 12px;
            margin-top: 5px;
        }
    </style>

    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                    <h4 class="card-title card-title-dash">Barcodes</h4>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-secondary text-sm" data-bs-toggle="modal" data-bs-target="#newCategoryModal"
                        type="button"><i class="mdi mdi-printer-outline"></i> Print</button>
                    <button class="btn btn-primary text-sm" data-bs-toggle="modal" data-bs-target="#newCategoryModal"
                        type="button"><i class="mdi mdi-download"></i> Export</button>
                </div>
            </div>


            <div class="barcode-grid">
                @foreach ($barcodes as $b)
                    <div class="barcode-item">
                        <strong>{{ $b->product->name }} ({{ $b->product->qty }} {{ $b->product->unit }})</strong>
                        {!! file_get_contents(storage_path('app/public/' . $b->barcode_path)) !!}
                        <p>{{ $b->barcode }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
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
