@if ($transactions->count())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            {{ $transactions->links('pagination::bootstrap-4') }}
        </div>
        <p class="text-muted">
            Показаны с {{ $transactions->firstItem() }} по {{ $transactions->lastItem() }} из
            {{ $transactions->total() }} результатов
        </p>
    </div>
@endif

