<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Base\ApiFormRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retrieve the user associated with the provided email.
     */
    public function userByEmail(): ?User
    {
        return User::where('email', $this->email)->first();
    }

    /**
     * Return the password from the request.
     */
    public function password(): string
    {
        return $this->password;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (is_null($this->userByEmail())) {
                    $validator->errors()->add('email', trans('auth.failed'));
                }
            },
        ];
    }
}
