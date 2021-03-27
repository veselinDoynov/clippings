<?php

namespace App\Services;


class Document
{
    const DOCUMENT_UPLOAD_MAPPING = [
        'Customer' => 'customer',
        'Vat number' => 'vatNumber',
        'Document number' => 'documentNumber',
        'Type' => 'type',
        'Parent document' => 'patientDocument',
        'Currency' => 'currency',
        'Total' => 'total'
    ];

    /**
     * @param array $data
     * @return float|int
     * @throws \Exception
     */
    public function calculate(array $data)
    {
        $currencies = json_decode($data['currencies'], true);
        if (!$this->validateCurrencies($currencies)) {
            throw new \Exception('Invalid currencies input.', 400);
        }

        return $this->processCalculation($data, $currencies);
    }

    protected function validateCurrencies(array $currencies)
    {
        if (!is_array($currencies)) {
            return false;
        }
        $baseCurrencyCounter = 0;
        foreach ($currencies as $currency => $currencyValue) {
            if ($currencyValue == 1) {
                $baseCurrencyCounter++;
            }
        }

        return $baseCurrencyCounter == 1;
    }


    protected function convertToCurrency(string $inputCurrency, string $outputCurrency, float $amount, array $currencies)
    {
        return ($currencies[$inputCurrency] / $currencies[$outputCurrency]) * $amount;
    }

    protected function processCalculation(array $data, array $currencies)
    {
        $total = 0;

        foreach ($data['csvData'] as $row) {
            if (isset($data['vatNumber']) && $data['vatNumber'] != $row['vatNumber']) {
                continue;
            }
            $total += $this->convertToCurrency($row['currency'], $data['outputCurrency'], $row['total'], $currencies);
        }

        return $total;
    }
}