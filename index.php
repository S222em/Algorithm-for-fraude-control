<?php

require __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/classes/NormalDistribution.php';
include __DIR__ . '/concepts/Concept.php';
include __DIR__ . '/concepts/Concept1.php';
include __DIR__ . '/concepts/Concept2.php';
include __DIR__ . '/concepts/Concept3.php';
include __DIR__ . '/concepts/Concept4.php';
include __DIR__ . '/concepts/Concept5.php';

// The Concept number (or ID) can be changed here to run a different concept
use App\Concepts\Concept5 as Concept;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Load the Excel sheet
$spreadsheet = IOFactory::load("data.xlsx");

$concept = new Concept(2.8);
// Get all the sheets from Excel
$sheets = $spreadsheet->getAllSheets();

foreach ($sheets as $sheet) {
    // Get all the rows in the sheet in JSON
    $rows = $sheet->toArray();

    // Sort the rows based on date
    uasort($rows, function ($el1, $el2) {
        return strtotime($el1[0]) - strtotime($el2[0]);
    });

    // Set the column title
    $sheet->setCellValue($concept->row . "1", "Concept " . $concept->conceptId);

    foreach ($rows as $key => $row) {
        // Index 0 only contains the column titles, which needs to be skipped
        if ($key === 0) continue;

        // Filter the rows based on the payment type of the current row
        $filteredRows = array_values(array_filter($rows, function ($el) use ($row) {
            return $el[1] === $row[1];
        }));
        // Find the current row in the filtered array
        $keyInFilter = array_search($row, $filteredRows);

        $offset = $concept->maxPreviousCount != 0 && $keyInFilter + 1 > $concept->maxPreviousCount ? $keyInFilter - $concept->maxPreviousCount : 0;
        $length = $concept->maxPreviousCount != 0 && $keyInFilter + 1 > $concept->maxPreviousCount ? $concept->maxPreviousCount + 1 : $keyInFilter + 1;

        // Reversing the array makes the array new to old
        $previous = array_reverse(array_slice($filteredRows, $offset, $length));

        $message = "";
        if ($concept->isToLittleData($previous)) $message = "not enough data";
        else $message = $concept->run($previous);

        if (empty($message)) $message = "ok";

        $coordinate = $concept->row . $key + 1;
        // Write the result to the Excel sheet
        $sheet->setCellValue($coordinate, $message);
    }
    // Set the auto filter to include all columns and rows
    $sheet->getAutoFilter()->setRange("A1:I" . "{$sheet->getHighestRow()}");
}

// Save the file
$writer = IOFactory::createWriter($spreadsheet, "Xlsx");
$writer->save("data.xlsx");
