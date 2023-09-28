<!doctype html>
<html>
@include('admin.header_before_login')
<body>
@stack('current-page-css')
@yield('content')
@include('admin.footer_before_login')
@stack('current-page-js')
@include('js-flash-message')
</body>
</html>