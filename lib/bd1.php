<?php
require_once 'cg.php';
set_time_limit(0); 
$time="SET time_zone='+5:30'";
mysql_query($time) or die(mysql_error());

function dbMultiQuery($sql,$dbHost, $dbUser, $dbPass, $dbName)
{
   /*	$result = mysql_query($sql) or die('<META HTTP-EQUIV="REFRESH" 
CONTENT="1;URL='.WEB_ROOT.'">'); */
	
	$dbConn = mysqli_connect ($dbHost, $dbUser, $dbPass, $dbName) or die ('MySQL connect failed. ' . mysql_error());
	
	 $result = mysqli_multi_query($dbConn,$sql) or die(mysql_error()); 

	
	return $result;
}



 ?>