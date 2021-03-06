@extends('layouts.auth_app')

@section('meta-content')
    <title> Reset Password | Dotori </title>
@endsection

@section('content')
<div id="login_wrap">
    <div class="index_l_box">
        <div class="inner" style="position:relative;">
            <div class="login_v_text">
                
            </div>
            <div class="login_div">
                <div>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="logo">
                            <img src="{{asset('img/acorn1.png')}}" height="auto" style="transform: scale(0.75)"/> 
                        </div>

                        <h3 class="subheader text-purple text-center">
                            {{__('Reset Password')}}
                        </h3>

                        <div class="index_input">
                            <label for="email">
                                {{__('Email Address')}}
                            </label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                value="{{ $email ?? old('email') }}" autocomplete="email" autofocus disabled>
                            <input type="hidden" name="email"  value="{{ $email ?? old('email') }}" required autocomplete="email">
                        </div>

                        <div class="index_input">
                            <label for="password">
                                {{__('Password')}}
                            </label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="index_input">
                            <label for="password-confirm">
                                {{__('Confirm Password')}}
                            </label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" 
                            required autocomplete="new-password">
                        </div>

                        <div class="index_input">
                            <button type="submit" class="btn btn-purple-bg">
                                {{__('Reset Password')}}
                            </button>
                        </div>
    
                        <div class="index_input">
                            <a class="text-purple" href="/login">
                                {{__('Back to login')}}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus disabled>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection
