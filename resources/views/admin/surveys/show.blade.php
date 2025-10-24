@extends('layouts.admin')

@section('title', 'Resultados - ' . $survey->title)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="h2 fw-bold">{{ $survey->title }}</h1>
                @if($survey->description)
                    <p class="text-muted">{{ $survey->description }}</p>
                @endif
            </div>
            <div class="btn-group" role="group">
                <a href="{{ route('surveys.show', $survey->slug) }}" target="_blank" class="btn btn-success">
                    <i class="bi bi-link-45deg"></i> Ver Pública
                </a>
                <a href="{{ route('admin.surveys.edit', $survey) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <button type="button" class="btn btn-danger" onclick="confirmReset()">
                    <i class="bi bi-arrow-clockwise"></i> Reset Votos
                </button>
                <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas generales -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-gradient rounded p-3">
                            <i class="bi bi-people text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Votantes Únicos</p>
                            <h3 class="mb-0">{{ $uniqueVoters }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-gradient rounded p-3">
                            <i class="bi bi-chat-dots text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Total Respuestas</p>
                            <h3 class="mb-0">{{ $totalVotes }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-gradient rounded p-3">
                            <i class="bi bi-question-circle text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Preguntas</p>
                            <h3 class="mb-0">{{ $survey->questions->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Link para compartir -->
    <div class="alert alert-info d-flex align-items-center" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>
        <div class="flex-grow-1">
            <strong>Link para compartir:</strong>
            <code class="ms-2">{{ url('/survey/' . $survey->slug) }}</code>
        </div>
        <button class="btn btn-sm btn-primary" onclick="copyToClipboard('{{ url('/survey/' . $survey->slug) }}')">
            <i class="bi bi-clipboard"></i> Copiar
        </button>
    </div>

    <!-- Resultados por pregunta -->
    @foreach($questionStats as $stat)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">{{ $stat['question'] }}</h5>
                <small class="text-muted">
                    <i class="bi bi-graph-up"></i> Total de votos: {{ $stat['total_votes'] }}
                </small>
            </div>
            <div class="card-body">
                @foreach($stat['options'] as $option)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-medium">{{ $option['text'] }}</span>
                            <span class="fw-bold text-primary">{{ $option['votes'] }} votos ({{ $option['percentage'] }}%)</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-primary bg-gradient" role="progressbar"
                                 style="width: {{ $option['percentage'] }}%"
                                 aria-valuenow="{{ $option['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                <strong>{{ $option['percentage'] }}%</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @if(count($questionStats) === 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-graph-down text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">Aún no hay votos</h5>
                <p class="text-muted">Comparte el link de la encuesta para comenzar a recibir respuestas.</p>
                <a href="{{ route('surveys.show', $survey->slug) }}" target="_blank" class="btn btn-primary mt-3">
                    <i class="bi bi-link-45deg"></i> Ver Encuesta Pública
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Formulario oculto para reset -->
<form id="resetForm" method="POST" action="{{ route('admin.surveys.reset', $survey) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('¡Link copiado al portapapeles!');
    });
}

function confirmReset() {
    const totalVotes = {{ $totalVotes }};
    const uniqueVoters = {{ $uniqueVoters }};

    if (confirm(`⚠️ ADVERTENCIA: Esta acción es IRREVERSIBLE\n\n` +
                `Se eliminarán:\n` +
                `• ${totalVotes} votos totales\n` +
                `• ${uniqueVoters} votantes únicos\n` +
                `• Todos los resultados de esta encuesta\n\n` +
                `¿Estás SEGURO de que deseas continuar?`)) {

        // Segunda confirmación
        if (confirm(`🔴 ÚLTIMA CONFIRMACIÓN\n\n` +
                    `Escribe "RESET" en la próxima ventana para confirmar`)) {

            const confirmation = prompt('Escribe "RESET" para confirmar (en mayúsculas):');

            if (confirmation === 'RESET') {
                // Asegurar que el formulario se envíe correctamente
                const form = document.getElementById('resetForm');
                // Verificar que el método sea POST
                form.method = 'POST';
                form.submit();
            } else {
                alert('❌ Operación cancelada. El texto no coincide.');
            }
        }
    }
}
</script>
@endsection
