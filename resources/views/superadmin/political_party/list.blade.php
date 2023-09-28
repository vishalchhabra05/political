@extends('layouts.superadmin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="heading-main-top">Political Party Management</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Political Party Management</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-12 mb-4">
                    <div class="box bg-white comman-form-design">
                        <div class="box-row top-search-table">
                            <a href="<?= route('superadmin.create_political_party') ?>" class="btn btn-primary " style="float: right;">
                                <i class="fa fa-plus"></i> Add New
                            </a>
                        </div>
                            <div class="box-content">
                                <table id="table_dataTable" class="table table-striped table-bordered table-hover table-img">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Party Name</th>
                                            <th scope="col">Logo</th>
                                            <th scope="col">National Id</th>
                                            <th scope="col">Full Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone Number</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col" class="action">Action</th>
                                        </tr>
                                    </thead>   
                                </table>
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

<script type="text/javascript">
$(function () {
    var table = $('#table_dataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        order: [[8, "desc"]],
        "ajax": {
            "url": '{!! route('superadmin.political_party.datatables') !!}',
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
            {data: 'party_name', name: 'party_name', orderable:false},
            {data: 'logo', name: 'logo', orderable:false},
            {data: 'national_id', name: 'national_id', orderable:false},
            {data: 'full_name', name: 'full_name', orderable:false},
            {data: 'email', name: 'email', orderable:false},
            {data: 'phone_number', name: 'phone_number', orderable:false},
            {data: 'status', name: 'status', orderable:false},
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
});
function changeStatus(id){
    swal({
        title: `Do you want to change Party Status?`,
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
                url: '{{route('superadmin.update_political_party_status')}}',
                type: 'POST',
                data:{id:id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        var politicalPartyTable = $('#table_dataTable').DataTable();
                        politicalPartyTable.draw();
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
function confirmDelete(id) {
    event.preventDefault();
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this record!",
        icon: "warning",
        buttons: true,
        closeOnClickOutside: false
    })
    
    .then((willDelete) => {
        // show loader
        showCustomBlockUI();
        if (willDelete) {
            jQuery.ajax({
                url: '{{route('superadmin.delete_political_party')}}',
                type: 'POST',
                data:{id:id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        var politicalPartyTable = $('#table_dataTable').DataTable();
                        politicalPartyTable.draw();
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
