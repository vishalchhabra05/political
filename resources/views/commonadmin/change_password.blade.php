@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">Change Password</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div> 
      <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-12 col-md-4 mb-4">
                    <form class="box bg-white" method="POST" id ="changePasswordForm" action="{{ route('save_change_password')}}">
                        <input type="hidden" name="change_id" value="{{ $entity->id }}">
                        @csrf
                        <div class="box-row flex-wrap">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <div class="input-group">
                                        <input type="password" minlength="6" maxlength="11" class="form-control" name="current_password" id="current_password" autocomplete="off" placeholder="Current Password">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>New Password</label>
                                    <div class="input-group">
                                        <input type="password" minlength="6" maxlength="11" class="form-control" name="new_password" id="new_password" placeholder="New Password">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <div class="input-group">
                                       <input type="password" minlength="6" maxlength="11" class="form-control" name="confirm_password" autocomplete="off" placeholder="Confirm New Password">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 text-center">
                                <a href="{{ route('dashboard') }}" class="btn light">Cancel</a>
                                <a href="javascript:void(0);" onclick="changePwdForm();" class="btn light">Submit</a>
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
        </div> 
@endsection
@push('current-page-js')
<script type="text/javascript">
$.validator.addMethod("notEqual", function (value, element, param) {
    return this.optional(element) || value != param;
}, "New password cannot be same as current password");

$("#changePasswordForm").validate({
    rules: {
        current_password: {
            required: true,
            minlength:6,
            maxlength:11
        },
        new_password:{
            required: true,
            minlength:6,
            maxlength:11,
            notEqual : function (){
                return $("#current_password").val()
            }
        },
        confirm_password:{
            required:true,
            minlength:6,
            maxlength:11,
            equalTo: "#new_password" 
        },
    },
    messages:{
        current_password:{
            required:'Current Password is required',
        },
        new_password:{
            required:'New Password is Required',
        },
        confirm_password:{
           required: 'Confirm New Password is Required',
           equalTo:'Confirm New Password should be same as New Password'
        },
    },
});

function changePwdForm(){
    if($("#changePasswordForm").valid()){
        $("#changePasswordForm").submit();
    }
}
</script>    
@endpush 

