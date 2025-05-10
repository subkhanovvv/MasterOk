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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
</style>
<div class="barcode-grid">
    @forelse ($barcodes as $barcode)
        <div class="barcode-item">
            <strong>{{ $barcode->name }} ({{ $barcode->qty }} {{ $barcode->unit }})</strong>
            {!! file_get_contents(storage_path('app/public/' . $barcode->barcode)) !!}
            <p>{{ $barcode->barcode_value }}</p>
        </div>
    @empty
            <div>No barcodes found matching your criteria.</div>
   @endforelse
</div>
