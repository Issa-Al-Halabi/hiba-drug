<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Traits\GeneralTrait;

class BrandsRequest extends FormRequest
{

    use GeneralTrait;
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
            $rules = [
                'brands.*.brand_id' => ['required','numeric','unique:brands,id'],
                'brands.*.brand_name' => ['required','string'],
                'brands.*.brand_image' => ['file','mimes:jpg,jpeg,png','max:3000'],

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
