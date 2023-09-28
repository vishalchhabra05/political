@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Add Category</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list_categories') }}">Category Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Category</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">

            {!! Form::model($entity, ['route' => ['store_categories'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'addCategoryForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
                <div class="box-row flex-wrap comman-form-design">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Category Name<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('category_name', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Category Name', 'maxlength'=>500]) !!}
                                <span class="help-block">
                                    <?= $errors->has('category_name') ? $errors->first('category_name') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6 mb-3"></div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Category Image<span class="required">*</span></label>
                            <div class="upload-img">
                                {!! Form::file('image', ['accept'=>'image/*', 'onchange' =>'previewImage(event)'], ['class'=>'form-control chkAlphabets']) !!}
                                <img id="preview" src="#" alt="Preview Image" style="display:none; max-width: 100px; max-height: 100px; margin-top: 10px;">
                                <span class="help-block">
                                    <?= $errors->has('image') ? $errors->first('image') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3"></div> 
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Category Type<span class="required">*</span></label>
                            <div class="">
                                {!! Form::select('category_type', (!empty(getCategoryType())?getCategoryType():[]), null, ['class' => 'form-control','id'=>'category_type', 'placeholder'=> 'Select Category Type']) !!}
                                <span class="help-block">
                                    <?= $errors->has('category_type') ? $errors->first('category_type') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 text-left">
                        <a href="{{ route('list_categories') }}" class="btn btn-primary">Cancel</a>
                        <a href="javascript:void(0);" onclick="addCategoryForm();" class="btn btn-primary ">Submit</a>
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

$("#addCategoryForm").validate({
    rules: {
        category_name: {
            required: true,
            maxlength: 500,
        },
        image: {
            required: true,
        },
        category_type: {
            required: false,
        }
    },
    messages:{
        
    }
});

function addCategoryForm(){
    // show loader
    showCustomBlockUI();
    if($("#addCategoryForm").valid()){
        $("#addCategoryForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush