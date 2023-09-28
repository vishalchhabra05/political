<script type="text/javascript">
function getNationalIdData(){
    // debugger;
    var getNationalIdVal = $("#national_id").val();
    if(getNationalIdVal.length > 7){
        jQuery.ajax({
            url: '/api/get-citizen',
            type: 'POST',
            data:{national_id:getNationalIdVal},
            success: function (response) {
                // debugger;
                console.log(response);
                if(response.isSuccess && response.isSuccess == true){
                    if(response.data){
                        isNationalIdVerified = 1;
                        var responseData = response.data;
                        setNationalIdData(responseData);
                    }else{
                        isNationalIdVerified = 0;
                        unsetNationalIdData();
                    }
                }else{
                    isNationalIdVerified = 0;
                    unsetNationalIdData();
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                isNationalIdVerified = 0;
                unsetNationalIdData();
            }
        });
    }else{
        isNationalIdVerified = 0;
        unsetNationalIdData();
    }
}

function setNationalIdData(responseData){
    if(responseData.full_Name){
        $("#first_name").val(responseData.full_Name);
        $("#first_name").attr('readonly', true);
    }
    if(responseData.first_Last_Name){
        var lastName = responseData.first_Last_Name;
        if(responseData.second_Last_Name){
            lastName = lastName+' '+responseData.second_Last_Name;
        }
        $("#last_name").val(lastName);
        $("#last_name").attr('readonly', true);
    }
    if(responseData.stateId){
        $("#stateId").attr('disabled', true);
        stateGlobalId = responseData.stateId;
    }
    if(responseData.cityId){
        $("#cityId").attr('disabled', true);
        cityGlobalId = responseData.cityId;
    }
    if(responseData.countryId){
        $("#countryId").val(responseData.countryId);
        $("#countryId").attr('disabled', true);
        $("#countryId").change();
    }
}

// This will be called when no data comes via national id. Unset all the set fields
function unsetNationalIdData(){
    // First name
    $("#first_name").val("");
    $("#first_name").attr('readonly', false);

    // Last name
    $("#last_name").val("");
    $("#last_name").attr('readonly', false);

    // Country, state, city
    stateGlobalId = "";
    cityGlobalId = "";
    $("#countryId").val("");
    $("#countryId").change();
    $("#countryId").attr('disabled', false);
    $("#stateId").attr('disabled', false);
    $("#cityId").attr('disabled', false);

    onCityChange();
}
</script>