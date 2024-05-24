@extends('layouts.app')

@section('content')
<style>
    .viewbutton{
        background-color: #D5E200!important;
    }
    .viewbutton:hover{
        background-color: #c4c853!important;
    }
    .emptype label{
    font-size: 19px;
    color: #3C332A;
    }
    .emptype select{
        background-color: white;
    }
</style>
    <div class="container">
        <h1>Create Employee</h1>
        <br>
        <form action="{{ action('App\Http\Controllers\EmployeeController@store') }}" class="form" method="post" enctype="multipart/form-data">
            @csrf
            <div class="formconntainer">
                <div class="mb-3">
                    <label>{{ __('First Name') }}</label>
                    <input type="text" id="fname" class=" @error('fname') is-invalid @enderror form-control"
                        name="fname" value="{{ old('fname') }}" required autocomplete="fname" autofocus>
                    @error('fname')
                        <div class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>{{ __('Last Name') }}</label>
                    <input type="text" id="lname" class="@error('lname') is-invalid @enderror form-control"
                        name="lname" value="{{ old('lname') }}" required autocomplete="lname" autofocus>
                    @error('lname')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>{{ __('Email') }}</label>
                    <input type="text" id="email" class="@error('email') is-invalid @enderror form-control"
                        name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="emptype mb-3">
                    <label>{{ __('Employee Type') }}</label>
                    <select class="form-select form-control mt-0" aria-label="employeeType" id="emptype" name="emptype"
                        required>
                        <option selected disabled>Select</option>
                        <option value="Admin">Admin</option>
                        <option value="Employee">Employee</option>
                    </select>

                    @error('emptype')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label>{{ __('Password') }}</label>
                    <input type="password" id="adminpass" class="@error('password') is-invalid @enderror form-control" name="password"
                        required autocomplete="new-password">
                    <span></span>

                    @error('password')
                        <div class="invalid-feedback mt-5" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn viewbutton" style="font-family: 'Rubik', sans-serif;">Save</button>
            </div>
        </form>
    </div>
@endsection
