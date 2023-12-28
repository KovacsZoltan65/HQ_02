<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubdomainRequest extends FormRequest
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
            'subdomain' => 'required|string', 
            'url' => 'required|url:http,https', 
            'name' => 'required|string',
            'db_host' => 'required', 
            'db_port' => 'required', 
            'db_name' => 'required|unique:subdomains,db_name,' . $this->db_name, 
            'db_user' => 'required|unique:subdomains,db_user' . $this->db_user, 
            'db_password' => 'required',
            //'notification' => '', 
            //'state_id' => '', 
            //'is_mirror' => '', 
            //'sso' => '',
            //'access_control_system' => '', 
            //'last_export' => ''
        ];
    }
}
