@extends('layouts.app')

@section('title', $survey->title)

@section('meta_description', $survey->description ?? 'Participa en esta encuesta y comparte tu opinión')

@section('og_image_full', url('images/default-survey-preview.jpg'))

@section('og_title', $survey->title)
@section('og_description', $survey->description ?? 'Participa en esta encuesta y comparte tu opinión')

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
            <div class="col-12 col-md-10 col-lg-8">
                <!-- Card principal -->
                <div class="card border-0 rounded-4 overflow-hidden" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
                    <!-- Banner -->
                    @if($survey->banner)
                        <div class="banner-wrapper-form">
                            <img src="{{ asset('storage/' . $survey->banner) }}"
                                 alt="Banner de {{ $survey->title }}"
                                 class="w-100 banner-img-form"
                                 style="display: block; height: auto; max-height: 400px; object-fit: contain; background: #f8f9fa;">
                        </div>
                    @else
                        <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center"
                             style="height: 200px; background: linear-gradient(180deg, #FCD116 0%, #FCD116 50%, #003893 75%, #CE1126 100%);">
                            <i class="bi bi-clipboard-data text-white" style="font-size: 4rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);"></i>
                        </div>
                    @endif

                    <div class="card-body p-4 p-md-5">
                        <!-- Título y descripción -->
                        <div class="text-center mb-5">
                            <h1 class="display-5 fw-bold text-dark mb-3">{{ $survey->title }}</h1>
                            @if($survey->description)
                                <p class="lead text-muted">{{ $survey->description }}</p>
                            @endif
                            <hr class="my-4">
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($hasVoted)
                            <!-- Ya votó -->
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                                </div>
                                <h2 class="h3 fw-bold text-dark mb-3">¡Gracias por participar!</h2>
                                <p class="lead text-muted mb-4">Ya has votado en esta encuesta anteriormente.</p>
                                <p class="text-muted">
                                    <i class="bi bi-info-circle"></i> Solo se permite un voto por persona.
                                </p>
                            </div>
                        @else
                            <!-- Formulario de votación -->
                            <form method="POST" action="{{ route('surveys.vote', $survey->slug) }}" id="voteForm">
                                @csrf
                                <input type="hidden" name="fingerprint" id="fingerprint">

                                <!-- Honeypot fields - campos trampa para bots (invisibles) -->
                                <input type="text" name="website" id="website" style="position:absolute;left:-9999px;width:1px;height:1px;" tabindex="-1" autocomplete="off">
                                <input type="text" name="url_field" id="url_field" style="position:absolute;left:-9999px;width:1px;height:1px;" tabindex="-1" autocomplete="off">

                                @foreach($survey->questions as $question)
                                    <div class="mb-5 pb-4 border-bottom">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                                 style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold">{{ $loop->iteration }}</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="fw-semibold text-dark mb-3">{{ $question->question_text }}</h5>

                                                @if($question->question_type === 'single_choice')
                                                    <!-- Radio buttons para selección única -->
                                                    @foreach($question->options->shuffle() as $option)

                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" type="radio"
                                                                   name="answers[{{ $question->id }}]"
                                                                   value="{{ $option->id }}"
                                                                   id="option{{ $option->id }}"
                                                                   required>
                                                            <label class="form-check-label fw-medium" for="option{{ $option->id }}">
                                                                {{ $option->option_text }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <!-- Checkboxes para selección múltiple -->
                                                    @foreach($question->options as $option)
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="answers[{{ $question->id }}][]"
                                                                   value="{{ $option->id }}"
                                                                   id="option{{ $option->id }}">
                                                            <label class="form-check-label fw-medium" for="option{{ $option->id }}">
                                                                {{ $option->option_text }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Botón de envío -->
                                <div class="d-grid gap-2 mt-5">
                                    <button type="submit" class="btn btn-lg text-white fw-bold shadow"
                                            style="background: linear-gradient(90deg, #FCD116 0%, #003893 50%, #CE1126 100%); padding: 1rem; border: none; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                                        <i class="bi bi-send-fill"></i> Enviar mi voto
                                    </button>
                                </div>

                                <div class="text-center mt-4">
                                    <small class="text-muted">
                                        <i class="bi bi-shield-check"></i> Tu voto es anónimo y seguro
                                    </small>
                                </div>
                            </form>
                        @endif
                    </div>

                    <!-- Footer del card -->
                    <div class="card-footer bg-light text-center py-3">
                        <small class="text-muted">
                            <i class="bi bi-clipboard-data"></i> Sistema de Encuestas
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Generar fingerprint único para el navegador
function generateFingerprint() {
    // Verificar si ya existe en localStorage
    let fingerprint = localStorage.getItem('survey_fingerprint');

    if (!fingerprint) {
        // Generar nuevo fingerprint basado en características del navegador
        const nav = window.navigator;
        const screen = window.screen;
        const data = [
            nav.userAgent,
            nav.language,
            screen.colorDepth,
            screen.width + 'x' + screen.height,
            new Date().getTimezoneOffset(),
            !!window.sessionStorage,
            !!window.localStorage
        ].join('|');

        // Generar hash simple
        let hash = 0;
        for (let i = 0; i < data.length; i++) {
            const char = data.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }

        fingerprint = 'fp_' + Math.abs(hash).toString(36) + '_' + Date.now().toString(36);
        localStorage.setItem('survey_fingerprint', fingerprint);
    }

    return fingerprint;
}

// Establecer fingerprint al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const fingerprint = generateFingerprint();
    document.getElementById('fingerprint').value = fingerprint;

    // Animación suave al hacer scroll a preguntas
    const formChecks = document.querySelectorAll('.form-check-input');
    formChecks.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.form-check').style.transform = 'scale(1.02)';
            this.closest('.form-check').style.transition = 'transform 0.2s';
        });

        input.addEventListener('blur', function() {
            this.closest('.form-check').style.transform = 'scale(1)';
        });
    });
});
</script>

<style>
/* Estilos del banner del formulario */
.banner-wrapper-form {
    overflow: hidden;
    background: #f8f9fa;
    min-height: 200px;
    max-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.banner-img-form {
    object-fit: contain !important;
    width: 100%;
    height: auto;
}

.form-check {
    transition: all 0.3s ease;
    padding: 0.75rem;
    border-radius: 0.5rem;
}

.form-check:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    margin-top: 0.15rem;
    cursor: pointer;
}

.form-check-input:checked {
    background: linear-gradient(135deg, #003893 0%, #CE1126 100%);
    border-color: #003893;
}

.form-check-label {
    cursor: pointer;
    font-size: 1.05rem;
}

.card {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-5 {
        font-size: 2rem;
    }

    .lead {
        font-size: 1rem;
    }

    .card-body {
        padding: 1.5rem !important;
    }

    .form-check-label {
        font-size: 0.95rem;
    }

    /* Banner móvil */
    .banner-wrapper-form {
        min-height: 150px !important;
        max-height: 250px !important;
    }

    .banner-img-form {
        max-height: 250px !important;
    }
}

@media (max-width: 576px) {
    .card-img-top {
        height: 150px !important;
    }

    .bg-primary.rounded-circle {
        width: 35px !important;
        height: 35px !important;
        font-size: 0.9rem;
    }

    /* Banner extra pequeño */
    .banner-wrapper-form {
        min-height: 120px !important;
        max-height: 200px !important;
    }

    .banner-img-form {
        max-height: 200px !important;
    }
}
</style>
@endsection

