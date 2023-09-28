@extends('layouts.superadmin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">Add State</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.list_state') }}">State Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add State</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-12 col-md-4 mb-4">
                    {!! Form::model($entity, ['route' => ['superadmin.store_state'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'addStateForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
                    {{ csrf_field() }}
                        <div class="box-row flex-wrap">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Country</label>
                                    <div class="">
                                        {!! Form::select('country_id', (!empty($country)?$country:['name']), null, ['required'=>'required','class' => 'form-control','id'=>'country', 'placeholder'=> 'Select Country']) !!}
                                        <span class="help-block">
                                            <?= $errors->has('country_id') ? $errors->first('country_id') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>State Name</label>
                                    <div class="">
                                        {!! Form::text('name', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'State Name', 'maxlength'=>255]) !!}
                                        <span class="help-block">
                                            <?= $errors->has('name') ? $errors->first('name') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 text-center">
                                <a href="{{ route('superadmin.list_state') }}" class="btn light">Cancel</a>
                                <button type="submit" class="btn light">Submit</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
@endsection
@push('current-page-js')
<script type="text/javascript">
$("#addStateForm").validate({
    rules: {
        name: {
            required: true,
            maxlength: 255,
        },
        country_id:{
            required:true
        },
    },
    messages:{
        name:{
            required: 'State is required.'
        },
        country_id:{
            required: 'Country is required.'
        }
    }
});
</script>
@endpush