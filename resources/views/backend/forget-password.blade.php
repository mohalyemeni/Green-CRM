@extends('layouts.auth')
@section('content')
    <div class="row w-100 mx-0 auth-page">
        <div class="col-md-8 col-xl-6 mx-auto">
            <div class="card">
                <div class="row">
                    <div class="col-md-4 pe-md-0">
                        <div class="auth-side-wrapper">

                        </div>
                    </div>
                    <div class="col-md-8 ps-md-0">
                        <div class="auth-form-wrapper px-4 py-5">
                            <a href="{{ route('admin.login') }}" class="noble-ui-logo d-block mb-2">{{ __('cpanel.panel') }}
                                <span>{{ __('cpanel.control') }}</span></a>
                            <h5 class="text-muted fw-normal mb-4">{{ __('cpanel.login.welcome') }}.</h5>
                            <form class="forms-sample" method="POST" action="{{ route('password.email') }}">
                                @csrf
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label for="UserName" class="form-label">{{ __('validation.attributes.username') }}</label>
                                    <input type="email" class="form-control" id="email" required
                                        name="email" value="{{ old('email') }}"
                                        placeholder="{{ __('validation.attributes.email') }}">
                                    @error('email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <button type="submit"
                                        class="btn btn-primary me-2 mb-2 mb-md-0 text-white">{{ __('cpanel.login.button') }}</button>
                                        @if (Route::has('password.request'))
                                            <a class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0"
                                                href="{{ route('password.request') }}">
                                                {{ __('cpanel.forget-password-msg') }}
                                            </a>
                                        @endif
                                </div>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="d-block mt-3 text-muted">
                                        {{ __('cpanel.register.question') }} {{ __('cpanel.register') }}
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
