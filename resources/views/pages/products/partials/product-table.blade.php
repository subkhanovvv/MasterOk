<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Фото</th>
                <th>Цена</th>
                <th>Статус</th>
                <th>Цена продажи</th>
                <th>Склад</th>
                <th>Штрих-код</th>
                <th>Действие</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->name }}</td>
                    <td>
                        <img src="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                            alt="{{ $p->name }}">
                    </td>
                    <td>
                        {{ number_format($p->price_uzs, 0, ',', ' ') }} сум
                    </td>

                    <td>
                        @php
                            $color =
                                $p->status === 'normal' ? 'success' : ($p->status === 'low' ? 'warning' : 'danger');

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
                    <td>{{ number_format($p->sale_price, 0, ',', ' ') }} сум
                    </td>
                    <td>{{ $p->qty }} {{ $p->unit }}</td>
                    <td>
                        @if ($p->barcode)
                            <p>{{ $p->barcode_value }}</p>
                        @else
                            <p>No barcode</p>
                        @endif
                    </td>
                    <td>
                        <div>
                            <a href="javascript:void(0);" title="Просмотр товара" data-bs-toggle="modal"
                                data-bs-target="#viewProductModal" data-id="{{ $p->id }}"
                                data-photo="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                                data-name="{{ $p->name }}" data-sale-price="{{ $p->sale_price }}"
                                data-uzs-price="{{ $p->price_uzs }}" data-usd-price="{{ $p->price_usd }}"
                                data-brand="{{ $p->get_brand->name ?? 'Нет бренда' }}"
                                data-category="{{ $p->get_category->name ?? 'Нет категории' }}"
                                data-barcode="{{ Storage::url($p->barcode) }}" data-qty="{{ $p->qty }}"
                                data-updated-at="{{ $p->updated_at->format('d-m-y') }}"
                                data-unit="{{ $p->unit }}"
                                data-description="{{ $p->short_description ?? 'Нет описания' }}"
                                data-created-at="{{ $p->created_at->format('d-m-y') }}"
                                data-status="{{ $statusRu }}" onclick="openModal(this)"
                                class="text-decoration-none">
                                <i class="mdi mdi-eye icon-sm text-success"></i>
                            </a>

                            <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                                data-bs-target="#editProductModal" data-id="{{ $p->id }}"
                                data-name="{{ $p->name }}" data-description="{{ $p->short_description }}"
                                data-uzs-price="{{ $p->price_uzs }}" data-usd-price="{{ $p->price_usd }}"
                                data-photo="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                                data-sale-price="{{ $p->sale_price }}" data-barcode="{{ Storage::url($p->barcode) }}"
                                data-brand="{{ $p->get_brand->name ?? 'Нет бренда' }}"
                                data-category="{{ $p->get_category->name ?? 'Нет категории' }}"
                                data-unit="{{ $p->unit }}" onclick="openModal(this)" class="text-decoration-none">
                                <i class="mdi mdi-pencil icon-sm text-primary"></i>
                            </a>

                            <a href="javascript:void(0);" title="Удалить" data-bs-toggle="modal"
                                data-bs-target="#deleteProductModal" data-id="{{ $p->id }}"
                                onclick="openModal(this)" class="text-decoration-none">
                                <i class="mdi mdi-delete icon-sm text-danger"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">Нет товар </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{-- <script>
    let usdToUzsRate = null;

    async function fetchExchangeRates() {
        try {
            const response = await fetch('https://open.er-api.com/v6/latest/USD');
            const data = await response.json();
            usdToUzsRate = data.rates.UZS;

            document.getElementById('usd-uzs-rate').textContent = usdToUzsRate.toFixed(2);

            updateUzsPrices(); // Update prices after fetching
        } catch (error) {
            console.error('Error fetching exchange rates:', error);
            usdToUzsRate = 12500; // fallback
            document.getElementById('usd-uzs-rate').textContent = usdToUzsRate.toFixed(2);
            updateUzsPrices();
        }
    }

    function updateUzsPrices() {
        document.querySelectorAll('.uzs-price').forEach(el => {
            const usd = parseFloat(el.dataset.usd);
            const uzs = usd * usdToUzsRate;
            el.textContent = `${uzs.toLocaleString()} so'm`;
        });
    }

    fetchExchangeRates();
</script> --}}
