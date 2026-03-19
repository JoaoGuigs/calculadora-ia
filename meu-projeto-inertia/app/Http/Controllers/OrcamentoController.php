<?php

namespace App\Http\Controllers;

use Gemini;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;

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
            $prompt = "Você é um especialista em estimativa de tempo para projetos de desenvolvimento de software e design freela. 
            Analise a seguinte descrição de projeto fornecida pelo usuário e sugira o número total de horas estimadas para conclusão. 
            
            REGRAS DE RESPOSTA:
            1. Retorne APENAS um número inteiro ou um intervalo simples (ex: '20' ou '15-25'). 
            2. Não escreva textos, justificativas ou a palavra 'horas'.
            3. Se a descrição for muito vaga, retorne 'vago'         DESCRIÇÃO: {$request->descricao}";

            try {
                // usa modelo estável e amplamente disponível
                $result = $client->generativeModel('models/gemini-2.5-flash')
                    ->generateContent($prompt);
                  
                $respostaTexto = $result->text();
                $sugestaoIa = trim($respostaTexto);
            } catch (\Exception $e) {
                
                \Log::error('Erro ao chamar Gemini: ' . $e->getMessage());
                $sugestaoIa = 'Erro ao usar IA';
            }
        }
        return Inertia::render('Calculadora', [
            'total' => $total,
            'historico' => $historico,
            'sugestaoIa' => $sugestaoIa,
        ]);
    }
}
