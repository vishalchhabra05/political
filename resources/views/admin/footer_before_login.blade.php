<script type="text/javascript" src="{{ asset('js/admin/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/admin/bootstrap.bundle.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/admin/script.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/admin/jquery.blockUI.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/developer.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/lobibox.min.js') }}"></script>
<script>

// Begin: Custom Block UI Function
function showCustomBlockUI(){
    $.blockUI({ overlayCSS: {'z-index': '99999'}, css: {'z-index': '999999'},message: '<div class="front_loader theme-bg-color" style="display:block;">Loading...</div>' });
}

function hideCustomBlockUI(){
    $.unblockUI();
}
// End: Custom Block UI Function

</script>