<?php

namespace App\Http\Requests\Endereco;

use Illuminate\Foundation\Http\FormRequest;

class CriarEnderecoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'cep' => preg_replace('/\D/', '', $this->cep),
            'rua' => $this->rua ? trim(preg_replace('/\s+/', ' ', $this->rua)) : null,
            'bairro' => $this->bairro ? trim(preg_replace('/\s+/', ' ', $this->bairro)) : null,
            'cidade' => $this->cidade ? trim(preg_replace('/\s+/', ' ', $this->cidade)) : null,
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
            //

            'cep' => ['required', 'string', 'size:8'],

            'rua' => ['required', 'string', 'max:255', 'regex:/^[\pL0-9\s\.\-\'º]+$/u'],

            'numero' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\s\-]+$/'],

            'complemento' => ['nullable', 'string', 'max:255'],

            'bairro' => ['required', 'string', 'max:255', 'regex:/^[\pL0-9\s\.\-\'º]+$/u'],

            'cidade' => ['required', 'string', 'max:255', 'regex:/^[\pL0-9\s\.\-\'º]+$/u'],

            'estado' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string'   => 'O campo :attribute deve ser uma string.',
            'max'      => 'O campo :attribute não pode ter mais que :max caracteres',
            'size'     => 'O campo :attribute deve ter exatamente :size caracteres.',
            'cep.size' => 'O CEP deve conter exatamente 8 números.',
            'rua.regex' => 'O campo rua contém caracteres inválidos.',
            'bairro.regex' => 'O campo bairro contém caracteres inválidos.',
            'cidade.regex' => 'O campo cidade contém caracteres inválidos.',
            'numero.required' => 'O campo número é obrigatório.',
            'numero.regex' => 'O campo número deve conter apenas números ou números e letras (ex: 123 ou 12A).',
            'estado.regex' => 'O campo estado deve conter apenas duas letras maiúsculas (ex: PR).',
        ];
    }

    public function attributes()
    {
        return [
            'cep' => 'CEP',
            'rua' => 'rua',
            'numero' => 'número',
            'complemento' => 'complemento',
            'bairro' => 'bairro',
            'cidade' => 'cidade',
            'estado' => 'estado',
        ];
    }
}
