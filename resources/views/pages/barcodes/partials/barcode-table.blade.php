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

    .barcode-actions {
        margin-top: 10px;
    }

    .barcode-actions a {
        display: inline-block;
        margin: 0 5px;
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 4px;
        text-decoration: none;
        color: white;
        background-color: #3490dc;
    }

    .barcode-actions a.download {
        background-color: #38c172;
    }
</style>

<div class="barcode-grid">
    @forelse ($barcodes as $barcode)
        <div class="barcode-item">

            <strong>{{ $barcode->name }} ({{ $barcode->qty }} {{ $barcode->unit }})</strong>

            <img src="{{ asset('storage/' . $barcode->barcode) }}" alt="Barcode Image">

            <div>
                <a href="javascript:void(0);" onclick="openBarcodeModal('print', '{{ $barcode->id }}')"
                    class="text-decoration-none">
                    <i class="mdi mdi-printer"></i>
                </a>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; display: flex; justify-content: center; align-items: center;">
            <p class="text-center m-0">Нет barcodes</p>
        </div>
    @endforelse
</div>
