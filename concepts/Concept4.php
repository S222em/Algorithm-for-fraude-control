<?php

namespace App\Concepts;

class Concept4 extends Concept3
{
    /**
     * Row the output will write to
     * @var string
     */
    public string $row = 'H';
    /**
     * ID of the concept
     * - Used for table naming
     * @var int
     */
    public int $conceptId = 4;

    /**
     * Checks if the given float is out of range given an array of floats, where the target float is the first element
     * @param array $previous
     * @return string|null
     */
    public function isRevenueOutOfRange(array $previous): null|string
    {
        $previousRevenue = $this->resolvePreviousRevenue($previous);
        $revenue = $previousRevenue[0];

        $filteredRevenue = array_filter($previousRevenue, function ($el) use ($revenue) {
            if ($el == 0.0) return false;
            else if ($revenue < 0.0) return $el < 0.0;
            else return $el >= 0.0;
        });

        if ($this->isToLittleData($filteredRevenue)) return "not enough data";

        $procentualChange = $this->method->getProcentualChangeFromAverage($revenue, $filteredRevenue);
        if (abs($procentualChange) > 50) {
            return "revenue {$procentualChange}% {$this->method->getAverage($filteredRevenue)} {$revenue}";
        }

        return null;
    }

    /**
     * Checks if the given float is out of range given an array of floats, where the target float is the first element
     * @param array $previous
     * @return string|null
     */
    public function isTransactionsOutOfRange(array $previous): string|null
    {
        $previousTransactions = $this->resolvePreviousTransactions($previous);
        $transactions = $previousTransactions[0];

        $procentualChange = $this->method->getProcentualChangeFromAverage($transactions, $previousTransactions);
        if (abs($procentualChange) > 50) {
            return "transactions {$procentualChange}% {$this->method->getAverage($previousTransactions)} {$transactions}";
        }

        return null;
    }
}