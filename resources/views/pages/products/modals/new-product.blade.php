<div class="modal fade" id="newProductModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h4 class="modal-title fw-bold">Добавить товар</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Left Column - Image Section -->
                        <div class="col-md-4 border-end pe-4">
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img id="preview" src="" alt="Image Preview"
                                        class="img-thumbnail rounded-circle border"
                                        style="width: 150px; height: 150px; object-fit: cover; display: none;">
                                    <div id="imagePlaceholder"
                                        class="rounded-circle border d-flex align-items-center justify-content-center"
                                        style="width: 150px; height: 150px; background-color: #f8f9fa;">
                                        <i class="bi bi-camera fs-1 text-muted"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <input type="file" name="photo" class="form-control form-control-sm"
                                        id="photo" onchange="previewImage(event)">
                                </div>
                            </div>



                        </div>

                        <!-- Right Column - Form Fields -->
                        <div class="col-md-8 ps-4">
                            <div class="row g-3">
                                <!-- Basic Info -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="name" id="productName"
                                            required>
                                        <label for="productName">Название товара</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input name="short_description" class="form-control" id="short_description">
                                        <label for="short_description">Краткое описание</label>
                                    </div>
                                </div>

                                <!-- Brand and Category -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="brand_id" class="form-select" id="brand">
                                            <option value="">Выберите бренд</option>
                                            @foreach ($brands as $b)
                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="brand">Бренд</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="category_id" class="form-select" id="category">
                                            <option value="">Выберите категория </option>
                                            @foreach ($categories as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="category">Категория</label>
                                    </div>
                                </div>

                                <!-- Pricing Section -->
                                <div class="col-12 mt-4">
                                    {{-- <h5 class="fw-bold text-uppercase text-muted mb-3">Цены</h5> --}}
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control decimal-input"
                                                    name="price_uzs" id="price_uzs" required>
                                                <label for="price_uzs">Цена</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" class="form-control decimal-input"
                                                    name="sale_price" id="sale_price" required>
                                                <label for="sale_price">Цена продажи</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="unit" class="form-select" id="unit" required>
                                                    <option disabled selected></option>
                                                    <option value="кг">кг</option>
                                                    <option value="г">г</option>
                                                    <option value="л">л</option>
                                                    <option value="мл">мл</option>
                                                    <option value="м">м</option>
                                                    <option value="см">см</option>
                                                    <option value="шт">шт</option>
                                                    <option value="пара">пара</option>
                                                    <option value="набор">набор</option>
                                                </select>
                                                <label for="unit">Малая единица</label>
                                            </div>
                                        </div>
                                        <div class="mb-12">
                                            <label class="form-label text-muted small mb-1">Штрих-код</label>

                                            <select id="barcode-option" class="form-select mb-2">
                                                <option value="auto">Автоматически сгенерируется</option>
                                                <option value="manual">Ввести вручную</option>
                                            </select>

                                            <div id="barcode-auto" class="bg-light p-2 rounded text-center">
                                                <small class="text-muted">Автоматически сгенерируется</small>
                                            </div>

                                            <div id="barcode-manual" class="d-none">
                                                <input type="text" name="barcode_value" class="form-control form-control-lg rounded"
                                                    placeholder="Введите штрих-код">
                                            </div>
                                        </div>
                                        {{-- <div class="col-12">
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="bi bi-arrow-left-right me-2"></i>
                                                1 USD = <strong id="usd-uzs-rate" class="ms-1">Загрузка...</strong>
                                                UZS
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Image Preview Function
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('imagePlaceholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
        }
    }

    // let usdToUzsRate = null;

    // async function fetchExchangeRates() {
    //     try {
    //         const response = await fetch('https://open.er-api.com/v6/latest/USD');
    //         const data = await response.json();
    //         usdToUzsRate = data.rates.UZS;
    //         document.getElementById('usd-uzs-rate').textContent = usdToUzsRate.toFixed(2);
    //     } catch (error) {
    //         console.error('Error fetching exchange rates:', error);
    //         // Fallback rate if API fails
    //         usdToUzsRate = 12500; // Example fallback rate
    //         document.getElementById('usd-uzs-rate').textContent = usdToUzsRate.toFixed(2);
    //     }
    // }

    // document.addEventListener('DOMContentLoaded', () => {
    //     fetchExchangeRates();
    //     setInterval(fetchExchangeRates, 3600000); // Refresh every hour

    //     const usdInput = document.getElementById('price_usd');
    //     const uzsInput = document.getElementById('price_uzs');

    //     usdInput.addEventListener('input', function() {
    //         if (usdToUzsRate) {
    //             const usdValue = parseFloat(this.value.replace(/,/g, '.')) || 0;
    //             const uzsValue = usdValue * usdToUzsRate;
    //             uzsInput.value = uzsValue.toFixed(2);
    //         }
    //     });

    //     // Also allow manual UZS entry without overwriting
    //     uzsInput.addEventListener('focus', function() {
    //         this.dataset.previousValue = this.value;
    //     });

    //     uzsInput.addEventListener('change', function() {
    //         if (this.value !== this.dataset.previousValue && usdToUzsRate) {
    //             // If UZS was manually changed, update USD
    //             const uzsValue = parseFloat(this.value.replace(/,/g, '.')) || 0;
    //             const usdValue = uzsValue / usdToUzsRate;
    //             usdInput.value = usdValue.toFixed(4);
    //         }
    //     });
    // });

    // // Decimal input handling
    // document.querySelectorAll('.decimal-input').forEach(input => {
    //     input.addEventListener('input', function() {
    //         let v = this.value
    //             .replace(/,/g, '.')
    //             .replace(/[^0-9.]/g, '');

    //         const parts = v.split('.');
    //         if (parts.length > 1) {
    //             v = parts[0] + '.' + parts.slice(1).join('').replace(/\./g, '');
    //             if (parts[1].length > 2) {
    //                 v = parts[0] + '.' + parts[1].substring(0, 2);
    //             }
    //         }

    //         this.value = v;
    //     });
    // });
</script>
<script>
    document.getElementById('barcode-option').addEventListener('change', function() {
        const manual = document.getElementById('barcode-manual');
        const auto = document.getElementById('barcode-auto');
        if (this.value === 'manual') {
            manual.classList.remove('d-none');
            auto.classList.add('d-none');
        } else {
            manual.classList.add('d-none');
            auto.classList.remove('d-none');
        }
    });
</script>
