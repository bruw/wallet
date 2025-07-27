<?php

namespace App\Http\Requests\Wallet\Deposit;

use App\Constants\Deposit\DepositConstants;
use App\Http\Requests\Base\ApiFormRequest;

class DepositRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:' . DepositConstants::MIN_VALUE,
                'max:' . DepositConstants::MAX_VALUE,
            ],
        ];
    }
}
