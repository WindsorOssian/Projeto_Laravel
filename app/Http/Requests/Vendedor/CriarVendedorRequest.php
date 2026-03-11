<?php

namespace App\Http\Requests\Vendedor;

use App\Http\Requests\Endereco\CriarEnderecoRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CriarVendedorRequest extends FormRequest
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
            'cpf' => preg_replace('/\D/', '', $this->cpf),
            'cep' => preg_replace('/\D/', '', $this->cep),
            'nome' => $this->nome
                ? trim(preg_replace('/\s+/', ' ', $this->nome))
                : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vendedor = [
            // Verificamos na tela os nomes
            'nome' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL]+(\s+[\pL]+)+$/u'],
            'email' => ['required', 'email:rfc,filter', 'unique:users,email', 'max:255'],
            'cpf' => [
                'required',
                'cpf',
                Rule::unique(\App\Models\VendedorModel::class, 'cpf')->where(function ($query) {
                    return $query->where('removido', false);
                }),
            ],
            'observacoes' => ['nullable', 'string'],
            'comissao' => ['required', 'numeric'],

        ];

        $endereco = (new CriarEnderecoRequest())->rules();

        return array_merge($vendedor, $endereco);
    }

    public function messages()
    {
        $vendedor = [
            'required' => 'O campo :attribute é obrigatório',
            'comissao.required' => 'O campo comissão é obrigatório',
            'string' => 'O campo :attribute deve ser uma string',
            'email.email' => 'Informe um endereço de email válido.',
            'email.rfc' => 'Informe um endereço de email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres',
            'numeric' => 'O campo :attribute deve ser um número',
            'cpf.cpf' => 'O CPF informado não é válido.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'nome.regex' => 'Informe o nome completo (nome e sobrenome, apenas letras).',

        ];

        $endereco = (new CriarEnderecoRequest())->messages();

        return array_merge($vendedor, $endereco);
    }
}
