<?php

namespace App\Http\Requests\Wallet\Transfer;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('accessAsConsumer', User::class);
    }

    /**
     * Returns the target wallet, throws an exception if it does not exist.
     */
    public function targetWallet(): Wallet
    {
        return Wallet::where([
            'public_key' => $this->target_public_key,
        ])->first();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric'],
            'target_public_key' => ['required', 'exists:wallet_keys,public_key'],
        ];
    }
}
