<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Wallet\Deposit\DepositRequest;
use App\Http\Requests\Wallet\Transfer\TransferRequest;
use App\Http\Resources\Wallet\WalletBaseResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    /**
     * Gets the user's wallet.
     */
    public function view()
    {
        $this->authorize('accessAsConsumer', User::class);

        return WalletBaseResource::make(request()->user()->wallet);
    }

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

    /**
     * Transfer the given amount from the user's wallet to the target wallet.
     */
    public function transfer(TransferRequest $request): JsonResponse
    {
        $request->user()->transfer(
            amount: $request->amount,
            targetWallet: $request->targetWallet(),
        );

        return response()->json(
            FlashMessage::success(trans_choice('flash_messages.success.registered.m', 1, [
                'model' => trans_choice('model.transfer', 1),
            ])),
            Response::HTTP_CREATED
        );
    }
}
