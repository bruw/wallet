<?php

namespace App\Actions\Validator;

use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserValidator
{
    public function __construct(
        private readonly User $user
    ) {}

    /**
     * Creates a new instance of UserValidator for the specified user.
     */
    public static function for(User $user): self
    {
        return new self($user);
    }

    /**
     * Validates if the given password matches the user's current password.
     */
    public function passwordMustBeTheUser(string $password): self
    {
        $isSamePassword = Hash::check($password, $this->user->password);

        throw_unless($isSamePassword, new HttpJsonResponseException(
            trans('auth.password'),
            Response::HTTP_UNAUTHORIZED
        ));

        return $this;
    }

    /**
     * Validates if the wallet is not blocked.
     */
    public function mustNotBeBlocked(): self
    {
        throw_if($this->user->wallet->isBlocked(), new HttpJsonResponseException(
            trans('actions.user.errors.wallet.blocked'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validates if the user owns the given wallet.
     */
    public function mustOwnWallet(Wallet $wallet): self
    {
        $isOwner = $this->user->is($wallet->user);

        throw_unless($isOwner, new HttpJsonResponseException(
            trans('auth.password'),
            Response::HTTP_UNAUTHORIZED
        ));

        return $this;
    }
}
