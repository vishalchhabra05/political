@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="heading-main-top">Survey Management</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Survey Management</li>
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
                            <a href="<?= route('create_survey') ?>" class="btn btn-primary " style="float: right;">
                                <i class="fa fa-plus"></i> Add New
                            </a>
                        </div>
                        @php
                            $superadminLoginAs = Session::get('loginAs');
                        @endphp
                        @if($superadminLoginAs != "Party")
                        <div class="box-row">
                            <div class="col-md-6 mb-3">
                                <h3>FILTERS</h3>
                                <br>
                                <div class="form-group">
                                    <label>Select Political Party</label>
                                    <div class="">
                                        {!! Form::select('political_party', (!empty($politicalParties)?$politicalParties:[]), null, ['class' => 'form-control','id'=>'political_party', 'placeholder'=> 'Select Political Party']) !!}
                                        <span class="help-block">
                                            <?= $errors->has('political_party') ? $errors->first('political_party') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="box-row">
                            <div class="box-content">
                                <table id="table_dataTable" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Party Name</th>
                                            <th scope="col">Survey Name</th>
                                            <th scope="col">Survey Type</th>
                                            <th scope="col">Start Date</th>
                                            <th scope="col">End Date</th>
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
    loadData();

    $("#political_party").change(function() {
        loadData();
    });
});

function loadData(onchange=null){
    // Get the existing DataTable instance
    var table = $('#table_dataTable').DataTable();

    // Destroy the instance
    table.destroy();

    $('#table_dataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        order: [[7, "desc"]],
        "ajax": {
            "url": '{!! route('survey.datatables') !!}',
            "dataType": "json",
            "type": "POST",
            "data": {_token: "{{csrf_token()}}", "political_party" : $("#political_party").val()}
        },
        columns: [
            {data: 'id', name: 'id', orderable:false, "visible": true, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'party_name', name: 'party_name', orderable:false},
            {data: 'survey_name', name: 'survey_name', orderable:true},
            {data: 'survey_type', name: 'survey_type', orderable:true},
            {data: 'start_date', name: 'start_date', orderable:true},
            {data: 'end_date', name: 'end_date', orderable:true},
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
}


function changeStatus(id){
    swal({
        title: `Are you sure?`,
        text: "",
        icon: "warning",
        buttons: true,
        // dangerMode: true,
        closeOnClickOutside: false,
    })
    .then((willUpdate) => {
        // show loader
        showCustomBlockUI();
        if (willUpdate) {
            jQuery.ajax({
                url: '{{route('update_survey_status')}}',
                type: 'POST',
                data:{id:id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        var surveyTable = $('#table_dataTable').DataTable();
                        surveyTable.draw();
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
