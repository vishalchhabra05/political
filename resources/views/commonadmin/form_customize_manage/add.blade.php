@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<?php
    if($formType == 'register'){
        $pageTitleName = "Member Register Form";
    }elseif($formType == 'profile'){
        $pageTitleName = "User Profile Form";
    }else{
        $pageTitleName = "Survey Form";
    }
?>
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Add Custom Field</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list_form_customization', ['formType' => $formType, 'formId' => $formId]) }}">{{$pageTitleName}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Custom Field</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['store_form_customization'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'addPoliticalPartyCustomForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
                <div class="box-row flex-wrap comman-form-design">
                    <!-- <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Select Political Party<span class="required">*</span></label>
                            <div class="">
                                @if(empty($formId))
                                    {!! Form::select('political_party', (!empty($politicalParties)?$politicalParties:[]), null, ['class' => 'form-control','id'=>'political_party', 'placeholder'=> 'Select Political Party']) !!}
                                    <span class="help-block">
                                        <?= $errors->has('political_party') ? $errors->first('political_party') : '' ?>
                                    </span>
                                @else
                                    <!-- In case of survey -->
                                    <!--<input type="hidden" name="political_party" value="{{$entity->political_party}}">
                                    {!! Form::text('politicalPartyName', null, ['id'=>'politicalPartyName', 'class'=>'form-control chkAlphabets', 'readonly'=>'true','placeholder'=>'Political Party Name']) !!}
                                @endif
                            </div>
                        </div>
                    </div> -->
                    <input type="hidden" name="form_type" value="{{$formType}}">
                    <input type="hidden" name="formId" value="{{$formId}}">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Field Name<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('field_name', null, ['id'=>'field_name', 'required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Field Name', 'maxlength'=>50]) !!}
                                <span class="help-block">
                                    <?= $errors->has('field_name') ? $errors->first('field_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Es Field Name<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('es_field_name', null, ['id'=>'es_field_name', 'required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Es Field Name', 'maxlength'=>50]) !!}
                                <span class="help-block">
                                    <?= $errors->has('es_field_name') ? $errors->first('es_field_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($formType == 'profile')
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label>Tab Type<span class="required">*</span></label>
                                <div class="">
                                    {!! Form::select('tab_type', (!empty($customFieldTabs)?$customFieldTabs:[]), null, ['class' => 'form-control','id'=>'tab_type', 'placeholder'=> 'Select']) !!}
                                    <span class="help-block">
                                        <?= $errors->has('tab_type') ? $errors->first('tab_type') : '' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Is Required<span class="required">*</span></label>
                            <div class="">
                                {!! Form::select('is_required', (!empty($isRequiredTypes)?$isRequiredTypes:[]), null, ['class' => 'form-control','id'=>'is_required', 'placeholder'=> 'Select']) !!}
                                <span class="help-block">
                                    <?= $errors->has('is_required') ? $errors->first('is_required') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Select Field Type</label>
                            <div class="">
                                {!! Form::select('field_type', (!empty($fieldTypes)?$fieldTypes:[]), null, ['class' => 'form-control','id'=>'field_type', 'placeholder'=> 'Select Field Type']) !!}
                                <span class="help-block">
                                    <?= $errors->has('field_type') ? $errors->first('field_type') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" id="fieldMinLngthDiv" style="display:none;">
                        <div class="form-group">
                            <label>Field Minimum Length</label>
                            <div class="">
                                {!! Form::number('field_min_length', null, ['id'=>'field_min_length', 'class'=>'form-control chkNumber', 'placeholder'=>'Field Minimum Length']) !!}
                                <span class="help-block">
                                    <?= $errors->has('field_min_length') ? $errors->first('field_min_length') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" id="fieldMaxLngthDiv" style="display:none;">
                        <div class="form-group">
                            <label>Field Maximum Length</label>
                            <div class="">
                                {!! Form::number('field_max_length', null, ['id'=>'field_max_length', 'class'=>'form-control chkNumber', 'placeholder'=>'Field Maximum Length']) !!}
                                <span class="help-block">
                                    <?= $errors->has('field_max_length') ? $errors->first('field_max_length') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" id="fieldDecPointDiv" style="display:none;">
                        <div class="form-group">
                            <label>Decimal Points</label>
                            <div class="">
                                {!! Form::number('decimal_points', null, ['id'=>'decimal_points', 'class'=>'form-control chkNumber', 'placeholder'=>'Decimal Points']) !!}
                                <span class="help-block">
                                    <?= $errors->has('decimal_points') ? $errors->first('decimal_points') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" id="fieldOptionDiv" style="display:none;">
                        <div class="form-group">
                            <label>Field Options (Add comma separated options)</label>
                            <div class="">
                                {!! Form::text('field_options', null, ['id'=>'field_options', 'class'=>'form-control', 'placeholder'=>'Field Options Ex - "Yes, No, None"']) !!}
                                <span class="help-block">
                                    <?= $errors->has('field_options') ? $errors->first('field_options') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 text-center">
                        <a href="{{ route('list_form_customization',['formType' => $formType, 'formId' => $formId]) }}" class="btn btn-primary ">Cancel</a>
                        <a href="javascript:void(0);" onclick="addPoliticalPartyCustomForm();" class="btn btn-primary ">Submit</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@push('current-page-js')
<script type="text/javascript">
$(document).ready(function(){
    $.validator.addMethod("validate_name", function(value, element) {
        if (/^[a-zA-Z\s]*$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Field should not contain numbers or special characters");

    $.validator.addMethod("greaterThan", function(value, element, param) {
        var $otherElement = $(param);
        if($otherElement.val()){
            return parseFloat(value) > parseFloat($otherElement.val());
        }else{
            return true;
        }
    }, "The \"Field Max Length\" must be greater than the \"Field Min Length\" field.");
});
$("#addPoliticalPartyCustomForm").validate({
    rules: {
        /*political_party: {
            required: true,
        },*/
        form_type: {
            required: true,
        },
        field_name: {
            required: true,
            // validate_name: true,
        },
        es_field_name: {
            required: true,
        },
        tab_type: {
            required: true,
        },
        is_required: {
            required: true,
        },
        field_type: {
            required: true,
        },
        field_min_length: {
            required: false,
        },
        field_max_length: {
            required: false,
            greaterThan: "#field_min_length",
        },
        decimal_points: {
            required: false,
        },
        field_options: {
            required: false,
        }
    },
    messages:{
        /*political_party:{
            required: 'Political party is required',
        },*/
        form_type:{
            required: 'Form type is required',
        },
        field_name:{
            required: 'Field name is required',
            validate_name: 'Field name should not contain numbers or special characters',
        },
        es_field_name:{
            required: 'Spanish field name is required',
        },
        field_type:{
            required: 'Field type is required',
        },
        field_options:{
            required: 'Field options are required',
        }
    }
});

// After error show/hide related fields
var oldFieldType = '{{old("field_type")}}';
if(oldFieldType){
    fieldShowHide($("#field_type").val());
}
/******************/

$('#field_type').on('change', function() {
    fieldShowHide(this.value);
});

function fieldShowHide(element){
    if(element == "checkbox" || element == "radio" || element == "dropdown"){
        $("#fieldOptionDiv").css("display", "block");
        $('#field_options').rules('add', {
          required: true
        });
    }else{
        $("#fieldOptionDiv").css("display", "none");
        $('#field_options').rules('add', {
          required: false
        });
    }

    if(element == "text" || element == "number" || element == "textarea"){
        $("#fieldMinLngthDiv").css("display", "block");
        $("#fieldMaxLngthDiv").css("display", "block");
    }else{
        $("#fieldMinLngthDiv").css("display", "none");
        $("#fieldMaxLngthDiv").css("display", "none");
    }

    if(element == "text"){
        $("#fieldDecPointDiv").css("display", "block");
    }else{
        $("#fieldDecPointDiv").css("display", "none");
    }
}

function addPoliticalPartyCustomForm(){
    // show loader
    showCustomBlockUI();
    if($("#addPoliticalPartyCustomForm").valid()){
        $("#addPoliticalPartyCustomForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush