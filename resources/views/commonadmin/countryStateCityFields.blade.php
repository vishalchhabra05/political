<?php
    $disabled = false;
    if(!empty($entity->id)){ // in edit case
        $disabled = true;
    }
?>
<div class="col-md-6 mb-3">
    <div class="form-group">
        <label>Country<span class="required">*</span></label>
        <div class="">
            {!! Form::select('country_id', (!empty($countries)?$countries:[]), null, ['class' => 'form-control', 'placeholder' => 'Select Country',"id"=>"countryId", "onChange" => "getStates()", 'disabled'=>$disabled]) !!}
            <input type="hidden" id="countryIdHidden" name="countryId" value="">
            <span class="help-block">
                <?= $errors->has('country_id') ? $errors->first('country_id') : '' ?>
            </span>
        </div>
    </div>
</div>
<div class="col-md-6 mb-3">
    <div class="form-group">
        <label>State<span class="required">*</span></label>
        <div class="">
            {!! Form::select('state_id', [], null, ['class' => 'form-control', 'placeholder' => 'Select State',"id"=>"stateId", "onChange" => "getCities()", 'disabled'=>$disabled]) !!}
            <input type="hidden" id="stateIdHidden" name="stateId" value="">
            <span class="help-block">
                <?= $errors->has('state_id') ? $errors->first('state_id') : '' ?>
            </span>
        </div>
    </div>
</div>
<div class="col-md-6 mb-3">
    <div class="form-group">
        <label>City<span class="required">*</span></label>
        <div class="">
            {!! Form::select('city_id', [], null, ['class' => 'form-control', 'placeholder' => 'Select City',"id"=>"cityId", "onchange"=>"onCityChange()", 'disabled'=>$disabled]) !!}
            <input type="hidden" id="cityIdHidden" name="cityId" value="">
            <span class="help-block">
                <?= $errors->has('city_id') ? $errors->first('city_id') : '' ?>
            </span>
        </div>
    </div>
</div>