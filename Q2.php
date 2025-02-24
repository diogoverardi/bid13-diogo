<?php
function loadCsvToArray($csvFile)
{
    if (empty($csvFile) || !file_exists($csvFile)) {
        die("File not found :(");
    }

    if (($handle = fopen($csvFile, "r")) !== false) {
        $data = array();
        while (($row = fgetcsv($handle, 1000, ",", '"', "\\")) !== false) {
            // validates the data extracted from the .csv and dumps into an array
            if (count($row) >= 2 && is_numeric($row[0]) && is_numeric($row[1])) {
                $data[] = array($row[0], $row[1]);
            }
        }

        fclose($handle);
        return $data;
    }

    die("Error when trying to open the file :(");
}

function createImage($data, $img_name)
{
    // generates a 13-digit image name if none was given
    if (empty($img_name)) {
        $img_name = mt_rand(1000000000000, 9999999999999) . '.png';
    }

    $width = 2402;
    $height = 2025;
    $padding = 13;
    $image = imagecreate($width, $height);

    // colours
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $bananaYellow = imagecolorallocate($image, 227, 207, 87);

    // Find min and max for scaling
    $x_values = array_column($data, 0);
    $y_values = array_column($data, 1);

    $x_min = min($x_values);
    $x_max = max($x_values);
    $y_min = min($y_values);
    $y_max = max($y_values);

    $x_range = $x_max - $x_min ?: 1;
    $y_range = $y_max - $y_min ?: 1;

    // draws the  axes
    imageline($image, $padding, $height - $padding, $width - $padding, $height - $padding, $black);
    imageline($image, $padding, $height - $padding, $padding, $padding, $black);

    // plot data points
    foreach ($data as $point) {
        $x = (int)$padding + (($point[0] - $x_min) / $x_range) * ($width - 2 * $padding);
        $y = (int)$height - $padding - (($point[1] - $y_min) / $y_range) * ($height - 2 * $padding);

        imagefilledellipse($image, (int)$x, (int)$y, 5, 5, $bananaYellow); // draws the dots 
    }

    // saves the image
    if (imagepng($image, $img_name)) {
        echo "Image was generated, please check '$img_name'\n";
    } else {
        echo "Error when generating the image.";
    }

    imagedestroy($image);
}


$csvFile = 'data.csv';
$dataArray = loadCsvToArray($csvFile);
createImage($dataArray, '');

# i have coded and tested this on my Terminal, so to run and the image just "php Q2.php" on your terminal.


#Diogo Verardi - 24FEB2025 - Quebec,Canada