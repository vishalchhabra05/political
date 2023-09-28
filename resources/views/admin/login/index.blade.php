@extends('layouts.super_admin_login')
@section('content')
    <div class="login-page">
        <div class="login-box password-page">
            <div class="contentBox">
                <div class="logo d-flex flex-wrap w-100">
                    <img src="{{ asset('images/political-party.png') }}" alt="logo">
                </div>
                <h1>Welcome!</h1>
                <p>Enter your email address and password to access admin panel.</p>
                @include('flash-message')
                <form class="mt-4" method="POST" action="{{ route('admin.login') }}" id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="input-group password-box">
                            <input type="text" name="email" class="form-control" autocomplete="off" placeholder="Email Address">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fal fa-envelope"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password
                            <!-- <a class="float-right" href="{{ route('admin.forgot_password') }}">Forgot your password?</a> -->
                        </label>
                        <div class="input-group password-box">
                            <!-- <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fal fa-lock"></i></span>
                            </div> -->
                            <input type="password" name="password" id="password" class="form-control" autocomplete="off" placeholder="Password">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <a href="javascript:;" class="toggle-password">
                                        <i class="fas fa-eye-slash" aria-hidden="true"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">Check me out</label>
                        </div>
                    </div> -->
                    <div class="form-group mb-0">
                        <button type="submit" class="btn w-100 light">Login</button>
                    </div>
                    @if (Route::has('admin.forgot_password'))
                        <a class="btn btn-link" href="{{ route('admin.forgot_password') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </form>
            </div>
            <div class="imgBox d-none d-md-block">
                <img src="{{ asset('images/login.jpg') }}" alt="image">
            </div>
        </div>
    </div>
@endsection
@push('current-page-js')
<script type="text/javascript">

$("body").on('click', '.toggle-password', function() {
    $(".toggle-password>i").toggleClass("fa-eye fa-eye-slash");
      var input = $("#password");
    if (input.attr("type") === "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

$(document).ready(function(){
    jQuery.validator.addMethod("validate_email", function(value, element) {
        if (/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Please enter a valid email address.");
});

$("#loginForm").validate({
    rules: {
        email: {
            required: true,
            maxlength: 100,
            validate_email: true,
        },
        password: {
            required: true,
        }
    },
    messages:{
        email:{
            required: 'Email is required',
        },
        password:{
            required: 'Password is required',
        }
    },
    errorPlacement: function(error, element) {
        // Insert the error message after the input element
        error.insertAfter(element);
    },
});
</script>
@endpush