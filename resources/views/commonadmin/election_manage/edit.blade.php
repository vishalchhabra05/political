@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<?php $pageTitleName = "Election Management"; ?>
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Edit Election</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list_elections') }}">{{$pageTitleName}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Election</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['update_elections'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editElectionForm', 'autocomplete'=>'off']) !!}
            {{ csrf_field() }}
            <input type="hidden" name="update_id" value="{{ $entity->id }}">
                <div class="box-row flex-wrap comman-form-design">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Election Name<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('election_name', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Election Name', 'maxlength'=>500]) !!}
                                <span class="help-block">
                                    <?= $errors->has('election_name') ? $errors->first('election_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php $todayDate = date('Y-m-d'); ?>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Start Date<span class="required">*</span></label>
                            <div class="">
                                {!! Form::date('start_date', null, ['required'=>'required','class'=>'form-control chkAlphabets', 'id'=>'start_date', 'min'=>$todayDate]) !!}
                                <span class="help-block">
                                    <?= $errors->has('start_date') ? $errors->first('start_date') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>End Date<span class="required">*</span></label>
                            <div class="">
                                {!! Form::date('end_date', null, ['required'=>'required','class'=>'form-control chkAlphabets', 'id'=>'end_date', 'min'=>$todayDate]) !!}
                                <span class="help-block">
                                    <?= $errors->has('end_date') ? $errors->first('end_date') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    @include('commonadmin.electoral_demographic_fields')
                    <div class="col-md-12 mb-3 text-left">
                        <a href="{{ route('list_elections') }}" class="btn btn-primary">Cancel</a>
                        <a href="javascript:void(0);" onclick="editElectionForm();" class="btn btn-primary">Submit</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@push('current-page-js')
@include('commonadmin.electoral_demographic_fields_js')
<script type="text/javascript">
$(document).ready(function(){
    $("#country_label").html('Country<span class="required">*</span>');
    <?php if(!empty($entity->demographicInfo->country_id)){ ?>
        var countryIds = "{{$entity->demographicInfo->country_id}}"; // Assuming it's a string like "2,3,5"
        var countryIdArray = countryIds.split(","); // Split the string into an array

        $("#country-dd").val(countryIdArray);
        $("#country-dd").trigger('change');
    <?php } ?>

    <?php if(!empty($entity->demographicInfo->neighbourhood_id)){ ?>
        var neighbourhoodIds = "{{$entity->demographicInfo->neighbourhood_id}}"; // Assuming it's a string like "2,3,5"
        var neighbourhoodIdArray = neighbourhoodIds.split(","); // Split the string into an array

        $("#neighbourhood-dd").val(neighbourhoodIdArray);
        $("#neighbourhood-dd").trigger('change');
    <?php } ?>

    $.validator.addMethod("validate_name", function(value, element) {
        if (/^[a-zA-Z\s]*$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Field should not contain numbers or special characters");

    $.validator.addMethod("greaterThanCurrentDate", function(value, element) {
        var currentDate = new Date();
        currentDate.setHours(0, 0, 0, 0); // Set time to midnight (00:00:00)
        
        var inputDate = new Date(value.replace(/-/g, '/'));
        inputDate.setHours(0, 0, 0, 0); // Set time to midnight (00:00:00)
        return inputDate >= currentDate;
    }, "Date must be greater than or equal to current date.");

    // Add a custom validation method
    $.validator.addMethod("greaterThanOrEqual", function(value, element, param) {
        var startDate = $(param).val();
        if (!value || !startDate) {
            // If either date is not provided, consider it valid
            return true;
        }
        
        return new Date(value) >= new Date(startDate);
    }, "End date must be greater than or equal to start date.");
});

$("#editElectionForm").validate({
    rules: {
        election_name: {
            required: true,
            maxlength: 500,
        },
        start_date: {
            required: true,
            date: true,
            greaterThanCurrentDate: true
        },
        end_date: {
            required: true,
            date: true,
            greaterThanOrEqual: "#start_date" // Specify the start date input field as the parameter
        },
        'country_id[]': {
            required: true
        }
    },
    messages:{
        election_name:{
            required: 'Election name is required',
        },
        start_date:{
            required: 'Start date is required',
        },
        end_date:{
            required: 'End date is required',
        },
    }
});

function editElectionForm(){
    // show loader
    showCustomBlockUI();
    if($("#editElectionForm").valid()){
        $("#editElectionForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush