<!doctype html>
<html>
@include('superadmin.header_before_login')
<body>
@stack('current-page-css')
@yield('content')
@include('superadmin.footer_before_login')
@stack('current-page-js')
@include('js-flash-message')
</body>
</html>