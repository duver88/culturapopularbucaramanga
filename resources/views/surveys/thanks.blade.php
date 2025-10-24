@extends('layouts.app')

@section('title', 'Gracias por participar - ' . $survey->title)

@section('meta_description', 'Resultados de la encuesta: ' . $survey->title)

@section('og_image_full', url('images/default-survey-preview.jpg'))

@section('og_title', 'Resultados - ' . $survey->title)
@section('og_description', 'Mira los resultados de esta encuesta. ' . ($survey->description ?? ''))

@section('content')
@php
    // Paleta de colores primarios profesionales para votaciones
    $colors = [
        ['#1e40af', '#3b82f6'], // Azul profesional
        ['#047857', '#10b981'], // Verde profesional
        ['#374151', '#6b7280'], // Gris oscuro
        ['#0891b2', '#06b6d4'], // Cyan profesional
        ['#1e3a8a', '#2563eb'], // Azul oscuro
        ['#065f46', '#059669'], // Verde oscuro
        ['#1f2937', '#4b5563'], // Negro/Gris
        ['#0e7490', '#0891b2'], // Teal profesional
    ];

    // Colores planos para el gráfico de pastel
    $chartColors = [
        '#2563eb', // Azul
        '#10b981', // Verde
        '#6b7280', // Gris
        '#06b6d4', // Cyan
        '#3b82f6', // Azul claro
        '#059669', // Verde oscuro
        '#4b5563', // Gris oscuro
        '#0891b2', // Teal
    ];
@endphp

