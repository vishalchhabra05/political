@extends('layouts.super_admin_login')
@push('current-page-css')
<style type="text/css">
    input.error {
        margin-top: 0px !important;
    }
</style>
@endpush
@section('content')
    <div class="login-page">
        <div class="login-box password-page">
            <div class="contentBox">
                <div class="logo d-flex flex-wrap w-100">
                    <img src="{{ asset('images/political-party.png') }}" alt="logo">
                </div>
                <h1>Reset Password</h1>
                @include('flash-message')
                <form class="mt-5" method="POST" id="resetPwdForm" action="{{ route('admin.reset',$token) }}">
                    @csrf
                    <div class="form-group">
                        <label>New Password</label>
                        <div class="input-group password-box">
                            <input type="password" class="form-control" name="new_password" id="new_password" placeholder="New Password">
                                 <div class="input-group-prepend">
                                <!-- <span class="input-group-text"><i class="fal fa-envelope"></i></span> -->
                                <span class="input-group-text">
                                    <a href="javascript:;" class="toggle-password">
                                        <i class="fas fa-eye-slash" aria-hidden="true"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="input-group password-box">
                            <!-- <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fal fa-envelope"></i></span>
                            </div> -->
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
                               <div class="input-group-prepend">
                                   <span class="input-group-text">
                                <a href="javascript:;" class="toggle-password2">
                                    <i class="fas fa-eye-slash" aria-hidden="true"></i>
                                </a>
                            </span>
                               </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <a href="javascript:void(0);" onclick="resetPwdForm();" class="btn w-100 light">Submit</a>
                    </div>
                    <div class="text-center">
                        <p>Back to <a href="{{ route('admin.home') }}">Login</a></p>
                    </div>
                </form>
            </div>
            <div class="imgBox d-none d-md-block">
                <img src="{{ asset('images/login.jpg') }}" alt="logo">
            </div>
        </div>
    </div>
@endsection
@push('current-page-js')
<script type="text/javascript">

$("body").on('click', '.toggle-password', function() {
    $(".toggle-password>i").toggleClass("fa-eye fa-eye-slash");
      var input = $("#new_password");
    if (input.attr("type") === "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

$("body").on('click', '.toggle-password2', function() {
    $(".toggle-password2>i").toggleClass("fa-eye fa-eye-slash");
    var input = $("#confirm_password");
    if (input.attr("type") === "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

$("#resetPwdForm").validate({
    rules: {
        new_password:{
            required: true,
            minlength:6,
            maxlength:11,
        },
        confirm_password:{
            required:true,
            minlength:6,
            maxlength:11,
            equalTo: "#new_password"
        },
    },
    messages:{
        new_password:{
            required:'New Password is required',
        },
        confirm_password:{
           required: 'Confirm Password is required',
           equalTo:'Confirm Password should be same as New Password'
       },
    },
});

function resetPwdForm(){
    // show loader
    showCustomBlockUI();
    if($("#resetPwdForm").valid()){
        $("#resetPwdForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush
