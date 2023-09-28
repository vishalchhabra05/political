@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="heading-main-top">Newsletter Management</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Newsletter Management</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-12 mb-4">
                    <div class="box bg-white comman-form-design">
                        <!-- <div class="box-row top-search-table">
                            <a href="<?= route('create_political_position') ?>" class="btn btn-primary " style="float: right;">
                                <i class="fa fa-plus"></i> Add New
                            </a>
                        </div> -->
                        @php
                            $superadminLoginAs = Session::get('loginAs');
                        @endphp
                        <div class="box-row">
                            <div class="box-content">
                                <table id="table_dataTable" class="table table-striped table-bordered table-hover table-img input-check-box">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Subscribed At</th>
                                            <!-- <th><input type="checkbox" name="select_all" value="1" id="checkbox-select-all"></th> -->
                                        </tr>
                                    </thead>   
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-4 mb-4">
                    {!! Form::model("", ['route' => ['send_newsletter'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'sendNewsletterForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
                        {{ csrf_field() }}
                        <div class="box-row flex-wrap">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Email Heading<span class="required">*</span></label>
                                    <div class="">
                                        {!! Form::text('email_heading', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Email Heading', 'maxlength'=>255]) !!}
                                        <span class="help-block">
                                            <?= $errors->has('email_heading') ? $errors->first('email_heading') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Subject<span class="required">*</span></label>
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
                                    <label>Message Greeting<span class="required">*</span></label>
                                    <div class="">
                                        {!! Form::textarea('message_greeting',null,['required'=>'required','id'=>'msggreeting_ckeditor','class'=>'form-control']) !!}
                                        <span class="help-block" id="err_msg_greeting">
                                            <?= $errors->has('message_greeting') ? $errors->first('message_greeting') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-12 mb-6">
                               <div class="form-group">
                                    <label>Message Body<span class="required">*</span></label>
                                    <div class="">
                                        {!! Form::textarea('message_body',null,['required'=>'required','id'=>'ckeditor','class'=>'form-control']) !!}
                                        <span class="help-block" id="err_msg_body">
                                            <?= $errors->has('message_body') ? $errors->first('message_body') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-6">
                               <div class="form-group">
                                    <label>Message Signature<span class="required">*</span></label>
                                    <div class="">
                                        {!! Form::textarea('message_signature',null,['required'=>'required','id'=>'msg_ckeditor','class'=>'form-control']) !!}
                                        <span class="help-block" id="err_msg_signature">
                                            <?= $errors->has('message_signature') ? $errors->first('message_signature') : '' ?>
                                        </span>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 text-center">
                                <a href="javascript:void(0);" onclick="sendNewsletterForm();" class="btn btn-primary">Submit</a>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
@endsection
@section('uniquepagestyle')
<link rel="stylesheet" href="{{asset('css/admin/dataTables.bootstrap4.min.css')}}">
@endsection
@push('current-page-js')
<script src="{{asset('js/admin/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/admin/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('ckeditor/ckeditor.js')}}"></script> 

<script type="text/javascript">
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
    }],
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
    }],
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
    }],
    // Remove the redundant buttons from toolbar groups defined above.
    removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord,Image'
});

// Create an object to store the checkbox states
var checkboxStates = {};

