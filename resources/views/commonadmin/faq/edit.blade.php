@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="heading-main-top">Edit FAQ</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('list_faq') }}">FAQ Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit FAQ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-12 col-md-4 mb-4">
                    {!! Form::model($entity, ['route' => ['update_faq'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editFaqForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
                    {{ csrf_field() }}
                    <input type="hidden" name="update_id" value="{{ $entity->id }}">
                        <div class="box-row flex-wrap comman-form-design">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Question</label>
                                    <div class="">
                                        {!! Form::text('question', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Question', 'maxlength'=>1000]) !!}
                                        <span class="help-block">
                                            <?= $errors->has('question') ? $errors->first('question') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-6">
                                <div class="form-group">
                                    <label>Answer</label>
                                    <div class="">
                                        {!! Form::textarea('answer',null,['required'=>'required','id'=>'ckeditor','class'=>'form-control']) !!}
                                        <span class="help-block">
                                            <?= $errors->has('answer') ? $errors->first('answer') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 text-center">
                                <a type="submit" href="{{ route('list_faq') }}" class="btn btn-primary">Cancel</a>
                                <a href="javascript:void(0);" onclick="editFaqForm();" class="btn btn-primary">Submit</a>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
@endsection
@push('current-page-js')
<script src="{{asset('ckeditor/ckeditor.js')}}"></script>
<script>
    CKEDITOR.replace('ckeditor',
     {
      toolbarGroups: [{
          "name": "basicstyles",
          "groups": ["basicstyles"]
        },
        {
          "name": "links",
          "groups": ["links"]
        },
        {
          "name": "paragraph",
          "groups": ["list", "blocks"]
        },
        {
          "name": "styles",
          "groups": ["styles"]
        },
        {
          "name": "about",
          "groups": ["about"]
        }
      ],
      removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord,Image'

    });
    function editFaqForm(){
        if($("#editFaqForm").valid()){
            if(CKEDITOR.instances['ckeditor'].getData() == ''){
                Lobibox.notify('error', {
                    icon:false,
                    msg: "Answer is required"
                });
            }
            else{
              $("#editFaqForm").submit();
            }  
        }
    }
</script>
<script type="text/javascript">
$("#editFaqForm").validate({
    rules: {
        question: {
            required: true,
            maxlength: 1000,
        }
    },
    messages:{
        question:{
            required: 'Question is required.'
        }
    }
});
</script>
@endpush