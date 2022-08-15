<?php

// Downloads csv file
$file = $_GET['path'];
$filename = $_GET['fname'];
header("Content-type: text/csv");
header('Content-Disposition: attachment; filename=test.csv');
$content = file_get_contents($file);
echo $content;
?>
