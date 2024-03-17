<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'     => [
                'required',
            ],
            'email'    => [
                'required',
            ],
            'password' => [
                'required',
            ],
            'type'  => [
                'required',
            ],
            'document'    => [
                'required'
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
}
