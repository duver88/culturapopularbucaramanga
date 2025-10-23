@extends('layouts.app')

@section('title', 'Gracias por participar')

@section('content')
<div class="min-vh-100 d-flex align-items-center position-relative" style="background: linear-gradient(135deg, #fff9e6 0%, #e6f2ff 50%, #ffe6e6 100%);">
    <!-- Efecto difuminado de fondo - Colores de Colombia -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 0;">
        <div class="blur-circle" style="position: absolute; top: -10%; left: -5%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(255, 209, 0, 0.2) 0%, transparent 70%); filter: blur(60px);"></div>
        <div class="blur-circle" style="position: absolute; bottom: -15%; right: -5%; width: 550px; height: 550px; background: radial-gradient(circle, rgba(206, 17, 38, 0.15) 0%, transparent 70%); filter: blur(60px);"></div>
        <div class="blur-circle" style="position: absolute; top: 30%; right: 10%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(0, 56, 168, 0.15) 0%, transparent 70%); filter: blur(50px);"></div>
        <div class="blur-circle" style="position: absolute; top: 50%; left: 20%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255, 209, 0, 0.12) 0%, transparent 70%); filter: blur(55px);"></div>
    </div>

    <div class="container py-5 position-relative" style="z-index: 1;">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card border-0 rounded-4 overflow-hidden" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
                    <div class="card-body text-center p-5">
                        <!-- Ícono de éxito animado -->
                        <div class="mb-4 success-animation">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 6rem;"></i>
                        </div>

                        <!-- Mensaje principal -->
                        <h1 class="display-5 fw-bold text-dark mb-3">¡Gracias por participar!</h1>
                        <p class="lead text-muted mb-4">Tu voto ha sido registrado exitosamente</p>

                        <hr class="my-4">

                        <!-- Información adicional -->
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <div class="p-4 bg-light rounded-3">
                                    <h5 class="fw-semibold text-dark mb-3">
                                        <i class="bi bi-clipboard-data text-primary"></i> {{ $survey->title }}
                                    </h5>
                                    @if($survey->description)
                                        <p class="text-muted small mb-0">{{ $survey->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Información de privacidad -->
                        <div class="alert alert-info border-0 shadow-sm" role="alert">
                            <i class="bi bi-shield-check-fill"></i>
                            <strong>Tu privacidad es importante</strong>
                            <p class="mb-0 mt-2 small">
                                Tu voto es completamente anónimo. No almacenamos ningún dato personal.
                                Solo guardamos tu voto para prevenir duplicados.
                            </p>
                        </div>

                        <!-- Recordatorio -->
                        <div class="mt-4 p-3 bg-warning bg-opacity-10 rounded-3">
                            <p class="mb-0 text-dark">
                                <i class="bi bi-info-circle-fill text-warning"></i>
                                <strong>Recuerda:</strong> Solo puedes votar una vez en esta encuesta.
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-light text-center py-3">
                        <small class="text-muted">
                            <i class="bi bi-calendar-check"></i>
                            Votación registrada el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
                        </small>
                    </div>
                </div>

                <!-- Mensaje adicional -->
                <div class="text-center mt-4">
                    <p class="text-muted">
                        <i class="bi bi-check-circle"></i> Puedes cerrar esta ventana
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Animación del ícono de éxito */
.success-animation {
    animation: successPop 0.6s ease-out;
}

@keyframes successPop {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Animación del card */
.card {
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .display-5 {
        font-size: 2rem;
    }

    .lead {
        font-size: 1rem;
    }

    .success-animation i {
        font-size: 4rem !important;
    }
}
</style>

<script>
// Confetti effect (opcional - celebración visual)
document.addEventListener('DOMContentLoaded', function() {
    // Pequeña vibración de celebración en dispositivos móviles
    if (navigator.vibrate) {
        navigator.vibrate([100, 50, 100]);
    }
});
</script>
@endsection
