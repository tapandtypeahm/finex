<?php
$string = 'Hello there is a special character \n in this string';
$pattern = '/\\\n/';
$replacement = '\\\\\n';
echo preg_replace($pattern, $replacement, $string);
echo preg_replace('/\\\n/','\\\\\n', 'Hello there is a special character \n in this string');
 ?>