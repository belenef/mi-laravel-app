@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Vibely" style="height:80px;">
                        <h4 class="mt-2">Crear Cuenta</h4>
                        <p class="text-muted">Únete a la comunidad musical.</p>
                    </div>
                    <form method="POST" action="{{ route('register.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button class="btn btn-outline-primary w-100" type="submit">Crear cuenta</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection