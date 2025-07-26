<?php

namespace Tests\Unit\Models\WalletKey;

use App\Models\WalletKey;
use RuntimeException;
use Tests\TestCase;

class GenerateRandomKeyTest extends TestCase
{
    public function test_should_generate_a_random_key_with_64_chars_length(): void
    {
        $key = WalletKey::generateRandomKey();

        $this->assertEquals(64, strlen($key));
    }

    public function test_should_throw_an_exception_when_max_depth_is_reached(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Maximum depth reached in random wallet key generation.');

        WalletKey::generateRandomKey(depth: 6);
    }
}
