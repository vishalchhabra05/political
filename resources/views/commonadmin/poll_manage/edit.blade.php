@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<?php
        $pageTitleName = "Poll Management";
?>
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Edit Poll</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list_poll') }}">{{$pageTitleName}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Poll</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['update_poll'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editPollForm', 'autocomplete'=>'off']) !!}
            {{ csrf_field() }}
            <input type="hidden" name="update_id" value="{{ $entity->id }}">
                <div class="box-row flex-wrap comman-form-design">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Poll Name<span class="required">*</span></label>
                            <div class="">
                                @if($entity->poll_type=='opinion')
                                     {!! Form::text('poll_name', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Poll Name', 'maxlength'=>500]) !!}
                                @else
                                {!! Form::text('poll_name', null, ['class'=>'form-control chkAlphabets','placeholder'=>'Poll Name', 'value'=>$entity->poll_name,'readonly'=>'readonly']) !!}
                                @endif
                                <span class="help-block">
                                    <?= $errors->has('poll_name') ? $errors->first('poll_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Poll Question<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('question', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Post Question', 'maxlength'=>500]) !!}
                                <span class="help-block">
                                    <?= $errors->has('poll_question') ? $errors->first('poll_question') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>From Date<span class="required">*</span></label>
                            <div class="">
                                {!! Form::date('start_date', null, ['required'=>'required','class'=>'form-control chkAlphabets', 'id'=>'start_date']) !!}
                                <span class="help-block">
                                    <?= $errors->has('start_date') ? $errors->first('start_date') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>To Date<span class="required">*</span></label>
                            <div class="">
                                {!! Form::date('expiry_date', null, ['required'=>'required','class'=>'form-control chkAlphabets', 'id'=>'expiry_date']) !!}
                                <span class="help-block">
                                    <?= $errors->has('expiry_date') ? $errors->first('expiry_date') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" id="fieldOptionDiv">
                        <div class="form-group">
                            <label>Poll Options (Add comma separated options)<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('poll_options', null, ['id'=>'poll_options', 'class'=>'form-control', 'placeholder'=>'Poll Options Ex - "Yes, No"']) !!}
                                <span class="help-block">
                                    <?= $errors->has('poll_options') ? $errors->first('poll_options') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    @include('commonadmin.electoral_demographic_fields')
                    <div class="col-md-12 mb-3 text-center">
                        <a href="{{ route('list_poll') }}" class="btn btn-primary">Cancel</a>
                        <a href="javascript:void(0);" onclick="editPollForm();" class="btn btn-primary">Submit</a>
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
function getTodayDate(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
    dd = '0' + dd;
    }

    if (mm < 10) {
    mm = '0' + mm;
    }

    today = yyyy + '-' + mm + '-' + dd;
    return today;
}

<?php if(!empty($entity->id) && !empty($entity->start_date)){ ?>
   // document.getElementById("start_date").setAttribute("min", "{{$entity->start_date}}");
    document.getElementById("start_date").setAttribute("min", getTodayDate());
    document.getElementById("expiry_date").setAttribute("min", "{{$entity->start_date}}");
<?php }else{ ?>
    document.getElementById("start_date").setAttribute("min", getTodayDate());
    document.getElementById("expiry_date").setAttribute("min", getTodayDate());
<?php } ?>

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
});

$("#editPollForm").validate({
    rules: {
        poll_name: {
            required: true,
            maxlength: 500,
        },
        question: {
            required: true,
        },
        poll_options: {
            required: true,
        },
        'country_id[]': {
            required: true
        }
    },
    messages:{
        
    }
});

function editPollForm(){
    // show loader
    showCustomBlockUI();
    if($("#editPollForm").valid()){
        $("#editPollForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush