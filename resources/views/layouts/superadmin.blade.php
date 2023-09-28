<!doctype html>
<html>
@include('superadmin.header_after_login')
<body>
    <input type="hidden" name="kajal" value="07-06-2023 12:56">
    <div class="navbar navbar-expand flex-column flex-md-row align-items-center navbar-custom">
    @include('superadmin.head_after_login')
    </div>
    @include('superadmin.sidebar')
    <div class="main-content">
        @include('flash-message')
        @yield('content')
        <div class="copyright">
            <p>©{{ date('Y') }} {{__('level.all_rights_reserved')}}  <a href="{{ route('dashboard') }}">{{ config('app.name', 'Laravel') }}</a></p>
        </div>
    </div>
    @include('superadmin.footer_after_login')
    @stack('current-page-js')
    @include('js-flash-message')
</body>
</html>