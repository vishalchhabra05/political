@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Banner Notice Management</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Banner Notice Management</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
 <div class="col-sm-12">
    <div class="row">
        <div class="col-sm-12 mb-4">
            <div class="box bg-white comman-form-design">
                <div class="box-row on-off-toggle align-items-center">
                    <h5 class="mb-0 mr-3">Banner Status</h5>
                    <label class="switch switch-green" id="chkBannerStatus">
                      <input type="checkbox" class="switch-input" {{$entity->status==1?'checked':''}}>
                      <span class="switch-label" data-on="On" data-off="Off"></span>
                      <span class="switch-handle"></span>
                    </label>
                 </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['update_banners'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editBannerForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
            <input type="hidden" name="update_id" id="update_id" value="{{ $entity->id }}">
                <div class="box-row flex-wrap comman-form-design">
                    <?php
                        $isContentExist = false;
                        $submitBtnCss = 'block';
                        if(!empty($entity->content_text)){
                            $isContentExist = 'none';
                        }
                    ?>
                    @if(!empty($entity->content_text))
                    <div class="col-md-12 mb-3">
                        <a href="#" onclick="editContent();" class="btn btn-primary " style="float: right;"><i class='fal fa-edit'></i> Edit</a>
                    </div>
                    @endif
                    
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <label>Content<span class="required">*</span></label>
                            <div class="">
                                {!! Form::text('content_text', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Content', 'maxlength'=>500, 'readonly' => $isContentExist, 'id' => 'contentText']) !!}
                                <span class="help-block">
                                    <?= $errors->has('content_text') ? $errors->first('content_text') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                    </div>

                    <div class="col-md-12 mb-3 text-left" id="submitBtnDiv" style="display:{{$isContentExist}}">
                        <!-- <a href="{{ route('list_categories') }}" class="btn btn-primary">Cancel</a> -->
                        <a href="javascript:void(0);" onclick="editBannerForm();" class="btn btn-primary">Submit</a>
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
    
    $('#chkBannerStatus').change(function(){
        var id = $('#update_id').val();
        changeStatus(id)
    });

    $.validator.addMethod("validate_name", function(value, element) {
        if (/^[a-zA-Z\s]*$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Field should not contain numbers or special characters");
});

function editContent(){
    // show loader
    showCustomBlockUI();
    $("#contentText").attr('readonly', false);
    $("#submitBtnDiv").css('display', 'block');


    // hide loader
    hideCustomBlockUI();
}


$("#editBannerForm").validate({
    rules: {
        content_text: {
            required: true,
            maxlength: 500,
        },
        /*category_type: {
            required: false,
        }*/
    },
    messages:{
        
    }
});

function editBannerForm(){
    // show loader
    showCustomBlockUI();
    if($("#editBannerForm").valid()){
        $("#editBannerForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}


function changeStatus(id){
    swal({
        title: `Do you want to change banner status?`,
        text: "",
        icon: "warning",
        buttons: true,
        // dangerMode: true,
        closeOnClickOutside: false,
        buttons: {
        cancel: "No",
        confirm: "Yes"
    },
    })
    .then((willUpdate) => {
        // show loader
        showCustomBlockUI();
        if (willUpdate) {
            jQuery.ajax({
                url: '{{route('update_banners_status')}}',
                type: 'POST',
                data:{id:id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        Lobibox.notify('success', {
                            icon:false,
                            msg: response.message
                        });
                    }else{
                        Lobibox.notify('error', {
                            icon:false,
                            msg: response.message
                        });
                    }
                    // hide loader
                    hideCustomBlockUI();
                }
            });
        }else{
            // hide loader
            hideCustomBlockUI();
        }
    });
}
</script>
@endpush