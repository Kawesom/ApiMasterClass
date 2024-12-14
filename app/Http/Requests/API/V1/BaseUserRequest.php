<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseUserRequest extends FormRequest
{
    /**
     * Maps any inputs that come with the request to the model parameters
     * @return void
     */
    public function mappedAttributes(array $otherAttributes = [])
    {
        //maps the incoming ticket request to the database column
        $attributeMap = array_merge([
            'data.attributes.name' => 'name',
            'data.attributes.email' => 'email',
            'data.attributes.password' => 'password',
            'data.attributes.isManager' => 'is_manager',
            //'data.attributes.createdAt' => 'created_at',
            //'data.attributes.updatedAt' => 'updated_at',
        ], $otherAttributes);

        $attributesToUpdate = [];

        foreach($attributeMap as $key => $attribute) {
            if($this->has($key)) {
                $value = $this->input($key);

                if($attribute === 'password') {
                    $value = bcrypt($value);
                }

                $attributesToUpdate[$attribute] = $value;
            }
        }

        return $attributesToUpdate;
    }
}
