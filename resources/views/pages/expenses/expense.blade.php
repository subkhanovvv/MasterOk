@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title card-title-dash">Склад</h4>
                    </div>
                    <div>
                        <a href="#" class="btn btn-success btn-lg text-white mb-0 me-0">
                            <i class="mdi mdi-plus"></i> Добавить расход
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>Фото</th>
                                <th>Цена (UZS)</th>
                                <th>Цена (USD)</th>
                                <th>Бренд</th>
                                <th>Статус</th>
                                <th>Скидочная цена</th>
                                <th>Остаток</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $p)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $p->name }}</td>
                                    <td>
                                        <img src="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                                            alt="{{ $p->name }}" class="image"
                                            style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>{{ number_format($p->price_uzs) }} UZS</td>
                                    <td>${{ number_format($p->price_usd, 2) }}</td>
                                    <td>{{ $p->get_brand->name }}</td>
                                    <td>
                                        @php
                                            $color = match ($p->status) {
                                                'normal' => 'success',
                                                'low' => 'warning',
                                                'out_of_stock' => 'danger',
                                                default => 'secondary',
                                            };

                                            $statusRu = match ($p->status) {
                                                'normal' => 'В наличии',
                                                'low' => 'Мало',
                                                'out_of_stock' => 'Нет в наличии',
                                                default => $p->status,
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ $statusRu }}</span>
                                    </td>
                                    <td>{{ $p->sale_price }}</td>
                                    <td>{{ $p->qty }} {{ $p->unit }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a data-bs-toggle="modal" data-bs-target="#viewProductModal"
                                                data-product="{{ $p->toJson() }}" onclick="viewProductModal(this)"
                                                href="#">
                                                <i class="mdi mdi-eye text-warning"></i>
                                            </a>
                                            <a href="#" onclick="set_id({{ $p->id }})">
                                                <i class="mdi mdi-coin"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="deleteProduct" data-bs-toggle="modal"
                                                data-bs-target="#deleteProductModal" data-id="{{ $p->id }}"
                                                data-token="{{ csrf_token() }}">
                                                <i class="mdi mdi-delete text-danger"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($products->count())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="text-muted mb-0">
                            Показаны с {{ $products->firstItem() }} по {{ $products->lastItem() }} из
                            {{ $products->total() }} результатов
                        </p>
                        <div class="pagination mb-0">
                            {{ $products->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите удалить этот товар?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-danger text-white" id="confirmDeleteBtn">Удалить</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var product_id;

        function set_id(id) {
            product_id = id;
        }

        function consume() {

            var form = new FormData();
            form.append("product_id", product_id);
            form.append("_token", '{{ csrf_token() }}');
            form.append("qty", $('#consuption').val());
            form.append("date", $('#consuption_date').val());


            var settings = {
                "url": "{{ env('APP_URL') }}/consume_product",
                "method": "POST",
                "timeout": 0,
                "processData": false,
                "mimeType": "multipart/form-data",
                "contentType": false,
                "data": form
            };

            $.ajax(settings).done(function(response) {

                let parsed = JSON.parse(response);

                console.log(response);

                set_data(parsed);

            });
        }
        $(document).on("click", ".deleteProduct", function() {
            var productId = $(this).data("id");
            var token = $(this).data("token");

            $("#confirmDeleteBtn").off("click").on("click", function() {
                $.ajax({
                    url: "/destroy-product/" + productId,
                    type: 'DELETE',
                    dataType: "JSON",
                    data: {
                        "_token": token,
                    },
                    success: function(response) {
                        $("#deleteProductModal").modal('hide');
                        alert(response.message || "Товар успешно удалён!");
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert("Ошибка при удалении товара");
                    }
                });
            });
        });
    </script>
@endsection
