@php
    $opt = DB::table('ms_options')->first();
@endphp

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=1024">

    <title>@yield('title') @if (trim($__env->yieldContent('title')))
            -
        @endif {{ $opt->title }}</title>
    <!-- plugins:css -->
    <link rel="shortcut icon"
        href="{{ asset('assets/admin/images/' . \Smt\Masterweb\Models\Option::first()->favicon) }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/iconfonts/font-awesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/css/vendor.bundle.addons.css') }}">

    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/admin/vendors/iconfonts/simple-line-icon/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/iconfonts/ti-icons/css/themify-icons.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/summernote/dist/summernote-bs4.css') }}">




    <link rel="shortcut icon" href="{{ asset('assets/admin/images/logo/logo-silaboy-mini.png') }}">
    <style>
        @font-face {
            font-family: 'gotham-light';
            src: url({{ asset('assets/public/fonts/gotham/Gotham-Light.otf') }});
        }

        @font-face {
            font-family: 'gotham-medium';
            src: url({{ asset('assets/public/fonts/gotham/Gotham-Medium.otf') }});
        }

        @font-face {
            font-family: 'gotham-thin';
            src: url({{ asset('assets/public/fonts/gotham/Gotham-Thin.otf') }});
        }

        @font-face {
            font-family: 'gotham-ultra';
            src: url({{ asset('assets/public/fonts/gotham/Gotham-Ultra.otf') }});
        }

        @font-face {
            font-family: 'gotham-narrow-black';
            src: url({{ asset('assets/public/fonts/gotham/GothamNarrow-Black.otf') }});
        }

        @font-face {
            font-family: 'gothamnarrow-book';
            src: url({{ asset('assets/public/fonts/gotham/GothamNarrow-Book.otf') }});
        }

        @font-face {
            font-family: 'gotham-narrow-thin';
            src: url({{ asset('assets/public/fonts/gotham/GothamNarrow-Thin.otf') }});
        }

        @font-face {
            font-family: 'gotham-narrow-ultra';
            src: url({{ asset('assets/public/fonts/gotham/GothamNarrow-Ultra.otf') }});
        }
    </style>

    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css" />

    <style>
        .smt-table th {
            background: #3ca8a8 !important;
            color: #fff;
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>


    <script src="{{ asset('assets/admin/js/input-mask.js') }}"></script>
    {{-- <link rel="stylesheet" href="{{ asset('assets/admin/vendors/datatables/datatables.min.css') }}"> --}}
    {{-- DATATABLES --}}
    <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.12.1/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('vendor/datatables/FixedHeader-3.2.3/css/fixedHeader.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/Responsive-2.3.0/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/Responsive-2.3.0/css/responsive.dataTables.min.css') }}">

    {{-- Select2 4.0.3 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4/dist/select2-bootstrap4.min.css') }}">
</head>
