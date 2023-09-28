<script type="text/javascript">
var stateGlobalId = "";
var cityGlobalId = "";
var isNationalIdVerified = 0;
<?php if(!empty($errors)){ ?>
    getNationalIdData();
    /*getStates();
    getCities();*/
<?php } ?>

function getStates(){
    var selectedCountryValue = $("#countryId").val();
    $("#countryIdHidden").val(selectedCountryValue);

    $.ajax({
        url: "{{ route('get_states') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#countryId").val()},
        beforeSend: function(){
            // show loader
            // showCustomBlockUI(); // commented as it was blinking on screen while adding national id
        },
        success: function(data){
            // debugger;
            $("#stateId").html(data);
            var oldState = "{{ (old('stateId')?old('stateId'):($entity->state_id ?? '')) }}";
            if(oldState){
                $("#stateId").val(oldState);
                $("#stateIdHidden").val(oldState);
            }else if(stateGlobalId){
                $("#stateId").val(stateGlobalId);
                $("#stateIdHidden").val(stateGlobalId);
            }
            getCities();
            // hide loader
            hideCustomBlockUI();
        }
    });
}

function getCities(){
    var selectedStateValue = $("#stateId").val();
    $("#stateIdHidden").val(selectedStateValue);

    $.ajax({
        url: "{{ route('get_cities') }}",
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "id" : $("#stateId").val()},
        beforeSend: function(){
            // show loader
            // showCustomBlockUI(); // commented as it was blinking on screen while adding national id
        },
        success: function(data){
            // debugger;
            $("#cityId").html(data);
            var oldCity = "{{ (old('cityId')?old('cityId'):($entity->city_id ?? '')) }}";
            if(oldCity){
                $("#cityId").val(oldCity);
                $("#cityIdHidden").val(oldCity);
            }else if(cityGlobalId){
                $("#cityId").val(cityGlobalId);
                $("#cityIdHidden").val(cityGlobalId);
            }
            // hide loader
            hideCustomBlockUI();
        }
    });
}

function onCityChange(){
    // debugger;
    var selectedCityValue = $("#cityId").val();
    $("#cityIdHidden").val(selectedCityValue);
}
</script>