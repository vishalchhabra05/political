@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <?php
            if($formType == 'register'){
                $pageTitleName = "Member Register Form";
            }elseif($formType == 'profile'){
                $pageTitleName = "User Profile Form";
            }else{
                $pageTitleName = "Survey Form";
            }
        ?>
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">View Custom Field</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('list_form_customization', ['formType' => $formType, 'formId' => $formId]) }}">{{$pageTitleName}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Custom Field</li>
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
                                            <th colspan="6">CUSTOM FIELD DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th>Party Name:</th>
                                            <td>@if(!empty($entity->politicalPartyInfo->party_name)){{ $entity->politicalPartyInfo->party_name }}@else N/A @endif</td>
                                            <th>Form Type:</th>
                                            <td colspan="3">@if(!empty($entity->formInfo->form_type)){{ toCamelCase($entity->formInfo->form_type) }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Field Name:</th>
                                            <td>@if(!empty($entity->field_name)){{ $entity->field_name }}@else N/A @endif</td>
                                            <th>Es Field Name:</th>
                                            <td colspan="3">@if(!empty($entity->es_field_name)){{ $entity->es_field_name }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Field Type:</th>
                                            <td>@if(!empty($entity->field_type)){{ toCamelCase($entity->field_type) }}@else N/A @endif</td>
                                            <th>Tab Type:</th>
                                            <td colspan="3">@if(!empty($entity->tab_type)){{ getCustomFieldTabs($entity->tab_type) }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Min Length:</th>
                                            <td>@if(!empty($entity->field_min_length)){{ $entity->field_min_length }}@else N/A @endif</td>
                                            <th>Max Length:</th>
                                            <td>@if(!empty($entity->field_max_length)){{ $entity->field_max_length }}@else N/A @endif</td>
                                            <th>Decimal Points:</th>
                                            <td>@if(!empty($entity->decimal_points)){{ $entity->decimal_points }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Field Options:</th>
                                            <td colspan="5">@if(!empty($entity->formFieldOptionInfo)){{ $entity->formFieldOptionInfo }}@else N/A @endif</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
@endsection