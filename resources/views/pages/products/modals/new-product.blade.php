
<div class="modal fade" id="newProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Новый товар</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" method="POST" action="{{ route('store-product') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex justify-content-between gap-3">
                        <div class="w-50">
                            <div class="form-group">
                                <label for="productName">Название товара</label>
                                <input type="text" class="form-control" placeholder="Название товара" name="name"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="brand">Бренд</label>
                                <select name="brand_id" class="form-select" required>
                                    <option disabled selected>Выберите бренд</option>
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="unit">Единица</label>
                                <select name="unit" class="form-select" required>
                                    <option disabled selected>Выберите единицу</option>
                                    <option value="кг">кг</option>
                                    <option value="г">г</option>
                                    <option value="л">л</option>
                                    <option value="мл">мл</option>
                                    <option value="м">м</option>
                                    <option value="см">см</option>
                                    <option value="шт">шт</option>
                                    <option value="коробка">коробка</option>
                                    <option value="упаковка">упаковка</option>
                                    <option value="рулон">рулон</option>
                                    <option value="пара">пара</option>
                                    <option value="дюжина">дюжина</option>
                                    <option value="набор">набор</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="photo">Загрузить изображения</label>
                                <input class="form-control form-control-sm" type="file" name="photo">
                            </div>
                            <div class="form-group">
                                <label for="price_usd">Цена в USD</label>
                                <input class="form-control" type="number" placeholder="Цена в долларах США"
                                    name="price_usd" id="price_usd" required>
                            </div>
                        </div>
                        <div class="w-50">
                            <div class="form-group">
                                <label for="short_description">Краткое описание</label>
                                <input type="text" class="form-control" placeholder="Краткое описание"
                                    name="short_description">
                            </div>
                            <div class="form-group">
                                <label for="category">Категория</label>
                                <select name="category_id" class="form-select" id="category" required>
                                    <option disabled selected value="">Выберите категорию</option>
                                    @foreach ($categories as $c)
                                        <option value="{{$c->id}}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tax">Налог</label>
                                <select name="tax" class="form-select" required>
                                    <option value="0" selected>0%</option>
                                    <option value="1">1%</option>
                                    <option value="2">2%</option>
                                    <option value="4">4%</option>
                                    <option value="6">6%</option>
                                    <option value="8">8%</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sale_price">Цена продажи</label>
                                <input type="number" class="form-control" name="sale_price" placeholder="Цена продажи"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="price_uzs">Цена в UZS</label>
                                <input type="number" class="form-control" placeholder="Цена в узбекских сумах"
                                    name="price_uzs" id="price_uzs" required>
                                <div class="text-small text-success mt-3">
                                    1 USD = <strong id="usd-uzs-rate">Загрузка...</strong> UZS
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-lg text-white">Сохранить</button>
                    </div>
                    </form>
            </div>
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
</script>

