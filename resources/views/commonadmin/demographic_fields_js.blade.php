<script type="text/javascript">
$(document).ready(function() {
    $(".personal#country-dd-pers").select2({
        placeholder: "Select Country",
        allowClear: true
    });

    $(".personal#state-dd-pers").select2({
        placeholder: "Select State",
        allowClear: true
    });

    $(".personal#city-dd-pers").select2({
        placeholder: "Select City",
        allowClear: true
    });

    $(".personal#munciple-district-dd-pers").select2({
        placeholder: "Select Municipal District",
        allowClear: true
    });

    $(".personal#town-dd-pers").select2({
        placeholder: "Select Town",
        allowClear: true
    });

    $(".personal#place-dd-pers").select2({
        placeholder: "Select Place",
        allowClear: true
    });

    $(".personal#neighbourhood-dd-pers").select2({
        placeholder: "Select Neighbourhood",
        allowClear: true
    });
});

// Demographic Filter
function getStatesDemogr(){
    $('.country-error').hide();
    $.ajax({
        url: "{{ route('get_states') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#country-dd-pers").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#state-dd-pers").html(data);

            <?php if(!empty($entity->demographicInfo->state_id)){ ?>
                var stateIds = "{{$entity->demographicInfo->state_id}}"; // Assuming it's a string like "2,3,5"
                var stateIdArray = stateIds.split(","); // Split the string into an array

                $("#state-dd-pers").val(stateIdArray);
                $("#state-dd-pers").trigger('change');
            <?php } ?>

            // Blank all other further dependant dropdowns
            $("#city-dd-pers").html("<option value=''>Select City</option>");
            $("#town-dd-pers").html("<option value=''>Select Town</option>");
            $("#munciple-district-dd-pers").html("<option value=''>Select Municiple District</option>");
            $("#place-dd-pers").html("<option value=''>Select Place</option>");
            // $("#neighbourhood-dd-pers").html("<option value=''>Select Neighbourhood</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                countryId = $("#country-dd-pers").val();

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

function getCitiesDemogr(){
    $.ajax({
        url: "{{ route('get_cities') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#state-dd-pers").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#city-dd-pers").html(data);

            <?php if(!empty($entity->demographicInfo->city_id)){ ?>
                var cityIds = "{{$entity->demographicInfo->city_id}}"; // Assuming it's a string like "2,3,5"
                var cityIdArray = cityIds.split(","); // Split the string into an array

                $("#city-dd-pers").val(cityIdArray);
                $("#city-dd-pers").trigger('change');
            <?php } ?>

            // Blank all other further dependant dropdowns
            $("#munciple-district-dd-pers").html("<option value=''>Select Municiple District</option>");
            $("#town-dd-pers").html("<option value=''>Select Town</option>");
            $("#place-dd-pers").html("<option value=''>Select Place</option>");
            // $("#neighbourhood-dd-pers").html("<option value=''>Select Neighbourhood</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                stateId = $("#state-dd-pers").val();

                loadData();
            }

            // hide loader
            hideCustomBlockUI();
        }
    });
}

function getMunicipalDistrictsDemogr(){
    $.ajax({
        url: "{{ route('get_municipal_districts') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#city-dd-pers").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#munciple-district-dd-pers").html(data);

            <?php if(!empty($entity->demographicInfo->municiple_district_id)){ ?>
                var municipalDistrictIds = "{{$entity->demographicInfo->municiple_district_id}}"; // Assuming it's a string like "2,3,5"
                var municipalDistrictIdArray = municipalDistrictIds.split(","); // Split the string into an array

                $("#munciple-district-dd-pers").val(municipalDistrictIdArray);
                $("#munciple-district-dd-pers").trigger('change');
            <?php } ?>

            // Blank all other further dependant dropdowns
            $("#town-dd-pers").html("<option value=''>Select Town</option>");
            $("#place-dd-pers").html("<option value=''>Select Place</option>");
            // $("#neighbourhood-dd-pers").html("<option value=''>Select Neighbourhood</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                cityId = $("#city-dd-pers").val();

                loadData();
            }

            // hide loader
            hideCustomBlockUI();
        }
    });
}

function getTownsDemogr(){
    $.ajax({
        url: "{{ route('get_towns') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#munciple-district-dd-pers").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#town-dd-pers").html(data);

            <?php if(!empty($entity->demographicInfo->town_id)){ ?>
                var townIds = "{{$entity->demographicInfo->town_id}}"; // Assuming it's a string like "2,3,5"
                var townIdArray = townIds.split(","); // Split the string into an array

                $("#town-dd-pers").val(townIdArray);
                $("#town-dd-pers").trigger('change');
            <?php } ?>

            // Blank all other further dependant dropdowns
            $("#place-dd-pers").html("<option value=''>Select Place</option>");
            // $("#neighbourhood-dd-pers").html("<option value=''>Select Neighbourhood</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                municipalDistrictId = $("#munciple-district-dd-pers").val();

                loadData();
            }

            // hide loader
            hideCustomBlockUI();
        }
    });
}

function getPlacesDemogr(){
    $.ajax({
        url: "{{ route('get_places') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#town-dd-pers").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#place-dd-pers").html(data);

            <?php if(!empty($entity->demographicInfo->place_id)){ ?>
                var placeIds = "{{$entity->demographicInfo->place_id}}"; // Assuming it's a string like "2,3,5"
                var placeIdArray = placeIds.split(","); // Split the string into an array

                $("#place-dd-pers").val(placeIdArray);
                $("#place-dd-pers").trigger('change');
            <?php } ?>

            // Blank all other further dependant dropdowns
            // $("#neighbourhood-dd-pers").html("<option value=''>Select Neighbourhood</option>");

            // For member management - filters only - this will work
            if(typeof pageName !== 'undefined' && pageName == "MemberManage"){
                // Update global variables
                townId = $("#town-dd-pers").val();

                loadData();
            }

            // hide loader
            hideCustomBlockUI();
        }
    });
}

function getNeighbourhoods(){
    $.ajax({
        url: "{{ route('get_neighbourhoods') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#place-dd-pers").val()},
        beforeSend: function(){
            // show loader
            showCustomBlockUI();
        },
        success: function(data){
            $("#neighbourhood-dd-pers").html(data);

            <?php if(!empty($entity->demographicInfo->neighbourhood_id)){ ?>
                var neighbourhoodIds = "{{$entity->demographicInfo->neighbourhood_id}}"; // Assuming it's a string like "2,3,5"
                var neighbourhoodIdArray = neighbourhoodIds.split(","); // Split the string into an array

                $("#neighbourhood-dd-pers").val(neighbourhoodIdArray);
                $("#neighbourhood-dd-pers").trigger('change');
            <?php } ?>

            // hide loader
            hideCustomBlockUI();
        }
    });
}
</script>