@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
        <?php
                $pageTitleName = "Poll Management";
        ?>
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">View Poll</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('list_poll') }}">{{$pageTitleName}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Poll</li>
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
                                            <th colspan="6">POLL DETAILS:</th>
                                        </tr>

                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>

                                        <tr>
                                            <th>Poll Name:</th>
                                            <td>@if(!empty($entity->poll_name)){{ ucfirst($entity->poll_name) }}@else N/A @endif</td>

                                            <th>Poll Type:</th>
                                            <td>@if(!empty($entity->poll_type)){{ ucfirst($entity->poll_type) }}@else N/A @endif</td>

                                           
                                        </tr>

                                        <tr>
                                            <th>Start Date:</th>
                                            <td>@if(!empty($entity->start_date) && $entity->start_date != '0000-00-00 00:00:00'){{ DMYHIADateFromat($entity->start_date) }}@else-@endif</td>
                                            <th>Expiry Date:</th>
                                            <td>@if(!empty($entity->expiry_date) && $entity->expiry_date != '0000-00-00'){{ DMYHIADateFromat($entity->expiry_date) }}@else-@endif</td>
                                        </tr>

 
                                        <tr>
                                            <th>Is Approved:</th>
                                            <td>@if(!empty($entity->is_approved)){{ ($entity->is_approved == 1?"YES":"NO") }}@else N/A @endif</td>
                                            <!-- <th>Approved By:</th>
                                            <td>@if(!empty($approvedUserInfo)){{ $approvedUserInfo }}@else N/A @endif</td> -->
                                            <th>Poll Question:</th>
                                            <td>@if(!empty($entity->question)){{ $entity->question }}@else N/A @endif</td>
                                        </tr>

                                        <tr>
                                            <th>Poll Options:</th>
                                            <td>@if(!empty($entity->PollOption))
                                                    @foreach($entity->PollOption as $k=>$option)
                                                        {{ (($k!=0)?',':'').$option['option'] }}
                                                    @endforeach
                                                @else N/A @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
@endsection