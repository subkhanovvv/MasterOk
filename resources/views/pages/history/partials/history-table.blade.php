<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Data transaction</th>
                <th>type</th>   
                <th>total amount</th>
                <th>products number</th>
                <th>payment method</th>
                <th>payment amount</th>
                <th>status</th>
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $t)
                <tr>
                    <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                    <td>{{ $t->created_at }}</td>
                    <td>{{ $t->items_count }} товаров</td>
                    <td>{{$t->type}}</td>
                    <td>{{$t->total_price}}</td>
                    <td>{{$t->payment_type}}</td>
                    <td>
                        <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                            data-bs-target="#editCategoryModal" data-id="{{ $t->id }}"
                            data-name="{{ $t->name }}"
                            data-photo="{{ $t->photo ? Storage::url($c->photo) : asset('admin/assets/images/default_product.png') }}"
                            onclick="openModal(this)" class="text-decoration-none">
                            <i class="mdi mdi-pencil icon-sm text-primary"></i>
                        </a>
                        <a href="javascript:void(0);" title="Удалить" data-bs-toggle="modal"
                            data-bs-target="#deleteCategoryModal" data-id="{{ $t->id }}"
                            onclick="openModal(this)" class="text-decoration-none">
                            <i class="mdi mdi-delete icon-sm text-danger"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Нет history</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
