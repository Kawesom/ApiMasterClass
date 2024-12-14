<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends BaseUserRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'data.attributes.name' => ['required','string'],
            'data.attributes.email' => ['required','email','unique:users,email'],
            'data.attributes.isManager' => ['required','boolean'],
            'data.attributes.password' => ['required','string'],
        ];

        return $rules;
    }
}
