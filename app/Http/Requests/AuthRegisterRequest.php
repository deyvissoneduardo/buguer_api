<?php

namespace App\Http\Requests;

use App\Rules\CPFValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AuthRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $emailValidation = ['required', 'string', 'email', 'max:255', 'unique:users,email'];

        if (Auth::check()) {
            $emailValidation[4] .= ',' . Auth::user()->id;
        }
        return [
            'name' => 'required',
            'cpf' => ['required', new CPFValidator()],
            'email' => $emailValidation,
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}
