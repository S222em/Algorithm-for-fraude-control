<?php

namespace App\Classes;

class NormalDistribution
{
    /**
     * Sensitivity
     * - How higher how lower the sensitivity
     * - How lower how higher the sensitivity
     * @var int
     */
    public int $sensitivity;

    public function __construct(int $sensitivity = 2)
    {
        $this->sensitivity = $sensitivity;
    }

    /**
     * Calculates O given an array of floats
     * @param array $numbers
     * @return float
     */
    public function getO(array $numbers): float
    {
        $average = $this->getAverage($numbers);

        $map = array_map(function ($value) use ($average) {
            return pow($value - $average, 2);
        }, $numbers);

        return pow((array_sum($map) / count($numbers)), 0.5);
    }

    /**
     * Calculates the average given an array of floats
     * @param array $numbers
     * @return float
     */
    public function getAverage(array $numbers): float
    {
        return array_sum($numbers) / count($numbers);
    }

    /**
     * Check if the given number is in the range
     * @param float $num
     * @param array $numbers
     * @param callable|null $callback
     * @return bool
     */
    public function isInRange(float $num, array $numbers, callable $callback = null): bool
    {
        return !($this->isLeft($num, $numbers, $callback) || $this->isRight($num, $numbers, $callback));
    }

    /**
     * Get the left outer range
     * @param array $numbers
     * @return float
     */
    public function getMaxLeft(array $numbers): float
    {
        return $this->getAverage($numbers) - ($this->getO($numbers) * $this->sensitivity);
    }

    /**
     * Get the right outer range
     * @param array $numbers
     * @return float
     */
    public function getMaxRight(array $numbers): float
    {
        return $this->getAverage($numbers) + ($this->getO($numbers) * $this->sensitivity);
    }

    /**
     * Check if the number is out of range on the left
     * @param float $num
     * @param array $numbers
     * @param callable|null $callback
     * @return bool
     */
    public function isLeft(float $num, array $numbers, callable $callback = null): bool
    {
        $maxLeft = $this->getMaxLeft($numbers);

        if ($callback) $maxLeft = $callback($maxLeft, 0);

        return $num < $maxLeft;
    }

    /**
     * Check if the number is out of range on the right
     * @param float $num
     * @param array $numbers
     * @param callable|null $callback
     * @return bool
     */
    public function isRight(float $num, array $numbers, callable $callback = null): bool
    {
        $maxRight = $this->getMaxRight($numbers);

        if ($callback) $maxRight = $callback($maxRight, 1);

        return $num > $maxRight;
    }

    /**
     * Calculates the procentual change given a float and an array of floats
     * @param float $num
     * @param array $numbers
     * @return int
     */
    public function getProcentualChangeFromAverage(float $num, array $numbers): int
    {
        $average = $this->getAverage($numbers);
        return (($num - $average) / $average) * 100;
    }
}