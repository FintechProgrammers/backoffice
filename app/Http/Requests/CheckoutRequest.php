<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class CheckoutRequest extends FormRequest
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
        $rules =  [
            'package_id'  => ['required', 'exists:services,uuid'],
            'payment_provider'  => ['required']
        ];

        if ($this->filled('referral_id')) {
            $rules['first_name'] = ['required', 'string'];
            $rules['last_name'] = ['required', 'string'];
            $rules['email'] = ['required', 'unique:users,email'];
            $rules['username'] = ['required', 'unique:users,username'];
            $rules['referral_id'] = ['required', 'exists:users,uuid'];
        } else {
            $rules['referral_id'] = ['nullable', 'exists:users,uuid'];
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
