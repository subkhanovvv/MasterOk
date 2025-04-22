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

        <div class="container-fluid page-body-wrapper">

            @include('layouts.sidebar.admin-sidebar')

            <div class="main-panel">

                <div class="content-wrapper">

                    <div class="row">

                        <div class="col-sm-12">

                            <div class="home-tab">

                                @yield('content')

                            </div>

                        </div>

                    </div>

                </div>

                @include('layouts.footer.admin-footer')

            </div>

        </div>

    </div>

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
