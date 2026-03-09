<!doctype html>
<html lang="ar" dir="rtl" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>{{config('app.name')}} | {{ __('dashboard.login') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Era Technology" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('backend/assets/images/favicon.ico')}}">

    <!-- Sweet Alert css-->
    <link href="{{asset('backend/assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{asset('backend/assets/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('backend/assets/css/bootstrap-rtl.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('backend/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('backend/assets/css/app-rtl.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('backend/assets/css/custom-rtl.min.css')}}" rel="stylesheet" type="text/css" />
    @yield('style')
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('partials.backend.header')

        <!-- removeNotificationModal -->
        @include('partials.backend.removeNotificationModal')
        <!-- /.modal -->
        <!-- ========== App Menu ========== -->
        @include('partials.backend.sidebar')
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                                <h4 class="mb-sm-0">@yield('title')</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="{{route('admin.index_route')}}">الرئيسية</a></li>
                                        <li class="breadcrumb-item active">{{$title}}</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->
                    <!-- start page contents -->
                    @yield('content')
                    {{$slot ?? ''}}
                    <!-- start page contents -->

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            @include('partials.backend.footer')
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <div class="customizer-setting d-none d-md-block">
        <div class="btn-info rounded-pill shadow-lg btn btn-icon btn-lg p-2" data-bs-toggle="offcanvas"
            data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
            <i class='mdi mdi-spin mdi-cog-outline fs-22'></i>
        </div>
    </div>

    <!-- Theme Settings -->
    @include('partials.backend.theme-settings')

    <!-- JAVASCRIPT -->
    <script src="{{asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('backend/assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('backend/assets/libs/node-waves/waves.min.js')}}"></script>
    <script src="{{asset('backend/assets/libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('backend/assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    <script src="{{asset('backend/assets/js/plugins.js')}}"></script>

    <!-- list.js min js -->
    <script src="{{asset('backend/assets/libs/list.js/list.min.js')}}"></script>

    <!--list pagination js-->
    <script src="{{asset('backend/assets/libs/list.pagination.js/list.pagination.min.js')}}"></script>

    <!-- Sweet Alerts js -->
    <script src="{{asset('backend/assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>

    <!-- App js -->
    <script src="{{asset('backend/assets/js/app.js')}}"></script>
    @yield('script')
</body>

</html>