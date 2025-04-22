@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Товары</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('index') }}">
                            <div class="text-tiny">Панель</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Товары</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <form class="form-search">
                            <fieldset class="name">
                                <input type="text" placeholder="Поиск..." class="" name="name" tabindex="2"
                                    value="" aria-required="true" required="">
                            </fieldset>
                            <div class="button-submit">
                                <button class="" type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('new-product') }}"><i class="icon-plus"></i>Новый
                        товар</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
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
                                            $bg = $p->status === 'normal'
                                                ? '#d1fae5'   // green
                                                : ($p->status === 'low'
                                                    ? '#fef3c7' // yellow
                                                    : '#fee2e2' // red
                                                );
                                    
                                            $color = $p->status === 'normal'
                                                ? '#065f46'
                                                : ($p->status === 'low'
                                                    ? '#92400e'
                                                    : '#991b1b'
                                                );
                                    
                                            // Russian translation
                                            $statusRu = match($p->status) {
                                                'normal' => 'В наличии',
                                                'low' => 'Мало',
                                                'out_of_stock' => 'Нет в наличии',
                                                default => $p->status
                                            };
                                        @endphp
                                    
                                        <span style="
                                            display: inline-block;
                                            padding: 0.25rem 0.5rem;
                                            border-radius: 0.375rem;
                                            font-weight: 600;
                                            background-color: {{ $bg }};
                                            color: {{ $color }};
                                        ">
                                            {{ $statusRu }}
                                        </span>
                                    </td>
                                    <td>{{$p->sale_price}}</td>
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
                                                    <i class="icon-edit-3"></i>
                                                </div>
                                            </a>
                                            <form action="#" method="POST">
                                                <div class="item text-danger delete">
                                                    <i class="icon-trash-2"></i>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
