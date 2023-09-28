@extends('layouts.superadmin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">View Political Party</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.list_political_party') }}">Political Party Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Political Party</li>
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
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th colspan="6">POLITICAL PARTY DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th>Party Name:</th>
                                            <td>@if(!empty($entity->party_name)){{ $entity->party_name }}@else N/A @endif</td>
                                            <th>Short Name:</th>
                                            <td>@if(!empty($entity->short_name)){{ $entity->short_name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Logo:</th>
                                            <td>@if(!empty($entity->logo))<img class="profile-user-img img-responsive img-circle" src="{{ $entity->logo }}" alt="Logo image" width="70">@else N/A @endif</td>
                                            <th>Party Slogan:</th>
                                            <td>@if(!empty($entity->party_slogan)){{ $entity->party_slogan }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th colspan="6">ADMIN DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th>National Id:</th>
                                            <td>@if(!empty($entity->partyAdminInfo->national_id)){{ $entity->partyAdminInfo->national_id }}@else N/A @endif</td>
                                            <th>First Name:</th>
                                            <td>@if(!empty($entity->partyAdminInfo->first_name)){{ $entity->partyAdminInfo->first_name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Last Name:</th>
                                            <td>@if(!empty($entity->partyAdminInfo->last_name)){{ $entity->partyAdminInfo->last_name }}@else N/A @endif</td>
                                            <th>Country:</th>
                                            <td>@if(!empty($entity->partyAdminInfo->countryInfo->name)){{ $entity->partyAdminInfo->countryInfo->name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>State:</th>
                                            <td>@if(!empty($entity->partyAdminInfo->stateInfo->name)){{ $entity->partyAdminInfo->stateInfo->name }}@else N/A @endif</td>
                                            <th>City:</th>
                                            <td>@if(!empty($entity->partyAdminInfo->cityInfo->name)){{ $entity->partyAdminInfo->cityInfo->name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Email:</th>
                                            <td>@if(!empty($entity->partyAdminInfo->email)){{ $entity->partyAdminInfo->email }}@else N/A @endif</td>
                                            <th>Phone Number:</th>
                                            <td>
                                                @if(!empty($entity->partyAdminInfo->countryCodeInfo->phonecode))
                                                    {{ $entity->partyAdminInfo->countryCodeInfo->phonecode.' ' }} 
                                                @endif
                                                @if(!empty($entity->partyAdminInfo->phone_number)){{ $entity->partyAdminInfo->phone_number }}@else N/A @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Alt Phone Number:</th>
                                            <td>
                                                @if(!empty($entity->partyAdminInfo->altCountryCodeInfaltC->phonecode))
                                                    {{ $entity->partyAdminInfo->altCountryCodeInfaltC->phonecode.' ' }} 
                                                @endif
                                                @if(!empty($entity->partyAdminInfo->alternate_phone_number)){{ $entity->partyAdminInfo->alternate_phone_number }}@else N/A @endif</td>
                                            <th>National Id Image</th>
                                            <td>@if(!empty($entity->partyAdminInfo->national_id_image))<img class="profile-user-img img-responsive img-circle" src="{{ $entity->partyAdminInfo->national_id_image }}" alt="National Id image" width="70">@else N/A @endif</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
@endsection