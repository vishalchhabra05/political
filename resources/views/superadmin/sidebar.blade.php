    <div class="dashboard-menu main-dashboard-sidebar niceScroll">
        <div class="nav-menu">
            <div class="user-info">
                <!-- <div class="user-icon"><img src="{{ asset('images/avatar-1.jpg') }}" alt="img"></div> -->
                <!-- <div class="user-name ">
                    <h5>{{ auth()->user()->full_name }}</h5>
                    <span class="h6 text-muted">{{__('level.administrator')}}</span>
                </div> -->

                <div class="admin-logo">
                        <a href="{{ route('dashboard') }}" class="">
                        <img class="head-logo" src="{{ asset('images/logo-political-admin.png') }}" alt="Logo">
                    </a>
                </div>

            </div>
                <div class="sidebar-menu-navbar">
                    <ul class="list-unstyled nav">
                        <!-- <li class="nav-item"><span class="menu-title text-muted">{{__('level.navigation')}}</span></li> -->
                        <li class="nav-item {{ (request()->is('*dashboard*')) ? 'active' : '' }}"><a href="{{ route('dashboard') }}" class="nav-link">
                              <i class="fal fa-home-alt"></i>
                            {{__('level.dashboard')}}</a></li>
                        @php
                            $getCurrentUserPermissions = getUserpermissions();
                            $adminRoleId = 1;
                            $superadminLoginAs = Session::get('loginAs');
                        @endphp
                        @if(Auth::guard('superadmin')->check())
        
                        @if($superadminLoginAs == "Superadmin")
                        <li class="nav-item {{ (request()->is('*political-party*')) ? 'active' : '' }}"><a href="{{ route('superadmin.list_political_party') }}" class="nav-link">
                        <i class="fal fa-users"></i>
                            {{__('level.politicalpartymanage')}}</a></li>
                        @endif
                        
                        @if($superadminLoginAs == "Party")
                        <li class="nav-item {{ (request()->is('*political-position*')) ? 'active' : '' }}">
                            <a href="{{ route('list_political_position') }}" class="nav-link">
                                <i class="fal fa-vote-yea"></i>
                                {{__('level.politicalpositionmanage')}}
                            </a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*categories*')) ? 'active' : '' }}">
                            <a href="{{ route('list_categories') }}" class="nav-link">
                                <i class="fad fa-clipboard-list"></i>
                                {{__('level.categoriesmanage')}}
                            </a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*elections*')) ? 'active' : '' }}">
                            <a href="{{ route('list_elections') }}" class="nav-link">
                                <i class="fal fa-users"></i>
                                {{__('level.electionsmanage')}}
                            </a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*contact-assignment*')) ? 'active' : '' }}">
                            <a href="{{ route('list_contact_assignments') }}" class="nav-link">
                                <i class="fal fa-tasks"></i>
                                {{__('level.contactassignment')}}
                            </a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*poll*')) ? 'active' : '' }}">
                            <a href="{{ route('list_poll') }}" class="nav-link">
                            <i class="fal fa-poll"></i>
                            {{__('level.pollsmanage')}}</a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*member*') && !request()->is('*list-poll-member-answer*')) ? 'active' : '' }}">
                            <a href="{{ route('list_member') }}" class="nav-link">
                            <i class="fal fa-users"></i>
                            {{__('level.membermanage')}}</a>
                        </li> 
        
                        <li class="nav-item {{ (request()->is('*banner*')) ? 'active' : '' }}">
                            <a href="{{ route('edit_banner') }}" class="nav-link">
                            <i class="fal fa-bell"></i>
                            {{__('level.bannersmanage')}}</a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*sub-admin*')) ? 'active' : '' }}">
                            <a href="{{ route('list_sub_admin') }}" class="nav-link">
                            <i class="fal fa-user"></i>
                            {{__('level.subadminmanage')}}</a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*newsletter*')) ? 'active' : '' }}">
                            <a href="{{ route('list_newsletter') }}" class="nav-link">
                            <i class="fad fa-th-list"></i>
                            {{__('level.newslettermanage')}}</a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*form-customization*') || request()->is('*survey*')) ? 'active' : '' }}"><a href="#" class="nav-link">
                        <i class="fa fa-check-square"></i>
                            {{__('level.customformmanage')}}</a>
                            <ul class="sub-menu">
                                <li class="nav-item {{ (request()->is('*form-customization*') && request()->is('*/register*')) ? 'active' : '' }} "><a href="{{ route('list_form_customization','register') }}" class="nav-link">{{__('level.memberregisterform')}}</a></li>
                                <li class="nav-item {{ (request()->is('*form-customization*') && request()->is('*/profile*')) ? 'active' : '' }} "><a href="{{ route('list_form_customization','profile') }}" class="nav-link">{{__('level.memberprofileform')}}</a></li>
                                <li class="nav-item {{ (request()->is('*survey*')) ? 'active' : '' }} "><a href="{{ route('list_survey') }}" class="nav-link">{{__('level.membersurveyform')}}</a></li>
                            </ul>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*/Partywall*')) ? 'active' : '' }}">
                            <a href="{{ route('list_party_wall','Partywall') }}" class="nav-link">
                                <i class="fad fa-th-list"></i>
                                {{__('level.partywallmanage')}}
                            </a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*/News*')) ? 'active' : '' }}"><a href="{{ route('list_party_wall','News') }}" class="nav-link">
                            <i class="far fa-newspaper"></i>
                            {{__('level.newsmanage')}}</a>
                        </li>

                        <li class="nav-item {{ (request()->is('*/Post*')) ? 'active' : '' }}"><a href="{{ route('list_party_wall','Post') }}" class="nav-link">
                            <i class="fad fa-th-list"></i>
                            {{__('level.postmanage')}}</a>
                        </li>

                        <li class="nav-item {{ (request()->is('*contactus*')) ? 'active' : '' }}"><a href="{{ route('list_contactus') }}" class="nav-link"><i class="fa fa-address-book"></i>{{__('level.contactusmanage')}}</a></li>

                        <li class="nav-item {{ (request()->is('*cms*')) ? 'active' : '' }}"><a href="{{ route('list_cms') }}" class="nav-link"><i class="fal fa-file-alt"></i>{{__('level.cmsmanage')}}</a>
                        </li>
        
                        <li class="nav-item {{ (request()->is('*faq*')) ? 'active' : '' }}"><a href="{{ route('list_faq') }}" class="nav-link"><i class="fa fa-question-circle"></i>{{__('level.faqmanage')}}</a></li>

                        <li class="nav-item {{ (request()->is('*site-setting*')) ? 'active' : '' }}"><a href="{{ route('site_setting') }}" class="nav-link"><i class="fa fa-cog"></i>{{__('level.sitesettingmanage')}}</a></li>
                        @endif
        
                        <!-- @if(Auth::user()->role_id == $adminRoleId || in_array('Email and Notifications Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('superadmin/list-email')) ? 'active' : '' }}"><a href="{{ route('superadmin.list_email') }}" class="nav-link"><i class="fa fa-envelope"></i>{{__('level.emailmanage')}}</a></li>
                        @endif
        
                        @if(Auth::user()->role_id == $adminRoleId || in_array('Advertisement Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('superadmin/list-advertisement')) ? 'active' : '' }}"><a href="{{ route('superadmin.list_advertisement') }}" class="nav-link"><i class="fas fa-ad"></i>{{__('level.advertisementmanage')}}</a></li>
                        @endif -->
        
                        @endif
                    </ul>
                </div>
        </div>
    </div>
    