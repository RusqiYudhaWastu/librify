<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
            // Hapus validasi 'email' format, ganti string biasa
            // Karena kita mau terima NISN (angka) atau Username (string) juga
            'email' => ['required', 'string'], 
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Ambil inputan dari form login (Field name tetap 'email')
        $input = $this->input('email');

        // 2. Logic Deteksi Tipe Login (Email / NISN / Username)
        $fieldType = 'username'; // Default anggap username

        if (is_numeric($input)) {
            // Jika inputan angka semua, berarti NISN
            $fieldType = 'nisn';
        } elseif (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            // Jika formatnya email valid, berarti Email
            $fieldType = 'email';
        }

        // 3. Coba Login ke Database
        // Kita pakai variable $fieldType yang sudah ditentukan di atas
        if (! Auth::attempt([$fieldType => $input, 'password' => $this->input('password')], $this->boolean('remember'))) {
            
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}