@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="heading-main-top">Member Management</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Registration Management</li>
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
                                <div class="col-md-12 mb-3">
                                    <h3>FILTERS</h3>
                                    <br>
                                    <div class="list_radio_button">
                                        <div class="radio_btn_list">
                                            <div class="comman-form-design">
                                                <input type="radio" class="filter_type" id="electoral" name="filter_type" checked value="electoral">
                                                <label for="electoral">Electoral</label>
                                            </div>
                                        </div>
                                        <div class="radio_btn_list">
                                            <div class="comman-form-design">
                                                <input type="radio" class="filter_type" id="personal" name="filter_type" value="personal">
                                                <label for="personal">Personal</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="electoral_demographic_div">
                                        @include('commonadmin.electoral_demographic_fields')
                                    </div>
                                    <div class="row" id="personal_demographic_div" style="display: none;">
                                        @include('commonadmin.demographic_fields')
                                    </div>
                                </div>
                            </div>
                            <div class="box-content">
                                <table id="table_dataTable" class="table table-striped table-bordered table-hover table-img">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">National Id</th>
                                            <th scope="col">Phone Number</th>
                                            <th scope="col">State</th>
                                            <th scope="col">City</th>
                                            <th scope="col">Municipal</th>
                                            <th scope="col">Affiliation</th>
                                            <th scope="col">Reference</th>
                                            <th scope="col">Parent</th>
                                            <th scope="col">Approved Status</th>
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
<script type="text/javascript">
var countryId = $("#country-dd").val();
var stateId = $("#state-dd").val();
var districtId = $("#district-dd").val();
var cityId = $("#city-dd").val();
var municipalDistrictId = $("#munciple-district-dd").val();
var townId = $("#town-dd").val();
var placeId = $("#place-dd").val();
var neighbourhoodId = $("#neighbourhood-dd").val();
var recintosId = $("#recintos-dd").val();
var collegeId = $("#college-dd").val();
var pageName = "MemberManage";
</script>
@include('commonadmin.demographic_fields_js')
@include('commonadmin.electoral_demographic_fields_js')
<script src="{{asset('js/admin/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/admin/dataTables.bootstrap4.min.js')}}"></script>

<script type="text/javascript">
$(function () {
    loadData();

    $("#place-dd").on('change', function(){
        // Update global variables
        placeId = $("#place-dd").val();

        loadData();
    })

    $("#neighbourhood-dd").on('change', function(){
        // Update global variables
        neighbourhoodId = $("#neighbourhood-dd").val();

        loadData();
    })

    $("#college-dd").on('change', function(){
        // Update global variables
        collegeId = $("#college-dd").val();

        loadData();
    })

    $(".filter_type").on('change', function(){
        // Update global variables
        var filterType = $("input[name='filter_type']:checked").val();
        if(filterType == "electoral"){
            $("#electoral_demographic_div").css("display", "flex");
            $("#personal_demographic_div").css("display", "none");

            $("#country-dd-pers").val([]);
            $("#country-dd-pers").trigger('change');
        }else if(filterType == "personal"){
            $("#personal_demographic_div").css("display", "flex");
            $("#electoral_demographic_div").css("display", "none");

            $("#country-dd").val([]);
            $("#country-dd").trigger('change');
        }
        resetGlobalDemographicVars();
        // loadData();
    })
});

function resetGlobalDemographicVars(){
    countryId = "";
    stateId = "";
    districtId = "";
    cityId = "";
    municipalDistrictId = "";
    townId = "";
    placeId = "";
    neighbourhoodId = "";
    recintosId = "";
    collegeId = "";
}

function loadData(onchange=null){
    // Get the existing DataTable instance
    var table = $('#table_dataTable').DataTable();

    // Destroy the instance
    table.destroy();

    $('#table_dataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: admin_page_length,
        order: [[12, "desc"]],
        "ajax": {
            "url": '{!! route('member.datatables') !!}',
            "dataType": "json",
            "type": "POST",
            "data": { 
                _token: "{{csrf_token()}}",
                country_id : countryId,
                state_id : stateId,
                district_id : districtId,
                city_id : cityId,
                municipal_district_id : municipalDistrictId,
                town_id : townId,
                place_id : placeId,
                neighbourhood_id : neighbourhoodId,
                recintos_id : recintosId,
                college_id : collegeId,
                filter_type : $("input[name='filter_type']:checked").val(),
            }
        },
        columns: [
            {data: 'id', name: 'id', orderable:false, "visible": true, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'full_name', name: 'full_name', orderable:false},
            {data: 'national_id', name: 'national_id', orderable:false},
            {data: 'phone_number', name: 'phone_number', orderable:false},
            {data: 'state', name: 'state', orderable:false},
            {data: 'city', name: 'city', orderable:false},
            {data: 'municipal', name: 'municipal', orderable:false},
            {data: 'affiliation', name: 'affiliation', orderable:false},
            {data: 'reference', name: 'reference', orderable:false},
            {data: 'parent_user_id', name: 'parent_user_id', orderable:false},
            {data: 'is_approved', name: 'is_approved', orderable:false},
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

function changeApprovalStatus(id,type){
    swal({
        title: `Do you want to change approval status?`,
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
                url: '{{route('update_member_approved_status')}}',
                type: 'POST',
                data:{id:id,type:type},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        var userTable = $('#table_dataTable').DataTable();
                        userTable.draw();
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


function changeStatus(id){
    swal({
        title: `Do you want to change status?`,
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
                url: '{{route('update_member_status')}}',
                type: 'POST',
                data:{id:id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if(response.status == true){
                        var registerTable = $('#table_dataTable').DataTable();
                        registerTable.draw();
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
