@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Edit Political Position</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list_political_position') }}">Political Position Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Political Position</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['update_political_position'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editPoliticalPositionForm', 'autocomplete'=>'off']) !!}
            {{ csrf_field() }}
            <input type="hidden" name="update_id" value="{{ $entity->id }}">
                <div class="box-row flex-wrap comman-form-design">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Political Position <span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('political_position', null, ['class'=>'form-control chkAlphabets','placeholder'=>'Political Position', 'maxlength'=>100]) !!}
                                <span class="help-block">
                                    <?= $errors->has('political_position') ? $errors->first('political_position') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 text-left">
                        <a href="{{ route('list_political_position') }}" class="btn btn-primary">Cancel</a>
                        <a href="javascript:void(0);" onclick="editPoliticalPositionForm();" class="btn btn-primary">Submit</a>
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

$("#editPoliticalPositionForm").validate({
    rules: {
        polition_position: {
            required: true,
            maxlength: 100,
            validate_name: true,
        },
    },
    messages:{
        polition_position:{
            validate_name: 'Name should not contain numbers or special characters',
        }
    }
});

function editPoliticalPositionForm(){
    // show loader
    showCustomBlockUI();
    if($("#editPoliticalPositionForm").valid()){
        $("#editPoliticalPositionForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush