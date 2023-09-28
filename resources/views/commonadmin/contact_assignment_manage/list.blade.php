@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
       <?php $pageTitleName = "Contact Assignment"; ?>
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
                            <div class="box-content">
                                <!-- start custom filter -->
                                <div class="panel panel-default">
                                    <div class="panel-body">

                                        <form method="POST" id="search-form" class="form-inline" role="form">
                                            <!-- <div class="col-md-2 float-left d-none">
                                                <button type="button" class="btn btn-primary ml-5 float-left" id="assignBtn">ASSIGN</button>
                                            </div> -->
                                            
                                            <div class="row allFilter d-none">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-4">
                                                            <div class="heading-section">
                                                                Please Select Filters
                                                            </div>
                                                        </div>
                                                        @include('commonadmin.electoral_demographic_fields')
                                                        <div class="col-md-3 mb-4">
                                                            <div class="form-group">
                                                                <label>Member Type</label>
                                                                <div class="multi-select-input">
                                                                    @php $memberFilters = getMemberFilters();
                                                                     @endphp
                                                                    {!! Form::select('signed-dd', (!empty($memberFilters)?$memberFilters:[]), null, ['class' => 'form-control',"id"=>"signed-dd"]) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 mb-4">
                                                           <div class="float-left">
                                                                <button type="button" class="btn btn-primary" id="filterBtn">FILTER</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- end custom filter -->
                                
                                <hr style="border-top: 2px solid #dddddd; height2px;width:100%">

                                <div class="row">
                                    <div class="col-12">
                                        <div class="heading-section mb-3">
                                            Select Member
                                        </div>
                                        <div class="form-group float-left">
                                            <select name="member_id" id="member_id" class="form-control  pr-5">
                                                <option value="">Select Member</option>
                                                @if(!empty($members))
                                                    @foreach($members as $member)
                                                    <option value="{{$member->id}}">
                                                        {{!empty($member->user)?$member->user->full_name:''}} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <table id="table_dataTable" class="table table-striped table-bordered table-hover table-img input-check-box">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Member</th>
                                            <th scope="col">Contact Member</th>
                                            <th scope="col" class="action">Action</th> 
                                        </tr>
                                    </thead>   
                                </table>

                                <table id="table_dataTable2" class="table table-striped table-bordered table-hover table-img input-check-box">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No.</th>
                                            <th scope="col">Sub Member</th>
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
@include('commonadmin.electoral_demographic_fields_js')

<script type="text/javascript">
var contactMemberIdFilter = "";
var signedIdFilter = "";
var countryIdFilter = "";
var stateIdFilter = "";
var cityIdFilter = "";
var townIdFilter = "";
var municipalIdFilter = "";
var placeIdFilter = "";
var neighbourhoodIdFilter = "";

$(function () {
    // Hide country error initially
    $('.country-error').hide();

    $('#filterBtn').click(function(){
        if($('#country-dd').val()!=""){
            contactMemberIdFilter = $('#member_id').val();
            signedIdFilter = $('#signed-dd').val();
            countryIdFilter = $('#country-dd').val();
            stateIdFilter = $('#state-dd').val();
            cityIdFilter = $('#city-dd').val();
            townIdFilter = $('#town-dd').val();
            municipalIdFilter = $('#munciple-district-dd').val();
            placeIdFilter = $('#place-dd').val();
            neighbourhoodIdFilter = $('#neighbourhood-dd').val();

            // $('#assignBtn').parent().removeClass('d-none');
            table.draw();
            $("#assignBtnFilter").css("display","block");
        }else{
            $('.country-error').show();
            $("#assignBtnFilter").css("display","none");
        }
    });

    // 1st Datatable - to assign members
    var table = $('#table_dataTable').DataTable({
        processing: true,
        serverSide: true,
        // searching: true,
        ajax: {
          url: "{{ route('contact_assignment.datatables') }}",
          type: "POST",
          data: function (d) {
                d._token = "{{csrf_token()}}",
                d.contact_member_id = contactMemberIdFilter,
                d.signed_id = signedIdFilter,
                d.country_id = countryIdFilter,
                d.state_id = stateIdFilter,
                d.city_id = cityIdFilter,
                d.town_id = townIdFilter,
                d.munciple_district_id = municipalIdFilter,
                d.place_id = placeIdFilter,
                d.neighbourhood_id = neighbourhoodIdFilter,
                d.chk_member = val = [];
                $('.chk_member:checkbox:checked').each(function(i){
                  val[i] = $(this).val();
                })
            }
        },
        columns: [
            {data: 'id', name: 'id', orderable:false, "visible": true, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'member_id', name: 'member_id', orderable: false},
            {data: 'contact_member_id', name: 'contact_member_id', orderable: false},
            {data: 'action', name: 'action', orderable: false}
        ],
        "columnDefs": [
            {"searchable": false, "targets": 0},
            {className: 'text-center', targets: [1]},
        ]
        , language: {
            searchPlaceholder: "Search",
            "infoFiltered": ""
        },
        "pageLength": 10,
    });

    var table2 = $('#table_dataTable2').DataTable({
        processing: true,
        serverSide: true,
        // searching: true,
        //info: false,
        pageLength: admin_page_length,
        //order: [[1, "desc"]],
        ajax: {
            url: '{!! route('contact_assignment.datatables2') !!}',
            dataType: "json",
            type: "POST",
            data: function (d) {
                d._token = "{{csrf_token()}}",
                d.member_id = $('#member_id').val(),
                d.chk_member2 = val = [];
                $('.chk_member2:checkbox:checked').each(function(i){
                  val[i] = $(this).val();
                })
            },
        },
        columns: [
            {data: 'id', name: 'id', orderable:false, "visible": true, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'member_id', name: 'member_id', orderable:false},
            {data: 'action', name: 'action', orderable: false}
            /* {data: 'status', name: 'status', orderable:false},
            {data: 'created_at', name: 'created_at', orderable: true},*/
        ],
        "columnDefs": [
            {"searchable": false, "targets": 0},
            {className: 'text-center', targets: [1]},
        ]
        , language: {
            searchPlaceholder: "Search",
            "infoFiltered": ""
        }
    });

    //  Add assign/unassign buttons beside search boxes
    $("#table_dataTable_filter").addClass("contact-assignment-filter");
    $("#table_dataTable2_filter").addClass("contact-assignment-filter");
    $("#table_dataTable_filter.contact-assignment-filter").append('<div class="assignBtnFilter" id="assignBtnFilter"><button type="button" class="btn btn-primary ml-5 float-left" id="assignBtn" disabled>ASSIGN</button></div>');
    $("#table_dataTable2_filter.contact-assignment-filter").append('<div class="unassignBtnFilter" id="unassignBtnFilter"><button type="button" class="btn btn-primary ml-5 float-left" id="unAssignBtn" disabled>UN-ASSIGN</button></div>');
    $("#assignBtnFilter").css("display","none");
    $("#unassignBtnFilter").css("display","none");

    $('#table_dataTable').on('change',"input[type='checkbox']",function(e){
        if($('.chk_member').filter(':checked').length > 0){
            $('#assignBtn').removeAttr('disabled');
        }else{
            $('#assignBtn').attr('disabled','disabled');
        }
    });

    $('#table_dataTable2').on('change',"input[type='checkbox']",function(e){
        if($('.chk_member2').filter(':checked').length > 0){
            $('#unAssignBtn').removeAttr('disabled');
        }else{
            $('#unAssignBtn').attr('disabled','disabled');
        }
    });

    $('#assignBtn').on('click', function(){
        // Check selected members
        var selectedAssignMembers = [];
        $('.chk_member:checkbox:checked').each(function(i){
            selectedAssignMembers[i] = $(this).val();
        })

        if(selectedAssignMembers.length > 0){
            swal({
                title: `Are you sure?`,
                text: "",
                icon: "warning",
                buttons: true,
                // dangerMode: true,
                closeOnClickOutside: false,
            })
            .then((willAssign) => {
                // show loader
                showCustomBlockUI();
                if (willAssign) {
                    jQuery.ajax({
                        url: '{{route('assign_members')}}',
                        type: 'POST',
                        data:{
                            checked_members: selectedAssignMembers,
                            contact_member: $('#member_id').val()
                        },
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if(response.status == true){
                                table.draw();
                                table2.draw();

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
        }else{
            Lobibox.notify('error', {
                icon:false,
                msg: 'Please select members to assign'
            });
        }
    });

    $('#unAssignBtn').click(function(){
        // Check selected members
        var selectedUnassignMembers = [];
        $('.chk_member2:checkbox:checked').each(function(i){
            selectedUnassignMembers[i] = $(this).val();
        })

        if(selectedUnassignMembers.length > 0){
            swal({
                title: `Are you sure?`,
                text: "",
                icon: "warning",
                buttons: true,
                // dangerMode: true,
                closeOnClickOutside: false,
            })
            .then((willAssign) => {
                // show loader
                showCustomBlockUI();
                if (willAssign) {
                    jQuery.ajax({
                        url: '{{route('un_assign_members')}}',
                        type: 'POST',
                        data:{
                            checked_members: selectedUnassignMembers,
                            contact_member: $('#member_id').val()
                        },
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if(response.status == true){
                                table.draw();
                                table2.draw();

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
        }else{
            Lobibox.notify('error', {
                icon:false,
                msg: 'Please select members to unassign'
            });
        }
    });
   

    // On change of "Select Member", all filter will show/hide
    $('#member_id').change(function(){
        if($('#member_id').val() != ""){
            $('.allFilter').removeClass('d-none');
            $('#unassignBtnFilter').css("display","block");

            contactMemberIdFilter = $('#member_id').val();
        }else{
            $('.allFilter').addClass('d-none');
            $('#unassignBtnFilter').css("display","none");
            $("#assignBtnFilter").css("display","none");

            // Blank country dropdown to reset the filter
            $("#country-dd").val("");
            $("#country-dd").trigger('change');

            // Blank all global variables
            contactMemberIdFilter = "";
            signedIdFilter = "";
            countryIdFilter = "";
            stateIdFilter = "";
            cityIdFilter = "";
            townIdFilter = "";
            municipalIdFilter = "";
            placeIdFilter = "";
            neighbourhoodIdFilter = "";
        }
        table.draw();
        table2.draw();
    });
});
</script>
@endpush
