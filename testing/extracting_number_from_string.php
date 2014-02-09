<?php
$string="gj1cz001";
preg_match('#[0-9]+$#', $string, $match);
$end_number=$match[0]; // Output: 8271

$pos = strrpos($string, $end_number);

    if($pos !== false)
    {
        $start_string = substr_replace($string, "", $pos, strlen($end_number));
    }
echo "starts with ".$start_string." ends with ".$end_number;

$new_number=$str = ltrim($end_number, '0');
echo "old no was".$string;
echo "new reg no is ".$start_string.$new_number;
 ?>