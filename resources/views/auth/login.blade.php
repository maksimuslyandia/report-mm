@extends('auth.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h3 class="text-center mt-5">Login</h3>
                <form method="POST" action="{{ route('login.custom') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <!-- Azure AD Login Button -->
{{--                <div class="mt-3">--}}
{{--                    <a href="{{ route('login.azure') }}" class="btn btn-success w-100">--}}
{{--                    <a href="/" class="btn btn-outline-primary  w-100">--}}
{{--                        <i class="fab fa-microsoft"></i>--}}
{{--                        Login with Azure AD--}}
{{--                    </a>--}}
{{--                </div>--}}
                @include('layouts.errors')
            </div>
        </div>
    </div>
@endsection
