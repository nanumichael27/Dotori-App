@extends('layouts.auth_app')

@section('meta-content')
    <title> Verify Email | Dotori </title>
@endsection

@section('content')
    <div id="login_wrap">
        <div class="index_l_box">
            <div class="inner" style="position:relative;">
                <div class="login_v_text">
                </div>
                <div class="login_div">
                    <div>
                        <div class="logo">
                            <img src="{{asset('img/acorn1.png')}}" height="auto" style="transform: scale(0.75)"/> 
                        </div>

                        <h3 class="subheader text-purple text-center">
                            {{ __('Verify Your Email Address')}}
                        </h3>

                        <div class="login_v_text">
                            
                        </div>
                        <div class="index_input">
                            @if (session('resent'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ __('A fresh verification link has been sent to your email address.') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                        </div>
                            
                        <div class="index_input">
                            <p>
                                {{ __('Before proceeding, please check your email for a verification link.') }} 
                            </p>
                        </div>


                        <div class="index_input">
                            <p style="margin-bottom:0.8em">
                                {{ __('If you did not receive the email, request a new verification link.') }}
                            </p>
                            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-purple-bd">
                                    {{ __('Request new link')}}
                                </button>
                            </form>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
