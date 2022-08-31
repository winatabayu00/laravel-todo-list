@php
$user = Auth::user();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('page-title')</title>

    @include('extends.styles')

</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">

            @include('components.navbar')


            @include('components.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-body mt-5">
                        {{ isset($page_body) ? $page_body : '' }}

                    </div>
                </section>
            </div>
            @include('components.footer')
        </div>
    </div>

    @include('extends.scripts')


    <!-- Page Specific JS File -->

</body>

</html>
