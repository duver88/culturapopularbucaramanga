<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function show($slug)
    {
        // Buscar la encuesta sin filtrar por is_active primero
        $survey = Survey::where('slug', $slug)
            ->with('questions.options')
            ->firstOrFail();

        // Si la encuesta no está activa, mostrar mensaje
        if (!$survey->is_active) {
            return view('surveys.inactive', compact('survey'));
        }

        // Verificar si ya votó (solo por fingerprint para permitir múltiples usuarios en la misma red)
        $fingerprint = request()->cookie('survey_fingerprint');

        $hasVoted = false;
        if ($fingerprint) {
            $hasVoted = Vote::where('survey_id', $survey->id)
                ->where('fingerprint', $fingerprint)
                ->exists();
        }

        // Si ya votó, redirigir a la página de agradecimiento con resultados
        if ($hasVoted) {
            return redirect()->route('surveys.thanks', $survey->slug);
        }

        return view('surveys.show', compact('survey', 'hasVoted'));
    }

    public function vote(Request $request, $slug)
    {
        $survey = Survey::where('slug', $slug)->firstOrFail();

        // Verificar que la encuesta esté activa
        if (!$survey->is_active) {
            return redirect()->route('surveys.show', $slug)
                ->with('error', 'Esta encuesta no está disponible para votar en este momento.');
        }

        $validated = $request->validate([
            'answers' => 'required|array|min:1|max:50',
            'answers.*' => 'required|exists:question_options,id',
            'fingerprint' => 'required|string|max:100',
            'device_data' => 'nullable|array',
            'device_data.user_agent' => 'nullable|string|max:500',
            'device_data.platform' => 'nullable|string|max:100',
            'device_data.screen_resolution' => 'nullable|string|max:50',
            'device_data.hardware_concurrency' => 'nullable|integer',
        ]);

        // Validar que las respuestas correspondan a preguntas de esta encuesta
        foreach ($validated['answers'] as $questionId => $optionId) {
            $validOption = \App\Models\QuestionOption::where('id', $optionId)
                ->whereHas('question', function($q) use ($survey, $questionId) {
                    $q->where('survey_id', $survey->id)
                      ->where('id', $questionId);
                })
                ->exists();

            if (!$validOption) {
                abort(422, 'Respuesta inválida detectada.');
            }
        }

        $ipAddress = $request->ip();
        $fingerprint = $request->input('fingerprint') ?? Str::random(40);
        $deviceData = $request->input('device_data', []);

        // SISTEMA INTELIGENTE DE DETECCIÓN DE FRAUDE

        // 1. Verificar por fingerprint exacto (mismo navegador)
        $exactMatch = Vote::where('survey_id', $survey->id)
            ->where('fingerprint', $fingerprint)
            ->exists();

        if ($exactMatch) {
            return back()->with('error', 'Ya has votado en esta encuesta.');
        }

        // 2. Verificar dispositivos similares con la misma IP (posible fraude)
        $votesFromSameIP = Vote::where('survey_id', $survey->id)
            ->where('ip_address', $ipAddress)
            ->get();

        if ($votesFromSameIP->isNotEmpty()) {
            $suspiciousScore = 0;
            $currentUserAgent = $deviceData['user_agent'] ?? '';
            $currentPlatform = $deviceData['platform'] ?? '';
            $currentResolution = $deviceData['screen_resolution'] ?? '';
            $currentCPU = $deviceData['hardware_concurrency'] ?? 0;

            foreach ($votesFromSameIP as $vote) {
                // Calcular similitud del dispositivo
                $similarity = 0;

                // User agent similar (mismo navegador base)
                if ($vote->user_agent && $currentUserAgent) {
                    similar_text($vote->user_agent, $currentUserAgent, $percent);
                    if ($percent > 80) $similarity += 40; // Muy sospechoso
                    elseif ($percent > 60) $similarity += 20;
                }

                // Misma plataforma
                if ($vote->platform == $currentPlatform) {
                    $similarity += 20;
                }

                // Misma resolución de pantalla
                if ($vote->screen_resolution == $currentResolution) {
                    $similarity += 25;
                }

                // Mismo número de núcleos CPU
                if ($vote->hardware_concurrency == $currentCPU && $currentCPU > 0) {
                    $similarity += 15;
                }

                $suspiciousScore = max($suspiciousScore, $similarity);
            }

            // Si el puntaje de sospecha es alto (>70%), bloquear
            // Esto bloquea intentos obvios de fraude pero permite diferentes dispositivos
            if ($suspiciousScore > 70) {
                return back()->with('error', 'Se ha detectado un posible intento de voto duplicado. Si crees que esto es un error, por favor contacta al administrador.');
            }
        }

        try {
            DB::beginTransaction();

            foreach ($validated['answers'] as $questionId => $optionId) {
                Vote::create([
                    'survey_id' => $survey->id,
                    'question_id' => $questionId,
                    'question_option_id' => $optionId,
                    'ip_address' => $ipAddress,
                    'fingerprint' => $fingerprint,
                    'user_agent' => $deviceData['user_agent'] ?? null,
                    'platform' => $deviceData['platform'] ?? null,
                    'screen_resolution' => $deviceData['screen_resolution'] ?? null,
                    'hardware_concurrency' => $deviceData['hardware_concurrency'] ?? null,
                ]);
            }

            DB::commit();

            $response = redirect()->route('surveys.thanks', $survey->slug)
                ->with('success', '¡Gracias por tu participación!');

            // Establecer cookie con fingerprint
            return $response->cookie('survey_fingerprint', $fingerprint, 525600); // 1 año

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar tu voto: ' . $e->getMessage());
        }
    }

    public function thanks($slug)
    {
        $survey = Survey::where('slug', $slug)
            ->with(['questions.options' => function($query) {
                $query->withCount('votes');
            }])
            ->firstOrFail();

        // Calcular estadísticas generales
        $totalVotes = Vote::where('survey_id', $survey->id)
            ->distinct('fingerprint')
            ->count('fingerprint');

        // Si no hay votos por fingerprint, contar por IP
        if ($totalVotes == 0) {
            $totalVotes = Vote::where('survey_id', $survey->id)
                ->distinct('ip_address')
                ->count('ip_address');
        }

        // Preparar datos para los gráficos
        $statistics = [];
        foreach ($survey->questions as $question) {
            $questionStats = [
                'question' => $question->question_text,
                'type' => $question->question_type,
                'options' => [],
                'total_responses' => 0
            ];

            $totalQuestionVotes = $question->options->sum('votes_count');
            $questionStats['total_responses'] = $totalQuestionVotes;

            foreach ($question->options as $option) {
                $percentage = $totalQuestionVotes > 0
                    ? round(($option->votes_count / $totalQuestionVotes) * 100, 1)
                    : 0;

                $questionStats['options'][] = [
                    'text' => $option->option_text,
                    'votes' => $option->votes_count,
                    'percentage' => $percentage,
                    'color' => $option->color ?? null
                ];
            }

            $statistics[] = $questionStats;
        }

        return view('surveys.thanks', compact('survey', 'totalVotes', 'statistics'));
    }

    public function checkVote($slug)
    {
        $survey = Survey::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $fingerprint = request()->input('fingerprint');

        $hasVoted = false;
        if ($fingerprint) {
            $hasVoted = Vote::where('survey_id', $survey->id)
                ->where('fingerprint', $fingerprint)
                ->exists();
        }

        return response()->json(['has_voted' => $hasVoted]);
    }
}

