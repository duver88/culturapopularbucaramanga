@extends('layouts.admin')

@section('title', 'Editar Encuesta')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Editar Encuesta</h1>
        <p class="text-muted">Actualiza la información de la encuesta</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.surveys.update', $survey) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Título -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">
                        <i class="bi bi-card-heading"></i> Título de la Encuesta *
                    </label>
                    <input type="text" class="form-control" name="title" id="title"
                           value="{{ old('title', $survey->title) }}" required>
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">
                        <i class="bi bi-textarea-t"></i> Descripción
                    </label>
                    <textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $survey->description) }}</textarea>
                </div>

                <!-- Banner -->
                <div class="mb-4">
                    <label for="banner" class="form-label fw-semibold">
                        <i class="bi bi-image"></i> Banner/Imagen
                    </label>
                    @if($survey->banner)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $survey->banner) }}" alt="Banner actual"
                                 class="img-thumbnail" style="max-height: 150px;">
                            <p class="small text-muted mt-1">Banner actual (sube una nueva imagen para reemplazarla)</p>
                        </div>
                    @endif
                    <input type="file" class="form-control" name="banner" id="banner" accept="image/*">
                    <small class="text-muted">Imagen opcional para mostrar en la encuesta</small>
                </div>

                <!-- Estado -->
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                               value="1" {{ old('is_active', $survey->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">
                            <i class="bi bi-toggle-on"></i> Encuesta Activa
                        </label>
                        <small class="d-block text-muted">Las encuestas inactivas no pueden recibir votos</small>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Preguntas -->
                <div class="mb-4">
                    <h5 class="mb-3 fw-semibold">
                        <i class="bi bi-question-circle"></i> Preguntas
                    </h5>

                    @foreach($survey->questions as $qIndex => $question)
                        <div class="card mb-3 border">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">Pregunta {{ $qIndex + 1 }}</h6>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="questions[{{ $qIndex }}][id]" value="{{ $question->id }}">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Texto de la Pregunta *</label>
                                    <input type="text" name="questions[{{ $qIndex }}][question_text]"
                                           value="{{ old('questions.'.$qIndex.'.question_text', $question->question_text) }}"
                                           required class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tipo de Pregunta *</label>
                                    <select name="questions[{{ $qIndex }}][question_type]" required class="form-select">
                                        <option value="single_choice" {{ $question->question_type == 'single_choice' ? 'selected' : '' }}>
                                            Opción Única (radio)
                                        </option>
                                        <option value="multiple_choice" {{ $question->question_type == 'multiple_choice' ? 'selected' : '' }}>
                                            Opción Múltiple (checkbox)
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Opciones de Respuesta *</label>
                                    @foreach($question->options as $oIndex => $option)
                                        <div class="input-group mb-2">
                                            <span class="input-group-text bg-light">{{ $oIndex + 1 }}</span>
                                            <input type="hidden" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][id]" value="{{ $option->id }}">
                                            <input type="text" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][option_text]"
                                                   value="{{ old('questions.'.$qIndex.'.options.'.$oIndex.'.option_text', $option->option_text) }}"
                                                   required placeholder="Texto de la opción" class="form-control">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Nota:</strong> Por el momento, solo puedes editar el texto de preguntas y opciones existentes.
                        No puedes agregar o eliminar preguntas/opciones en una encuesta publicada.
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Actualizar Encuesta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
