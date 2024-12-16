<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class ReplaceTicketsRequest extends BaseTicketsRequest
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
    {      //the dots('.') in the keys are for accessing elements in the data json response array
        $rules = [
            'data' => ['required','array'],
            'data.attributes' => ['required','array'],
            'data.attributes.title' => ['required','string'],
            'data.attributes.description' => ['required','string'],
            'data.attributes.status' => ['required','string','in:Completed,Cancelled,Hold,Active'],
            'data.relationships' => 'required|array',
            'data.relationships.author' => 'required|array',
            'data.relationships.author.data' => 'required|array',
            'data.relationships.author.data.id' => ['required','integer'],
        ];

        return $rules;
    }

}
