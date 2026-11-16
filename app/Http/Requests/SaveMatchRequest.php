<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_TECHNICAL_OFFICIAL;
    }

    public function rules(): array
    {
        $teamIds = collect([
            $this->input('id_pasukan_1'),
            $this->input('id_pasukan_2'),
        ])
            ->filter(fn (mixed $teamId): bool => filter_var($teamId, FILTER_VALIDATE_INT) !== false)
            ->map(fn (mixed $teamId): int => (int) $teamId)
            ->all();

        $playerBelongsToMatch = Rule::exists('pemain', 'id')
            ->where(fn ($query) => $query->whereIn('id_pasukan', $teamIds));

        return [
            'match_label' => ['nullable', 'string', 'max:255'],
            'id_kumpulan' => ['required', 'integer', Rule::exists('kumpulan', 'id')],
            'id_pasukan_1' => [
                'required',
                'integer',
                Rule::exists('pasukan', 'id')->where('id_kumpulan', $this->input('id_kumpulan')),
            ],
            'id_pasukan_2' => [
                'required',
                'integer',
                'different:id_pasukan_1',
                Rule::exists('pasukan', 'id')->where('id_kumpulan', $this->input('id_kumpulan')),
            ],
            'tarikh' => ['required', 'date_format:Y-m-d'],
            'masa' => ['required', 'date_format:H:i'],
            'tempat' => ['required', 'string', 'max:255'],
            'is_completed' => ['sometimes', 'boolean'],
            'goal_scorers' => ['sometimes', 'array', 'max:255'],
            'goal_scorers.*' => [
                'required',
                'integer',
                $playerBelongsToMatch,
            ],
            'cards' => ['sometimes', 'array', 'max:255'],
            'cards.*.player_id' => [
                'required',
                'integer',
                $playerBelongsToMatch,
            ],
            'cards.*.jenis_kad' => ['required', Rule::in(['kuning', 'merah'])],
            'cards.*.minit' => ['required', 'integer', 'between:0,255'],
            'cards.*.catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->boolean('is_completed')) {
                    return;
                }

                if ($this->filled('goal_scorers') || $this->filled('cards')) {
                    $validator->errors()->add(
                        'is_completed',
                        __('Mark the match as completed before adding goals or cards.'),
                    );
                }
            },
        ];
    }
}
