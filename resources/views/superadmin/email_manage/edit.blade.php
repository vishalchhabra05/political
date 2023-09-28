@extends('layouts.superadmin')
@section('content')
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3 m-0">Edit Ads</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.list_email') }}">Email Template Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Email Template</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['superadmin.update_email'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editEmailForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
            <input type="hidden" name="update_id" value="{{ $entity->id }}">
                <div class="box-row flex-wrap">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Email Heading</label>
                            <div class="">
                                {!! Form::text('email_template', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Email Template', 'maxlength'=>255]) !!}
                                <span class="help-block">
                                    <?= $errors->has('email_template') ? $errors->first('email_template') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Subject</label>
                            <div class="">
                                {!! Form::text('subject', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Subject', 'maxlength'=>500]) !!}
                                <span class="help-block">
                                    <?= $errors->has('subject') ? $errors->first('subject') : '' ?>
                                </span>
                            </div>
                       </div>
                    </div>
                    <div class="col-md-12 mb-6">
                      <div class="form-group">
                            <label>Message Greeting</label>
                            <div class="">
                                {!! Form::textarea('message_greeting',null,['required'=>'required','id'=>'msggreeting_ckeditor','class'=>'form-control']) !!}
                                <span class="help-block">
                                    <?= $errors->has('message_greeting') ? $errors->first('message_greeting') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-12 mb-6">
                       <div class="form-group">

                            <label>Message Body</label>
                            <div class="">
                                {!! Form::textarea('message_body',null,['required'=>'required','id'=>'ckeditor','class'=>'form-control']) !!}
                                <span class="help-block">
                                    <?= $errors->has('message_body') ? $errors->first('message_body') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-6">
                       <div class="form-group">
                            <label>Message Signature</label>
                            <div class="">
                                {!! Form::textarea('message_signature',null,['required'=>'required','id'=>'msg_ckeditor','class'=>'form-control']) !!}
                                <span class="help-block">
                                    <?= $errors->has('message_signature') ? $errors->first('message_signature') : '' ?>
                                </span>
                            </div> 
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 text-center">
                        <a href="{{ route('superadmin.list_email') }}" class="btn light">Cancel</a>
                        <a href="javascript:void(0);" onclick="editEmailForm();" class="btn light">Submit</a>
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
    CKEDITOR.replace('msg_ckeditor',
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
    CKEDITOR.replace('msggreeting_ckeditor',
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
       // Remove the redundant buttons from toolbar groups defined above.
        removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord,Image'
    });
    function editEmailForm(){
        if($("#editEmailForm").valid()){
            if(CKEDITOR.instances['msggreeting_ckeditor'].getData() == ''){
                Lobibox.notify('error', {
                    icon:false,
                    msg: "Message Greeting is required"
                });
            }
            else if(CKEDITOR.instances['ckeditor'].getData() == ''){
                Lobibox.notify('error', {
                    icon:false,
                    msg: "Message Body is required"
                });
            }
            else if(CKEDITOR.instances['msg_ckeditor'].getData() == ''){
                Lobibox.notify('error', {
                    icon:false,
                    msg: "Message Signature is required"
                });
            }
            else{
              $("#editEmailForm").submit();
            }  
        }
    }
</script>
<script type="text/javascript">
$("#editEmailForm").validate({
    rules: {
        email_template: {
            required: true,
            maxlength: 255, 
        },
        subject:{
            required: true,
            maxlength: 500
        },
    },
    messages:{
        email_template:'Email Heading is required',
        subject:'Subject is Required',
    },
});
</script>
@endpush