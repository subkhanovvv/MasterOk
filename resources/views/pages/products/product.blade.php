@extends('layouts.admin')

@section('content')
    <div class="row">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Товары</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>Цена (UZS)</th>
                                <th>Цена (USD)</th>
                                <th>Бренд</th>
                                <th>Статус</th>
                                <th>Цена распродажи</th>
                                <th>Склад</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $p)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="pname">
                                        <div class="image">
                                            <img src="{{ Storage::url($p->photo) }}" alt="" class="image">
                                        </div>
                                        <div class="name">
                                            <a href="#" class="body-title-2">{{ $p->name }}</a>
                                            <div class="text-tiny mt-3">{{ $p->get_category->name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($p->price_uzs) }} UZS</td>
                                    <td>${{ number_format($p->price_usd, 2) }}</td>
                                    <td>{{ $p->get_brand->name }}</td>
                                    <td>
                                        @php
                                            $color =
                                                $p->status === 'normal'
                                                    ? 'success'
                                                    : ($p->status === 'low'
                                                        ? 'danger'
                                                        : 'warning');

                                            // Russian translation
                                            $statusRu = match ($p->status) {
                                                'normal' => 'В наличии',
                                                'low' => 'Мало',
                                                'out_of_stock' => 'Нет в наличии',
                                                default => $p->status,
                                            };
                                        @endphp

                                        <span class="badge badge-{{ $color }}">
                                            {{ $statusRu }}
                                        </span>
                                    </td>
                                    <td>{{ $p->sale_price }}</td>
                                    <td>{{ $p->qty }} {{ $p->unit }}</td>

                                    <td>
                                        <div class="list-icon-function">
                                            <a href="#" target="_blank">
                                                <div class="item eye">
                                                    <i class="icon-eye"></i>
                                                </div>
                                            </a>
                                            <a href="#">
                                                <div class="item edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </div>
                                            </a>
                                            <form action="#" method="POST">
                                                <div class="item text-danger delete">
                                                    <i class="mdi mdi-delete"></i>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="divider"></div>
        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
            {{ $products->links() }}
        </div>
    </div>
    </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".delete").forEach(function(button) {
                button.addEventListener("click", function(e) {
                    e.preventDefault();
                    let form = this.closest("form");

                    Swal.fire({
                        title: "Вы уверены?",
                        text: "Это действие невозможно отменить!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#dc3545",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "Да, удалить!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
