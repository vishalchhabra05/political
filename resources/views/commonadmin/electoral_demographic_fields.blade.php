<div class="col-md-3 mb-4">
    <div class="form-group">
        <label id="country_label">Country</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control electoral" name="country_id[]" onchange="getStates();" multiple="multiple"
                id="country-dd">
                <option value=''>Select Country</option>
                @foreach ($countries as $key=>$value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            <span class="country-error error float-left">Please select country</span>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>State</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 electoral" name="state_id[]" onchange="getCitiesNDistricts();" multiple="multiple"
                id="state-dd">
                <option value=''>Select State</option>
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>District</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 electoral" name="district_id[]" multiple="multiple"
                id="district-dd">
                <option value=''>Select District</option>
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>City</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 electoral" name="city_id[]" onchange="getMunicipalDistrictsNRecintos();" multiple="multiple"
                id="city-dd">
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>Municipal District</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 electoral" name="municipal_district_id[]" onchange="getRecintos();" multiple="multiple"
                id="munciple-district-dd">
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>Recintos</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 electoral" name="recintos_id[]" onchange="getColleges();" multiple="multiple"
                id="recintos-dd">
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>College</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 electoral" name="college_id[]" multiple="multiple"
                id="college-dd">
            </select>
        </div>
    </div>
</div>
