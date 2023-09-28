<!doctype html>
<html>
@include('superadmin.header_after_login')
<body>
    <input type="hidden" name="kajal" value="09-05-2023 11:50">
    <div class="maincontenttest">
            <div class="navbar navbar-expand flex-column flex-md-row align-items-center navbar-custom">
    @include('superadmin.head_after_login')
    </div>
    <div class="main-content">
        @include('flash-message')
        @yield('content')
        <div class="copyright">
            <p>©{{ date('Y') }} {{__('level.all_rights_reserved')}}  <a href="{{ route('dashboard') }}">{{ config('app.name', 'Laravel') }}</a></p>
        </div>
        <a class="back-left-btn" id="backbutton" style="display:none;"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
    </div>
    </div>
    @include('superadmin.footer_after_login')
    @stack('current-page-js')
    @include('js-flash-message')
</body>
</html>