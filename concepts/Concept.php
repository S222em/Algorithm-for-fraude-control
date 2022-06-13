<?php

namespace App\Concepts;

use App\Classes\NormalDistribution;

abstract class Concept
{
    /**
     * Method that is used
     * @var NormalDistribution
     */
    public NormalDistribution $method;
    /**
     * Row the output will write to
     * @var string
     */
    public string $row;
    /**
     * ID of the concept
     * - Used for table naming
     * @var int
     */
    public int $conceptId;
    /**
     * Max amount of previous data
     * @var int
     */
    public int $maxPreviousCount = 30;
    /**
     * Min amount of previous data
     * @var int
     */
    public int $minPreviousCount = 30;

    public function __construct(float $sensitivity)
    {
        $this->method = new NormalDistribution($sensitivity);
    }

    /**
     * Checks if there is not enough data
     * @param array $array
     * @param bool $includesCurrent
     * @return bool
     */
    public function isToLittleData(array $array, bool $includesCurrent = false): bool
    {
        if ($this->minPreviousCount == 0) return false;

        $count = $includesCurrent ? count($array) - 1 : count($array);

        return $count < $this->minPreviousCount;
    }

    /**
     * Runs the algorithm as defined
     * @param array $previous
     * @return string|null
     */
    public abstract function run(array $previous): string|null;
}