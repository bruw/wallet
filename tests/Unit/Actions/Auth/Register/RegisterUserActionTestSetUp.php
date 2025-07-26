<?php

namespace Tests\Unit\Actions\Auth\Register;

use App\Dto\Auth\RegisterUserDto;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserActionTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected RegisterUserDto $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $faker = \Faker\Factory::create('pt_BR');

        $this->data = new RegisterUserDto(
            name: $faker->name(),
            email: '7Yr4Q@example.com',
            cpf: $faker->cpf(),
            phone: $faker->cellPhoneNumber(),
            password: 'password',
        );
    }
}
