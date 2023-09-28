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
                    <h1 class="heading-main-top">{{$pageTitleName}}</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{$pageTitleName}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-12 mb-4">
                    <div class="box bg-white comman-form-design">
                        @if($formType != 'Post')
                        <div class="box-row top-search-table">
                            <a href="<?= route('create_party_wall',['formType' => $formType]) ?>" class="btn btn-primary " style="float: right;">
                                <i class="fa fa-plus"></i> Add New
                            </a>
                        </div>
                        @endif
                            <div class="box-content">
                                <table id="table_dataTable" class="table table-striped table-bordered table-hover table-img">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Posted By</th>
                                            <th scope="col">Posted By User</th>
                                            @if($formType == 'News')
                                                <th scope="col">Category</th>
                                            @endif
                                            <th scope="col">Post Heading</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Approval Status</th>
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
        <?php if($formType == 'News'){ ?>
            order: [[8, "desc"]],
        <?php }else{ ?>
            order: [[7, "desc"]],
        <?php } ?>
        "ajax": {
            "url": '{!! route('party_wall.datatables') !!}',
            "dataType": "json",
            "type": "POST",
            "data": {_token: "{{csrf_token()}}", formType: '{{$formType}}'}
        },
        columns: [
            {data: 'id', name: 'id', orderable:false, "visible": true, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'posted_by', name: 'posted_by', orderable:false},
            {data: 'posted_by_user', name: 'posted_by_user', orderable:false},
            <?php if($formType == 'News'){ ?>
                {data: 'category', name: 'category', orderable:false},
            <?php } ?>
            {data: 'post_heading', name: 'post_heading', orderable:true},
            {data: 'image', name: 'image', orderable:false},
            {data: 'approval_status', name: 'approval_status', orderable:false},
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
function changeApprovalStatus(id, approveResponse, formType){
    swal({
        title: `Do you want to change approval status of Party Wall Post?`,
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
                url: '{{route('update_party_wall_approval_status')}}',
                type: 'POST',
                data:{id:id,formType:formType,approval_resp:approveResponse},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        var partyTable = $('#table_dataTable').DataTable();
                        partyTable.draw();
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

function changeStatus(id,formType){
    swal({
        title: `Do you want to change `+formType.toLowerCase()+` status?`,
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
                url: '{{route('update_party_wall_status')}}',
                type: 'POST',
                data:{id:id,formType:formType},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        var partyWallPostTable = $('#table_dataTable').DataTable();
                        partyWallPostTable.draw();
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
function confirmDelete(id,formType) {
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
                url: '{{route('delete_party_wall')}}',
                type: 'POST',
                data:{id:id,formType:formType},
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
