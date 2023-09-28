@extends('layouts.super_admin_login')
@section('content')
    <div class="login-page">
        <div class="login-box">
            <div class="contentBox">
                <div class="logo d-flex flex-wrap w-100">
                    <img src="{{ asset('images/political-party.png') }}" alt="logo">
                </div>
                <h1>Reset Password</h1>
                <p>Enter your email address and we'll send you an email with instructions to reset your password.</p>
                @include('flash-message')
                <form class="mt-5" method="POST" action="{{ route('admin.send_verification_email') }}" id="resetPwdForm">
                    @csrf
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fal fa-envelope"></i></span>
                            </div>
                            <input type="text" class="form-control" name="email" placeholder="Email Address">
                        </div>
                        <span class="help-block">
                            <?= $errors->has('email') ? $errors->first('email') : '' ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <a href="javascript:void(0);" onclick="resetPwdForm();" class="btn w-100 light">Submit</a>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('admin.home') }}">Back to Login</a>
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
$("#addPartyWallForm").validate({
    rules: {
        email: {
            required: true,
        }
    },
    messages:{
        
    }
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