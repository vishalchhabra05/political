@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<?php
    if($formType == 'Partywall'){
        $pageTitleName = "Party Wall Management";
    }elseif($formType == 'News'){
        $pageTitleName = "Article/News Management";
    }else{
        $pageTitleName = "Post Management";
    }
?>
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Add Post</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list_party_wall', ['formType' => $formType]) }}">{{$pageTitleName}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Post</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['store_party_wall'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'addPartyWallForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
                <div class="box-row flex-wrap comman-form-design">
                    @if($formType == 'News')
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Select Category<span class="required">*</span></label>
                            <div class="">
                                {!! Form::select('category_id', (!empty($categories)?$categories:[]), null, ['class' => 'form-control','id'=>'category_id', 'placeholder'=> 'Select Post Category']) !!}
                                <span class="help-block">
                                    <?= $errors->has('category_id') ? $errors->first('category_id') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Post Heading<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('post_heading', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Post Heading', 'maxlength'=>500]) !!}
                                <span class="help-block">
                                    <?= $errors->has('post_heading') ? $errors->first('post_heading') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>From Date<span class="required">*</span></label>
                            <div class="">
                                @if($formType=='Partywall')
                                    {!! Form::date('from_date', null, ['required'=>'required','class'=>'form-control chkAlphabets', 'id'=>'from_date']) !!}
                                @else
                                    {!! Form::date('from_date', null, ['required'=>'required','class'=>'form-control chkAlphabets', 'id'=>'from_date']) !!}
                                @endif
                                <span class="help-block">
                                    <?= $errors->has('from_date') ? $errors->first('from_date') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>To Date</label>
                            <div class="">
                                @if($formType=='Partywall')
                                    {!! Form::date('to_date', null, ['class'=>'form-control chkAlphabets', 'id'=>'to_date']) !!}
                                @else
                                    {!! Form::date('to_date', null, ['class'=>'form-control chkAlphabets', 'id'=>'to_date']) !!}
                                @endif
                                <span class="help-block">
                                    <?= $errors->has('to_date') ? $errors->first('to_date') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Post Description<span class="required">*</span></label>
                            <div class="">
                                {!! Form::textarea('post_description', null, ['required'=>'required','class'=>'form-control','placeholder'=>'Description', 'maxlength'=>2000]) !!}
                                <span class="help-block">
                                    <?= $errors->has('post_description') ? $errors->first('post_description') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="form_type" value="{{$formType}}">
                    <input type="hidden" name="posted_date_time" id="posted_date_time" value="">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Post Image<span class="required">*</span> (Max 5MB file can be uploaded)</label>
                            <div class="upload-img">
                                {!! Form::file('post_image', ['accept'=>'image/*', 'onchange' =>'previewImage(event)'], ['class'=>'form-control chkAlphabets']) !!}
                                <img id="preview" src="#" alt="Preview Image" style="display:none; max-width: 100px; max-height: 100px; margin-top: 10px;">
                                <span class="help-block">
                                    <?= $errors->has('post_image') ? $errors->first('post_image') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($formType == 'News')
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Post Video (Max 15MB file can be uploaded)</label>
                            <div class="upload-img">
                                {!! Form::file('post_video', ['accept'=>'video/mp4,video/mov,video/webm'], ['class'=>'form-control chkAlphabets']) !!}
                                <span class="help-block">
                                    <?= $errors->has('post_video') ? $errors->first('post_video') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-12 mb-3 text-center">
                        <a href="{{ route('list_party_wall',['formType' => $formType]) }}" class="btn btn-primary">Cancel</a>
                        <a href="javascript:void(0);" onclick="addPartyWallForm();" class="btn btn-primary ">Submit</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@push('current-page-js')
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

<?php if(empty($entity->id)){ ?>
    document.getElementById("from_date").setAttribute("min", getTodayDate());
    document.getElementById("to_date").setAttribute("min", getTodayDate());

    document.getElementById("from_date").value = getTodayDate();
<?php } ?>

$(document).ready(function(){
    $.validator.addMethod("validate_name", function(value, element) {
        if (/^[a-zA-Z\s]*$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Field should not contain numbers or special characters");

    // let currentDate = new Date().toJSON().slice(0, 10);
    let currentDateTime = getCurrentDate();
    $("#posted_date_time").val(currentDateTime);
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
}

$("#addPartyWallForm").validate({
    rules: {
        post_heading: {
            required: true,
            maxlength: 500,
        },
        post_image: {
            required: true,
        },
        post_description: {
            required: true,
            maxlength: 2000,
        }
    },
    messages:{
        
    }
});

function addPartyWallForm(){
    // show loader
    showCustomBlockUI();
    if($("#addPartyWallForm").valid()){
        $("#addPartyWallForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush