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
            <a class="nav-link" href="{{ route('products.index') }}">
                <i class="menu-icon mdi mdi-arrange-send-backward"></i>
                <span class="menu-title">Товары</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('categories.index') }}">
                <i class="menu-icon mdi mdi-shape-outline"></i>
                <span class="menu-title">Категории</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('barcode.index') }}">
                <i class="menu-icon mdi mdi-barcode"></i>
                <span class="menu-title">Штрихкоды</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('brands.index') }}">
                <i class="menu-icon mdi mdi-tag"></i>
                <span class="menu-title">Бренды</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item nav-category">Транзакции</li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('suppliers.index') }}">
                <i class="menu-icon mdi mdi-truck"></i>
                <span class="menu-title">Поставщики</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('consumption.index') }}">
                <i class="menu-icon mdi mdi-database-minus"></i>
                <span class="menu-title">Расход</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('intake.index') }}">
                <i class="menu-icon mdi mdi-database-plus"></i>
                <span class="menu-title">Приход</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('history.index') }}">
                <i class="menu-icon mdi mdi-history"></i>
                <span class="menu-title">История</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="menu-icon mdi mdi-file-chart"></i>
                <span class="menu-title">Отчёт</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
    </ul>
</nav>
