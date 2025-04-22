<style>
    .currency-container {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .currency-card {
        background-color: #f9f9f9;
        color: #333333;
        border-radius: 10px;
        padding: 12px 16px;
        width: 180px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease;
        display: flex;
        flex-direction: column;
    }

    .currency-card:hover {
        transform: translateY(-2px);
    }

    .price-row {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .flag {
        width: 24px;
        height: 16px;
        border-radius: 2px;
        object-fit: cover;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
    }

    .price {
        font-size: 1.5rem;
        font-weight: bold;
        color: #1a2c2a;
    }

    .change {
        font-size: 0.85rem;
        margin-top: 6px;
        color: #4a5568;
    }
</style>
<div class="header-dashboard">
    <div class="wrap">
        <div class="header-left">
            <a href="index-2.html">
                <img class="" id="logo_header_mobile" alt="" src="{{ asset('images/logo/logo3.png') }}"
                    data-light="{{ asset('images/logo/logo.png') }}" data-dark="{{ asset('images/logo/logo3.png') }}"
                    data-width="154px" data-height="52px" data-retina="{{ asset('images/logo/logo3.png') }}">
            </a>
            <div class="button-show-hide">
                <i class="icon-menu-left"></i>
            </div>
            <form class="form-search flex-grow">
                <fieldset class="name">
                    <input type="text" placeholder="Search here..." class="show-search" name="name" tabindex="2"
                        value="" aria-required="true" required="">
                </fieldset>
                <div class="button-submit">
                    <button class="" type="submit"><i class="icon-search"></i></button>
                </div>
                <div class="box-content-search" id="box-content-search">
                    <ul class="mb-24">
                        <li class="mb-14">
                            <div class="body-title">Top selling product</div>
                        </li>
                        <li class="mb-14">
                            <div class="divider"></div>
                        </li>
                        <li>
                            <ul>
                                <li class="product-item gap14 mb-10">
                                    <div class="image no-bg">
                                        <img src="{{ asset('images/products/17.png') }}" alt="">
                                    </div>
                                    <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <a href="product-list.html" class="body-text">Dog Food
                                                Rachael Ray Nutrish®</a>
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-10">
                                    <div class="divider"></div>
                                </li>
                                <li class="product-item gap14 mb-10">
                                    <div class="image no-bg">
                                        <img src="images/products/18.png" alt="">
                                    </div>
                                    <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <a href="product-list.html" class="body-text">Natural
                                                Dog Food Healthy Dog Food</a>
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-10">
                                    <div class="divider"></div>
                                </li>
                                <li class="product-item gap14">
                                    <div class="image no-bg">
                                        <img src="images/products/19.png" alt="">
                                    </div>
                                    <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <a href="product-list.html" class="body-text">Freshpet
                                                Healthy Dog Food and Cat</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="">
                        <li class="mb-14">
                            <div class="body-title">Order product</div>
                        </li>
                        <li class="mb-14">
                            <div class="divider"></div>
                        </li>
                        <li>
                            <ul>
                                <li class="product-item gap14 mb-10">
                                    <div class="image no-bg">
                                        <img src="images/products/20.png" alt="">
                                    </div>
                                    <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <a href="product-list.html" class="body-text">Sojos
                                                Crunchy Natural Grain Free...</a>
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-10">
                                    <div class="divider"></div>
                                </li>
                                <li class="product-item gap14 mb-10">
                                    <div class="image no-bg">
                                        <img src="images/products/21.png" alt="">
                                    </div>
                                    <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <a href="product-list.html" class="body-text">Kristin
                                                Watson</a>
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-10">
                                    <div class="divider"></div>
                                </li>
                                <li class="product-item gap14 mb-10">
                                    <div class="image no-bg">
                                        <img src="images/products/22.png" alt="">
                                    </div>
                                    <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <a href="product-list.html" class="body-text">Mega
                                                Pumpkin Bone</a>
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-10">
                                    <div class="divider"></div>
                                </li>
                                <li class="product-item gap14">
                                    <div class="image no-bg">
                                        <img src="images/products/23.png" alt="">
                                    </div>
                                    <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <a href="product-list.html" class="body-text">Mega
                                                Pumpkin Bone</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </form>
            <div class="popup-wrap user type-header">
                <div class="currency-container">
                    <div class="currency-card" title="Доллар США">
                        <div class="price-row">
                            <img src="{{ asset('images/usd.png') }}" alt="UZ Flag" class="flag">
                            <div class="price" id="usd-uzs">Loading...</div>
                        </div>
                        <div class="change" id="uzs-change"></div>
                    </div>
                    <div class="currency-card" title="Российский рубль">
                        <div class="price-row">
                            <img src="{{ asset('images/rub.png') }}" alt="RU Flag" class="flag">
                            <div class="price" id="rub-uzs">Loading...</div>
                        </div>
                        <div class="change" id="rub-uzs-change"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-grid">
            <div class="popup-wrap message type-header">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton2"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="header-item">
                            <span class="text-tiny">1</span>
                            <i class="icon-bell"></i>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end has-content" aria-labelledby="dropdownMenuButton2">
                        <li>
                            <h6>Notifications</h6>
                        </li>
                        <li>
                            <div class="message-item item-1">
                                <div class="image">
                                    <i class="icon-noti-1"></i>
                                </div>
                                <div>
                                    <div class="body-title-2">Discount available</div>
                                    <div class="text-tiny">Morbi sapien massa, ultricies at rhoncus
                                        at, ullamcorper nec diam</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="message-item item-2">
                                <div class="image">
                                    <i class="icon-noti-2"></i>
                                </div>
                                <div>
                                    <div class="body-title-2">Account has been verified</div>
                                    <div class="text-tiny">Mauris libero ex, iaculis vitae rhoncus
                                        et</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="message-item item-3">
                                <div class="image">
                                    <i class="icon-noti-3"></i>
                                </div>
                                <div>
                                    <div class="body-title-2">Order shipped successfully</div>
                                    <div class="text-tiny">Integer aliquam eros nec sollicitudin
                                        sollicitudin</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="message-item item-4">
                                <div class="image">
                                    <i class="icon-noti-4"></i>
                                </div>
                                <div>
                                    <div class="body-title-2">Order pending: <span>ID 305830</span>
                                    </div>
                                    <div class="text-tiny">Ultricies at rhoncus at ullamcorper
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li><a href="#" class="tf-button w-full">View all</a></li>
                    </ul>
                </div>
            </div>
            <div class="popup-wrap user type-header">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton3"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="header-user wg-user">
                            <span class="image">
                                <img src="{{ asset('images/avatar/user.png') }}" alt="">
                            </span>
                            <span class="flex flex-column">
                                <span class="body-title mb-2">MasterOk</span>
                                <span class="text-tiny">Admin</span>
                            </span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end has-content" aria-labelledby="dropdownMenuButton3">
                        <li>
                            <a href="{{route('profile')}}" class="user-item">
                                <div class="icon">
                                    <i class="icon-user"></i>
                                </div>
                                <div class="body-title-2">Аккаунт</div>
                            </a>
                        </li>
                        {{-- <li>
                            <a href="#" class="user-item">
                                <div class="icon">
                                    <i class="icon-mail"></i>
                                </div>
                                <div class="body-title-2">Inbox</div>
                                <div class="number">27</div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="user-item">
                                <div class="icon">
                                    <i class="icon-file-text"></i>
                                </div>
                                <div class="body-title-2">Taskboard</div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="user-item">
                                <div class="icon">
                                    <i class="icon-headphones"></i>
                                </div>
                                <div class="body-title-2">Support</div>
                            </a>
                        </li>
                        <li> --}}
                            <a href="{{route('logout')}}" class="user-item">
                                <div class="icon">
                                    <i class="icon-log-out"></i>
                                </div>
                                <div class="body-title-2">Выйти</div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    let previousPrices = {
        UZS: null,
        RUB: null,
        RUBUZS: null
    };

    async function fetchExchangeRates() {
        try {
            const response = await fetch('https://open.er-api.com/v6/latest/USD');
            const data = await response.json();

            updatePrice('UZS', data.rates.UZS, 'usd-uzs', 'uzs-change');

            const rubUzsRate = data.rates.UZS / data.rates.RUB;
            updatePrice('RUBUZS', rubUzsRate, 'rub-uzs', 'rub-uzs-change');

            previousPrices.UZS = data.rates.UZS;
            previousPrices.RUB = data.rates.RUB;
            previousPrices.RUBUZS = rubUzsRate;
        } catch (error) {
            console.error('Error fetching exchange rates:', error);
        }
    }

    function updatePrice(currency, newPrice, priceId, changeId) {
        const priceElement = document.getElementById(priceId);
        const changeElement = document.getElementById(changeId);

        priceElement.textContent = newPrice.toFixed(2);

        if (previousPrices[currency] !== null) {
            const change = ((newPrice - previousPrices[currency]) / previousPrices[currency]) * 100;
            const changeText = change.toFixed(2);
            const arrow = change >= 0 ? '↑' : '↓';

            changeElement.textContent = `${arrow} ${Math.abs(changeText)}%`;
            changeElement.className = `change ${change >= 0 ? 'up' : 'down'}`;
        }
    }

    fetchExchangeRates();
    setInterval(fetchExchangeRates, 10000);
</script>
