<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     */
    public function create(array $input): User
    {

        Validator::make(
            $input,
            [

                /*
                |--------------------------------------------------------------------------
                | Nome
                |--------------------------------------------------------------------------
                */
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:255',
                    'regex:/^[\pL]+(\s+[\pL]+)+$/u'
                ],

                /*
                |--------------------------------------------------------------------------
                | Email
                |--------------------------------------------------------------------------
                */
                'email' => [
                    'required',
                    'email:rfc,filter',
                    'max:255',
                    'unique:users,email'
                ],

                /*
                |--------------------------------------------------------------------------
                | Senha
                |--------------------------------------------------------------------------
                | Usa as regras padrão do Laravel Fortify
                */
                'password' => $this->passwordRules(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Mensagens personalizadas
            |--------------------------------------------------------------------------
            */
            [
                'name.required' => 'O campo nome é obrigatório.',
                'name.string' => 'O campo nome deve ser um texto.',
                'name.min' => 'O nome deve ter no mínimo :min caracteres.',
                'name.max' => 'O nome deve ter no máximo :max caracteres.',
                'name.regex' => 'Informe o nome completo (nome e sobrenome, apenas letras).',

                'email.required' => 'O campo email é obrigatório.',
                'email.email' => 'Informe um endereço de email válido.',
                'email.unique' => 'Este email já está cadastrado.',
                'email.max' => 'O email deve ter no máximo :max caracteres.',

                'password.required' => 'A senha é obrigatória.',
                'password.confirmed' => 'A confirmação da senha não confere.',
                'password.min' => 'A senha deve ter no mínimo :min caracteres.',
                'password.max' => 'A senha deve ter no máximo :max caracteres.',
                
            ]
        )->validate();


        /*
        |--------------------------------------------------------------------------
        | Criação do usuário
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Garantir que as roles existam
        |--------------------------------------------------------------------------
        */

        $roleAdmin = Role::query()->where('name', 'admin')->first();

        if (!$roleAdmin) {
            $roleAdmin = Role::create(['name' => 'admin']);
        }

        $roleVendedor = Role::query()->where('name', 'vendedor')->first();

        if (!$roleVendedor) {
            $roleVendedor = Role::create(['name' => 'vendedor']);
        }


        /*
        |--------------------------------------------------------------------------
        | Definir role padrão
        |--------------------------------------------------------------------------
        */

        $user->addRole('admin');

        return $user;
    }
}