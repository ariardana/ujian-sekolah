<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:191'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Resolve the credential field based on the identifier value.
     *
     * Rules:
     *   - contains "@" => email (teacher)
     *   - all digits   => nisn (student)
     *   - otherwise    => username (admin)
     */
    public function resolveField(): string
    {
        $identifier = (string) $this->string('identifier');

        if (str_contains($identifier, '@')) {
            return 'email';
        }

        if (ctype_digit($identifier)) {
            return 'nisn';
        }

        return 'username';
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $field = $this->resolveField();
        $credentials = [
            $field => trim((string) $this->string('identifier')),
            'password' => (string) $this->string('password'),
            'is_active' => true,
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower((string) $this->string('identifier')).'|'.$this->ip());
    }
}
