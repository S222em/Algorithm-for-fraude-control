<?php

namespace App\Concepts;

class Concept1 extends Concept
{
    /**
     * Row the output will write to
     * @var string
     */
    public string $row = 'E';
    /**
     * ID of the concept
     * - Used for table naming
     * @var int
     */
    public int $conceptId = 1;

    /**
     * Runs the algorithm as defined
     * @param array $previous
     * @return string|null
     */
    public function run(array $previous): string|null
    {
        return $this->isRevenueOutOfRange($previous) ?? $this->isTransactionsOutOfRange($previous) ?? $this->isTransactionsZero($previous);
    }

    /**
     * Checks if the given float is out of range given an array of floats, where the target float is the first element
     * @param array $previous
     * @return string|null
     */
    public function isRevenueOutOfRange(array $previous): string|null
    {
        $previousRevenue = $this->resolvePreviousRevenue($previous);
        $revenue = $previousRevenue[0];

        if (!$this->method->isInRange($revenue, $previousRevenue)) {
            if ($this->method->isRight($revenue, $previousRevenue)) {
                return "revenue is too large {$this->method->getProcentualChangeFromAverage($revenue, $previousRevenue)}%";
            } else {
                return "revenue is too small {$this->method->getProcentualChangeFromAverage($revenue, $previousRevenue)}%";
            }
        }

        return null;
    }

    /**
     * Checks if there were no transactions over a period of a given time
     * @param $previous
     * @return string|null
     */
    public function isTransactionsZero($previous): string|null
    {
        $previousTransactions = $this->resolvePreviousTransactions($previous);

        if (array_sum(array_slice($previousTransactions, 0, 3)) == 0.0) {
            if (array_sum(array_slice($previousTransactions, 0, 30)) == 0.0) {
                // In this case we expect the targeted client is a new client, which we can't verify here
                return "no transactions";
            } else {
                return "no transactions for 3 days";
            }
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

        if (!$this->method->isInRange($transactions, $previousTransactions)) {
            if ($this->method->isRight($transactions, $previousTransactions)) {
                return "amount of transactions too large {$this->method->getProcentualChangeFromAverage($transactions, $previousTransactions)}%";
            } else {
                return "amount of transactions too small {$this->method->getProcentualChangeFromAverage($transactions, $previousTransactions)}%";
            }
        }

        return null;
    }

    /**
     * Resolves an Excel row into floats
     * @param array $previous
     * @return array
     */
    public function resolvePreviousRevenue(array $previous): array
    {
        return array_values(array_map(function (string $string) {
            return floatval($string);
        }, array_column($previous, 3)));
    }

    /**
     * Resolves an Excel row into floats
     * @param array $previous
     * @return array
     */
    public function resolvePreviousTransactions(array $previous): array
    {
        return array_values(array_map(function (string $string) {
            return floatval($string);
        }, array_column($previous, 2)));
    }
}