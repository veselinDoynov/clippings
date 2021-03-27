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
        if (!is_array($currencies) || !$this->validateCurrencies($currencies) ) {
            throw new \Exception('Invalid currencies input.', 400);
        }

        return $this->processCalculation($data, $currencies);
    }

    protected function validateCurrencies(array $currencies)
    {
        $baseCurrencyCounter = 0;
        foreach ($currencies as $currency => $currencyValue) {
            if ($currencyValue == 1) {
                $baseCurrencyCounter++;
            }
        }

        return $baseCurrencyCounter == 1;
    }


    protected function validateCurrencyInput(string $inputCurrency, string $outputCurrency, float $amount, array $currencies): bool
    {
        if(
            !isset($currencies[$inputCurrency])
            ||
            !isset($currencies[$outputCurrency])
            ||
            $currencies[$inputCurrency] <=0
            ||
            $currencies[$outputCurrency] <= 0
            ||
            $amount < 0
        ) {
            return false;
        }

        return true;
    }

    protected function convertToCurrency(string $inputCurrency, string $outputCurrency, float $amount, array $currencies)
    {
        return ($currencies[$inputCurrency] / $currencies[$outputCurrency]) * $amount;
    }

    /**
     * @param array $data
     * @param array $currencies
     * @return float|int
     * @throws \Exception
     */
    protected function processCalculation(array $data, array $currencies)
    {
        $total = 0;

        foreach ($data['csvData'] as $row) {
            if (isset($data['vatNumber']) && $data['vatNumber'] != $row['vatNumber']) {
                continue;
            }
            if (!$this->validateCurrencyInput($row['currency'], $data['outputCurrency'], $row['total'], $currencies)) {
                throw new \Exception('Invalid currencies input.', 400);
            }
            $total += $this->convertToCurrency($row['currency'], $data['outputCurrency'], $row['total'], $currencies);
        }

        return $total;
    }
}