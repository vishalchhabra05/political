@extends('layouts.superadmin')
@section('content')
    @php 
        $superadminLoginAs = Session::get('loginAs');
    @endphp
    @if($superadminLoginAs == "Superadmin")
        <div class="dashboard-page-content">
            <h1 class="heading-main-top mb-4">{{__('level.dashboard')}}</h1>
            <div class="row">
                <div class="col-lg-6 col-md-7">
                    <div class="bg-dashboard-top">
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="box bg-white">
                                        <div class="box-row">
                                            <div class="box-content">
                                                <h6>Total User</h6>
                                                <p class="h1 m-0">0</p>
                                            </div>
                                            <div class="box-icon cart">
                                                <div id="today-revenue" style='width: 100%; height: 100px;'><i class="fa fa-user"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="box bg-white">
                                        <div class="box-row">
                                            <div class="box-content">
                                                <h6>Total Subadmins</h6>
                                                <p class="h1 m-0">0</p>
                                            </div>
                                            <div class="box-icon cart">
                                                <div id="today-revenue" style='width: 100%; height: 100px;'><i class="fa fa-users"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @else
        @include('commonadmin.common_admin_dashboard')
    @endif
@endsection