<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'country'       => ['nullable', 'string', 'exists:countries,iso2'],
            'state'         => ['nullable', 'string'],
            'city'          => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'string'],
            'zip_code'      => ['nullable', 'numeric'],
        ];
    }
}
