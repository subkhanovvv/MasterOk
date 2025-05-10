@if ($barcodes->count())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            {{ $barcodes->links('pagination::bootstrap-4') }}
        </div>
        <p class="text-muted">
            Показаны с {{ $barcodes->firstItem() }} по {{ $barcodes->lastItem() }} из
            {{ $barcodes->total() }} результатов
        </p>
    </div>
@endif
