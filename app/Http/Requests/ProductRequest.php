<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Traits\GeneralTrait;

class ProductRequest extends FormRequest
{

    use GeneralTrait;
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
            $rules = [
                'products.*.brand_id' => ['numeric','required'],
                'products.*.store_id' => ['numeric','required'],
                'products.*.name' => ['required','string'],
                'products.*.unit_price' => ['required', 'numeric'],
                'products.*.purchase_price' => ['required', 'numeric'],
                'products.*.quantity' => ['numeric'],
                'products.*.notes' => ['string'],
                'products.*.Scientific_formula' => ['string'],
                'products.*.normal_offer' => ['numeric'],
                'products.*.q_normal_offer' => ['numeric'],
                'products.*.featured_offer' => ['numeric'],
                'products.*.q_featured_offer' => ['numeric'],
                'products.*.expiry_date' => ['date'],
                'products.*.demand_limit' => ['numeric'],
                'products.*.num_id' => ['required','numeric'],

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
