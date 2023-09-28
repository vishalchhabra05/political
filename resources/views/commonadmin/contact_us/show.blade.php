@extends( (auth()->user()->role_id == 1) ? 'layouts.superadmin' : 'layouts.admin')
@section('content')
    <div class="page-title col-sm-12">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3 m-0">View Contact Enquiry</h1>
            </div>
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('list_contactus') }}">Contact Us Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Contact Enquiry</li>
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
                                        <th>Name:</th>
                                        <td>@if(!empty($entity->name)){{ $entity->name }}@else-@endif</td>
                                        <th>Email:</th>
                                        <td>@if(!empty($entity->email)){{ $entity->email }}@else-@endif</td>
                                    </tr>

                                    <tr>
                                        <th>Phone Number:</th>
                                        <td colspan="3">@if(!empty($entity->phone_number)){{ $entity->phone_number }}@else-@endif</td>
                                    </tr>

                                    <tr>
                                        <th>Message:</th>
                                        <td colspan="3">@if(!empty($entity->message)){{ $entity->message }}@else-@endif</td>
                                    </tr>

                                    <tr>
                                        <th>Reply:</th>
                                        <td colspan="3">@if(!empty($entity->reply)){{ $entity->reply }}@else-@endif</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

