<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="../index.html">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item nav-category">Products</li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('category')}}" >
                <i class="menu-icon mdi mdi-floor-plan"></i>
                <span class="menu-title">Categories</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"  href="{{route('product')}}" >
                <i class="menu-icon mdi mdi-arrange-send-backward"></i>
                <span class="menu-title">Products</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="menu-icon mdi mdi-barcode"></i>
                <span class="menu-title">Barcodes</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
    </ul>
</nav>
