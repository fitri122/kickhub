<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveGroupTeamsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_TECHNICAL_OFFICIAL;
    }

    public function rules(): array
    {
        return [
            'team_ids' => ['sometimes', 'array', 'max:4'],
            'team_ids.*' => ['required', 'integer', 'distinct', Rule::exists('pasukan', 'id')],
        ];
    }
}
