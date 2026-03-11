<?php

namespace App\Http\Requests\Venda;

use Illuminate\Foundation\Http\FormRequest;

class CriarVendaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $itens = collect($this->itens ?? [])->map(function ($item) {
            $item['valor'] = str_replace(',', '.', $item['valor']);
            return $item;
        });

        $this->merge([
            'itens' => $itens->toArray(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'data'               => ['required', 'date'],
            'data_venda' => ['required', 'date', 'before_or_equal:today'],
            'id_cliente'         => ['required', 'exists:tb_cliente,id'],
            // 'id_vendedor'        => ['required', 'exists:users,id'],
            'itens'              => ['required', 'array', 'min:1', 'max:100'],
            'itens.*.id_produto' => ['required', 'exists:tb_produto,id'],
            'itens.*.quantidade' => [
                'required',
                'integer',
                'min:1',
                'max:99',
            ],
            'itens.*.valor' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
        ];
    }

    public function messages()
    {
        return [
            'data_venda.required' => 'Informe a data da venda.',
            'data_venda.date' => 'A data da venda deve ser válida.',
            'data_venda.before_or_equal' => 'A data da venda não pode ser futura.',

            'id_cliente.required' => 'Selecione um cliente.',
            'id_cliente.exists' => 'O cliente selecionado é inválido.',

            'itens.required' => 'Adicione pelo menos um item à venda.',
            'itens.min' => 'Adicione pelo menos um item à venda.',

            'itens.*.id_produto.required' => 'Selecione um produto.',
            'itens.*.id_produto.exists' => 'O produto selecionado é inválido.',

            'itens.*.quantidade.required' => 'Informe a quantidade.',
            'itens.*.quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'itens.*.quantidade.min' => 'A quantidade deve ser maior que zero.',

            'itens.*.valor.required' => 'Informe o valor do produto.',
            'itens.*.valor.numeric' => 'O valor deve ser numérico.',

            'itens.*.valor.max' => 'O valor do produto não pode ser maior que 99999999.99.',
            'itens.*.quantidade.max' => 'A quantidade é muito grande.',
            'itens.*.valor.min' => 'O valor não pode ser negativo.',
        ];
    }
}
