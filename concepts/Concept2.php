<?php

namespace App\Concepts;

class Concept2 extends Concept1
{
    /**
     * Row the output will write to
     * @var string
     */
    public string $row = 'F';
    /**
     * ID of the concept
     * - Used for table naming
     * @var int
     */
    public int $conceptId = 2;

    /**
     * Runs the algorithm as defined
     * @param array $previous
     * @return ?string
     */
    public function run(array $previous): ?string
    {
        return parent::run($previous) ?? $this->isRevenueZero($previous);
    }

    /**
     * Checks if there were 0 revenue over a period of a given time
     * @param array $previous
     * @return ?bool
     */
    public function isRevenueZero(array $previous): ?bool
    {
        $prevRevenue = $this->resolvePreviousRevenue($previous);

        if (array_sum(array_slice($prevRevenue, 0, 4)) == 0.0) return "revenue is 0";

        return null;
    }

    /**
     * Checks if the given float is out of range given an array of floats, where the target float is the first element
     * @param array $previous
     * @return ?string
     */
    public function isRevenueOutOfRange(array $previous): ?string
    {
        $previousRevenue = $this->resolvePreviousRevenue($previous);
        $revenue = $previousRevenue[0];

        $filteredRevenue = array_filter($previousRevenue, function ($el) use ($revenue) {
            if ($el == 0.0) return false;
            else if ($revenue < 0.0) return $el < 0.0;
            else return $el >= 0.0;
        });

        if ($this->isToLittleData($filteredRevenue)) return "not enough data";

        if (!$this->method->isInRange($revenue, $filteredRevenue)) {
            if ($this->method->isRight($revenue, $filteredRevenue)) {
                return "revenue too large {$this->method->getMaxRight($filteredRevenue)} {$this->method->getProcentualChangeFromAverage($revenue, $filteredRevenue)}%";
            } else {
                return "revenue too small {$this->method->getMaxLeft($filteredRevenue)} {$this->method->getProcentualChangeFromAverage($revenue, $filteredRevenue)}%";
            }
        }

        return null;
    }
}