<?php
$string="/1213/037";
preg_match('#[0-9]+$#', $string, $match);
$end_number=$match[0]; // Output: 8271
echo $end_number;

 ?>