<?php

namespace App\Http\Controllers;

use Gemini;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;

class OrcamentoController extends Controller
{
    public function index(Request $request)
    {
        // Recupera histórico atual da sessão (ou array vazio)
        $historico = $request->session()->get('historico', []);

        // Primeira visita (sem parâmetros): só mostra total 0 e histórico existente
        if (! $request->has('valor_hora')) {
            return Inertia::render('Calculadora', [
                'total' => 0,
                'historico' => $historico,
            ]);
        }



        $request->validate([
            'valor_hora' => 'required|numeric|min:0',
            'horas' => 'required|numeric|min:0',
        ], [
            'required' => 'O Campo :attribute é obrigatório',
            'numeric' => 'O Campo :attribute deve ser um número',
            'min' => 'O Campo :attribute deve ser maior que 0',
        ]);
        if ($request->valor_hora && $request->horas) {
            $total = ($request->valor_hora * $request->horas) * 0.94;
        }
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

        if ($request->filled('descricao')) {
            $apiKey = config('gemini.api_key');


            // Cliente HTTP Guzzle com verificação de SSL desativada 
            $httpClient = new GuzzleClient([
                'verify' => false,
            ]);

            $client = \Gemini::factory()
                ->withApiKey($apiKey)
                ->withHttpClient($httpClient)
                ->make();
            $prompt = "Você é um Gerente de Projetos Tech. Analise o escopo: '{$request->descricao}'.
    Retorne um JSON com a seguinte estrutura:
    {
      \"total_sugerido\": (int),
      \"complexidade\": \"baixa|media|alta\",
      \"tarefas\": [
        { \"item\": \"Nome da tarefa\", \"horas\": (int), \"descricao\": \"breve explicação\" }
      ]
    }
    Estime as horas de forma realista para um desenvolvedor sênior.";
            try {
                // usa modelo estável e amplamente disponível
                $modelo = $client->generativeModel('models/gemini-2.5-flash-lite') // Recomendo 1.5 ou 2.0 para estabilidade
                    ->withGenerationConfig(new GenerationConfig(
                        responseMimeType: ResponseMimeType::APPLICATION_JSON
                    ));
                
                $result = $modelo->generateContent($prompt);
                $dadosIA = $result->text();
                $decoded = json_decode($dadosIA, true);

                if (is_array($decoded)) {
                    $totalSugerido = $decoded['total_sugerido'] ?? null;
                    $tarefas = $decoded['tarefas'] ?? [];

                    if (!is_array($tarefas)) {
                        $tarefas = [];
                    }

                    $sugestaoIa = $totalSugerido;
                    $dadosArray = [
                        'total_sugerido' => $totalSugerido,
                        'tarefas' => $tarefas,
                    ];
                }
            } catch (\Exception $e) {

                Log::error('Erro ao chamar Gemini: ' . $e->getMessage());
                $sugestaoIa = 'Erro ao usar IA';
                $dadosArray = [];
            }
        }
        return Inertia::render('Calculadora', [
            'total' => $total,
            'historico' => $historico,
            'dadosIA' => $dadosArray,
            'sugestaoIa' => $sugestaoIa,
        ]);
    }
}
