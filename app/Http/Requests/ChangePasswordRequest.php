<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'password' => 'required|min:8|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required|min:8'
        ];
    }
}

/*
 'password' => 'min:6|required_with:confirm_password|same:confirm_password',
 'confirm_password' => 'min:6'
 VAGY
 'password' => 'required|min:6|confirmed',
 'confirm_password' => 'required|min:6'
 */