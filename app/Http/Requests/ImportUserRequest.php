<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ImportUserRequest extends FormRequest
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
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'unique:users,username'],
            'sponsor' => ['nullable', 'exists:users,uuid'],
            'package' => ['required', 'exists:services,uuid'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'], // Ensure end_date is after start_date
            'country' => ['required', 'exists:countries,iso2'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $startDate = $this->input('start_date');
            $endDate = $this->input('end_date');

            // Check if end_date is greater than start_date
            if (strtotime($endDate) <= strtotime($startDate)) {
                $validator->errors()->add('end_date', 'The end date must be greater than the start date.');
            }
        });
    }
}
