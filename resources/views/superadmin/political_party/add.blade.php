@extends('layouts.superadmin')
@section('content')
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Add Political Party</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.list_political_party') }}">Political Party Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Political Party</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['superadmin.store_political_party'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'addPoliticalPartyForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
                <div class="box-row flex-wrap comman-form-design">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Party Name<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('party_name', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Party Name', 'maxlength'=>100]) !!}
                                <span class="help-block">
                                    <?= $errors->has('party_name') ? $errors->first('party_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Short Name</label>
                            <div class="">
                                {!! Form::text('short_name', null, ['class'=>'form-control chkAlphabets','placeholder'=>'Short Name', 'maxlength'=>100]) !!}
                                <span class="help-block">
                                    <?= $errors->has('short_name') ? $errors->first('short_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Logo<span class="required">*</span></label>
                            <div class="upload-img">
                                {!! Form::file('logo', ['accept'=>'image/*', 'onchange' =>'previewImage(event, "logo")'], ['class'=>'form-control chkAlphabets']) !!}
                                <img id="preview_logo" src="#" alt="Preview Image" style="display:none; max-width: 100px; max-height: 100px; margin-top: 10px;">
                                <span class="help-block">
                                    <?= $errors->has('logo') ? $errors->first('logo') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Party Slogan</label>
                            <div class="">
                                {!! Form::text('party_slogan', null, ['class'=>'form-control','placeholder'=>'Party Slogan', 'maxlength'=>100]) !!}
                                <span class="help-block">
                                    <?= $errors->has('party_slogan') ? $errors->first('party_slogan') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <hr>
                        <label>ADMIN DETAILS</label>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>National Id<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('national_id', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'National Id', 'id'=>'national_id', 'maxlength'=>100, 'onkeyup' => "getNationalIdData()"]) !!}
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
                                {!! Form::text('first_name', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'First Name', 'id'=>'first_name', 'maxlength'=>100]) !!}
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
                                {!! Form::text('last_name', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Last Name', 'id'=>'last_name', 'maxlength'=>100]) !!}
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
                                {!! Form::email('email',null, array('required'=>'required', 'class' => 'form-control','placeholder'=>'Email', 'maxlength'=>100)) !!}
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
                            <div class="upload-img">
                                {!! Form::file('national_id_image', ['accept'=>'image/*', 'onchange' =>'previewImage(event, "nationalIdImage")'], ['class'=>'form-control chkAlphabets']) !!}
                                <img id="preview_national_id" src="#" alt="Preview Image" style="display:none; max-width: 100px; max-height: 100px; margin-top: 10px;">
                                <span class="help-block">
                                    <?= $errors->has('national_id_image') ? $errors->first('national_id_image') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 text-center">
                        <a href="{{ route('superadmin.list_political_party') }}" class="btn btn-primary">Cancel</a>
                        <a href="javascript:void(0);" onclick="addPoliticalPartyForm();" class="btn btn-primary ">Submit</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@push('current-page-js')
@include('commonadmin.fetchNationalIdDataJs')
@include('commonadmin.countryStateCityFieldsJs')
<script type="text/javascript">
$(document).ready(function(){
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

function previewImage(event, imageType) {
    var input = event.target;
    if(imageType == 'logo'){
        var preview = document.getElementById('preview_logo');
    }else{
        var preview = document.getElementById('preview_national_id');
    }

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            preview.setAttribute('src', e.target.result);
            preview.style.display = 'block';
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#addPoliticalPartyForm").validate({
    rules: {
        party_name: {
            required: true,
            maxlength: 100,
        },
        short_name: {
            required: false,
            maxlength: 100,
        },
        logo: {
            required: true,
        },
        party_slogan: {
            required: false,
            maxlength: 100,
        },
        national_id: {
            required: true,
            maxlength: 100,
        },
        first_name: {
            required: true,
            maxlength: 100,
            validate_name: true,
        },
        last_name: {
            required: true,
            maxlength: 100,
            validate_name: true,
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
            required: true,
        }
        /*password: {
            required: true,
            minlength: 6,
            maxlength: 11,
        },*/
    },
    messages:{
        first_name:{
            validate_name: 'First Name should not contain numbers or special characters',
        },
        last_name:{
            validate_name: 'Last Name should not contain numbers or special characters',
        }
    }
});

function addPoliticalPartyForm(){
    // show loader
    showCustomBlockUI();
    if($("#addPoliticalPartyForm").valid()){
        if(isNationalIdVerified == 1){
            $("#addPoliticalPartyForm").submit();
        }else{
            Lobibox.notify('error', {
                icon:false,
                msg: "National id not valid"
            });
            // hide loader
            hideCustomBlockUI();
        }
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush