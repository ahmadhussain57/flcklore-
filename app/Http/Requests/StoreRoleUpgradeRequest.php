<?php

namespace App\Http\Requests;

use App\Models\RoleUpgradeRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleUpgradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $section = $this->input('section');

        return [
            'section' => ['required', Rule::in(['content', 'marketing'])],
            'requested_role' => [
                'required',
                'string',
                Rule::in(RoleUpgradeRequest::allowedRolesFor((string) $section)),
            ],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'section.required' => 'يجب اختيار القسم.',
            'section.in' => 'القسم غير صالح.',
            'requested_role.required' => 'يجب اختيار الدور المطلوب.',
            'requested_role.in' => 'الدور المطلوب غير مسموح لهذا القسم.',
        ];
    }
}