<!-- General CSS Files -->
<link rel="stylesheet" href="{{ asset('stisla/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/fontawesome-free-6.1.1-web/css/all.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/DataTables/datatables.min.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/datetimepicker/jquery.datetimepicker.css') }}">

<!-- Template CSS -->
<link rel="stylesheet" href="{{ asset('stisla/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('stisla/css/components.css') }}">

<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-94034622-3');
</script>

@yield('page-styles')
