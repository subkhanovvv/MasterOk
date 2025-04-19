@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Новый товар</h3>
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
                        <a href="{{ route('product') }}">
                            <div class="text-tiny">Товары</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Новый товар</div>
                    </li>
                </ul>
            </div>
            <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data"
                action="{{ route('store-product') }}">
                @csrf
                <div class="wg-box">
                    <fieldset class="name">
                        <div class="body-title mb-10">Название товара <span class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Введите название товара" name="name"
                            tabindex="0" value="{{ old('name') }}" aria-required="true" required="">
                        <div class="text-tiny">Не превышайте 100 символов при вводе названия товара.</div>
                    </fieldset>

                    <fieldset class="shortdescription">
                        <div class="body-title mb-10">Краткое описание <span class="tf-color-1">*</span></div>
                        <textarea class="mb-10 ht-150" name="short_description" placeholder="Краткое описание" tabindex="0"
                            aria-required="true" required></textarea>
                        <div class="text-tiny">Не превышайте 100 символов при вводе краткого описания.</div>
                    </fieldset>

                    <fieldset>
                        <div class="body-title mb-10">Загрузить изображения <span class="tf-color-1">*</span></div>
                        <div class="upload-image flex-grow">
                            <div class="item" id="imgpreview" style="display:none">
                                <img src="" class="effect8" alt="">
                            </div>
                            <div id="upload-file" class="item up-load">
                                <label class="uploadfile" for="myFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="body-text">Перетащите изображения сюда или выберите <span
                                            class="tf-color">кликните
                                            для выбора</span></span>
                                    <input type="file" id="myFile" name="photo" accept="photo/*">
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="wg-box">
                    <div class="gap22 cols">
                        <fieldset class="category">
                            <div class="body-title mb-10">Категория <span class="tf-color-1">*</span></div>
                            <div class="select">
                                <select name="category_id">
                                    <option>Выберите категорию</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                        <fieldset class="brand">
                            <div class="body-title mb-10">Бренд <span class="tf-color-1">*</span></div>
                            <div class="select">
                                <select name="brand_id">
                                    <option>Выберите бренд</option>
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                    </div>
                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Цена в USD <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Цена в долларах США" name="price_usd"
                                tabindex="0" value="" aria-required="true" required="">
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Цена в UZS <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Цена в узбекских сумах" name="price_uzs"
                                tabindex="0" value="" aria-required="true" required="">
                            <div class="text-tiny text-success">1 USD = <strong id="usd-uzs-rate">Загрузка...</strong> UZS
                            </div>
                        </fieldset>
                    </div>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Цена продажи <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Цена продажи в узбекских сумах" name="sale_price"
                                tabindex="0" value="" aria-required="true" required="">
                        </fieldset>
                    </div>

                    <div class="cols gap22">
                        <fieldset class="name">
                            <div class="body-title mb-10">Количество <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Введите количество" name="qty"
                                tabindex="0" aria-required="true">
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Единица</div>
                            <div class="select mb-10">
                                <select name="unit">
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
                        </fieldset>
                    </div>
                    <div class="cols gap10">
                        <button class="tf-button w-full" type="submit">Добавить товар</button>
                    </div>
                </div>
            </form>
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
        document.addEventListener('DOMContentLoaded', () => {
            const usdInput = document.querySelector('input[name="price_usd"]');
            const uzsInput = document.querySelector('input[name="price_uzs"]');

            // Function to format numbers with commas or periods
            function formatNumber(number) {
                return new Intl.NumberFormat('ru-RU').format(number); // Use 'ru-RU' for comma as thousand separator
            }

            usdInput.addEventListener('input', () => {
                if (usdToUzsRate) {
                    const usdValue = parseFloat(usdInput.value);
                    if (!isNaN(usdValue)) {
                        const uzsValue = usdValue * usdToUzsRate;
                        uzsInput.value = formatNumber(uzsValue.toFixed(2));
                    } else {
                        uzsInput.value = '';
                    }
                }
            });

            fetchExchangeRates();
            setInterval(fetchExchangeRates, 10000); // Optional: refresh rate
        });
    </script>
@endsection