$(function () {
    var table = $('#table_dataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        order: [[3, "desc"]],
        "ajax": {
            "url": '{!! route('newsletter.datatables') !!}',
            "dataType": "json",
            "type": "POST",
            "data": {_token: "{{csrf_token()}}"}
        },
        columns: [
            {data: 'id', name: 'id', orderable:false, "visible": true, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'name', name: 'name', orderable:false},
            {data: 'email', name: 'email', orderable:true},
            {data: 'subscribed_at', name: 'subscribed_at', orderable:false},
            // {data: 'action', name: 'action', orderable: false}
        ],
        "columnDefs": [
            {"searchable": false, "targets": 0},
            {className: 'text-center', targets: [1]},
        ]
        , language: {
            searchPlaceholder: "Search"
        }
    });

    // Handle click on "Select all" control
    $('#checkbox-select-all').on('click', function(){
        var selectAllCheck = this.checked;

        // Get all rows with search applied
        var rows = table.rows({ 'search': 'applied' }).nodes();
        // Check/uncheck checkboxes for all rows in the table
        $('input[type="checkbox"]', rows).prop('checked', selectAllCheck);

        $('.checkSubscriber').each(function (index, obj) {
            var checkboxId = $(obj).attr('id');
            checkboxStates[checkboxId] = selectAllCheck;
        });
    });

    // Handle click on checkbox to set state of "Select all" control
    $('#table_dataTable tbody').on('change', 'input[type="checkbox"]', function() {
        var checkboxId = $(this).attr('id');
        checkboxStates[checkboxId] = this.checked;

        // If checkbox is not checked
        if (!this.checked) {
            var el = $('#checkbox-select-all').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if (el && el.checked && ('indeterminate' in el)) {
                // Set visual state of "Select all" control as 'indeterminate'
                el.indeterminate = true;
            }
            $('#checkbox-select-all').prop('checked', false);
        } else {
            var allCheckboxes = $('input[type="checkbox"]', '#table_dataTable tbody');
            var allChecked = allCheckboxes.length === allCheckboxes.filter(':checked').length;
            $('#checkbox-select-all').prop('checked', allChecked);
            $('#checkbox-select-all').prop('indeterminate', false);
        }
    });

    // Handle table redraw event (e.g., when navigating to a different page)
    table.on('draw', function() {
        // Restore the checkbox states
        var rows = table.rows({ 'search': 'applied' }).nodes();
        var selectAllCheck = $('#checkbox-select-all').prop('checked');
        if(selectAllCheck){
            rows.each(function(index) {
                var checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', true);
            });
            // Set in checkboxStates
            $('.checkSubscriber').each(function (index, obj) {
                var checkboxId = $(obj).attr('id');
                checkboxStates[checkboxId] = selectAllCheck;
                $(obj).prop('checked', true);
            });
        }else{
            rows.each(function(index) {
                var checkbox = $(this).find('input[type="checkbox"]');
                var checkboxId = checkbox.attr('id');
                // var checkboxId = 'checkbox-' + index;
                // checkbox.attr('id', checkboxId);
                checkbox.prop('checked', checkboxStates[checkboxId]);
            });

            // Set in checkboxStates
            $('.checkSubscriber').each(function (index, obj) {
                var checkboxId = $(obj).attr('id');
                $(obj).prop('checked', checkboxStates[checkboxId]);
            });
        }
    });

    // Onchange event of ckeditors
    CKEDITOR.instances.msggreeting_ckeditor.on('change', function() { 
        if(CKEDITOR.instances['msggreeting_ckeditor'].getData() == ''){
            $("#err_msg_greeting").text("Message Greeting is required");
        }else{
            $("#err_msg_greeting").text("");
        }
    });

    CKEDITOR.instances.ckeditor.on('change', function() { 
        if(CKEDITOR.instances['ckeditor'].getData() == ''){
            $("#err_msg_body").text("Message Body is required");
        }else{
            $("#err_msg_body").text("");
        }
    });

    CKEDITOR.instances.msg_ckeditor.on('change', function() { 
        if(CKEDITOR.instances['msg_ckeditor'].getData() == ''){
            $("#err_msg_signature").text("Message Signature is required");
        }else{
            $("#err_msg_signature").text("");
        }
    });

});

$("#sendNewsletterForm").validate({
    rules: {
        email_heading: {
            required: true,
            maxlength: 255,
        },
        subject: {
            required: true,
            maxlength: 500,
        }
    },
    messages:{
        email_heading:{
            required: 'Email heading is required',
        },
        subject:{
            required: 'Subject is required',
        },
    }
});

function sendNewsletterForm(){
    // show loader
    showCustomBlockUI();

    var msg_greeting_exist = 1;
    var msg_body_exist = 1;
    var msg_sign_exist = 1;
    if(CKEDITOR.instances['msggreeting_ckeditor'].getData() == ''){
        msg_greeting_exist = 0;
        $("#err_msg_greeting").text("Message Greeting is required");
    }

    if(CKEDITOR.instances['ckeditor'].getData() == ''){
        msg_body_exist = 0;
        $("#err_msg_body").text("Message Body is required");
    }

    if(CKEDITOR.instances['msg_ckeditor'].getData() == ''){
        msg_sign_exist = 0;
        $("#err_msg_signature").text("Message Signature is required");
    }

    if($("#sendNewsletterForm").valid()){
        if(msg_greeting_exist && msg_body_exist && msg_sign_exist){
            $("#sendNewsletterForm").submit();
        }else{
            // hide loader
            hideCustomBlockUI();
        }
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}

</script>
@endpush
