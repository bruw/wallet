<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Messages\FlashMessage;
use App\Http\Requests\Wallet\Deposit\DepositRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WalletController
{
    /**
     * Deposit the given amount into the user's wallet.
     */
    public function deposit(DepositRequest $request): JsonResponse
    {
        $request->user()->deposit($request->amount);

        return response()->json(
            FlashMessage::success(trans_choice('flash_messages.success.registered.m', 1, [
                'model' => trans_choice('model.deposit', 1),
            ])),
            Response::HTTP_CREATED
        );
    }
}
