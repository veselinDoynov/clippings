<?php

namespace App\Tests\Api;

use Codeception\Util\HttpCode;

class CaclulateCest extends BaseCest
{
    /**
     * @group calculate
     * @param \ApiTester $I
     */
    public function calculate(\ApiTester $I)
    {

        $postData = [
            'currencies' => '{"EUR":1,"USD":0.846405,"LV":0.51129188,"GBP":0.878}',
            'outputCurrency' => 'GBP'
        ];

        $I->sendPOST('/documents/calculation', $postData, ['file' => codecept_data_dir('clippings-data.csv')]);
        $total = json_decode($I->grabResponse(), true);
        $I->assertEquals('5,003.58', number_format($total, 2));
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @group calculate
     * @param \ApiTester $I
     */
    public function calculateWithVatNumber(\ApiTester $I)
    {

        $postData = [
            'currencies' => '{"EUR":1,"USD":0.846405,"LV":0.51129188,"GBP":0.878}',
            'outputCurrency' => 'GBP',
            'vatNumber' => 123465123
        ];

        $I->sendPOST('/documents/calculation', $postData, ['file' => codecept_data_dir('clippings-data.csv')]);
        $total = json_decode($I->grabResponse(), true);
        $I->assertEquals('1,413.90', number_format($total, 2));
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @group calculate
     * @param \ApiTester $I
     */
    public function calculateWrongFileData(\ApiTester $I)
    {

        $postData = [
            'currencies' => '{"EUR":1,"USD":0.846405,"LV":0.51129188,"GBP":0.878}',
            'outputCurrency' => 'GBP'
        ];

        $I->sendPOST('/documents/calculation', $postData, ['file' => codecept_data_dir('clippings-data-wrong.csv')]);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @group calculate
     * @param \ApiTester $I
     */
    public function calculateFileRequired(\ApiTester $I)
    {

        $postData = [
            'currencies' => '{"EUR":1,"USD":0.846405,"LV":0.51129188,"GBP":0.878}',
            'outputCurrency' => 'GBP'
        ];

        $I->sendPOST('/documents/calculation', $postData);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @group calculate
     * @param \ApiTester $I
     */
    public function calculateCurrenciesRequired(\ApiTester $I)
    {

        $postData = [
            'file' => codecept_data_dir('clippings-data.csv'),
            'outputCurrency' => 'GBP'
        ];

        $I->sendPOST('/documents/calculation', $postData);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @group calculate
     * @param \ApiTester $I
     */
    public function calculateOutputCurrencyRequired(\ApiTester $I)
    {

        $postData = [
            'file' => codecept_data_dir('clippings-data.csv'),
            'currencies' => '{"EUR":1,"USD":0.846405,"LV":0.51129188,"GBP":0.878}',
        ];

        $I->sendPOST('/documents/calculation', $postData);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }
}