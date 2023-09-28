<div class="col-md-3 mb-4">
    <div class="form-group">
        <label id="country_label">Country</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control personal" name="country_id[]" onchange="getStatesDemogr();" multiple="multiple"
                id="country-dd-pers">
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
            <select class="select2-multiple form-control pr-5 personal" name="state_id[]" onchange="getCitiesDemogr();" multiple="multiple"
                id="state-dd-pers">
                <option value=''>Select State</option>
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>City</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 personal" name="city_id[]" onchange="getMunicipalDistrictsDemogr();" multiple="multiple"
                id="city-dd-pers">
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>Municipal District</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 personal" name="municipal_district_id[]" onchange="getTownsDemogr();" multiple="multiple"
                id="munciple-district-dd-pers">
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>Town</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 personal" name="town_id[]" onchange="getPlacesDemogr();" multiple="multiple"
                id="town-dd-pers">
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>Place</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 personal" name="place_id[]" multiple="multiple"
                id="place-dd-pers">
            </select>
        </div>
    </div>
</div>
<div class="col-md-3 mb-4">
    <div class="form-group">
        <label>Neighbourhood</label>
        <div class="multi-select-input">
            <select class="select2-multiple form-control pr-5 personal" name="neighbourhood_id[]" multiple="multiple"
                id="neighbourhood-dd-pers">
                <option value=''>Select Neighbourhood</option>
                @foreach ($neighbourhoods as $key=>$value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
            