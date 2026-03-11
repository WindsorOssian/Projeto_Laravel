<?php

namespace App\Http\Requests\Venda;

use Illuminate\Foundation\Http\FormRequest;

class EditarVendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'                 => ['required', 'integer', 'exists:tb_venda,id'],
            'data_venda'         => ['required', 'date'],
            'id_cliente'         => ['required', 'exists:tb_cliente,id'],
            // 'id_vendedor' => ['required', 'exists:users,id'],
            'itens'              => ['required', 'array', 'min:1'],
            // 'itens.*.id' => ['nullable', 'exists:tb_item,id'],
            'itens.*.id_produto' => ['required', 'exists:tb_produto,id'],
            'itens.*.valor'      => ['required', 'numeric', 'min:0'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'exists' => 'O :attribute selecionado é inválido.',
            'array' => 'O campo :attribute deve ser um array.',
            'min' => 'O campo :attribute deve ter no mínimo :min.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'numeric' => 'O campo :attribute deve ser um número válido.',
        ];
    }
}