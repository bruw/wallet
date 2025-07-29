<?php

namespace Tests\Unit\Constants\Transfer;

use App\Constants\Transfer\TransferConstants;
use App\Models\Transfer;
use Tests\TestCase;

class TransferConstantsTest extends TestCase
{
    public function test_should_thrown_an_exception_when_the_min_value_is_different_than_10(): void
    {
        $this->assertEquals(TransferConstants::MIN_VALUE, '10.00');
    }

    public function test_should_thrown_an_exception_when_the_max_value_is_different_than_1000(): void
    {
        $this->assertEquals(TransferConstants::MAX_VALUE, '1000.00');
    }
}
