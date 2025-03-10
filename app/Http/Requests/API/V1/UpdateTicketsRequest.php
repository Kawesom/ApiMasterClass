<?php

namespace App\Http\Requests\API\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTicketsRequest extends BaseTicketsRequest
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
            'data.attributes.title' => ['sometimes','string'],
            'data.attributes.description' => ['sometimes','string'],
            'data.attributes.status' => ['sometimes','string','in:Completed,Cancelled,Hold,Active'],
            'data.relationships.author.data.id' => ['prohibited'],
        ];
       // Auth::loginUsingId(Auth::id());
        //forces them to only submit requests that have title, description & status
        if (Auth::user()->tokenCan(Abilities::UpdateTicket)) {
            $rules['data.relationships.author.data.id'] = ['sometimes','integer'];
        }
        return $rules;
    }
}
