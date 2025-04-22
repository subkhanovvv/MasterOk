<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('index') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Панель</span>
            </a>
        </li>
        <li class="nav-item nav-category">Товар</li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('product') }}">
                <i class="menu-icon mdi mdi-arrange-send-backward"></i>
                <span class="menu-title">Товары</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('category') }}">
                <i class="menu-icon mdi mdi-shape-outline"></i>
                <span class="menu-title">Категории</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('barcode') }}">
                <i class="menu-icon mdi mdi-barcode"></i>
                <span class="menu-title">Штрихкоды</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('warehouse') }}">
                <i class="menu-icon mdi mdi-warehouse"></i>
                <span class="menu-title">Склад</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item nav-category">Бренд</li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('brand') }}">
                <i class="menu-icon mdi mdi-tag"></i>
                <span class="menu-title">Бренды</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item nav-category">Прочее</li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('history') }}">
                <i class="menu-icon mdi mdi-history"></i>
                <span class="menu-title">История</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('report') }}">
                <i class="menu-icon mdi mdi-file-chart"></i>
                <span class="menu-title">Отчёт</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
    </ul>
</nav>
