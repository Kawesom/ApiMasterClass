<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketsRequest extends FormRequest
{
    /**
     * Maps any inputs that come with the request to the model parameters
     * @return array
     */
    public function mappedAttributes(array $otherAttributes = [])
    {
        //maps the incoming ticket request to the database column
        $attributeMap = array_merge([
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'users_id',
        ], $otherAttributes);

        $attributesToUpdate = [];

        foreach($attributeMap as $key => $attribute) {
            if($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }

    public function messages() {
        return [
            'data.attributes.status' => 'The value given was invalid, please use Active, Completed, Hold or Cancelled.',
        ];
    }
}
