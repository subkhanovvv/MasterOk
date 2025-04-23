@extends('layouts.admin')

@section('content')
    <div class="row">

        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title card-title-dash">Products</h4>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-lg text-white mb-0 me-0" data-bs-toggle="modal"
                            data-bs-target="#newProductModal" type="button"><i class="mdi mdi-plus"></i>Add new</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>photo</th>
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
                                    <td>
                                        {{ $p->name }}
                                        {{-- {{ $p->get_category->name }} --}}
                                    </td>
                                    <td><img src="{{ Storage::url($p->photo) }}" alt="" class="image"></td>
                                    <td>{{ number_format($p->price_uzs) }} uzs</td>
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
                                        <div class="list-icon-function d-flex justify-content-center gap-2">
                                            <a href="">
                                                <i class="mdi mdi-eye icon-sm text-warning"></i>
                                            </a>
                                            <a href="">
                                                <i class="mdi mdi-pencil icon-sm"></i>
                                            </a>
                                            <a href="">
                                                <i class="mdi mdi-delete icon-sm text-danger"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="divider"></div>
    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
        {{ $products->links() }}
    </div>

    @include('pages.products.modals.new-product')
    @include('pages.products.modals.edit-product')
    @include('pages.products.modals.view-product')
    @include('pages.products.modals.delete-product')

    <div class="modal fade" id="viewProductModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Новый товар</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div>{{$products}}</div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-lg text-white">Сохранить</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(function() {
            $("#myFile").on("change", function(e) {
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr("src", URL.createObjectURL(file));
                    $("#imgpreview").show();
                }
            });
        });

        let usdToUzsRate = null;

        async function fetchExchangeRates() {
            try {
                const response = await fetch('https://open.er-api.com/v6/latest/USD');
                const data = await response.json();

                usdToUzsRate = data.rates.UZS;

                document.getElementById('usd-uzs-rate').textContent = usdToUzsRate.toFixed(2);
            } catch (error) {
                console.error('Ошибка при получении курса обмена:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const usdInput = document.querySelector('input[name="price_usd"]');
            const uzsInput = document.querySelector('input[name="price_uzs"]');

            usdInput.addEventListener('input', () => {
                if (usdToUzsRate) {
                    const usdValue = parseFloat(usdInput.value);
                    if (!isNaN(usdValue)) {
                        const uzsValue = usdValue * usdToUzsRate;
                        uzsInput.value = uzsValue.toFixed(2);
                    } else {
                        uzsInput.value = '';
                    }
                }
            });

            fetchExchangeRates();
            setInterval(fetchExchangeRates, 10000); // Опционально: обновление курса
        });
    </script>
@endsection
