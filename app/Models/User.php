<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Actions\Auth\Login\LoginAction;
use App\Actions\Auth\Register\RegisterUserAction;
use App\Actions\Wallet\Deposit\DepositAction;
use App\Dto\Auth\LoginDto;
use App\Dto\Auth\RegisterUserDto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    ================= ** Relationships ** =========================================================
    */

    /**
     * The roles that belong to the User
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Gets the user's wallet.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /*
    ================= ** Helpers ** =========================================================
    */

    /**
     * Determines if the user is a consumer.
     */
    public function isConsumer(): bool
    {
        return $this->roles()->where('name', 'consumer')->exists();
    }

    /*
    ================= ** Actions ** =========================================================
    */

    /**
     * Registers a new user with the given data.
     */
    public static function register(RegisterUserDto $data): LoginDto
    {
        return (new RegisterUserAction($data))->execute();
    }

    /**
     * Authenticated the user in to the application.
     */
    public static function login(User $user, string $password): LoginDto
    {
        return (new LoginAction($user, $password))->execute();
    }

    /**
     * Deposit the given amount into the user's wallet.
     */
    public function deposit(string $amount): Deposit
    {
        return (new DepositAction($this, $amount))->execute();
    }
}
