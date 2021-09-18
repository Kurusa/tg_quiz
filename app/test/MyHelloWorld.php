<?php
function repeatedString($s, $n) {
    $stringChars = str_split($s);
    for ($i = 0; $i <= $n; $i++) {
        foreach ($stringChars as $sChar) {
            if (strlen($s) < $n) {
                $s .= $sChar;
            }
        }
    }

    echo $s;
}

function addChars(&$string) {
    $stringChars = str_split($string);
    foreach ($stringChars as $sChar) {
        if (strlen($s) < $n) {
            $s .= $sChar;
        }
    }
}

repeatedString('test', 10);