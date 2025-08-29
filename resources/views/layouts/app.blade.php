<!doctype html>
<html lang="en">
@include('layouts.head')
<body>




@include('layouts.sidebar')
@include('layouts.navbar')

<!-- Content -->
<div id="content">
    @yield('content', 'No content here')
    {{--    <div class="mt-4">--}}
    {{--        <h1>Main Content Area</h1>--}}
    {{--        <p>This is where your page content goes.</p>--}}
    {{--        <i class="fas fa-home"></i> Dashboard--}}
    {{--        <i class="far fa-user"></i> Profile--}}
    {{--        <i class="fab fa-github"></i> GitHub--}}
    {{--    </div>--}}
</div>

@include('layouts.scripts')

</body>
</html>
