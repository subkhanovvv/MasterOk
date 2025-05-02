@extends('layouts.admin')

@section('content')
    <style>
        .barcode-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .barcode-item {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .barcode-item svg {
            max-width: 100%;
            height: 80px;
            margin: 0 auto;
        }
        .barcode-item strong {
            display: block;
            margin-bottom: 10px;
            font-size: 15px;
            color: #333;
        }
        .barcode-item p {
            font-size: 13px;
            margin-top: 8px;
            color: #666;
        }
        .filter-card {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>

    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4 class="card-title card-title-dash">Barcode Management</h4>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-secondary text-sm" onclick="window.print()">
                        <i class="mdi mdi-printer-outline"></i> Print
                    </button>
                    <a href="#" class="btn btn-primary text-sm">
                        <i class="mdi mdi-download"></i> Export
                    </a>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="filter-card">
                <form method="GET" action="#">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="text" name="search" class="form-control" placeholder="Search by name or barcode" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>In Stock</option>
                                <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Barcode Grid -->
            <div class="barcode-grid">
                @forelse ($barcodes as $barcode)
                    <div class="barcode-item">
                        <strong>{{ $barcode->name }} ({{ $barcode->qty }} {{ $barcode->unit }})</strong>
                        {!! file_get_contents(storage_path('app/public/' . $barcode->barcode)) !!}
                        <p>{{ $barcode->barcode_value }}</p>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">No barcodes found matching your criteria.</div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <div class="pagination mb-0">
                    {{ $barcodes->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
                <p class="text-muted mb-0">
                    Showing {{ $barcodes->firstItem() }} to {{ $barcodes->lastItem() }} of
                    {{ $barcodes->total() }} results
                </p>
            </div>
        </div>
    </div>
@endsection