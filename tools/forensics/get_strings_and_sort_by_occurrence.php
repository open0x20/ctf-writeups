<?php

if ($argc < 3) {
    echo 'Usage: php ./get_strings_and_sort_by_occurrence.php FILENAME LIMIT' . PHP_EOL;
    echo 'The argument LIMIT limits the amount of result lines printed' . PHP_EOL;
    echo PHP_EOL;
    exit(1);
}

exec('strings ' . $argv[1], $output);

foreach ($output as $o) {
    if (isset($collection[$o])) {
        $collection[$o]++;
    } else {
        $collection[$o] = 1;
    }
}


arsort($collection, SORT_DESC);

echo 'Occurrences: String' . PHP_EOL;

$i = 0;
foreach($collection as $k => $v) {
    echo str_pad($v, 11, ' ', STR_PAD_LEFT) . ': ' . $k . PHP_EOL;
    $i++;

    if ($i > $argv[2]) {
        break;
    }
}
