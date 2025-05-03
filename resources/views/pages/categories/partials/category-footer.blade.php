@if ($categories->count())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            {{ $categories->links('pagination::bootstrap-4') }}
        </div>
        <p class="text-muted">
            Показаны с {{ $categories->firstItem() }} по {{ $categories->lastItem() }} из
            {{ $categories->total() }} результатов
        </p>
    </div>
@endif

