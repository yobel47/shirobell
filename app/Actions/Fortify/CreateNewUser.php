<?php

namespace App\Actions\Fortify;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Public registration always creates a customer account.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique(User::class)],
            'phone' => ['nullable', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'password' => $this->passwordRules(),
        ], [
            'phone.regex' => 'Nomor WhatsApp tidak valid. Contoh: 081234567890.',
        ])->validate();

        $user = new User([
            'name' => $input['name'],
            'email' => Str::lower($input['email']),
            'phone' => $input['phone'] ?? null,
            'password' => $input['password'],
        ]);
        $user->role = UserRole::Customer;
        $user->save();

        return $user;
    }
}
