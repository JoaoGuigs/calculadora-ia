<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Services\Orcamento\OrcamentoService;
use App\Services\Orcamento\OrcamentoPromptBuilder;
use App\Services\Orcamento\OrcamentoJsonParser;
use App\Http\Requests\OrcamentoRequest;

class OrcamentoController extends Controller
{
    public function index(OrcamentoRequest $request)
    {
        // Recupera histórico atual da sessão (ou array vazio)
        $historico = $request->session()->get('historico', []);

        // Primeira visita (sem parâmetros): só mostra total 0 e histórico existente
        if (! $request->has('valor_hora')) {
            return Inertia::render('Calculadora', [
                'total' => 0,
                'historico' => $historico,
                'dadosIA' => [],
                'sugestaoIa' => null,
            ]);
        }



        $total = ($request->valor_hora * $request->horas) * 0.94;
        // Já validado que os campos existem

        $novoCalculo = [
            'valor' => $total,
            'data' => now()->format('d/m/Y'),
            'hora' => now()->format('H:i:s'),
        ];

        $historico[] = $novoCalculo;
        $request->session()->put('historico', array_values($historico));


        $sugestaoIa = null;
        $dadosArray = [];
        $riscos = [];

        if ($request->filled('descricao')) {
            try {
                $contextoAnterior = $request->session()->get('dadosIA', []);

                $service = new OrcamentoService(
                    new \App\Services\Gemini\GeminiClient(),
                    new OrcamentoPromptBuilder(),
                    new OrcamentoJsonParser(),
                );

                $resultado = $service->gerarOuRefinar(
                    descricao: $request->descricao,
                    ajuste: $request->filled('ajuste') ? $request->ajuste : null,
                    jsonAnterior: $contextoAnterior,
                );

                $riscos = $resultado->riscos;
                $tarefas = $resultado->tarefas;
                $totalSugerido = $resultado->total_sugerido;

                $request->session()->put('dadosIA', $resultado->toArray());

                $sugestaoIa = $totalSugerido;
                $dadosArray = [
                    'total_sugerido' => $totalSugerido,
                    'tarefas' => $tarefas,
                ];
            } catch (\Exception $e) {

                Log::error('Erro ao chamar Gemini: ' . $e->getMessage());
                $sugestaoIa = 'Erro ao usar IA';
                $dadosArray = [];
                $riscos = [];
            }
        }

        return Inertia::render('Calculadora', [
            'total' => $total,
            'historico' => $historico,
            'dadosIA' => $dadosArray,
            'sugestaoIa' => $sugestaoIa,
            'riscos' => $riscos,
        ]);
    }
}
