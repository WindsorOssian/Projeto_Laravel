<?php

namespace App\Http\Requests\Vendedor;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Endereco\CriarEnderecoRequest;
use Illuminate\Validation\Rule;

class EditarVendedorRequest extends FormRequest
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
            'id_vendedor' => ['required', 'integer', 'exists:tb_vendedor,id_vendedor'],
            'nome' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL]+(\s+[\pL]+)+$/u'],
            'email' => ['required', 'email:rfc,filter', 'max:255', 'unique:users,email,' . $this->route('idVendedor') . ',id'],
            'cpf' => [
                'required',
                'cpf',
                Rule::unique(\App\Models\VendedorModel::class, 'cpf')->ignore($this->id_vendedor, 'id_vendedor')->where(function ($query) {
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
            'integer' => ':attribute deve ser um número inteiro',
            'exists' => ':attribute não existe',
            'string' => 'O campo :attribute deve ser uma string',
            'email.email' => 'Informe um endereço de email válido.',
            'email.rfc' => 'Informe um endereço de email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres',
            'comissao.required' => 'O campo comissão é obrigatório',
            'numeric' => 'O campo :attribute deve ser um número',
            'array' => ':attribute deve ser um array',
            'cpf.cpf' => 'O CPF informado não é válido.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'nome.regex' => 'Informe o nome completo (nome e sobrenome, apenas letras).',
        ];

        $endereco = (new CriarEnderecoRequest())->messages();

        return array_merge($vendedor, $endereco);
    }
}
