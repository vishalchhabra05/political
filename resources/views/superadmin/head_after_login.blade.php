<div class="container-fluid">
    @php
        $checkLoginAs = Session::get('loginAs');
        $checkLoginAsParty = Session::get('loginAsParty');
        if($checkLoginAs == "Party"){
            $partyDetail = getPartyName($checkLoginAsParty);
        }
    @endphp
    <!-- <a href="{{ route('dashboard') }}" class="navbar-brand mr-0 mr-md-2 logo">
        <img class="head-logo" src="{{ asset('images/political-party.png') }}" alt="Logo">
    </a> -->
    @if(!empty($checkLoginAs) && $checkLoginAs == "Party")
        <h4>Loggedin As : {{$partyDetail->party_name}}</h4>
    @endif
    <!-- <ul class="navbar-nav flex-row ml-auto d-flex align-items-center list-unstyled topnav-menu mb-0">    
        <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="flag-icon flag-icon-{{Config::get('languages')[App::getLocale()]['flag-icon']}}"></span> {{ Config::get('languages')[App::getLocale()]['display'] }}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                @foreach (Config::get('languages') as $lang => $language)
                    @if ($lang != App::getLocale())
                            <a class="dropdown-item" href="{{ route('superadmin.lang.switch', $lang) }}"><span class="flag-icon flag-icon-{{$language['flag-icon']}}"></span> {{$language['display']}}</a>
                    @endif
                @endforeach
                </div>
        </li>
    </ul> -->

    <ul class="navbar-nav flex-row ml-auto d-flex align-items-center list-unstyled topnav-menu mb-0">
        <li class="dropdown user-link">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                aria-haspopup="false" aria-expanded="false">
                <div class="profile-img-box">
                <img class="head-logo" src="{{ asset('images/dummy-image.jpg') }}" alt="Logo">
                </div>
                <span class="noti-icon-badge"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-lg profile-image-dropdown">
                <!-- <a href="{{ url('superadmin/profile-user',auth()->user()->id) }}" class="dropdown-item"> <i class="fal fa-user"></i> {{__('level.my_profile')}}</a> -->
                @if(!empty($checkLoginAs))
                <a href="{{ url('/change-password',auth()->user()->id) }}" class="dropdown-item"> <i class="fal fa-key"></i> {{__('level.change_password')}}</a>
                @endif
                <!-- <div class="dropdown-divider"></div> -->
                <a href="{{ route('superadmin.logout') }}" class="dropdown-item"><i class="fal fa-sign-out"></i> {{__('level.logout')}}</a>
            </div>
        </li>
    </ul>
</div>