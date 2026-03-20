<?php

namespace App\Services\Orcamento;

use App\DTO\OrcamentoResultado;

class OrcamentoJsonParser
{
    /**
     * @return OrcamentoResultado
     */
    public function parse(string $text): OrcamentoResultado
    {
        $decoded = json_decode($text, true);

        // Algumas respostas podem vir com texto extra antes/depois do JSON.
        if (!is_array($decoded)) {
            $start = strpos($text, '{');
            $end = strrpos($text, '}');

            if ($start !== false && $end !== false && $end > $start) {
                $candidate = substr($text, $start, $end - $start + 1);
                $decoded = json_decode($candidate, true);
            }
        }

        if (!is_array($decoded)) {
            return new OrcamentoResultado(null, [], []);
        }

        $tarefasRaw = $decoded['tarefas'] ?? [];
        $tarefas = is_array($tarefasRaw) ? $tarefasRaw : [];
        dd($tarefas);
        $tarefasNorm = [];
        foreach ($tarefas as $t) {
            if (!is_array($t)) {
                continue;
            }

            $horas = $t['horas'] ?? 0;
            $horas = is_numeric($horas) ? (int) $horas : 0;

            $tarefasNorm[] = [
                // Mantemos os nomes originais e garantimos que o front consiga renderizar
                'item' => $t['item'] ?? ($t['nome'] ?? ''),
                'nome' => $t['nome'] ?? null,
                'horas' => $horas,
                'descricao' => $t['descricao'] ?? '',
            ];
        }

        $riscosRaw = $decoded['riscos'] ?? [];
        $riscos = is_array($riscosRaw) ? $riscosRaw : [];
        $riscosNorm = [];
        foreach ($riscos as $r) {
            if (!is_array($r)) {
                continue;
            }
            $riscosNorm[] = [
                'alerta' => $r['alerta'] ?? '',
                'impacto' => $r['impacto'] ?? '',
                'probabilidade' => $r['probabilidade'] ?? null,
            ];
        }

        $totalSugerido = $decoded['total_sugerido'] ?? null;
        $totalSugerido = is_numeric($totalSugerido) ? (int) $totalSugerido : null;

        // Fallback quando o modelo não retorna total_sugerido.
        if ($totalSugerido === null && !empty($tarefasNorm)) {
            $totalSugerido = array_sum(array_column($tarefasNorm, 'horas'));
        }
        dd($tarefasNorm);
        return new OrcamentoResultado($totalSugerido, $tarefasNorm, $riscosNorm);
    }
}

