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

        // Verificar si ya votó
        $ipAddress = request()->ip();
        $fingerprint = request()->cookie('survey_fingerprint');

        $hasVoted = Vote::where('survey_id', $survey->id)
            ->where(function ($query) use ($ipAddress, $fingerprint) {
                $query->where('ip_address', $ipAddress);
                if ($fingerprint) {
                    $query->orWhere('fingerprint', $fingerprint);
                }
            })
            ->exists();

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

        // Verificar nuevamente si ya votó (por si pasó el middleware)
        $hasVoted = Vote::where('survey_id', $survey->id)
            ->where(function ($query) use ($ipAddress, $fingerprint) {
                $query->where('ip_address', $ipAddress)
                    ->orWhere('fingerprint', $fingerprint);
            })
            ->exists();

        if ($hasVoted) {
            return back()->with('error', 'Ya has votado en esta encuesta.');
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
        $survey = Survey::where('slug', $slug)->firstOrFail();
        return view('surveys.thanks', compact('survey'));
    }

    public function checkVote($slug)
    {
        $survey = Survey::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $ipAddress = request()->ip();
        $fingerprint = request()->input('fingerprint');

        $hasVoted = Vote::where('survey_id', $survey->id)
            ->where(function ($query) use ($ipAddress, $fingerprint) {
                $query->where('ip_address', $ipAddress);
                if ($fingerprint) {
                    $query->orWhere('fingerprint', $fingerprint);
                }
            })
            ->exists();

        return response()->json(['has_voted' => $hasVoted]);
    }
}
