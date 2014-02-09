<?php
require_once('lib/cg.php');
    $sql="SELECT NOW()";
    $result=dbQuery($sql);
    $resultArray=dbResultToArray($result);
	$today= date('Y-m-d',strtotime($resultArray[0][0]));
	echo $today;			
 ?>