    <!-- General JS Scripts -->
    <script src="{{ asset('stisla/js/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('stisla/js/modules/tooltip.js') }}"></script>
    <script src="{{ asset('stisla/js/modules/popper.js') }}"></script>
    <script src="{{ asset('stisla/js/modules/bootstrap.min.js') }}"></script>
    <script src="{{ asset('stisla/js/modules/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('stisla/js/modules/moment.min.js') }}"></script>
    <script src="{{ asset('stisla/js/stisla.js') }}"></script>

    <!-- JS Libraies -->
    <script type="text/javascript" src="{{ asset('vendor/DataTables/datatables.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('page-library-scripts')

    <!-- Page Specific JS File -->
    @yield('page-additional-scripts')

    <!-- Template JS File -->
    <script src="{{ asset('stisla/js/scripts.js') }}"></script>
    <script src="{{ asset('stisla/js/custom.js') }}"></script>

    @yield('page-scripts')
