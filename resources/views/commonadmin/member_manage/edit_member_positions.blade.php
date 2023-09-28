@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Edit Member Position</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('list_position', ['memberId' => base64_encode($memberId)]) }}">List Member Positions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Member Position</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['update_position'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editMemberPositionForm', 'autocomplete'=>'off']) !!}
            {{ csrf_field() }}
            <input type="hidden" name="update_id" value="{{ $entity->id }}">
                <div class="box-row flex-wrap comman-form-design">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label>Select Position<span class="required">*</span></label>
                            <div class="">
                                {!! Form::select('political_position_id', (!empty($partyPositions)?$partyPositions:[]), null, ['class' => 'form-control','id'=>'political_position_id', 'placeholder'=> 'Select Position', 'disabled'=> 'true']) !!}
                                <span class="help-block">
                                    <?= $errors->has('political_position_id') ? $errors->first('political_position_id') : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="memberId" value="{{$memberId}}">
                    @include('commonadmin.electoral_demographic_fields')
                    <div class="col-md-12 mb-3 text-left">
                        <a href="{{ route('list_position',['memberId' => base64_encode($memberId)]) }}" class="btn btn-primary">Cancel</a>
                        <a href="javascript:void(0);" onclick="editMemberPositionForm();" class="btn btn-primary ">Submit</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@push('current-page-js')
@include('commonadmin.electoral_demographic_fields_js')
<script type="text/javascript">
$(document).ready(function(){
    <?php if(!empty($entity->demographicInfo->country_id)){ ?>
        var countryIds = "{{$entity->demographicInfo->country_id}}"; // Assuming it's a string like "2,3,5"
        var countryIdArray = countryIds.split(","); // Split the string into an array

        $("#country-dd").val(countryIdArray);
        $("#country-dd").trigger('change');
    <?php } ?>

    <?php if(!empty($entity->demographicInfo->neighbourhood_id)){ ?>
        var neighbourhoodIds = "{{$entity->demographicInfo->neighbourhood_id}}"; // Assuming it's a string like "2,3,5"
        var neighbourhoodIdArray = neighbourhoodIds.split(","); // Split the string into an array

        $("#neighbourhood-dd").val(neighbourhoodIdArray);
        $("#neighbourhood-dd").trigger('change');
    <?php } ?>
});

$("#editMemberPositionForm").validate({
    rules: {
        'country_id[]': {
            required: true
        }
    },
    messages:{
    },
    errorPlacement: function(error, element){
        if(element.hasClass("select2-hidden-accessible") && element.attr("multiple")) {
            // Multiselect Select2 dropdown
            error.insertAfter(element.next(".select2-container"));
        }else{
            error.insertAfter(element);
        }
    }
});

function editMemberPositionForm(){
    // show loader
    showCustomBlockUI();
    if($("#editMemberPositionForm").valid()){
        $("#editMemberPositionForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
</script>
@endpush