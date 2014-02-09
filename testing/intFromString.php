<?php $str = 'In My Cart :  items ';
$int = filter_var($str, FILTER_SANITIZE_NUMBER_INT);
if($int==null)
$int=9999;
echo $int; ?>