<div class="min-vh-100 position-relative py-5" style="background: linear-gradient(135deg, #fff9e6 0%, #e6f2ff 50%, #ffe6e6 100%);">
    <!-- Efecto difuminado de fondo - Colores de Colombia -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 0; top: 0; left: 0;">
        <div class="blur-circle" style="position: absolute; top: -10%; left: -5%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(255, 209, 0, 0.2) 0%, transparent 70%); filter: blur(60px);"></div>
        <div class="blur-circle" style="position: absolute; bottom: -15%; right: -5%; width: 550px; height: 550px; background: radial-gradient(circle, rgba(206, 17, 38, 0.15) 0%, transparent 70%); filter: blur(60px);"></div>
        <div class="blur-circle" style="position: absolute; top: 30%; right: 10%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(0, 56, 168, 0.15) 0%, transparent 70%); filter: blur(50px);"></div>
        <div class="blur-circle" style="position: absolute; top: 50%; left: 20%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255, 209, 0, 0.12) 0%, transparent 70%); filter: blur(55px);"></div>
    </div>

    <div class="container position-relative" style="z-index: 1;">
        <!-- Tarjeta única con todo el contenido -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="card border-0 rounded-4 overflow-hidden shadow-lg success-card" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.98);">
                    <!-- Banner de la encuesta (si existe) -->
                    @if($survey->banner)
                        <div class="banner-wrapper">
                            <img src="{{ asset('storage/' . $survey->banner) }}"
                                 alt="Banner de {{ $survey->title }}"
                                 class="w-100 banner-img"
                                 style="display: block; height: auto; max-height: 400px; object-fit: contain; background: #f8f9fa;">
                        </div>
                    @endif

                    <div class="card-body p-4 p-md-5">
                        <!-- Título de la encuesta debajo del banner -->
                        @if($survey->banner)
                            <div class="text-center mb-4 pb-3 border-bottom">
                                <h3 class="fw-bold text-dark mb-2">
                                    <i class="bi bi-clipboard-data text-primary"></i> {{ $survey->title }}
                                </h3>
                                @if($survey->description)
                                    <p class="text-muted mb-0">{{ $survey->description }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Sección de éxito -->
                        <div class="text-center mb-5 pb-4 border-bottom">
                            <!-- Ícono de éxito animado -->
                            <div class="mb-4 success-animation">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            </div>

                            <!-- Mensaje principal -->
                            <h1 class="display-6 fw-bold text-dark mb-3">¡Gracias por participar!</h1>
                            <p class="lead text-muted mb-0">Tu voto ha sido registrado exitosamente</p>

                            @if(!$survey->banner)
                                <hr class="my-4">
                                <h5 class="fw-semibold text-dark mb-2">
                                    <i class="bi bi-clipboard-data text-primary"></i> {{ $survey->title }}
                                </h5>
                                @if($survey->description)
                                    <p class="text-muted small mb-0">{{ $survey->description }}</p>
                                @endif
                            @endif
                        </div>

                        <!-- Resultados en Tiempo Real -->
                        <div class="results-section">
                            <h3 class="fw-bold text-dark mb-4 text-center">
                                <i class="bi bi-bar-chart-fill text-primary"></i> Resultados en Tiempo Real
                            </h3>

                        @foreach($statistics as $index => $stat)
                            <div class="question-block mb-5 pb-4 @if(!$loop->last) border-bottom @endif">
                                <!-- Pregunta -->
                                <div class="d-flex align-items-start gap-3 mb-4">
                                    <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex-grow-1">
                                        <h5 class="fw-semibold text-dark mb-2">{{ $stat['question'] }}</h5>
                                        <small class="text-muted">
                                            <i class="bi bi-chat-left-text"></i> {{ $stat['total_responses'] }} respuestas
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Gráfico de pastel -->
                                    <div class="col-12 col-md-5 mb-4 mb-md-0">
                                        <div class="chart-container" style="position: relative; height: 280px;">
                                            <canvas id="chart-{{ $index }}"></canvas>
                                        </div>
                                    </div>

                                    <!-- Opciones con barras de progreso -->
                                    <div class="col-12 col-md-7">
                                        <div class="options-container">
                                    @foreach($stat['options'] as $optIndex => $option)
                                        <div class="option-item mb-3 fade-in" style="animation-delay: {{ $optIndex * 0.1 }}s;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-medium text-dark">{{ $option['text'] }}</span>
                                                <span class="fw-bold text-dark" style="min-width: 60px; text-align: right; font-size: 1.1rem;">
                                                    <span class="percentage-counter" data-target="{{ $option['percentage'] }}">0</span>%
                                                </span>
                                            </div>

                                            <!-- Barra de progreso animada con gradiente -->
                                            <div class="progress shadow-sm" style="height: 30px; border-radius: 15px; background: rgba(0,0,0,0.05);">
                                                <div class="progress-bar progress-bar-animated progress-bar-striped position-relative overflow-visible"
                                                     role="progressbar"
                                                     style="width: 0%;
                                                            background: linear-gradient(90deg, {{ $colors[$optIndex % count($colors)][0] }} 0%, {{ $colors[$optIndex % count($colors)][1] }} 100%);
                                                            border-radius: 15px;
                                                            transition: width 1.5s ease-out {{ $optIndex * 0.2 }}s;"
                                                     data-width="{{ $option['percentage'] }}"
                                                     aria-valuenow="{{ $option['percentage'] }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">

                                                    @if($option['percentage'] > 0)
                                                        <!-- Efecto de brillo -->
                                                        <span class="shine-effect"></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                            <!-- Información de privacidad -->
                            <div class="alert alert-info border-0 shadow-sm mt-5" role="alert" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.08) 0%, rgba(13, 202, 240, 0.08) 100%);">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-shield-check-fill text-info" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="fw-bold mb-2">Tu privacidad es importante</h6>
                                        <p class="mb-0 small">
                                            Tu voto es completamente anónimo. No almacenamos ningún dato personal.
                                            Solo guardamos tu voto para prevenir duplicados.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-light text-center py-3 border-0">
                        <small class="text-muted">
                            <i class="bi bi-calendar-check"></i>
                            Votación registrada el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensaje adicional -->
        <div class="text-center mt-4">
            <p class="text-muted">
                <i class="bi bi-info-circle"></i> Los resultados se actualizan en tiempo real
            </p>
        </div>
    </div>
</div>

