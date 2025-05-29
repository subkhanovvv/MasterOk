<style>
    .currency-container {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .currency-card {
        background-color: #ffffff;
        color: #333333;
        border-radius: 10px;
        padding: 6px 8px;
        width: 143px;
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
        gap: 10px;
    }

    .flag {
        width: 22px;
        height: 14px;
        border-radius: 2px;
        object-fit: cover;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
    }

    .price {
        font-size: 0.9rem;
        font-weight: bold;
        color: #1a2c2a;
    }

    .change {
        font-size: 0.5rem;
        /* margin-top: 10px; */
        color: #554a68;
    }

    #fullscreenBtn {
        position: fixed;
        top: 15px;
        right: 15px;
        z-index: 999;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes mdi-bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-12px);
        }

        60% {
            transform: translateY(-6px);
        }
    }

    .bounce {
        display: inline-block;
        animation: mdi-bounce 1s infinite;
    }
</style>
<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        </div>
        <div>
            <a class="navbar-brand brand-logo" href="/">
                <img src="{{ asset('../admin/assets/images/logo.png') }}" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="/">
                <img src="{{ asset('../admin../assets/images/ico.png') }}" alt="logo" />
            </a>
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-top">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <i class="mdi mdi-fullscreen icon-sm" id="fullscreenIcon" onclick="toggleFullscreen()"></i>
            </li>
            <li class="nav-item">
                <div class="currency-container ">
                    <div class="currency-card" title="Доллар США">
                        <div class="price-row">
                            <img src="{{ asset('../admin../assets/images/flags/usd.png') }}" alt="UZ Flag"
                                class="flag">
                            <div class="price" id="usd-uzs">Loading...</div>
                            <div class="change" id="uzs-change"></div>
                        </div>
                    </div>
                    <div class="currency-card" title="Российский рубль">
                        <div class="price-row">
                            <img src="{{ asset('../admin../assets/images/flags/rub.png') }}" alt="RU Flag"
                                class="flag">
                            <div class="price" id="rub-uzs">Loading...</div>
                            <div class="change" id="rub-uzs-change"></div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="img-xs rounded-circle" src="{{ asset('../admin../assets/images/profile.jpg') }}"
                        alt="Profile image">
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <div class="dropdown-header text-center">
                        <strong class="mb-1 mt-3 font-weight-semibold text-capitalize">{{ Auth::user()->name }}</strong>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile') }}"><i
                            class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My
                        Profile</a>
                    <a class="dropdown-item" href="{{ route('settings') }}"><i
                            class="dropdown-item-icon mdi mdi-cog text-primary me-2"></i> Settings</a>
                    <a class="dropdown-item" href="{{ route('logout') }}"><i
                            class="dropdown-item-icon mdi mdi-logout text-primary me-2"></i>Sign
                        Out</a>
                    <a href="" class="dropdown-item">
                        <i class="dropdown-item-icon mdi mdi-information-slab-circle-outline text-info me-2"></i>
                        Инфо
                    </a>
                    <a href="" class="dropdown-footer align-items-center d-flex justify-content-center text-decoration-none text-dark">
                        <i class="mdi mdi-gift-outline icon-sm text-danger me-2 bounce"></i> Рефералка
                    </a>



    </div>
    </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
        data-bs-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
    </button>
    </div>
</nav>
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
    setInterval(fetchExchangeRates, 100);

    function toggleFullscreen() {
        const elem = document.documentElement;
        const icon = document.getElementById("fullscreenIcon");

        if (!document.fullscreenElement) {
            elem.requestFullscreen().then(() => {
                icon.classList.remove("mdi-fullscreen");
                icon.classList.add("mdi-fullscreen-exit");
            }).catch(err => {
                console.error("Failed to enter fullscreen:", err);
            });
        } else {
            document.exitFullscreen().then(() => {
                icon.classList.remove("mdi-fullscreen-exit");
                icon.classList.add("mdi-fullscreen");
            }).catch(err => {
                console.error("Failed to exit fullscreen:", err);
            });
        }
    }
</script>
