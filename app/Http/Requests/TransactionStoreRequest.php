<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransactionStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'payee'     => [
                'required',
            ],
            'payer'    => [
                'required',
            ],
            'value' => [
                'required',
            ]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
