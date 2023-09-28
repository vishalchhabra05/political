@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="heading-main-top">Contact Us Management</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Contact Us Management</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-12 mb-4">
                    <div class="box bg-white comman-form-design">
                        <div class="box-row">
                        </div>
                        <div class="box-row">
                            <div class="box-content">
                                <table id="table_dataTable" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone Number</th>
                                            <th scope="col">Message</th>
                                            <th scope="col">Reply</th>
                                            <th scope="col">Received At</th>
                                            <th scope="col" class="action">Action</th>
                                        </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
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
<script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.2/sweetalert2.all.js"></script>
<script type="text/javascript">
$(function () {
    var table = $('#table_dataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        order: [[6, "desc"]],
        "ajax": {
            "url": '{!! route('contactus.datatables') !!}',
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
            {data: 'name', name: 'name', orderable: true},
            {data: 'email', name: 'email', orderable: false},
            {data: 'phone_number', name: 'phone_number', orderable: false},
            {data: 'message', name: 'message', orderable: false, width: '30%'},
            {data: 'reply', name: 'reply', orderable: false, width: '30%'},
            {data: 'created_at', name: 'created_at', orderable: true},
            {data: 'action', name: 'action', orderable: false}
        ],
        "columnDefs": [
            {"searchable": false, "targets": 0},
            {className: 'text-center', targets: [1]},
        ]
        , language: {
            searchPlaceholder: "Search"
        }
    });

    // $('#dataTable').dataTable({dom: 'lrt'});
});

function sendReply(id) {
    swal({
        title: "",
        text: "Write your reply message.",
        input: 'textarea',
        inputAttributes: {
            maxlength: '2000'
        },
        icon: "warning",
        buttons: true,
        showCancelButton: true,
        closeOnConfirm: false,
        closeOnCancel: false,
        allowOutsideClick: false,
        preConfirm: () => {
            const reply = Swal.getPopup().querySelector('textarea.swal2-textarea').value
            if (!reply) {
                Swal.showValidationMessage(`Please enter reply message`)
            }
            return { reply: reply }
        }
    })
    .then(function(result) {
       if(result.value == ""){
            return null
       }else if(result.value && result.dismiss != "cancel") {
            // show loader
            showCustomBlockUI();
            jQuery.ajax({
                url: '{{route('update_contactus')}}',
                type: 'POST',
                data:{token:id, reply_message: result.value},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        var contactUsTable = $('#table_dataTable').DataTable();
                        contactUsTable.draw();
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
        }
    });
}

</script>
@endpush
