<?php

namespace Tests\Unit\Constants\Deposit;

use App\Constants\Deposit\DepositConstants;
use Tests\TestCase;

class DepositConstantsTest extends TestCase
{
    public function test_should_thrown_an_exception_when_the_min_value_is_different_than_5(): void
    {
        $this->assertEquals(DepositConstants::MIN_VALUE, '5.00');
    }

    public function test_should_thrown_an_exception_when_the_max_value_is_different_than_1000(): void
    {
        $this->assertEquals(DepositConstants::MAX_VALUE, '1000.00');
    }
}
