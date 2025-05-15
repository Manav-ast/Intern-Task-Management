<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminRequest extends FormRequest
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
        $admin = request()->route('admin');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ];

        if (request()->isMethod('post')) {
            // Create rules
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('admins')];
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        } else {
            // Update rules
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', Rule::unique('admins')->ignore($admin->id)];
            $rules['password'] = ['nullable', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        if (request()->filled('roles')) {
            $superAdminRole = Role::where('slug', 'super-admin')->first()?->id;
            if ($superAdminRole && in_array($superAdminRole, request()->input('roles'))) {
                abort(422, 'Cannot assign super admin role to other users.');
            }
        }
    }
}
