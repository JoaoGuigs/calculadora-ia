<?php

namespace App\DTO;

final class OrcamentoResultado
{
    /**
     * @param int|null $total_sugerido
     * @param array $tarefas Array de tarefas (cada tarefa pode conter item/nome, horas e descricao)
     * @param array $riscos Array de riscos (cada risco pode conter alerta, impacto e probabilidade)
     */
    public function __construct(
        public readonly ?int $total_sugerido,
        public readonly array $tarefas,
        public readonly array $riscos,
    ) {}

    public function toArray(): array
    {
        return [
            'total_sugerido' => $this->total_sugerido,
            'tarefas' => $this->tarefas,
            'riscos' => $this->riscos,
        ];
    }
}

