@if ($suppliers->count())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            {{ $suppliers->links('pagination::bootstrap-4') }}
        </div>
        <p class="text-muted">
            Показаны с {{ $suppliers->firstItem() }} по {{ $suppliers->lastItem() }} из
            {{ $suppliers->total() }} результатов
        </p>
    </div>
@endif

