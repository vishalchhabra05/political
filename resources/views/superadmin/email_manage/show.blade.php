@extends('layouts.superadmin')
@section('content')
        <div class="page-title col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 m-0">Email Details</h1>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.list_email') }}">Email Template Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Email Template</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-lg-12 col-md-4 mb-4">
                    <div class="box bg-white">
                        <div class="box-title pb-0">
                            <h5>Email Template Details</h5>
                        </div>
                        <table class="table table-bordered table-hover">
                           <tbody>
                                <tr>
                                    <th>Email-Template:</th>
                                    <td>{{ $entity->email_template }}</td>
                                    <th>Subject:</th>
                                    <td>{{ $entity->subject }}</td>
                                </tr>
                                <tr>
                                    <th>Message-Greeting:</th>
                                    <td>{{ $entity->message_greeting }}</td>
                                    <th>Message-Body:</th>
                                    <td>{{ $entity->message_body }}</td>
                                </tr>
                                <tr>
                                    <th>Message-Signature:</th>
                                    <td>{{ $entity->message_signature }}</td>
                                    <th>Last Updated By:</th>
                                    <td>{{ $entity->user->first_name}}</td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ DMYDateFromat($entity->created_at) }}</td>
                                </tr>
                           </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection