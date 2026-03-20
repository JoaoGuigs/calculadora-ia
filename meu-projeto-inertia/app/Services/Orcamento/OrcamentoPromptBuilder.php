<?php

namespace App\Services\Orcamento;

class OrcamentoPromptBuilder
{
    public function buildInitial(string $descricao): string
    {
        return "Voce é um  gerente de projetos tech. o Espoco original é '{$descricao}'. 
Retorne um JSON com a seguinte estrutura:
{
  \"total_sugerido\": (int),
  \"complexidade\": \"baixa|media|alta\",
  \"tarefas\": [
    { \"item\": \"Nome da tarefa\", \"horas\": (int), \"descricao\": \"breve explicação\" }
  ],
  \"riscos\":[
        {\"alerta\": \"Titulo do risco\", \"impacto\": \"impacto do risco\", \"probabilidade\": \"probabilidade do risco\"}
  ]
}
Estime as horas de forma realista para um desenvolvedor sênior.";
    }

    public function buildRefine(string $descricao, string $ajuste, array $jsonAnterior): string
    {
        $promptBase = "Voce é um  gerente de projetos tech. o Espoco original é '{$descricao}'.";
        $jsonAnteriorStr = !empty($jsonAnterior) ? json_encode($jsonAnterior) : '{}';

        return "{$promptBase}

Anteriormente, você sugeriu este JSON: {$jsonAnteriorStr}.

O usuário solicitou o seguinte ajuste: '{$ajuste}'.

REGRAS:
1. Mantenha a mesma estrutura JSON.
2. Atualize as horas e descrições das tarefas com base no ajuste solicitado.
3. Retorne APENAS o novo JSON atualizado.";
    }
}

