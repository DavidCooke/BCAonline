<?php

$mismatchedLinesCount = count($mismatchedLines);

echo <<<EOT
Dear {$full_name},

These are the mismatching names for individual members comparing the imported user data to the master data.

There are ${mismatchedLinesCount} row(s).


EOT;

foreach ($mismatchedLines as $line => $detail) {

    echo $detail['ImportedUser']['bca_no'] . " Import: " .
        $detail['ImportedUser']['forename'] . " " .
        $detail['ImportedUser']['surname'] . " - " .
        $detail['ImportedUser']['organisation'] . " / " .
        $detail['ImportedUser']['class']. " / " .
        $detail['ImportedUser']['email']. " / " .
        $detail['ImportedUser']['address1']. " / " .
        $detail['ImportedUser']['address2'] . "\n";

    echo $detail['User']['bca_no'] . " Master: " .
        $detail['User']['forename'] . " " .
        $detail['User']['surname'] . " - " .
        $detail['User']['organisation'] . " / " .
        $detail['User']['class']. " / " .
        $detail['User']['email']. " / " .
        $detail['User']['address1']. " / " .
        $detail['User']['address2'] . "\n\n";
}

echo "\n";

?>