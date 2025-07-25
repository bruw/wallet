<?php

namespace App\Dto\Auth;

class RegisterUserDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $cpf,
        public readonly string $cellPhone,
        public readonly string $password
    ) {}
}
