<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $roleId = request()->route('role')?->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($roleId)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($roleId)],
            'description' => ['nullable', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }
}
