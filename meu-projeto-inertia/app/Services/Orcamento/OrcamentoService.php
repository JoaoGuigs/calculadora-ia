<?php

namespace App\Services\Orcamento;

use App\DTO\OrcamentoResultado;
use App\Services\Gemini\GeminiClient;

class OrcamentoService
{
    public function __construct(
        private readonly GeminiClient $geminiClient,
        private readonly OrcamentoPromptBuilder $promptBuilder,
        private readonly OrcamentoJsonParser $parser,
    ) {}

    public function gerar(string $descricao): OrcamentoResultado
    {
        return $this->gerarOuRefinar($descricao, null, []);
    }

    public function gerarOuRefinar(
        string $descricao,
        ?string $ajuste,
        array $jsonAnterior,
    ): OrcamentoResultado {
        $prompt = $ajuste !== null && $ajuste !== ''
            ? $this->promptBuilder->buildRefine($descricao, $ajuste, $jsonAnterior)
            : $this->promptBuilder->buildInitial($descricao);

        $modelo = 'models/gemini-2.5-flash';
        $texto = $this->geminiClient->generate($prompt, $modelo, true);

        return $this->parser->parse($texto);
    }
}

