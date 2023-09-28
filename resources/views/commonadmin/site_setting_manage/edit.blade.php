@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
<div class="page-title col-sm-12">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="heading-main-top">Site Setting Management</h1>
        </div>
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Site Setting Management</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="row">
        <div class="col-lg-12 col-md-4 mb-4">
            {!! Form::model($entity, ['route' => ['update_sitesetting'], 'method' => 'POST', 'class'=>'box bg-white validate', 'id'=>'editSettingForm', 'autocomplete'=>'off', 'enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
                <div class="box-row flex-wrap comman-form-design">
                    @foreach($entity as $key => $setting)
                        <div class="col-md-12 mb-6">
                            <div class="form-group">
                                <label>{{ $setting->name }}</label>
                                <div class="">
                                    @if($setting->field_type == 'TEXT')
                                        {!! Form::text($setting->slug, $setting->value, ['class'=>'form-control', 'placeholder'=>$setting->name,'required'=>true]) !!}
                                    @elseif($setting->field_type == 'URL')
                                        {!! Form::url($setting->slug, $setting->value, ['class'=>'form-control', 'placeholder'=>$setting->name,'required'=>true]) !!}
                                    @elseif($setting->field_type == 'NUMBER')
                                        {!! Form::number($setting->slug, $setting->value, ['class'=>'form-control', 'placeholder'=>$setting->name,'required'=>true]) !!}
                                    @elseif($setting->field_type == 'TEXTAREA')
                                        {!! Form::textarea($setting->slug, $setting->value, ['class'=>'form-control', 'placeholder'=>$setting->name,'required'=>true,'maxlength'=>'2000']) !!}
                                    @elseif($setting->field_type == 'EMAIL')
                                        {!! Form::email($setting->slug, $setting->value, ['class'=>'form-control', 'placeholder'=>$setting->name,'required'=>true,'maxlength'=>'100']) !!}
                                    @elseif($setting->field_type == 'CKEDITOR' && $setting->slug == 'contact_us_support_content')
                                        {!! Form::textarea($setting->slug, $setting->value,['required'=>'required','id'=>'ckeditor1','class'=>'form-control']) !!}
                                    @elseif($setting->field_type == 'CKEDITOR' && $setting->slug == 'contact_us_sales_enquiry_content')
                                        {!! Form::textarea($setting->slug, $setting->value,['required'=>'required','id'=>'ckeditor2','class'=>'form-control']) !!}
                                    @elseif($setting->field_type == 'FILE')
                                        <input type="file" id="{{$setting->slug}}" name="{{$setting->slug}}" accept=".jpeg, .jpg, .png" />
                                    @endif
                                    <span class="help-block">
                                        <?= $errors->has($setting->slug) ? $errors->first($setting->slug) : '' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-md-12 mb-3 text-center">
                        <a href="javascript:void(0);" onclick="editSettingForm();" class="btn btn-primary">Submit</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@push('current-page-js')
<script src="{{asset('ckeditor/ckeditor.js')}}"></script> 
<script type="text/javascript">
function editSettingForm(){
    // show loader
    showCustomBlockUI();
    if($("#editSettingForm").valid()){
        $("#editSettingForm").submit();
    }else{
        // hide loader
        hideCustomBlockUI();
    }
}
$("#editSettingForm").validate({
    rules: {
        facebook_url: {
            required: true,
            maxlength: 255,
        },
        instagram_url: {
            required: true,
            maxlength: 255,
        },
        twitter_url: {
            required: true,
            maxlength: 255,
        },
        linkedin_url: {
            required: true,
            maxlength: 255,
        },
        contact_email: {
            required: true,
            maxlength: 100,
        },
        contact_phoneno: {
            required: true,
            maxlength: 16,
        },
        contact_address: {
            required: true,
            maxlength: 500,
        },
    }
});
</script>
@endpush