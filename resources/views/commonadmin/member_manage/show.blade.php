@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">View Member Detail</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('list_member') }}">Member Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Member Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-12 col-md-4 mb-4">
                    <div class="box bg-white">
                        <div class="box-body box-profile">
                                @if(!empty($data->members->profile_image))
                                    <img class="profile-user-img img-responsive img-circle" src="{{$data->members->profile_image}}" alt="User profile picture" width="100">
                                @endif

                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th colspan="6">USER DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th>Party Name:</th>
                                            <td>@if(!empty($data->politicalPartyInfo->party_name)){{ $data->politicalPartyInfo->party_name }}@else N/A @endif</td>
                                            <th>National ID:</th>
                                            <td>@if(!empty($data->national_id)){{ $data->national_id }}@else N/A @endif</td>
                                            <th>Full Name:</th>
                                            <td>@if(!empty($data->full_name)){{ $data->full_name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Email:</th>
                                            <td>@if(!empty($data->email)){{ $data->email }}@else N/A @endif</td>
                                            <th>Phone Number:</th>
                                            <td>@if(!empty($data->phone_number)) @if(!empty($data->country_code)){{ $data->country_code }}@endif {{ $data->phone_number }}@else N/A @endif</td>
                                            <th>Alternate Phone Number:</th>
                                            <td>@if(!empty($data->alternate_phone_number)) @if(!empty($data->alt_country_code)){{ $data->alt_country_code }}@endif {{ $data->alternate_phone_number }}@else N/A @endif</td>
                                        </tr>

                                         <tr>
                                            <th>Login Type:</th>
                                            <td>@if(!empty($data->login_type)){{ $data->login_type }}@else N/A @endif</td>
                                            <th>Google:</th>
                                            <td>@if(!empty($data->google_id)) Connected @else N/A @endif</td>
                                            <th>Facebook:</th>
                                           <td>@if(!empty($data->facebook_id)) Connected @else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Linkedin:</th>
                                            <td>@if(!empty($data->linkedin_id)) Connected @else N/A @endif</td>
                                            <th>Status:</th>
                                            <td>@if(isset($data->status)){{
                                                ($data->status == 0) ? "Inactive" : (($data->status == 1) ? "Active" : '') }} @else N/A @endif</td>
                                            <th>Email Verified:</th>
                                            <td>@if(!empty($data->email_verified_at) && $data->email_verified_at != '0000-00-00 00:00:00'){{ DMYHIADateFromat($data->email_verified_at) }}@else-@endif</td>
                                        </tr>

                                        <tr>
                                            <th>Phone Verified:</th>
                                            <td>@if(!empty($data->phone_verified_at) && $data->phone_verified_at != '0000-00-00 00:00:00'){{ DMYHIADateFromat($data->phone_verified_at) }}@else-@endif</td>
                                            <th>Parent:</th>
                                            <td>@if(!empty($data->parent_user_id)){{ $data['parent']->full_name }}@else N/A @endif</td>
                                            <th>Relationship:</th>
                                            <td>@if(!empty($data->relationship_status)){{ $data->relationship_status }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Register Type:</th>
                                            <td>@if(!empty($data->register_type)){{ $data->register_type }}@else N/A @endif</td>
                                            <th>Created:</th>
                                            <td colspan="3">@if(!empty($data->created_at) && $data->created_at != '0000-00-00 00:00:00'){{ DMYHIADateFromat($data->created_at) }}@else-@endif</td>
                                        </tr>

                                        @if(!empty($registerFormExtraFields))
                                            @forEach($registerFormExtraFields->formFieldInfo as $key => $extraFields)
                                                @if($key==0 || $key%3==0)
                                                    <tr>
                                                @endif
                                                    <th>{{$extraFields->field_name}}:</th>
                                                    <td>@if(!empty($extraFields->memberExtraFormfield->value)){{ $extraFields->memberExtraFormfield->value }} @elseif(!empty($extraFields->memberExtraFormfield) && !empty($extraFields->memberExtraFormfield->formFieldOptionInfo)) {{$extraFields->memberExtraFormfield->formFieldOptionInfo->option}} @else N/A @endif</td>
                                            @endForEach
                                        @endif

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th colspan="6">MEMBER DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th>Address:</th>
                                            <td>@if(!empty($data->members->address)){{ $data->members->address }}@else N/A @endif</td>
                                            <th>Country:</th>
                                            <td>@if(!empty($data->members->country->name)){{ $data->members['country']->name }}@else N/A @endif</td>
                                            <th>State:</th>
                                            <td>@if(!empty($data->members->state->name)){{ $data->members['state']->name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>City:</th>
                                            <td>@if(!empty($data->members->city->name)){{ $data->members['city']->name }}@else N/A @endif</td>
                                            <th>Town:</th>
                                            <td>@if(!empty($data->members->town->name)){{ $data->members['town']->name }}@else N/A @endif</td>
                                            <th>Munciple District:</th>
                                            <td>@if(!empty($data->members['munciple_district']->name)){{ $data->members['munciple_district']->name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Place:</th>
                                            <td>@if(!empty($data->members['place']->name)){{ $data->members['place']->name }}@else N/A @endif</td>
                                            <th>Neighbourhood:</th>
                                            <td>@if(!empty($data->members->neighbourhood->name)){{ $data->members['neighbourhood']->name }}@else N/A @endif</td>
                                            <th>DOB:</th>
                                            <td>@if(!empty($data->members->dob)){{ $data->members->dob }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>AGE:</th>
                                            <td>@if(!empty($data->members->age)){{ $data->members->age }}@else N/A @endif</td>
                                            <th>Gender:</th>
                                            <td>@if(!empty($data->members->gender)){{ $data->members->gender }}@else N/A @endif</td>
                                            <th>Recommend:</th>
                                            <td>@if(!empty($data->members['reference']->full_name)){{ $data->members['reference']->full_name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Is Approved:</th>
                                            <td>@if(isset($data->members->status)){{
                                                ($data->members->status == 0) ? "Draft: Member register but not completed profile" : (($data->members->status == 1) ? "Pending : Completed profile , but not approved by admin" : (($data->members->status == 2) ? "Approved : Approved by admin" : (($data->members->status == 3) ? "Reject : Admin reject the Member" : ""))) }} @else N/A @endif
                                            </td>
                                        </tr>

                                        @if(!empty($profileFormPersExtraFields))
                                            @forEach($profileFormPersExtraFields->formFieldInfo as $key => $extraFields)
                                                @if($key==0 || $key%3==0)
                                                    <tr>
                                                @endif
                                                    <th>{{$extraFields->field_name}}:</th>
                                                    <td>@if(!empty($extraFields->memberExtraFormfield->value)){{ $extraFields->memberExtraFormfield->value }} @elseif(!empty($extraFields->memberExtraFormfield) && !empty($extraFields->memberExtraFormfield->formFieldOptionInfo)) {{$extraFields->memberExtraFormfield->formFieldOptionInfo->option}} @else N/A @endif</td>
                                            @endForEach
                                        @endif

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th colspan="6">ELECTORAL LOGISTIC DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <?php
                                            $electoralDemographic = (!empty($data->members->memberElectoralInfo->electoralDemographic)?$data->members->memberElectoralInfo->electoralDemographic:"");
                                        ?>
                                        <tr>
                                            <th>Electoral College:</th>
                                            <td>@if(!empty($data->members->memberElectoralInfo->electoral_college)){{ $data->members->memberElectoralInfo->electoral_college }}@else N/A @endif</td>
                                            <th>Electoral Precinct:</th>
                                            <td>@if(!empty($data->members->memberElectoralInfo->electoral_precint)){{ $data->members->memberElectoralInfo->electoral_precint }}@else N/A @endif</td>
                                            <th>Country:</th>
                                            <td>@if(!empty($electoralDemographic->country->name)){{ $electoralDemographic->country->name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>State:</th>
                                            <td>@if(!empty($electoralDemographic->state->name)){{ $electoralDemographic->state->name }}@else N/A @endif</td>
                                            <th>City:</th>
                                            <td>@if(!empty($electoralDemographic->city->name)){{ $electoralDemographic->city->name }}@else N/A @endif</td>
                                            <th>Municipal District:</th>
                                            <td>@if(!empty($electoralDemographic->municipalDistrictInfo->name)){{ $electoralDemographic->municipalDistrictInfo->name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Town:</th>
                                            <td>@if(!empty($electoralDemographic->townInfo->name)){{ $electoralDemographic->townInfo->name }}@else N/A @endif</td>
                                            <th>Place:</th>
                                            <td>@if(!empty($electoralDemographic->placeInfo->name)){{ $electoralDemographic->placeInfo->name }}@else N/A @endif</td>
                                            <th>Neighbourhood:</th>
                                            <td>@if(!empty($electoralDemographic->neighbourhoodInfo->name)){{ $electoralDemographic->neighbourhoodInfo->name }}@else N/A @endif</td>
                                        </tr>

                                        @if(!empty($profileFormElectExtraFields))
                                            @forEach($profileFormElectExtraFields->formFieldInfo as $key => $extraFields)
                                                @if($key==0 || $key%3==0)
                                                    <tr>
                                                @endif
                                                    <th>{{$extraFields->field_name}}:</th>
                                                    <td>@if(!empty($extraFields->memberExtraFormfield->value)){{ $extraFields->memberExtraFormfield->value }} @elseif(!empty($extraFields->memberExtraFormfield) && !empty($extraFields->memberExtraFormfield->formFieldOptionInfo)) {{$extraFields->memberExtraFormfield->formFieldOptionInfo->option}} @else N/A @endif</td>
                                            @endForEach
                                        @endif

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th colspan="6">WORK INFO DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        @if(!empty($data->members->memberWorkInfos))
                                            @forEach($data->members->memberWorkInfos as $key => $workInfoRow)
                                                <tr>
                                                    <th colspan="6">Work Info [{{$key + 1}}]</th>
                                                </tr>
                                                <tr>
                                                    <th>Work Status:</th>
                                                    <td>@if(!empty($workInfoRow->work_status)){{ $workInfoRow->work_status }}@else N/A @endif</td>
                                                    <th>Job Type:</th>
                                                    <td>@if(!empty($workInfoRow->job_type)){{ $workInfoRow->job_type }}@else N/A @endif</td>
                                                    <th>Company Industry:</th>
                                                    <td>@if(!empty($workInfoRow->companyIndustry->name)){{ $workInfoRow->companyIndustry->name }}@else N/A @endif</td>
                                                </tr>

                                                <tr>
                                                    <th>Company Name:</th>
                                                    <td>@if(!empty($workInfoRow->company_name)){{ $workInfoRow->company_name }}@else N/A @endif</td>
                                                    <th>Job Title:</th>
                                                    <td>@if(!empty($workInfoRow->job_title)){{ $workInfoRow->job_title }}@else N/A @endif</td>
                                                    <th>Company Phone:</th>
                                                    <td>@if(!empty($workInfoRow->company_phone)) @if(!empty($workInfoRow->countryCodeInfo->phonecode)){{ $workInfoRow->countryCodeInfo->phonecode }}@endif {{ $workInfoRow->company_phone }}@else N/A @endif</td>
                                                </tr>

                                                <tr>
                                                    <th>Country:</th>
                                                    <td>@if(!empty($workInfoRow->workDemographic->country->name)){{ $workInfoRow->workDemographic->country->name }}@else N/A @endif</td>
                                                    <th>State:</th>
                                                    <td>@if(!empty($workInfoRow->workDemographic->state->name)){{ $workInfoRow->workDemographic->state->name }}@else N/A @endif</td>
                                                    <th>City:</th>
                                                    <td>@if(!empty($workInfoRow->workDemographic->city->name)){{ $workInfoRow->workDemographic->city->name }}@else N/A @endif</td>
                                                </tr>
                                            @endForEach
                                        @else
                                            <tr>
                                                <th colspan="6">N/A</th>
                                            </tr>
                                        @endif

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th colspan="6">EDUCATIONAL INFO DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        @if(!empty($data->members->memberEducationalInfos))
                                            @forEach($data->members->memberEducationalInfos as $key => $educationalInfoRow)
                                                <tr>
                                                    <th colspan="6">Educational Info [{{$key + 1}}]</th>
                                                </tr>
                                                <tr>
                                                    <th>Degree Level:</th>
                                                    <td>@if(!empty($educationalInfoRow->degree_level)){{ $educationalInfoRow->degree_level }}@else N/A @endif</td>
                                                    <th>Degree:</th>
                                                    <td colspan="3">@if(!empty($educationalInfoRow->bachelor_degree)){{ $educationalInfoRow->bachelor_degree->name }}@else N/A @endif</td>
                                                </tr>

                                                <tr>
                                                    <th>Institution Name:</th>
                                                    <td>@if(!empty($educationalInfoRow->institution_name)){{ $educationalInfoRow->institution_name }}@else N/A @endif</td>
                                                    <th>Stream:</th>
                                                    <td colspan="3">@if(!empty($educationalInfoRow->stream)){{ $educationalInfoRow->stream }}@else N/A @endif</td>
                                                </tr>
                                            @endForEach
                                        @else
                                            <tr>
                                                <th colspan="6">N/A</th>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
@endsection