@if ($products->count())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
        <p class="text-muted">
            Показаны с {{ $products->firstItem() }} по {{ $products->lastItem() }} из
            {{ $products->total() }} результатов
        </p>
    </div>
@endif

