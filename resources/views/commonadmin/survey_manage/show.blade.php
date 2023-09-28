@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">View Survey</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('list_survey') }}">Survey Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Survey</li>
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
                                            <th colspan="6">SURVEY DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th>Survey Name:</th>
                                            <td>@if(!empty($entity->survey_name)){{ $entity->survey_name }}@else N/A @endif</td>
                                            <th>Survey Type:</th>
                                            <td>@if(!empty($entity->survey_type)){{ $entity->survey_type }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>End Date:</th>
                                            <td colspan="3">@if(!empty($entity->end_date)){{ $entity->end_date }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th colspan="6">DEMOGRAPHIC DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th>Country:</th>
                                            <td>@if(!empty($entity->demographicInfo->country_id)){{ $entity->demographicInfo->country_id }}@else N/A @endif</td>
                                            <th>State:</th>
                                            <td>@if(!empty($entity->demographicInfo->state_id)){{ $entity->demographicInfo->state_id }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>City:</th>
                                            <td>@if(!empty($entity->demographicInfo->city_id)){{ $entity->demographicInfo->city_id }}@else N/A @endif</td>
                                            <th>Town:</th>
                                            <td>@if(!empty($entity->demographicInfo->town_id)){{ $entity->demographicInfo->town_id }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Municipal District:</th>
                                            <td>@if(!empty($entity->demographicInfo->municiple_district_id)){{ $entity->demographicInfo->municiple_district_id }}@else N/A @endif</td>
                                            <th>Place:</th>
                                            <td>@if(!empty($entity->demographicInfo->place_id)){{ $entity->demographicInfo->place_id }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Neighbourhood:</th>
                                            <td>@if(!empty($entity->demographicInfo->neighbourhood_id)){{ $entity->demographicInfo->neighbourhood_id }}@else N/A @endif</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
@endsection