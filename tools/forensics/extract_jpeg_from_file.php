<?php

if ($argc < 2) {
    echo 'Usage: php ./extract_jpeg_from_file.php FILENAME' . PHP_EOL;
    echo 'Images will be extracted to extracted_images/ ' . PHP_EOL;
    echo PHP_EOL;
    exit(1);
}

$data = file_get_contents($argv[1]);

exec('mkdir extracted_images');

$image_data = "";
$active_image = false;
$end_of_image = false;

$filesize = strlen($data);

for ($i = 0; $i < $filesize; $i++) {

    if ($i+2 < $filesize) {
        if ($data[$i] == "\xff" && $data[$i+1] == "\xd8" && $data[$i+2] == "\xff") {
            $active_image = true;
            $image_data = "";
            echo 'Found image start at byte ' . $i . PHP_EOL;
        }
    }
    if ($i+1 < $filesize) {
        if ($data[$i] == "\xff" && $data[$i+1] == "\xd9") {
            $end_of_image = true;
            echo 'Found image end at byte ' . ($i + 1) . PHP_EOL;
        }
    }

    if ($end_of_image) {
        if (!strlen($image_data) == 0) {
            file_put_contents('./extracted_images/' . $i . '.jpg', $image_data . "\xff\xd9");
        }
        $image_data = "";
        $end_of_image = false;
        $active_image = false;
        continue;
    }

    if ($active_image) {
        $image_data.= $data[$i];
    }
}
