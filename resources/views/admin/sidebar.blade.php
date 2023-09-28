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
            @php
                $getCurrentUserPermissions = getUserpermissions();
            @endphp
                <div class="sidebar-menu-navbar">
                    <ul class="list-unstyled nav">
                        <!-- <li class="nav-item"><span class="menu-title text-muted">{{__('level.navigation')}}</span></li> -->
                        <li class="nav-item {{ (request()->is('*dashboard*')) ? 'active' : '' }}"><a href="{{ route('dashboard') }}" class="nav-link">
                            @include('superadmin.sidebar_svg_icon')
                            {{__('level.dashboard')}}</a>
                        </li>
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Political Position Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*political-position*')) ? 'active' : '' }}"><a href="{{ route('list_political_position') }}" class="nav-link">
                            <i class="fal fa-vote-yea"></i>
                            {{__('level.politicalpositionmanage')}}</a>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Categories Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*categories*')) ? 'active' : '' }}"><a   href="{{ route('list_categories') }}" class="nav-link">
                            <i class="fad fa-clipboard-list"></i>
                            {{__('level.categoriesmanage')}}</a>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Elections Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*elections*')) ? 'active' : '' }}">
                            <a href="{{ route('list_elections') }}" class="nav-link">
                            <i class="fal fa-users"></i>
                            {{__('level.electionsmanage')}}</a>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Contact Assignment Management', $getCurrentUserPermissions))
                         <li class="nav-item {{ (request()->is('*contact-assignment*')) ? 'active' : '' }}">
                            <a href="{{ route('list_contact_assignments') }}" class="nav-link">
                            <i class="fal fa-tasks"></i>
                            {{__('level.contactassignment')}}</a>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Polls Management', $getCurrentUserPermissions))
                            <li class="nav-item {{ (request()->is('*poll*')) ? 'active' : '' }}">
                                <a href="{{ route('list_poll') }}" class="nav-link">
                                <i class="fal fa-poll"></i>
                                {{__('level.pollsmanage')}}</a>
                            </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Member Management', $getCurrentUserPermissions))
                            <li class="nav-item {{ (request()->is('*member*') && !request()->is('*list-poll-member-answer*')) ? 'active' : '' }}">
                                <a href="{{ route('list_member') }}" class="nav-link">
                                <i class="fal fa-users"></i>
                                {{__('level.membermanage')}}</a>
                            </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Banner Management', $getCurrentUserPermissions))
                            <li class="nav-item {{ (request()->is('*banner*')) ? 'active' : '' }}">
                                <a href="{{ route('edit_banner') }}" class="nav-link">
                                <i class="fal fa-bell"></i>
                                {{__('level.bannersmanage')}}</a>
                            </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Sub Admin Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*sub-admin*')) ? 'active' : '' }}">
                            <a href="{{ route('list_sub_admin') }}" class="nav-link">
                            <i class="fal fa-user"></i>
                            {{__('level.subadminmanage')}}</a>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Newsletter Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*newsletter*')) ? 'active' : '' }}">
                            <a href="{{ route('list_newsletter') }}" class="nav-link">
                            <i class="fad fa-th-list"></i>
                            {{__('level.newslettermanage')}}</a>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Member Register Custom Form Management', $getCurrentUserPermissions) || in_array('User Profile Custom Form Management', $getCurrentUserPermissions) || in_array('Survey Custom Form Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*form-customization*') || request()->is('*survey*')) ? 'active' : '' }}"><a href="#" class="nav-link">
                        <i class="fa fa-check-square"></i>
                            {{__('level.customformmanage')}}</a>
                            <ul class="sub-menu">
                                @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Member Register Custom Form Management', $getCurrentUserPermissions))
                                    <li class="nav-item {{ (request()->is('*form-customization*') && request()->is('*/register*')) ? 'active' : '' }} "><a href="{{ route('list_form_customization','register') }}" class="nav-link">{{__('level.memberregisterform')}}</a></li>
                                @endif
        
                                @if(in_array(Auth::user()->role_id, [1,2]) || in_array('User Profile Custom Form Management', $getCurrentUserPermissions))
                                <li class="nav-item {{ (request()->is('*form-customization*') && request()->is('*/profile*')) ? 'active' : '' }} "><a href="{{ route('list_form_customization','profile') }}" class="nav-link">{{__('level.memberprofileform')}}</a></li>
                                @endif
        
                                @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Survey Custom Form Management', $getCurrentUserPermissions))
                                <li class="nav-item {{ (request()->is('*survey*')) ? 'active' : '' }} "><a href="{{ route('list_survey') }}" class="nav-link">{{__('level.membersurveyform')}}</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Party Wall Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*/Partywall*')) ? 'active' : '' }}"><a href="{{ route('list_party_wall','Partywall') }}" class="nav-link">
                            <i class="fad fa-th-list"></i>
                            {{__('level.partywallmanage')}}</a>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Article/News Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*/News*')) ? 'active' : '' }}"><a href="{{ route('list_party_wall','News') }}" class="nav-link">
                            <i class="far fa-newspaper"></i>
                            {{__('level.newsmanage')}}</a>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Post Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*/Post*')) ? 'active' : '' }}"><a href="{{ route('list_party_wall','Post') }}" class="nav-link">
                            <i class="fad fa-th-list"></i>
                            {{__('level.postmanage')}}</a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Contact Us Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*contactus*')) ? 'active' : '' }}"><a href="{{ route('list_contactus') }}" class="nav-link"><i class="fa fa-address-book"></i>{{__('level.contactusmanage')}}</a></li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('CMS Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*cms*')) ? 'active' : '' }}"><a href="{{ route('list_cms') }}" class="nav-link"><i class="fal fa-file-alt"></i>{{__('level.cmsmanage')}}</a>
                        </li>
                        @endif
        
                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('FAQ Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*faq*')) ? 'active' : '' }}"><a href="{{ route('list_faq') }}" class="nav-link"><i class="fa fa-question-circle"></i>{{__('level.faqmanage')}}</a></li>
                        @endif

                        @if(in_array(Auth::user()->role_id, [1,2]) || in_array('Site Setting Management', $getCurrentUserPermissions))
                        <li class="nav-item {{ (request()->is('*site-setting*')) ? 'active' : '' }}"><a href="{{ route('site_setting') }}" class="nav-link"><i class="fa fa-cog"></i>{{__('level.sitesettingmanage')}}</a></li>
                        @endif
                    </ul>
                </div>
        </div>
    </div>
    