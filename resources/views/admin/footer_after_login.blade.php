    <script type="text/javascript" src="{{ asset('js/admin/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/bootstrap.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/daterangepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/jquery.nicescroll.min.js') }}"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="{{ asset('js/admin/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('laravel-ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/sweetalert.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/admin/jquery.blockUI.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/developer.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lobibox.min.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script>

$(document).ready(function(){
    $('#dataTable').DataTable({
        dom: 'Plfrtip',
        stateSave: true
    });
});

function getCurrentDate(){
    // Get current date and time
    var currentDateTime = new Date();

    // Extract date components
    var year = currentDateTime.getFullYear();
    var month = String(currentDateTime.getMonth() + 1).padStart(2, '0');
    var day = String(currentDateTime.getDate()).padStart(2, '0');

    // Extract time components
    var hours = String(currentDateTime.getHours()).padStart(2, '0');
    var minutes = String(currentDateTime.getMinutes()).padStart(2, '0');
    var seconds = String(currentDateTime.getSeconds()).padStart(2, '0');

    // Format the datetime string
    var formattedDateTime = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;

    console.log('formattedDateTime '+formattedDateTime);
    return formattedDateTime;
}

// Begin: Custom Block UI Function
function showCustomBlockUI(){
    $.blockUI({ overlayCSS: {'z-index': '99999'}, css: {'z-index': '999999'},message: '<div class="front_loader theme-bg-color" style="display:block;">Loading...</div>' });
}

function hideCustomBlockUI(){
    $.unblockUI();
}
// End: Custom Block UI Function

var admin_page_length = "<?= Config::get('params.admin_page_length') ?>";

const today = new Date();

$(function(){
    $(".addDOB").datepicker({
        endDate:today,
        todayHighlight:true,
        clearBtn:true,
        autoclose:true,
        format:'dd-mm-yyyy'
    }).datepicker('setDate',today);

    $(".editDOB").datepicker({
        endDate:today,
        todayHighlight:true,
        clearBtn:true,
        autoclose:true,
        format:'dd-mm-yyyy'
    });
});

CKEDITOR.replace('content',{
    customConfig : 'config.js',
    toolbar : 'simple'
});

$(function () {
    //Initialize Select2 Elements
    $('.select2').select2()
    CKEDITOR.replace('editor1')
});

$.validator.addMethod(
    "regex",
    function(value, element, regexp) {
        return this.optional(element) || regexp.test(value);
    },
    "Please check your input."
);

$.validator.addMethod("validate_name", function(value, element) {
    console.log(element);
    if (/^[a-zA-Z\s]*$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, "Field should not contain numbers or special characters");

$.validator.addMethod("validate_email", function(value, element) {
    // if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
    if (/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, "Please enter a valid email address.");

$("input").on("keypress", function(e) {
    if (e.which === 32 && !this.value.length)
        e.preventDefault();
});
$("textarea").on("keypress", function(e) {
    if (e.which === 32 && !this.value.length)
        e.preventDefault();
});

$('.checknumber').bind('keyup paste ', function(){
    this.value = this.value.replace(/[^0-9]/g, '');
});

</script>