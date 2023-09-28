@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Edit CMS Management</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list_cms') }}">CMS Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit CMS Management</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['update_cms'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editCmsForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
            <input type="hidden" name="update_id" value="{{ $entity->id }}">
                <div class="box-row flex-wrap comman-form-design">
                    <div class="col-md-12 mb-6">
                        <div class="form-group">
                            <label>Title</label>
                            <div class="">
                                {!! Form::text('title', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Cms Title', 'maxlength'=>255]) !!}
                                <span class="help-block">
                                    <?= $errors->has('title') ? $errors->first('title') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-6">
                      <div class="form-group">
                            <label>Description</label>
                            <div class="">
                                {!! Form::textarea('description',null,['required'=>'required','id'=>'ckeditor','class'=>'form-control']) !!}
                                <span class="help-block">
                                    <?= $errors->has('description') ? $errors->first('description') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 text-center">
                        <a type="submit" href="{{ route('list_cms') }}" class="btn btn-primary">Cancel</a>
                        <a href="javascript:void(0);" onclick="editCmsForm();" class="btn btn-primary">Submit</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@push('current-page-js')
<script src="{{asset('ckeditor/ckeditor.js')}}"></script> 
<script>
    CKEDITOR.replace('ckeditor');
    function editCmsForm(){
        if($("#editCmsForm").valid()){
            if($('#ckeditor').length == 1){
                if(CKEDITOR.instances['ckeditor'].getData() == ''){
                    Lobibox.notify('error', {
                        icon:false,
                        msg: "Description is required"
                    });
                }
                else{
                  $("#editCmsForm").submit();
                }
            }else{
                $("#editCmsForm").submit();
            }
        }
    }
</script>
<script type="text/javascript">
$("#editCmsForm").validate({
    rules: {
        title: {
            required: true,
            maxlength: 255, 
        }
    },
    messages:{
        title:'Title is required',
    },
});
</script>
@endpush