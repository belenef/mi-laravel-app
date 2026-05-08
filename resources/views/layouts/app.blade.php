<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vibely</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/faviconvibely.png') }}">
    @stack('styles')
</head>

<body>
    <!-- ... (navbar content remains same) ... -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/faviconvibely.png') }}" alt="Vibely" class="me-2" style="height:48px;">
                <span class="fw-bold text-purple">Vibely</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    @if(Auth::check())
                        <li class="nav-item me-2">
                            <a href="{{ route('player') }}" class="btn btn-outline-purple btn-sm">
                                <i class="bi bi-play-circle"></i> Reproductor
                            </a>
                        </li>
                        <li class="nav-item me-2"><a class="nav-link" href="{{ route('home') }}">Feed</a></li>
                        <li class="nav-item me-2"><a class="nav-link" href="{{ route('grupos') }}">Grupos</a></li>
                        <li class="nav-item me-2"><a class="nav-link" href="{{ route('tienda') }}"><i
                                    class="bi bi-cart"></i> Tienda</a></li>
                        <li class="nav-item me-2"><a class="nav-link" href="{{ route('playlists.create') }}">Crear
                                playlist</a></li>
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile') }}">Mi perfil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Acceder / Crear cuenta</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="{{ Route::currentRouteName() === 'index' ? 'my-0' : 'container my-4' }}">
        @yield('content')
    </main>

    @if(Route::currentRouteName() !== 'player')
        @include('components.music-player')
    @endif

    <footer class="bg-white pt-5 pb-3 border-top mt-5">
        <div class="container">
            <div class="row g-4 mb-5">
                <!-- Columna 1: Brand & Contacto -->
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('assets/img/faviconvibely.png') }}" alt="Vibely" class="me-2"
                            style="height:40px;">
                        <h4 class="fw-bold text-purple mb-0">Vibely</h4>
                    </div>
                    <p class="text-muted mb-4 small">
                        La plataforma social musical donde sentir el ritmo es solo el principio. Únete a la comunidad y
                        haz que tu música hable por ti.
                    </p>
                    <h6 class="fw-bold text-purple mb-2">Contacto del Creador</h6>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-1"><i class="fa-solid fa-envelope me-2"></i> belen@vibely.com</li>
                        <li class="mb-1"><i class="fa-solid fa-location-dot me-2"></i> Madrid, España</li>
                    </ul>
                </div>

                <!-- Columna 2: Navegación -->
                <div class="col-6 col-lg-2">
                    <h6 class="fw-bold text-dark mb-3">Explorar</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('index') }}"
                                class="text-decoration-none text-muted hover-purple">Inicio</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}"
                                class="text-decoration-none text-muted hover-purple">Feed</a></li>
                        <li class="mb-2"><a href="{{ route('tienda') }}"
                                class="text-decoration-none text-muted hover-purple">Tienda</a></li>
                        <li class="mb-2"><a href="{{ route('player') }}"
                                class="text-decoration-none text-muted hover-purple">Reproductor</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Tu Cuenta -->
                <div class="col-6 col-lg-2">
                    <h6 class="fw-bold text-dark mb-3">Cuenta</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('profile') }}"
                                class="text-decoration-none text-muted hover-purple">Mi Perfil</a></li>
                        <li class="mb-2"><a href="{{ route('playlists.create') }}"
                                class="text-decoration-none text-muted hover-purple">Crear Playlist</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}"
                                class="text-decoration-none text-muted hover-purple">Iniciar Sesión</a></li>
                    </ul>
                </div>

                <!-- Columna 4: Redes Sociales -->
                <div class="col-lg-4 text-lg-end">
                    <h6 class="fw-bold text-dark mb-3">Síguenos</h6>
                    <div class="d-flex gap-3 justify-content-lg-end">
                        <a href="#"
                            class="social-icon text-white bg-purple rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#"
                            class="social-icon text-white bg-purple rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                        <a href="#"
                            class="social-icon text-white bg-purple rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <a href="#"
                            class="social-icon text-white bg-purple rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </div>
                    <p class="text-muted small mt-4">Disponible pronto en:</p>
                    <div class="d-flex gap-2 justify-content-lg-end opacity-50">
                        <i class="fa-brands fa-apple fs-3"></i>
                        <i class="fa-brands fa-google-play fs-3"></i>
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-10">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted small mb-0">© {{ date('Y') }} Vibely · Siente. Comparte. Vibra.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0 small">
                        <li class="list-inline-item me-3"><a href="#"
                                class="text-decoration-none text-muted">Privacidad</a></li>
                        <li class="list-inline-item"><a href="#" class="text-decoration-none text-muted">Condiciones</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <style>
        .navbar {
            z-index: 1060;
            /* Higher than most elements to ensure dropdowns are on top */
        }

        .hover-purple:hover {
            color: #6f42c1 !important;
        }

        .social-icon {
            transition: transform 0.2s ease, background 0.2s ease;
            text-decoration: none;
        }

        .social-icon:hover {
            transform: translateY(-3px);
            background-color: #59359a !important;
        }

        .bg-purple {
            background-color: #6f42c1;
        }

        .text-purple {
            color: #6f42c1;
        }

        .navbar-nav .nav-link {
            color: #555;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            color: #6f42c1;
        }

        body {
            background-color: #f8f9fa;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>

</html>