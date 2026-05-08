@extends('layouts.app')

@section('content')
    <!-- Hero Section: Full Width -->
    <div class="container-fluid px-0 mb-5">
        <div class="hero-section text-center py-5 shadow-sm"
            style="background: white; border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="container py-5">
                <img src="{{ asset('assets/img/faviconvibely.png') }}" alt="Vibely" class="mb-4 pulse-animation"
                    style="height:160px;">
                <h1 class="display-2 fw-bold text-purple mb-2">Vibely</h1>
                <h5 class="fw-bold text-purple mb-4" style="letter-spacing: 8px; opacity: 0.8;">SIENTE. COMPARTE. VIBRA</h5>
                <p class="lead text-muted mx-auto mb-5"
                    style="max-width: 800px; font-weight: 400; line-height: 1.8; font-size: 1.3rem;">
                    La experiencia musical definitiva. Conecta con el ritmo de tu comunidad, descubre canciones según tu
                    estado de ánimo y vibra en una sintonía única.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('register') }}"
                        class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg fw-bold">Empezar ahora gratis</a>
                    <a href="{{ route('login') }}"
                        class="btn btn-outline-purple btn-lg px-5 py-3 rounded-pill fw-bold">Entrar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container mb-5 py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Todo lo que necesitas para tu música</h2>
            <div class="bg-purple mx-auto mt-2" style="width: 60px; height: 4px; border-radius: 2px;"></div>
        </div>

        <div class="row g-4">
            <!-- Feature 1: Feed -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 text-center shadow-sm rounded-4 bg-white">
                    <div class="bg-light rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-users text-purple fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Comunidad Activa</h5>
                    <p class="text-muted small">Sigue a otros melómanos, mira lo que escuchan y comparte tus descubrimientos
                        diarios en el Feed.</p>
                </div>
            </div>

            <!-- Feature 2: Smart Playlists -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 text-center shadow-sm rounded-4 bg-white">
                    <div class="bg-light rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-bolt-lightning text-purple fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Smart Playlists</h5>
                    <p class="text-muted small">¿Cómo te sientes hoy? Genera listas automáticas basadas en tus emociones con
                        un solo clic.</p>
                </div>
            </div>

            <!-- Feature 3: Store -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 text-center shadow-sm rounded-4 bg-white">
                    <div class="bg-light rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-bag-shopping text-purple fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Tienda Vibely</h5>
                    <p class="text-muted small">Consigue merchandising exclusivo, pases para eventos y contenido premium de
                        tus artistas favoritos.</p>
                </div>
            </div>

            <!-- Feature 4: Player -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 text-center shadow-sm rounded-4 bg-white">
                    <div class="bg-light rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-compact-disc text-purple fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Vibely Player</h5>
                    <p class="text-muted small">Un reproductor integrado que te acompaña en toda la plataforma sin
                        interrumpir tu navegación.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Section: Focus on Visuals -->
    <div class="container mb-5 py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <h2 class="display-5 fw-bold text-dark mb-4">Descubre música que conecta contigo</h2>
                <p class="text-muted mb-4 fs-5">Nuestro algoritmo avanzado no solo analiza géneros, sino el **vibe** de cada
                    canción para ofrecerte siempre lo que tu cuerpo pide.</p>
                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-circle-check text-purple"></i>
                        <span class="fw-bold text-dark">Generación por estados de ánimo</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-circle-check text-purple"></i>
                        <span class="fw-bold text-dark">Playlists colaborativas con amigos</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-circle-check text-purple"></i>
                        <span class="fw-bold text-dark">Descubrimiento semanal personalizado</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="shadow-lg rounded-5 overflow-hidden position-relative"
                    style="background: var(--vibely-gradient); height: 400px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-wave-square text-white opacity-25" style="font-size: 200px;"></i>
                    <div class="position-absolute text-center text-white p-4">
                        <i class="fa-solid fa-headphones-simple mb-3" style="font-size: 80px;"></i>
                        <h4 class="fw-bold">Calidad de estudio</h4>
                        <p class="small mb-0">Audio en alta fidelidad para tus sentidos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Final CTA: Full Width -->
    <div class="container-fluid px-0 mt-5">
        <div class="py-5 text-white text-center shadow-lg"
            style="background: var(--vibely-gradient);">
            <div class="container py-5">
                <h2 class="display-4 fw-bold mb-4">¿Listo para sentir el ritmo?</h2>
                <p class="lead mb-5 opacity-75 fs-4">Únete a miles de usuarios y empieza a compartir tus vibes hoy mismo.
                </p>
                <a href="{{ route('register') }}"
                    class="btn btn-light btn-lg text-purple fw-bold px-5 py-3 rounded-pill shadow-lg hover-scale">Registrarse
                    gratis</a>
                <div class="mt-5">
                    <p class="mb-0">¿Ya tienes cuenta? <a href="{{ route('login') }}"
                            class="text-white fw-bold text-decoration-underline">Entra aquí</a></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-scale {
            transition: transform 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        .pulse-animation {
            animation: pulse_logo 3s infinite ease-in-out;
        }

        @keyframes pulse_logo {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
@endsection