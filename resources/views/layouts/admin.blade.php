<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>MasterOk</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ 'admin/assets/vendors/feather/feather.css' }}">
    <link rel="stylesheet" href="{{ 'admin/assets/vendors/mdi/css/materialdesignicons.min.css' }}">
    <link rel="stylesheet" href="{{ 'admin/assets/vendors/ti-icons/css/themify-icons.css' }}">
    <link rel="stylesheet" href="{{ 'admin/assets/vendors/typicons/typicons.css' }}">
    <link rel="stylesheet" href="{{ 'admin/assets/vendors/simple-line-icons/css/simple-line-icons.css' }}">
    <link rel="stylesheet" href="{{ 'admin/assets/vendors/css/vendor.bundle.base.css' }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ 'admin/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css' }}">
    <link rel="stylesheet" type="text/css" href="{{ 'admin/assets/js/select.dataTables.min.css' }}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ 'admin/assets/css/vertical-layout-light/style.css' }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ 'admin/assets/images/favicon.ico' }}" />
</head>

<body class="body">

    <div class="container-scroller">

        @include('layouts.header.admin-header')
        <div class="theme-setting-wrapper">
            <div id="settings-trigger"><i class="ti-settings"></i></div>
            <div id="theme-settings" class="settings-panel">
                <i class="settings-close ti-close"></i>
                <p class="settings-heading">SIDEBAR SKINS</p>
                <div class="sidebar-bg-options selected" id="sidebar-light-theme">
                    <div class="img-ss rounded-circle bg-light border me-3"></div>Light
                </div>
                <div class="sidebar-bg-options" id="sidebar-dark-theme">
                    <div class="img-ss rounded-circle bg-dark border me-3"></div>Dark
                </div>
                <p class="settings-heading mt-2">HEADER SKINS</p>
                <div class="color-tiles mx-0 px-4">
                    <div class="tiles success"></div>
                    <div class="tiles warning"></div>
                    <div class="tiles danger"></div>
                    <div class="tiles info"></div>
                    <div class="tiles dark"></div>
                    <div class="tiles default"></div>
                </div>
            </div>
        </div>
        <div class="container-fluid page-body-wrapper">

            @include('layouts.sidebar.admin-sidebar')

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Закрыть"></button>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (Session::has('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ Session::get('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Закрыть"></button>
                            </div>
                        @endif



                        @yield('content')




                    </div>

                </div>

                @include('layouts.footer.admin-footer')

            </div>

        </div>

    </div>

    <script>
        let scannedData = '';
        let scanTimeout;

        // Listen for keydown events globally (even if input is not focused)
        document.addEventListener('keydown', function(e) {
            if (e.key.length === 1) {
                scannedData += e.key;
            }

            if (scanTimeout) clearTimeout(scanTimeout);

            // Wait for scanner to finish sending data (timeout)
            scanTimeout = setTimeout(() => {
                if (scannedData) {
                    handleScan(scannedData);
                    scannedData = ''; // Reset after handling
                }
            }, 50); // Adjust timeout based on scanner speed
        });

        // Handle the scanned data
        function handleScan(data) {
            console.log("Scanned:", data);

            // Send the scanned data to the backend
            fetch('{{ route('scan.transaction') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        scanned_data: data
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        // Update the Consume modal with product details
                        showConsumeModal(res.product);
                    } else {
                        alert(res.message);
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Error verifying scanned data.');
                });
        }

        // Function to show the Consume modal with product details
        function showConsumeModal(product) {
            // Set product details in the modal
            document.getElementById('consume_product_id').value = product.id;
            document.getElementById('consume_product_photo').src = product.photo_url; // Set correct path for image
            document.getElementById('consume_product_name').innerText = product.name;
            document.getElementById('consume_product_sale_price').innerText = product.sale_price;
            document.getElementById('consume_hidden_total_price').value = product.sale_price;

            // Show the modal
            $('#consumeProductModal').modal('show');
        }
    </script>

    <script src="{{ asset('admin/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('admin/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('admin/assets/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendors/progressbar.js/progressbar.min.js') }}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('admin/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin/assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('admin/assets/js/template.js') }}"></script>
    <script src="{{ asset('admin/assets/js/settings.js') }}"></script>
    <script src="{{ asset('admin/assets/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ asset('admin/assets/js/jquery.cookie.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('admin/assets/js/proBanner.js') }}"></script>
</body>

</html>
