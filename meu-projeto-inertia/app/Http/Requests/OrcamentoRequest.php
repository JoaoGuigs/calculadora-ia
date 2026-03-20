<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrcamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor_hora' => 'sometimes|required|numeric|min:0',
            'horas' => 'sometimes|required|numeric|min:0',
            'descricao' => 'sometimes|nullable|string',
            'ajuste' => 'sometimes|nullable|string',
        ];
    }
}

