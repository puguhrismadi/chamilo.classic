<?
$survey_name=$_GET['survey_name'];
$filename ="temp/".$survey_name.".csv";
$survey_namem=$survey_name.".csv";
header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: public');
header('Pragma: no-cache');
header('Content-Type:application/csv');
header('Content-Length: '.filesize($filename));
header('Content-Disposition: attachment; filename='.$survey_namem);
readfile($filename);
?>