<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
            'nip' => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^(08|628)[0-9]{8,13}$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, dan titik.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'nip.regex' => 'NIP hanya boleh mengandung angka.',
            'nip.max' => 'NIP maksimal 18 karakter.',
            'phone.regex' => 'Nomor telepon tidak valid (contoh: 08xxxxxxxxxx).',
            'phone.max' => 'Nomor telepon maksimal 15 karakter.',
        ];
    }
}
