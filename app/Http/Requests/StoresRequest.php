<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Traits\GeneralTrait;

class StoresRequest extends FormRequest
{

    use GeneralTrait;
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
            $rules = [
                'brands.*.store_id' => ['required','numeric','unique:stores,id'],
                'stores.*.store_name' => ['required','string'],
                'stores.*.store_image' =>['file','mimes:jpg,jpeg,png','max:3000'],
            ];

        switch ($this->method()) {
            case 'POST': {
                    return $rules;
                }
            case 'PUT':
            case 'PATCH': {
                    return $rules;
                }
            default:
                break;
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnError($validator->errors()));
    }
}
