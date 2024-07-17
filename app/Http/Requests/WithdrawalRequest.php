<?php


namespace App\Http\Requests;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class WithdrawalRequest extends FormRequest
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
        return [
            'amount' => ['required', 'numeric'],
            'payment_method' => ['required', 'string'],
            'wallet_address' => ['nullable', 'required_if:payment_method,crypto'],
            'token' => ['required', 'numeric', 'digits:4'],
            'provider_id' => ['required', 'string'],
        ];
    }

    /**
     * Handle the after validation logic.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $providerId = $this->input('provider_id');

        // get provider information
        $provider = \App\Models\Provider::whereUuid($providerId)->first();

        if (!$provider) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => serviceDownMessage(),
                'errors' => [],
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        if ($provider->short_name == 'nowpayment') {

            $validated = $this->validated();

            $validated['currency'] = 'usdttrc20';

            // Call the validateAddress method and pass the validated data and currency
            $nowpaymentService = new \App\Services\NowpaymentsService();

            $validateAddress = [
                'address'     => $validated['wallet_address'],
                'currency'    => $validated['currency']
            ];

            $verifyAddress = $nowpaymentService->validateAddress($validateAddress);

            if ($verifyAddress['code'] == 'BAD_ADDRESS_VALIDATION_REQUEST') {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => $verifyAddress['message'],
                    'errors' => [],
                ], Response::HTTP_UNPROCESSABLE_ENTITY));
            }
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}