<style>
/* Estilos del banner */
.banner-wrapper {
    overflow: hidden;
    background: #f8f9fa;
    min-height: 200px;
    max-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.banner-img {
    animation: fadeIn 0.8s ease-out;
    object-fit: contain !important;
    width: 100%;
    height: auto;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Animación del ícono de éxito */
.success-animation {
    animation: successPop 0.6s ease-out;
}

@keyframes successPop {
    0% {
        transform: scale(0) rotate(-180deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(10deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

/* Animación de las cards */
.success-card {
    animation: slideDown 0.6s ease-out;
}

.stats-card {
    animation: slideUp 0.8s ease-out 0.3s both;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animación de entrada para opciones */
.fade-in {
    animation: fadeInScale 0.5s ease-out both;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: translateX(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
}

/* Transiciones suaves para elementos */
.results-section {
    animation: fadeInUp 0.7s ease-out 0.4s both;
}

/* Efecto de brillo en las barras */
.shine-effect {
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shine 2s infinite;
}

@keyframes shine {
    0% {
        left: -100%;
    }
    100% {
        left: 200%;
    }
}

/* Hover effects */
.option-item {
    transition: all 0.3s ease;
    padding: 10px;
    border-radius: 10px;
}

.option-item:hover {
    background: rgba(13, 110, 253, 0.05);
    transform: translateX(5px);
}

.progress {
    overflow: visible;
}

.progress-bar {
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Animación de la pregunta */
.question-block {
    animation: fadeInUp 0.6s ease-out both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Estilos para el contenedor del gráfico */
.chart-container {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
}

.chart-container canvas {
    max-width: 100%;
    height: auto !important;
}

/* Responsive para tablets */
@media (max-width: 992px) {
    .chart-container {
        height: 300px !important;
    }
}

/* Responsive para móviles */
@media (max-width: 768px) {
    /* Ajustes generales */
    .min-vh-100 {
        padding: 1rem 0 !important;
    }

    .container {
        padding: 0 0.5rem !important;
    }

    /* Tipografía móvil */
    .display-6 {
        font-size: 1.5rem !important;
    }

    .lead {
        font-size: 0.95rem !important;
    }

    h3 {
        font-size: 1.25rem !important;
    }

    h5 {
        font-size: 1rem !important;
    }

    /* Ícono de éxito */
    .success-animation i {
        font-size: 3rem !important;
    }

    /* Card principal */
    .card-body {
        padding: 1.5rem 1rem !important;
    }

    /* Banner móvil */
    .banner-wrapper {
        min-height: 150px !important;
        max-height: 250px !important;
    }

    .banner-img {
        max-height: 250px !important;
    }

    /* Sección de éxito */
    .text-center.mb-5.pb-4 {
        margin-bottom: 2rem !important;
        padding-bottom: 1.5rem !important;
    }

    /* Título de resultados */
    .results-section h3 {
        margin-bottom: 1.5rem !important;
    }

    /* Pregunta móvil */
    .question-block {
        margin-bottom: 2rem !important;
        padding-bottom: 1.5rem !important;
    }

    .question-block .badge {
        width: 32px !important;
        height: 32px !important;
        font-size: 0.9rem !important;
    }

    .question-block h5 {
        font-size: 0.95rem !important;
        line-height: 1.4 !important;
    }

    .question-block small {
        font-size: 0.8rem !important;
    }

    /* Gráfico móvil - MÁS GRANDE */
    .chart-container {
        height: 320px !important;
        margin-bottom: 2rem !important;
        padding: 10px !important;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
    }

    /* En móvil, el gráfico ocupa el 100% del ancho */
    .col-md-5 {
        order: 1;
    }

    .col-md-7 {
        order: 2;
    }

    /* Opciones móviles */
    .option-item {
        margin-bottom: 1rem !important;
        padding: 8px !important;
    }

    .option-item .d-flex {
        flex-wrap: wrap;
        gap: 6px !important;
    }

    .option-item .fw-medium {
        font-size: 0.9rem !important;
        flex: 1 1 100%;
        margin-bottom: 4px;
    }

    .option-item .fw-bold {
        font-size: 1rem !important;
        min-width: auto !important;
        flex: 1 1 100%;
        text-align: left !important;
    }

    /* Barras de progreso móvil */
    .progress {
        height: 22px !important;
        border-radius: 11px !important;
    }

    /* Alert de privacidad */
    .alert {
        padding: 1rem !important;
        margin-top: 2rem !important;
    }

    .alert h6 {
        font-size: 0.9rem !important;
    }

    .alert p {
        font-size: 0.8rem !important;
    }

    .alert i.bi-shield-check-fill {
        font-size: 1.2rem !important;
    }

    /* Footer */
    .card-footer {
        padding: 0.75rem !important;
    }

    .card-footer small {
        font-size: 0.75rem !important;
    }

    /* Mensaje adicional */
    .text-center.mt-4 p {
        font-size: 0.85rem !important;
    }
}

/* Responsive para móviles pequeños */
@media (max-width: 576px) {
    /* Ajustes extremos para pantallas muy pequeñas */
    .display-6 {
        font-size: 1.3rem !important;
    }

    .card-body {
        padding: 1rem 0.75rem !important;
    }

    .chart-container {
        height: 280px !important;
    }

    .question-block .d-flex {
        gap: 0.5rem !important;
    }

    .option-item .fw-medium {
        font-size: 0.85rem !important;
    }

    .progress {
        height: 20px !important;
        border-radius: 10px !important;
    }

    /* Banner extra pequeño */
    .banner-wrapper {
        min-height: 120px !important;
        max-height: 200px !important;
    }

    .banner-img {
        max-height: 200px !important;
    }
}

/* Partículas de confetti (opcional) */
.confetti {
    position: fixed;
    width: 10px;
    height: 10px;
    background: #f0f;
    position: absolute;
    animation: confetti-fall 3s linear infinite;
}

@keyframes confetti-fall {
    to {
        transform: translateY(100vh) rotate(360deg);
    }
}
</style>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vibración de celebración en dispositivos móviles
    if (navigator.vibrate) {
        navigator.vibrate([100, 50, 100, 50, 200]);
    }

    // Animación de los porcentajes
    const percentageCounters = document.querySelectorAll('.percentage-counter');
    percentageCounters.forEach((element, index) => {
        const target = parseFloat(element.getAttribute('data-target'));
        const duration = 1500;
        const increment = target / (duration / 16);
        let current = 0;

        const updatePercentage = () => {
            current += increment;
            if (current < target) {
                element.textContent = current.toFixed(1);
                requestAnimationFrame(updatePercentage);
            } else {
                element.textContent = target.toFixed(1);
            }
        };

        setTimeout(updatePercentage, 800 + (index * 100));
    });

    // Animación de las barras de progreso
    setTimeout(() => {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach((bar, index) => {
            const targetWidth = bar.getAttribute('data-width');
            setTimeout(() => {
                bar.style.width = targetWidth + '%';
            }, index * 200);
        });
    }, 600);

    // Crear efecto de confetti con colores institucionales de Colombia
    function createConfetti() {
        const colors = ['#FCD116', '#003893', '#CE1126', '#1e3a8a', '#047857', '#3b82f6']; // Amarillo, Azul, Rojo Colombia + institucionales
        const confettiCount = 40; // Menos confetti para efecto más sobrio

        for (let i = 0; i < confettiCount; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.style.position = 'fixed';
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = confetti.style.width;
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.top = '-10px';
                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                confetti.style.opacity = Math.random();
                confetti.style.zIndex = '9999';
                confetti.style.pointerEvents = 'none';

                document.body.appendChild(confetti);

                const duration = Math.random() * 3 + 2;
                const rotation = Math.random() * 360;

                confetti.animate([
                    { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
                    { transform: `translateY(${window.innerHeight + 10}px) rotate(${rotation}deg)`, opacity: 0 }
                ], {
                    duration: duration * 1000,
                    easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
                });

                setTimeout(() => {
                    confetti.remove();
                }, duration * 1000);
            }, i * 30);
        }
    }

    // Lanzar confetti después de un pequeño delay
    setTimeout(createConfetti, 400);

    // Añadir delays escalonados a las preguntas
    const questionBlocks = document.querySelectorAll('.question-block');
    questionBlocks.forEach((block, index) => {
        block.style.animationDelay = (0.4 + index * 0.2) + 's';
    });

    // Crear gráficos de pastel para cada pregunta
    const chartData = @json($statistics);
    const chartColors = @json($chartColors);

    chartData.forEach((question, index) => {
        const ctx = document.getElementById(`chart-${index}`);
        if (!ctx) return;

        const labels = question.options.map(opt => opt.text);
        const data = question.options.map(opt => opt.percentage);
        const colors = question.options.map((opt, idx) => chartColors[idx % chartColors.length]);

        // Detectar si es móvil
        const isMobile = window.innerWidth <= 768;
        const isSmallMobile = window.innerWidth <= 576;

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: isMobile ? 3 : 3,
                    hoverOffset: isMobile ? 12 : 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: isMobile ? 12 : 15,
                            font: {
                                size: isSmallMobile ? 11 : (isMobile ? 13 : 12),
                                family: "'Inter', system-ui, -apple-system, sans-serif",
                                weight: '500'
                            },
                            color: '#374151',
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: isMobile ? 10 : 8,
                            boxHeight: isMobile ? 10 : 8,
                            // Truncar texto largo en móvil
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const meta = chart.getDatasetMeta(0);
                                        const style = meta.controller.getStyle(i);

                                        // Truncar texto en móvil
                                        let displayLabel = label;
                                        if (isSmallMobile && label.length > 25) {
                                            displayLabel = label.substring(0, 25) + '...';
                                        } else if (isMobile && label.length > 30) {
                                            displayLabel = label.substring(0, 30) + '...';
                                        }

                                        return {
                                            text: displayLabel,
                                            fillStyle: style.backgroundColor,
                                            strokeStyle: style.borderColor,
                                            lineWidth: style.borderWidth,
                                            hidden: !chart.getDataVisibility(i),
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        padding: isMobile ? 12 : 12,
                        titleFont: {
                            size: isMobile ? 14 : 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: isMobile ? 13 : 13
                        },
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.parsed.toFixed(1) + '%';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: isMobile ? 1000 : 1500,
                    delay: 600 + (index * 200)
                }
            }
        });
    });
});
</script>
@endsection

