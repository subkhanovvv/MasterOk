@if ($brands->count())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            {{ $brands->links('pagination::bootstrap-4') }}
        </div>
        <p class="text-muted">
            Показаны с {{ $brands->firstItem() }} по {{ $brands->lastItem() }} из
            {{ $brands->total() }} результатов
        </p>
    </div>
@endif
