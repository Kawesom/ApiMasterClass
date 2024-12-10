<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketsRequest extends BaseTicketsRequest
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
            'data.attributes.title' => ['required','string'],
            'data.attributes.description' => ['required','string'],
            'data.attributes.status' => ['required','string','in:Completed,Cancelled,Hold,Active'],
        ];

        if($this->routeIs('tickets.store')) {
            $rules['data.relationships.author.data.id'] = ['required','integer'];
        }

        return $rules;
    }

}
