@extends('layouts.superadmin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">Edit Advertisement</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.list_advertisement') }}">Advertisement Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Advertisement</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-12 col-md-4 mb-4">
                    {!! Form::model($entity, ['route' => ['superadmin.update_advertisement'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editAdvertisementForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
                    {{ csrf_field() }}
                    <input type="hidden" name="update_id" value="{{ $entity->id }}">
                        <div class="box-row flex-wrap">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Title</label>
                                    <div class="">
                                        {!! Form::text('title', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Title', 'maxlength'=>255]) !!}
                                        <span class="help-block">
                                            <?= $errors->has('title') ? $errors->first('title') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <div class="">
                                        {!! Form::date('end_date', null, ['required'=>'required','class'=>'form-control chkAlphabets', 'id'=>'end_date','placeholder'=>'Title']) !!}
                                        <span class="help-block">
                                            <?= $errors->has('end_date') ? $errors->first('end_date') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Link</label>
                                    <div class="">
                                        {!! Form::url('link_url', null, ['required'=>'required','class'=>'form-control chkAlphabets','placeholder'=>'Link', 'maxlength'=>500]) !!}
                                        <span class="help-block">
                                            <?= $errors->has('link_url') ? $errors->first('link_url') : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label>Image</label>
                                    <div class="">
                                        {!! Form::file('image', ['accept'=>'image/*'], ['class'=>'form-control chkAlphabets']) !!}
                                        <span class="help-block">
                                            <?= $errors->has('image') ? $errors->first('image') : '' ?>
                                        </span>
                                        <img class="profile-user-img img-responsive img-circle" src="{{$entity->image}}" alt="advertisement image" width="100" style="padding-top: 10px;">
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-6 mb-3">
                                <img class="profile-user-img img-responsive img-circle" src="{{$entity->image}}" alt="advertisement image" width="100">
                            </div> -->
                            <div class="col-md-12 mb-3 text-center">
                                <a type="submit" href="{{ route('superadmin.list_advertisement') }}" class="btn light">Cancel</a>
                                <button type="submit" class="btn light">Submit</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
@endsection
@push('current-page-js')
<script type="text/javascript">
$("#editAdvertisementForm").validate({
    rules: {
        title: {
            required: true,
            maxlength: 255,
        },
        link_url: {
            required: true,
            maxlength: 500,
        },
    },
    messages:{
        title:{
            required: 'Title is required.'
        },
        link_url:{
            required: 'Link is required.'
        }
    }
});
</script>
@endpush