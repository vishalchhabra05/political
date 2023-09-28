@extends('layouts.superadmin_on_login')
@section('content')
    <div class="dashboard-page-content">
        <h1 class="heading-main-top mb-4">{{__('level.login_as')}}</h1>
        <form class="mt-4" method="POST" action="{{ route('redirectPartySelection') }}" id="loginAsForm">
            @csrf
            <div id="selectLoginAsDiv">
                <div class="admin-party-button">
                    <div class="radio-box">
                        <div class="check-box-create">
                            <input hidden type="radio" id="superadmin" name="login_as" checked value="Superadmin">
                            <label for="superadmin"> <i class="fa fa-user" aria-hidden="true"></i> Superadmin</label>
                        </div>
                    </div>
                    <div class="radio-box">
                        <div class="check-box-create">
                            <input hidden type="radio" id="party" name="login_as" value="Party">
                            <label for="party"><i class="fa fa-users" aria-hidden="true"></i> Party</label>
                        </div>
                    </div>
                </div>
                <a class="btn btn-primary" onclick="submitLoginAs('part1');">Continue</a>
            </div>

            <div id="selectPartyDiv" style="display:none;">
                <div class="admin-party-button">
                    @foreach($politicalParties as $key => $ppInfo)
                        <div class="radio-box">
                            <div class="check-box-create">
                                <input checked type="radio" id="party_{{$ppInfo->id}}" name="selected_party" value="{{$ppInfo->id}}">
                                <label for="party_{{$ppInfo->id}}">
                                    <div class="party-logo-img">
                                        @if(!empty($ppInfo->logo))
                                            <img class="head-logo" src="{{ url('/').$ppInfo->logo}}" alt="Logo">
                                        @else
                                            <img class="head-logo" src="{{ asset('images/congress.png') }}" alt="Logo">
                                        @endif
                                    </div>
                                    {{$ppInfo->party_name}} 
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a class="btn btn-primary" onclick="submitLoginAs('part2');">Submit</a>
            </div>
        </form>
    </div>
@endsection
@push('current-page-js')
<script type="text/javascript">
$(document).ready(function() {
    $("#backbutton").click(function(){
        $("#selectLoginAsDiv").css("display", "block");
        $("#selectPartyDiv").css("display", "none");
        $("#backbutton").css("display", "none");
    }); 
});
function submitLoginAs(formpart){
    var selectedLoginAs = $("input[name='login_as']:checked").val();
    if(selectedLoginAs){
        if(selectedLoginAs == "Superadmin"){
            $("#loginAsForm").submit();
        }else if(selectedLoginAs == "Party"){
            if(formpart == "part1"){
                $("#selectLoginAsDiv").css("display", "none");
                $("#selectPartyDiv").css("display", "block");
                $("#backbutton").css("display", "inline-flex");
            }else{
                var selectedParty = $("input[name='selected_party']:checked").val();
                if(selectedParty){
                    $("#loginAsForm").submit();
                }else{
                    Lobibox.notify('error', {
                        icon:false,
                        msg: 'Select party before continue'
                    });
                }
            }
        }else{
            Lobibox.notify('error', {
                icon:false,
                msg: 'Select how you want to continue'
            });
        }
    }else{
        Lobibox.notify('error', {
            icon:false,
            msg: 'Select how you want to continue'
        });
    }
}
</script>
@endpush