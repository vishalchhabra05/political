@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <?php
            if($formType == 'Partywall'){
                $pageTitleName = "Party Wall Management";
            }elseif($formType == 'News'){
                $pageTitleName = "Article/News Management";
            }else{
                $pageTitleName = "Post Management";
            }
        ?>
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">View Post</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('list_party_wall', ['formType' => $formType]) }}">{{$pageTitleName}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Post</li>
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
                                            <th colspan="6">POST DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th>Post Heading:</th>
                                            <td>@if(!empty($entity->post_heading)){{ $entity->post_heading }}@else N/A @endif</td>
                                            <th>Posted Date Time:</th>
                                            <td>@if(!empty($entity->posted_date_time) && $entity->posted_date_time != '0000-00-00 00:00:00'){{ DMYHIADateFromat($entity->posted_date_time) }}@else-@endif</td>
                                        </tr>

                                        <tr>
                                            <th>From Date:</th>
                                            <td>@if(!empty($entity->from_date) && $entity->from_date != '0000-00-00 00:00:00'){{ DMYHIADateFromat($entity->from_date) }}@else-@endif</td>
                                            <th>To Date:</th>
                                            <td>@if(!empty($entity->to_date) && $entity->to_date != '0000-00-00 00:00:00'){{ DMYHIADateFromat($entity->to_date) }}@else-@endif</td>
                                        </tr>

                                        <tr>
                                            <th>Post Image:</th>
                                            <td>@if(!empty($entity->post_image))<img class="profile-user-img img-responsive img-circle" src="{{ $entity->post_image }}" alt="Logo image" width="70">@else N/A @endif</td>
                                            @if($formType=='News')
                                                <th>Post Video:</th>
                                                <td>@if(!empty($entity->post_video))<video controls style="width: 100px; height: 100px;">
                                                  <source width="100px" height="100px" src="{{$entity->post_video}}" type="video/mp4">
                                                  Your browser does not support the video tag.
                                                </video>@else N/A @endif</td>
                                            @endif
                                        </tr>

                                        <tr>
                                            @php
                                                $superadminRoleId = Config('params.role_ids.superadmin');
                                                $adminRoleId = Config('params.role_ids.admin');
                                                $subAdminRoleId = Config('params.role_ids.subadmin');
                                                $memberRoleId = Config('params.role_ids.member');

                                                $approvedUserInfo = "N/A";
                                                if(!empty($entity->approved_by_role)){
                                                    if(in_array($entity->approved_by_role, [$superadminRoleId, $adminRoleId,$subAdminRoleId])){
                                                        $approvedUserInfo = $entity->approvedByUser->full_name;
                                                    }
                                                }
                                            @endphp
                                            <th>Is Approved:</th>
                                            <td>@if(!empty($entity->is_approved)){{ ($entity->is_approved == 1?"YES":"NO") }}@else N/A @endif</td>
                                            <th>Approved By:</th>
                                            <td>@if(!empty($approvedUserInfo)){{ $approvedUserInfo }}@else N/A @endif</td>
                                        </tr>

                                        @php
                                            $postedByUserInfo = "N/A";
                                            if(!empty($entity->postedByMemberInfo->user->full_name)){
                                                $postedByUserInfo = $entity->postedByMemberInfo->user->full_name;
                                            }elseif(!empty($entity->postedByAdminInfo->full_name)){
                                                $postedByUserInfo = $entity->postedByMemberInfo->full_name;
                                            }
                                        @endphp
                                        <tr>
                                            <th>Posted By:</th>
                                            <td colspan="3">{{ $postedByUserInfo }}</td>
                                        </tr>

                                        <tr>
                                            <th>Post Description:</th>
                                            <td colspan="3">@if(!empty($entity->post_description)){{ $entity->post_description }}@else N/A @endif</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
@endsection