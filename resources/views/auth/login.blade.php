@extends('layouts.auth') 

@section('content')
<div class="container">
    <div class="row d-flex justify-content-center align-items-center vh-100">
        <div class="col-12 col-md-6 col-lg-4">
            
            <div class="text-center mb-4">
                {{-- <a href="/"><img src="{{ asset('assets/compiled/svg/logo.svg') }}" height="35" alt="Logo"></a> --}}
                <h2 class="auth-title mt-3" style="font-size: 2rem;">Log in</h2>
                <p class="auth-subtitle text-muted" style="font-size: 0.9rem;">Sistem Peresepan Obat RS Delta Surya</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-content">
                    <div class="card-body p-3"> <form method="POST" action="{{ route('login') }}" class="form form-vertical">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group has-icon-left mb-2"> <label for="email-id-icon" class="mb-1" style="font-size: 0.85rem;">Email</label>
                                            <div class="position-relative">
                                                <input
                                                    type="email"
                                                    class="form-control form-control-md @error('email') is-invalid @enderror"
                                                    placeholder="Email"
                                                    id="email-id-icon"
                                                    name="email"
                                                    value="{{ old('email') }}"
                                                    required
                                                />
                                                <div class="form-control-icon">
                                                    <i class="bi bi-envelope"></i>
                                                </div>
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group has-icon-left mb-2">
                                            <label for="password-id-icon" class="mb-1" style="font-size: 0.85rem;">Password</label>
                                            <div class="position-relative">
                                                <input
                                                    type="password"
                                                    class="form-control form-control-md @error('password') is-invalid @enderror"
                                                    placeholder="Password"
                                                    required
                                                    name="password"
                                                    id="password-id-icon"
                                                />
                                                <div class="form-control-icon">
                                                    <i class="bi bi-lock"></i>
                                                </div>
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert" style="font-size: 0.75rem;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check d-flex align-items-center mt-3 mb-3">
                                            <input
                                                type="checkbox"
                                                id="remember-me-v"
                                                class="form-check-input me-2"
                                                name="remember"
                                                @checked(old('remember'))
                                            />
                                            <label class="form-check-label text-gray-600" for="remember-me-v">
                                                Ingat saya
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn btn-primary btn-block btn-sm shadow-sm py-2">
                                            Masuk
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection