@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="heading-main-top">View Member Answers</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('list_poll') }}">Poll Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Member Answers</li>
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
                            <h2 class="heading-main-top">Poll Title - {{$pollInfo->poll_name}}</h2>
                        </div>
                            <div class="box-content">
                                <table id="table_dataTable" class="table table-striped table-bordered table-hover table-img">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Full Name</th>
                                            <th scope="col">Email</th>
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
        order: [[1, "asc"]],
        "ajax": {
            "url": '{!! route('poll_member_answer.datatables') !!}',
            "dataType": "json",
            "type": "POST",
            "data": {_token: "{{csrf_token()}}", "poll_id": {{$pollInfo->id}}}
        },
        columns: [
            {data: 'id', name: 'id', orderable:false, "visible": true, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'full_name', name: 'full_name', orderable:true},
            {data: 'email', name: 'email', orderable:true},
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

function updateMemberPollAnswer(memberId, pollId){
    // debugger;
    var pollOptionId = $("#poll_answer_"+memberId).val();
    jQuery.ajax({
        url: '{{route('update_member_poll_answer')}}',
        type: 'POST',
        data:{member_id:memberId, poll_id:pollId, poll_option_id:pollOptionId},
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if(response.status == true){
                var pollsTable = $('#table_dataTable').DataTable();
                pollsTable.draw();
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
</script>
@endpush
