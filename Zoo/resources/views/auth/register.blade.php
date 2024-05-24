{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="fname" class="col-md-4 col-form-label text-md-end">{{ __('First Name') }}</label>

                            <div class="col-md-6">
                                <input id="fname" type="text" class="form-control @error('fname') is-invalid @enderror" name="fname" value="{{ old('fname') }}" required autocomplete="fname" autofocus>

                                @error('fname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="lname" class="col-md-4 col-form-label text-md-end">{{ __('Last Name') }}</label>

                            <div class="col-md-6">
                                <input id="lname" type="text" class="form-control @error('lname') is-invalid @enderror" name="lname" value="{{ old('lname') }}" required autocomplete="lname" autofocus>

                                @error('lname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

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
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

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
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}


@extends('layouts.app')

@section('content')
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 my-5">
            <br><br><br>
<div class="loginform shadow-lg mb-5" style="margin: 5rem 0!important; ">
    
    <h1>{{ __('Register') }}</h1>
    <form name="form" method="post" action="{{ route('register') }}">
        @csrf
        <div class="txt_field">
            <input type="text" id="fname" class=" @error('fname') is-invalid @enderror" name="fname" value="{{ old('fname') }}" required autocomplete="fname" autofocus>
            <span></span>
            <label>{{ __('First Name') }}</label>

            @error('fname')
            <div class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>

        <div class="txt_field">
            <input type="text" id="lname" class="@error('lname') is-invalid @enderror" name="lname" value="{{ old('lname') }}" required autocomplete="lname" autofocus>
            <span></span>
            <label>{{ __('Last Name') }}</label>

            @error('lname')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="txt_field">
            <input type="text" id="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            <span></span>
            <label>{{ __('Email') }}</label>

            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="emptype">
            <label for="emptype">{{ __('Employee Type') }}</label>
            <select class="form-select form-control" aria-label="employeeType" id="emptype" name="emptype" required>
                <option selected disabled>Select</option>
                <option value="Marketing">Marketing</option>
                <option value="Top Management">Top Management</option>
                <option value="Employee">Employee</option>
            </select>

            @error('emptype')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="txt_field">
            <input type="password" id="adminpass" class="@error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            <span></span>
            <label>{{ __('Password') }}</label>

            @error('password')
            <div class="invalid-feedback mt-5" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>

        <div class="txt_field">
            <input type="password" id="password-confirm" name="password_confirmation" required autocomplete="new-password">
            <span></span>
            <label>{{ __('Confirm Password') }}</label>
        </div>

        <div class="forgotpass"><a href="{{route('login')}}">Already have an account? Login</a></div>
        <input type="submit" value="{{ __('Register') }}" name="loginbutton">
    </form>
    <br>
</div>
<br><br>
        </div></div>
<br><br>
@endsection