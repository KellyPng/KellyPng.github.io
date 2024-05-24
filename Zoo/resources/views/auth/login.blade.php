@extends('layouts.app')

@section('content')
<style>
    body{
        background-image: url('images/jungle.jpg');
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: 100% 100%;
    }
    .invalid-feedback{
        color: red!important;
    }
</style>
<div class="loginform shadow-lg">
    <h1>{{ __('Login') }}</h1>
    <form name="form" method="post" action="{{ route('login') }}" class="pb-3">
        @csrf
        <div class="txt_field">
            <input type="text" id="email" name="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            <span></span>
            <label>{{ __('Email Address') }}</label>

            @error('email')
            <span class="invalid-feedback" role="alert" style="color: red;!important">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="txt_field">
            <input type="password" id="password" name="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
            <span></span>
            <label>{{ __('Password') }}</label>

            @error('password')
            <span class="invalid-feedback" role="alert" style="color: red;">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="row mb-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
        </div>

        

        <!-- <div class="forgotpass">Forgot Password?</div> -->
        <input type="submit" value="{{ __('Login') }}" name="loginbutton">

        @if (Route::has('password.request'))
        <div class="forgotpass"><a href="{{ route('password.request') }}">
            {{ __('Forgot Your Password?') }}
        </a></div>
        @endif
    </form>
</div>
@endsection