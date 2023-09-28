@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="heading-main-top">List Member Positions</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('list_member') }}">Member Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">List Member Positions</li>
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
                            <a href="<?= route('create_postion', ['memberId' => base64_encode($memberId)]) ?>" class="btn btn-primary " style="float: right;">
                                <i class="fa fa-plus"></i> Add New
                            </a>
                        </div>
                            <div class="box-content">
                                <table id="table_dataTable" class="table table-striped table-bordered table-hover table-img">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Position</th>
                                            <th scope="col">Country</th>
                                            <th scope="col">State</th>
                                            <th scope="col">City</th>
                                            <th scope="col">Municipal District</th>
                                            <th scope="col">Town</th>
                                            <th scope="col">Place</th>
                                            <th scope="col">Neighbourhood</th>
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
        order: [[9, "desc"]],
        "ajax": {
            "url": '{!! route('position.datatables') !!}',
            "dataType": "json",
            "type": "POST",
            "data": {_token: "{{csrf_token()}}", member_id: '{{$memberId}}'}
        },
        columns: [
            {data: 'id', name: 'id', orderable:false, "visible": true, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'position', name: 'position', orderable:false},
            {data: 'country', name: 'country', orderable:false},
            {data: 'state', name: 'state', orderable:false},
            {data: 'city', name: 'city', orderable:false},
            {data: 'municipal_district', name: 'municipal_district', orderable:false},
            {data: 'town', name: 'town', orderable:false},
            {data: 'place', name: 'place', orderable:false},
            {data: 'neighbourhood', name: 'neighbourhood', orderable:false},
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
                url: '{{route('destroy_position')}}',
                type: 'POST',
                data:{id:id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        var memberPostionTable = $('#table_dataTable').DataTable();
                        memberPostionTable.draw();
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
