<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Traits\GeneralTrait;

class PharmicesRequest extends FormRequest
{

    use GeneralTrait;
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'Pharmacies.*.num_id' => ['required','numeric'],
            'Pharmacies.*.f_name' => ['string'],
            'Pharmacies.*.l_name' => ['string'],
            'Pharmacies.*.phone' => ['required', 'numeric'],
            'Pharmacies.*.name' => ['required', 'string'],
            'Pharmacies.*.land_number' => ['required', 'numeric'],
            'Pharmacies.*.address' => ['required', 'string'],
            'Pharmacies.*.card_number' => ['required', 'string'],
            'Pharmacies.*.city' => ['required', 'string'],
            'Pharmacies.*.region' => ['required', 'string'],
            'Pharmacies.*.group' => ['required', 'string'],
            'Pharmacies.*.is_active' => ['required', 'string'],

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
