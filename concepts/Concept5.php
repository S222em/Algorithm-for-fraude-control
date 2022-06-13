<?php

namespace App\Concepts;

class Concept5 extends Concept3
{
    /**
     * Row the output will write to
     * @var string
     */
    public string $row = 'I';
    /**
     * ID of the concept
     * - Used for table naming
     * @var int
     */
    public int $conceptId = 5;

    /**
     * Rounds a float 10's/100's, based on the value of the float and the direction to round to
     * @param float $float
     * @param int $direction
     * @return float
     */
    public function roundTransactions(float $float, int $direction): float
    {
        $roundToo = $float > 100 ? 100 : 10;
        if ($direction == 1) return ceil($float / $roundToo) * $roundToo;
        else return floor($float / $roundToo) * $roundToo;
    }

    /**
     * Rounds a float to 100's, based on the direction to round to
     * @param float $float
     * @param int $direction
     * @return float
     */
    public function roundRevenue(float $float, int $direction): float
    {
        if ($direction == 1) return ceil($float / 100) * 100;
        else return floor($float / 100) * 100;
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

        if (!$this->method->isInRange($transactions, $previousTransactions, [$this, 'roundTransactions']) && abs($this->method->getProcentualChangeFromAverage($transactions, $previousTransactions)) > 40) {
            // ^ Check if the amount of transactions is in range
            if ($this->method->isRight($transactions, $previousTransactions, [$this, 'roundTransactions'])) {
                return "amount of transactions too large {$this->method->getMaxRight($previousTransactions)} {$this->method->getProcentualChangeFromAverage($transactions, $previousTransactions)}%";
            } else {
                return "amount of transactions too small {$this->method->getMaxLeft($previousTransactions)} {$this->method->getProcentualChangeFromAverage($transactions, $previousTransactions)}%";
            }
        }

        return null;
    }

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

        if (!$this->method->isInRange($revenue, $filteredRevenue, [$this, 'roundRevenue']) && abs($this->method->getProcentualChangeFromAverage($revenue, $filteredRevenue)) > 40) {
            // ^ Check if the revenue is in range
            if ($this->method->isRight($revenue, $filteredRevenue, [$this, 'roundRevenue'])) {
                return "revenue too large {$this->method->getMaxRight($filteredRevenue)} {$this->method->getProcentualChangeFromAverage($revenue, $filteredRevenue)}%";
            } else {
                return "revenue too small {$this->method->getMaxLeft($filteredRevenue)} {$this->method->getProcentualChangeFromAverage($revenue, $filteredRevenue)}%";
            }
        }

        return null;
    }
}