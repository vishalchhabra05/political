<!doctype html>
<html>
@include('admin.header_after_login')
<body>
    <input type="hidden" name="kajal" value="27-07-2023 11:26">
    <div class="navbar navbar-expand flex-column flex-md-row align-items-center navbar-custom">
    @include('admin.head_after_login')
    </div>
    @include('admin.sidebar')
    <div class="main-content">
        @include('flash-message')
        @yield('content')
        <div class="copyright">
            <p>©{{ date('Y') }} {{__('level.all_rights_reserved')}}  <a href="{{ route('dashboard') }}">{{ config('app.name', 'Laravel') }}</a></p>
        </div>
    </div>
    @include('admin.footer_after_login')
    @stack('current-page-js')
    @include('js-flash-message')
</body>
</html>