<div class="modal fade" id="newProductModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавить товар</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-4">

                        <!-- LEFT COLUMN -->
                        <div class="col-12 col-md-4">
                            <div class="form-group mb-3">
                                <label for="productName">Название товара</label>
                                <input type="text" class="form-control" name="name" id="productName" placeholder="Название товара" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="brand">Бренд</label>
                                <select name="brand_id" class="form-select" id="brand" required>
                                    <option disabled selected>Выберите бренд</option>
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="short_description">Краткое описание</label>
                                <input name="short_description" class="form-control" id="short_description" placeholder="Краткое описание">
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo">Изображение</label>
                                <input type="file" name="photo" class="form-control form-control-sm" onchange="previewImage(event)">
                            </div>

                            <div class="preview mb-3" id="imagePreview" style="display: none;">
                                <img id="preview" src="" alt="Image Preview" class="img-thumbnail" style="max-width: 100%;">
                            </div>
                        </div>

                        <!-- MIDDLE COLUMN -->
                        <div class="col-12 col-md-4">
                            <div class="form-group mb-3">
                                <label for="category">Категория</label>
                                <select name="category_id" class="form-select" id="category" required>
                                    <option disabled selected value="">Выберите категорию</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="price_usd">Цена в USD</label>
                                <input type="text" class="form-control decimal-input" name="price_usd" id="price_usd" placeholder="USD" required>
                            </div>

                            <div class="form-group mb-1">
                                <label for="price_uzs">Цена в UZS</label>
                                <input type="text" class="form-control decimal-input" name="price_uzs" id="price_uzs" placeholder="UZS" required>
                            </div>
                            <small class="text-primary">1 USD = <strong id="usd-uzs-rate">Загрузка...</strong> UZS</small>

                            <div class="form-group mt-3 mb-3">
                                <label for="sale_price">Цена продажи</label>
                                <input type="number" class="form-control decimal-input" name="sale_price" id="sale_price" placeholder="Цена продажи" required>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN -->
                      <div class="col-12 col-md-4">
                            {{-- <div class="form-group mb-3">
                                <label for="tax">Налог</label>
                                <select name="tax" class="form-select" id="tax" required>
                                    <option value="0" selected>0%</option>
                                    <option value="1">1%</option>
                                    <option value="2">2%</option>
                                    <option value="4">4%</option>
                                    <option value="6">6%</option>
                                    <option value="8">8%</option>
                                </select>
                            </div> --}} 

                            <div class="form-group mb-3">
                                <label for="unit">Малая единица измерения</label>
                                <select name="unit" class="form-select" id="unit" required>
                                    <option disabled selected>Выберите единицу</option>
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
                            </div>

                            <div class="form-group mb-3">
                                <label for="stock_unit">Складская единица</label>
                                <select name="stock_unit" class="form-select" id="stock_unit">
                                    <option disabled selected>Выберите складскую единицу</option>
                                    <option value="коробка">коробка</option>
                                    <option value="упаковка">упаковка</option>
                                    <option value="рулон">рулон</option>
                                    <option value="набор">набор</option>
                                    <option value="дюжина">дюжина</option>
                                    <option value="шт">шт</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="units_per_stock">Количество в складской единице</label>
                                <input type="number" min="1" name="units_per_stock" class="form-control" id="units_per_stock" placeholder="Например: 10">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
        setInterval(fetchExchangeRates, 10000);
    });
    document.querySelectorAll('.decimal-input').forEach(input => {
        input.addEventListener('input', function() {
            let v = this.value
                .replace(/,/g, '.')
                .replace(/[^0-9.]/g, '');

            const firstDot = v.indexOf('.');
            if (firstDot !== -1) {
                v = v.slice(0, firstDot + 1) +
                    v.slice(firstDot + 1).replace(/\./g, '');
            }
            this.value = v;
        });
    });
</script>
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }
</script>
