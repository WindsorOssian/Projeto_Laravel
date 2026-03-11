<?php

namespace App\Http\Requests\Produto;

use Illuminate\Foundation\Http\FormRequest;

class CriarProdutoRequest extends FormRequest
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
        if ($this->preco) {
            $this->merge([
                'preco' => str_replace(',', '.', $this->preco),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Verificamos na tela os nomes
            'nome' => ['required', 'string', 'max:255', 'min:2'],
            'preco' => [
            'required',
            'numeric',
            'min:0',
            'gt:0',
            'max:99999999.99',
            'regex:/^\d+(\.\d{1,2})?$/'
            ],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'preco.required' => 'O campo preço é obrigatório.',
            'preco.numeric'  => 'O preço deve ser um número válido.',
            'preco.min'      => 'O preço não pode ser negativo.',
            'preco.max'      => 'O preço não pode ser maior que 99999999.99',
            'preco.regex' => 'O preço deve ter no máximo 2 casas decimais.',
            'preco.gt' => 'O preço deve ser maior que zero.',
            'string'   => 'O campo :attribute deve ser uma string.',
            'max'      => 'O campo :attribute não pode exceder :max caracteres.',
            'min'      => 'O campo :attribute deve ser maior ou igual a :min.',
            'nome.min' => 'O nome do produto deve ter no mínimo :min caracteres.',
        ];
    }
}
