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
                    <a class="tf-button style-1 w208" href="{{route('new-product')}}"><i class="icon-plus"></i>Добавить товар</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>Цена (UZS)</th>
                                <th>Цена (USD)</th>
                                <th>Штрихкод</th>
                                <th>Категория</th>
                                <th>Бренд</th>
                                <th>Статус</th>
                                <th>Склад</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $p)
                                <tr>
                                    <td>{{ $p->id }}</td>
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
                                    <td>{{ $p->barcode }}</td>
                                    <td>{{ $p->get_category->name }}</td>
                                    <td>{{ $p->get_brand->name }}</td>
                                    <td>
                                        <span
                                            class="
                                            px-2 py-1 rounded font-semibold
                                            @if ($p->qty <= 0) bg-red-100 text-red-700
                                            @elseif($p->qty < 5)
                                                bg-red-200 text-red-800
                                            @elseif($p->qty < 20)
                                                bg-yellow-100 text-yellow-800
                                            @else
                                                bg-green-100 text-green-700 @endif
                                        ">
                                            {{ $p->qty }} {{ $p->unit }}
                                        </span>
                                    </td>

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
                    {{-- Здесь может быть пагинация --}}
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
