<?php

namespace App\Http\Requests\Settings;

use App\Concerns\PasswordValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Mensagens para a senha atual
            'current_password.required' => 'O campo senha atual é obrigatório.',
            'current_password.current_password' => 'A senha atual informada está incorreta.',

            // Mensagens para a nova senha (que você viu no print)
            'password.required' => 'O campo nova senha é obrigatório.',
            'password.confirmed' => 'A confirmação da nova senha não coincide.',
            'password.min' => 'A nova senha deve ter pelo menos :min caracteres.',
            'password.string' => 'A senha deve ser um texto válido.',
        ];
    }
}
