<?php

namespace App\Http\Requests\API\V1;

use App\Models\User;
use App\Permissions\V1\Abilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        $isTicketsController = $this->routeIs('tickets.store');
        $authorIdAttr = $isTicketsController ? 'data.relationships.author.data.id' : 'author';
        $user = Auth::user();
        $authorRule = 'required|integer|exists:users,id';

        $rules = [
            'data' => ['required','array'],
            'data.attributes' => ['required','array'],
            'data.attributes.title' => ['required','string'],
            'data.attributes.description' => ['required','string'],
            'data.attributes.status' => ['required','string','in:Completed,Cancelled,Hold,Active'],
        ];

        if ($isTicketsController) {
            $rules['data.relationships'] = 'required|array';
            $rules['data.relationships.author'] = 'required|array';
            $rules['data.relationships.author.data'] = 'required|array';
        }

        $rules[$authorIdAttr] = $authorRule . '|size:' . $user->id;

        if ($user->tokenCan(Abilities::CreateTicket)) {
            $rules[$authorIdAttr] = $authorRule;
        }

        return $rules;
    }

    protected function prepareForValidation() {
        if ($this->routeIs('authors.tickets.store')) {
            $this->merge([
                'author' => $this->route('author')
            ]);
        }
    }

    public function bodyParameters() {
        $documentation = [
            'data.attributes.title' => [
                'description' => "The ticket's title (method)",
                'example' => 'No-example' //doesn't work
            ],
            'data.attributes.description' => [
                'description' => "The ticket's description",
                'example' => 'No-example',
            ],
            'data.attributes.status' => [
                'description' => "The ticket's status",
                'example' => 'No-example',
            ],
        ];

        if ($this->routeIs('tickets.store')) {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => 'No-example'
            ];
        } else {
            $documentation['author'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => 'No-example'
            ];
        }

        return $documentation;

    }

}
