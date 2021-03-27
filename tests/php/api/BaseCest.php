<?php

namespace App\Tests\Api;

class BaseCest
{
    /**
     * @param \ApiTester $I
     */
    public function _before(\ApiTester $I)
    {
        $I->haveHttpHeader('Accept', "application/json");
    }
}
