<?php

namespace App\Console\Commands;

use App\Models\User;
use Filament\Commands\MakeUserCommand as FilamentMakeUserCommand;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeFilamentUserCommand extends FilamentMakeUserCommand
{
    protected $signature = 'make:filament-user
{--name= : The name of the user}
{--email= : The email address of the user}
{--password= : The password of the user}
{--phone= : The phone number of the user}';
    protected function askWithValidation(string $question, array $rules)
    {
        while (true) {
            $value = $this->ask($question);
            $validator = Validator::make(['field' => $value], ['field' => $rules]);
            if ($validator->fails()) {
                $this->error($validator->errors()->first('field'));
                continue;
            }
            return $value;
        }
    }
    protected function createUser(): User
    {
        $name = $this->option('name') ?? $this->askWithValidation(
            'Name',
            ['required', 'string', 'max:255']
        );
        $email = $this->option('email') ?? $this->askWithValidation(
            'Email address',
            ['required', 'email', 'unique:users,email']
        );
        $password = $this->option('password') ?? $this->askWithValidation(
            'Password',
            ['required', 'min:8']
        );
        $phone = $this->option('phone') ?? $this->ask('Phone number (optional)');
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'phone' => $phone,
        ]);
        $user->markEmailAsVerified();
        return $user;
    }
}
