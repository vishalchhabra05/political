@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3 m-0">Edit Sub Admin</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list_sub_admin') }}">Sub Admin Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Sub Admin</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['update_sub_admin'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editSubAdminForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
            <input type="hidden" name="update_id" value="{{ $entity->id }}">
                <div class="box-row flex-wrap comman-form-design">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>National Id<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('national_id', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'National Id', 'maxlength'=>100, 'readonly'=>true]) !!}
                                <span class="help-block">
                                    <?= $errors->has('national_id') ? $errors->first('national_id') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>First Name<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('first_name', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'First Name', 'maxlength'=>100, 'readonly'=>true]) !!}
                                <span class="help-block">
                                    <?= $errors->has('first_name') ? $errors->first('first_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Last Name<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('last_name', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Last Name', 'maxlength'=>100, 'readonly'=>true]) !!}
                                <span class="help-block">
                                    <?= $errors->has('last_name') ? $errors->first('last_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    @include('commonadmin.countryStateCityFields')
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Email<span class="required">*</span></label>
                            <div class="">
                                {!! Form::email('email', null, ['required'=>'required','class'=>'form-control','placeholder'=>'Email', 'maxlength'=>100]) !!}
                                <span class="help-block">
                                    <?= $errors->has('email') ? $errors->first('email') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Phone Number<span class="required">*</span></label>
                            <div class="phone-number-country-code">
                                <div class="country-code-section">
                                    {!! Form::select('country_code', (!empty($countryCodes)?$countryCodes:[]), null, ['class' => 'form-control','id'=>'country_code', 'placeholder'=> 'Select Country Code']) !!}
                                </div>
                                <div class="phone-number-section">
                                    {!! Form::text('phone_number', null, ['required'=>'required','class'=>'form-control chkNumber', 'placeholder'=>'Phone Number', 'maxlength'=>16,'minlength' =>9]) !!}
                                </div>
                                <span class="help-block">
                                    <?= $errors->has('phone_number') ? $errors->first('phone_number') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Alternate Phone Number</label>
                            <div class="phone-number-country-code">
                                <div class="country-code-section">
                                    {!! Form::select('alt_country_code', (!empty($countryCodes)?$countryCodes:[]), null, ['class' => 'form-control','id'=>'alt_country_code', 'placeholder'=> 'Select Country Code']) !!}
                                </div>
                                <div class="phone-number-section">
                                    {!! Form::text('alternate_phone_number', null, ['required'=>'required','class'=>'form-control chkNumber', 'placeholder'=>'Alternate Phone Number', 'maxlength'=>16,'minlength' =>9]) !!}
                                </div>
                                <span class="help-block">
                                    <?= $errors->has('alternate_phone_number') ? $errors->first('alternate_phone_number') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>National Id Image<span class="required">*</span></label>
                            <div class="upload-file-img">
                                {!! Form::file('national_id_image', ['accept'=>'image/*', 'onchange' =>'previewImage(event)'], ['class'=>'form-control chkAlphabets']) !!}
                                <img id="preview" src="#" alt="Preview Image" style="display:none; max-width: 100px; max-height: 100px; margin-top: 10px;">
                                <span class="help-block">
                                    <?= $errors->has('national_id_image') ? $errors->first('national_id_image') : '' ?>
                                </span>
                                @if($entity->national_id_image)
                                    <img class="profile-user-img img-responsive img-circle" src="{{$entity->national_id_image}}" alt="National id image" id="previewOldImg" width="100" style="padding-top: 10px;">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Permission</label>
                            <div class="multi-select-input">
                                @php $allPermissions = getpermission(); @endphp
                                <select class="select2-multiple form-control" name="permission[]" multiple="multiple"
                                    id="selectpermission">
                                    @foreach ($allPermissions as $key=>$value)
                                    <option value="{{ $key }}" @if(in_array($key, $permission) ) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 text-center">
                        <a href="{{ route('list_sub_admin') }}" class="btn btn-primary">Cancel</a>
                        <a href="javascript:void(0);" onclick="editSubAdminForm();" class="btn btn-primary">Submit</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@push('current-page-js')
@include('commonadmin.countryStateCityFieldsJs')
<script type="text/javascript">
$(document).ready(function() {
    getStates();
    getCities();
    
    $("#selectpermission").select2({
        placeholder: "Select Permission",
        allowClear: true
    });

    $.validator.addMethod("validate_name", function(value, element) {
        if (/^[a-zA-Z\s]*$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Field should not contain numbers or special characters");

    jQuery.validator.addMethod("validate_email", function(value, element) {
        if (/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Please enter a valid email address.");
});

function previewImage(event) {
    var input = event.target;
    var preview = document.getElementById('preview');

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            preview.setAttribute('src', e.target.result);
            preview.style.display = 'block';
        }

        reader.readAsDataURL(input.files[0]);
    }
    $("#previewOldImg").css("display", "none");
}

$("#editSubAdminForm").validate({
    rules: {
        national_id: {
            required: true,
            maxlength: 100,
        },
        first_name: {
            required: true,
            maxlength: 100,
            // validate_name: true,
        },
        last_name: {
            required: true,
            maxlength: 100,
            // validate_name: true,
        },
        country_id: {
            required: true,
        },
        state_id: {
            required: true,
        },
        city_id: {
            required: true,
        },
        email: {
            required: true,
            maxlength: 100,
            validate_email: true,
        },
        country_code: {
            required: true,
        },
        phone_number: {
            required: true,
            minlength: 9,
            maxlength: 16,
        },
        alt_country_code: {
            required: false,
        },
        alternate_phone_number: {
            required: false,
            minlength: 9,
            maxlength: 16,
        },
        national_id_image: {
            required: false,
        },
        "permission[]": {
            required: true 
        },
    },
    messages:{
        /*full_name:{
            validate_name: 'Full Name should not contain numbers or special characters',
        }*/
    },
    errorPlacement: function(error, element){
        if(element.hasClass("select2-hidden-accessible") && element.attr("multiple")) {
            // Multiselect Select2 dropdown
            error.insertAfter(element.next(".select2-container"));
        }else{
            error.insertAfter(element);
        }
    }
});

function editSubAdminForm(){
    // show loader
    showCustomBlockUI();
    if($("#editSubAdminForm").valid()){
        $("#editSubAdminForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush