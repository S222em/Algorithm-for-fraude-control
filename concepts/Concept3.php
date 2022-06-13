<?php

namespace App\Concepts;

class Concept3 extends Concept2
{
    /**
     * Row the output will write to
     * @var string
     */
    public string $row = 'G';
    /**
     * ID of the concept
     * - Used for table naming
     * @var int
     */
    public int $conceptId = 3;

    /**
     * Resolves an Excel row into floats
     * @param array $previous
     * @return array
     */
    public function resolvePreviousRevenue(array $previous): array
    {
        return array_values(array_map(function (array $array) {
            return floatval($array[3]) / floatval($array[2]);
        }, $previous));
    }

}