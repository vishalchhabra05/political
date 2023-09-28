<script type="text/javascript">
$(document).ready(function() {
    $(".electoral#country-dd").select2({
        placeholder: "Select Country",
        allowClear: true
    });

    $(".electoral#state-dd").select2({
        placeholder: "Select State",
        allowClear: true
    });

    $(".electoral#district-dd").select2({
        placeholder: "Select District",
        allowClear: true
    });

    $(".electoral#city-dd").select2({
        placeholder: "Select City",
        allowClear: true
    });

    $(".electoral#munciple-district-dd").select2({
        placeholder: "Select Municipal District",
        allowClear: true
    });

    $(".electoral#recintos-dd").select2({
        placeholder: "Select Recintos",
        allowClear: true
    });

    $(".electoral#college-dd").select2({
        placeholder: "Select College",
        allowClear: true
    });
});

// Demographic Filter
function getStates(){
    $('.country-error').hide();
    $.ajax({
        url: "{{ route('get_states') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#country-dd").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#state-dd").html(data);

            <?php if(!empty($entity->demographicInfo->state_id)){ ?>
                var stateIds = "{{$entity->demographicInfo->state_id}}"; // Assuming it's a string like "2,3,5"
                var stateIdArray = stateIds.split(","); // Split the string into an array

                $("#state-dd").val(stateIdArray);
                $("#state-dd").trigger('change');
            <?php } ?>

            // Blank all other further dependant dropdowns
            $("#district-dd").html("<option value=''>Select District</option>");
            $("#city-dd").html("<option value=''>Select City</option>");
            $("#munciple-district-dd").html("<option value=''>Select Municiple District</option>");
            $("#recintos-dd").html("<option value=''>Select Recintos</option>");
            $("#college-dd").html("<option value=''>Select College</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                countryId = $("#country-dd").val();

                /*var userTable = $('#table_dataTable').DataTable();
                userTable.draw();*/
                loadData();
            }

            // hide loader
            hideCustomBlockUI();
        }
    });
}

// Hide country error initially
$('.country-error').hide();

function getCitiesNDistricts(){
    $.ajax({
        url: "{{ route('get_cities_n_districts') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#state-dd").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#city-dd").html(data.city);
            $("#district-dd").html(data.district);

            <?php if(!empty($entity->demographicInfo->city_id)){ ?>
                var cityIds = "{{$entity->demographicInfo->city_id}}"; // Assuming it's a string like "2,3,5"
                var cityIdArray = cityIds.split(","); // Split the string into an array

                $("#city-dd").val(cityIdArray);
                $("#city-dd").trigger('change');
            <?php } ?>

            <?php if(!empty($entity->demographicInfo->district_id)){ ?>
                var districtIds = "{{$entity->demographicInfo->district_id}}"; // Assuming it's a string like "2,3,5"
                var districtIdArray = districtIds.split(","); // Split the string into an array

                $("#district-dd").val(districtIdArray);
            <?php } ?>

            // Blank all other further dependant dropdowns
            $("#munciple-district-dd").html("<option value=''>Select Municiple District</option>");
            $("#recintos-dd").html("<option value=''>Select Recintos</option>");
            $("#college-dd").html("<option value=''>Select College</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                stateId = $("#state-dd").val();

                loadData();
            }

            // hide loader
            hideCustomBlockUI();
        }
    });
}

function getMunicipalDistrictsNRecintos(){
    $.ajax({
        url: "{{ route('get_municipal_districts_n_recintos') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#city-dd").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#munciple-district-dd").html(data.municipalDist);
            $("#recintos-dd").html(data.recintos);

            <?php if(!empty($entity->demographicInfo->municiple_district_id)){ ?>
                var municipalDistrictIds = "{{$entity->demographicInfo->municiple_district_id}}"; // Assuming it's a string like "2,3,5"
                var municipalDistrictIdArray = municipalDistrictIds.split(","); // Split the string into an array

                $("#munciple-district-dd").val(municipalDistrictIdArray);
                $("#munciple-district-dd").trigger('change');
            <?php } ?>

            <?php if(!empty($entity->demographicInfo->recintos_id)){ ?>
                var recintosIds = "{{$entity->demographicInfo->recintos_id}}"; // Assuming it's a string like "2,3,5"
                var recintosIdArray = recintosIds.split(","); // Split the string into an array

                $("#recintos-dd").val(recintosIdArray);
                $("#recintos-dd").trigger('change');
            <?php } ?>

            // Blank all other further dependant dropdowns
            $("#college-dd").html("<option value=''>Select College</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                cityId = $("#city-dd").val();

                loadData();
            }

            // hide loader
            hideCustomBlockUI();
        }
    });
}

function getRecintos(){
    $.ajax({
        url: "{{ route('get_recintos') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#munciple-district-dd").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#recintos-dd").html(data);

            <?php if(!empty($entity->demographicInfo->recintos_id)){ ?>
                var recintosIds = "{{$entity->demographicInfo->recintos_id}}"; // Assuming it's a string like "2,3,5"
                var recintosIdArray = recintosIds.split(","); // Split the string into an array

                $("#recintos-dd").val(recintosIdArray);
                $("#recintos-dd").trigger('change');
            <?php } ?>

            // Blank all other further dependant dropdowns
            $("#college-dd").html("<option value=''>Select College</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                municipalDistrictId = $("#munciple-district-dd").val();

                loadData();
            }

            // hide loader
            hideCustomBlockUI();
        }
    });
}

function getColleges(){
    $.ajax({
        url: "{{ route('get_colleges') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#recintos-dd").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#college-dd").html(data);

            <?php if(!empty($entity->demographicInfo->college_id)){ ?>
                var collegeIds = "{{$entity->demographicInfo->college_id}}"; // Assuming it's a string like "2,3,5"
                var collegeIdArray = collegeIds.split(","); // Split the string into an array

                $("#college-dd").val(collegeIdArray);
                $("#college-dd").trigger('change');
            <?php } ?>

            // Blank all other further dependant dropdowns
            // $("#neighbourhood-dd").html("<option value=''>Select Neighbourhood</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                recintosId = $("#town-dd").val();

                loadData();
            }

            // hide loader
            hideCustomBlockUI();
        }
    });
}
</script>