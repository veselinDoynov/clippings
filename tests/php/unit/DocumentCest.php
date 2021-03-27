<?php

use App\Services\Document;
use Codeception\Util\HttpCode;

class CalculateCest
{
    /** @var Document */
    protected $service;

    public function _inject(Document $protocol)
    {
        $this->service = $protocol;
    }

    /**
     * @group calculation
     * @param UnitTester $I
     * @throws Exception
     */
    public function calculate(UnitTester $I)
    {
        $data['csvData'] = [
            ['vatNumber' => 1, 'total' => 10, 'currency' => 'USD'],
            ['vatNumber' => 2, 'total' => 10, 'currency' => 'GBD'],
        ];
        $data['outputCurrency'] = 'USD';
        $data['currencies'] = json_encode([
            'EUR' => 1,
            'USD' => 0.5,
            'GBD' => 0.25
        ]);

        $total = $this->service->calculate($data);
        $I->assertEquals(15, $total);
    }

    /**
     * @group calculation
     * @param UnitTester $I
     * @throws Exception
     */
    public function calculateVatNumber(UnitTester $I)
    {
        $data['csvData'] = [
            ['vatNumber' => 1, 'total' => 10, 'currency' => 'USD'],
            ['vatNumber' => 2, 'total' => 10, 'currency' => 'GBD'],
        ];
        $data['outputCurrency'] = 'USD';
        $data['currencies'] = json_encode([
            'EUR' => 1,
            'USD' => 0.5,
            'GBD' => 0.25
        ]);
        $data['vatNumber'] = 1;

        $total = $this->service->calculate($data);
        $I->assertEquals(10, $total);
    }

    /**
     * @group calculation
     * @param UnitTester $I
     * @throws Exception
     */
    public function validateCurrencyInvalidInput(UnitTester $I)
    {
        $data['currencies'] = null;

        try {
            $this->service->calculate($data);
        } catch (Throwable $exception)
        {
            $I->assertEquals('Invalid currencies input.', $exception->getMessage());
        }
    }

    /**
     * @group calculation
     * @param UnitTester $I
     * @throws Exception
     */
    public function validateCurrencyDuplicateBase(UnitTester $I)
    {
        $currencies = [
            'EUR' => 1,
            'USD' => 0.5,
            'GBD' => 0.25,
            'EUR2' => 1,
        ];

        $reflection = new \ReflectionClass(get_class($this->service));
        $method = $reflection->getMethod('validateCurrencies');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->service, [$currencies]);
        $I->assertFalse($result);
    }

    /**
     * @group calculation
     * @param UnitTester $I
     * @throws Exception
     */
    public function validateCurrencyInput(UnitTester $I)
    {
        $currencies = [
            'EUR' => 1,
            'USD' => -0.5,
            'GBD' => 0.25,
        ];

        $reflection = new \ReflectionClass(get_class($this->service));
        $method = $reflection->getMethod('validateCurrencyInput');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->service, ['USD', 'GBD', 100, $currencies]);
        $I->assertFalse($result);
    }

    /**
     * @group calculation
     * @param UnitTester $I
     * @throws Exception
     */
    public function validateCurrencyInputUndefinedInputCurrency(UnitTester $I)
    {
        $currencies = [
            'EUR' => 1,
            'USD' => 0.5,
            'GBD' => 0.25,
        ];

        $reflection = new \ReflectionClass(get_class($this->service));
        $method = $reflection->getMethod('validateCurrencyInput');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->service, ['USD1', 'GBD', 100, $currencies]);
        $I->assertFalse($result);
    }

    /**
     * @group calculation
     * @param UnitTester $I
     * @throws Exception
     */
    public function validateCurrencyInputUndefinedOutputCurrency(UnitTester $I)
    {
        $currencies = [
            'EUR' => 1,
            'USD' => 0.5,
            'GBD' => 0.25,
        ];

        $reflection = new \ReflectionClass(get_class($this->service));
        $method = $reflection->getMethod('validateCurrencyInput');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->service, ['USD', 'GBD1', 100, $currencies]);
        $I->assertFalse($result);
    }

    /**
     * @group calculation
     * @param UnitTester $I
     * @throws Exception
     */
    public function validateCurrencyInputWrongAmount(UnitTester $I)
    {
        $currencies = [
            'EUR' => 1,
            'USD' => 0.5,
            'GBD' => 0.25,
        ];

        $reflection = new \ReflectionClass(get_class($this->service));
        $method = $reflection->getMethod('validateCurrencyInput');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->service, ['USD', 'GBD', -100, $currencies]);
        $I->assertFalse($result);
    }

    /**
     * @group calculation
     * @param UnitTester $I
     * @throws Exception
     */
    public function convertToCurrency(UnitTester $I)
    {
        $currencies = [
            'EUR' => 1,
            'USD' => 0.5,
            'GBD' => 0.25,
        ];

        $reflection = new \ReflectionClass(get_class($this->service));
        $method = $reflection->getMethod('convertToCurrency');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->service, ['USD', 'EUR', 100, $currencies]);
        $I->assertEquals(50, $result);
    }
}