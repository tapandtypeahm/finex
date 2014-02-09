<?php
require_once 'cg.php';

$dbConn = mysql_connect ($dbHost, $dbUser, $dbPass) or die ('MySQL connect failed. ' . mysql_error());
mysql_select_db($dbName) or die('Cannot select database. ' . mysql_error());

$time="SET time_zone='+5:30'";
mysql_query($time) or die(mysql_error());

function dbQuery($sql)
{
   /*	$result = mysql_query($sql) or die('<META HTTP-EQUIV="REFRESH" 
CONTENT="1;URL='.WEB_ROOT.'">'); */
     
	 $result = mysql_query($sql) or die(mysql_error()); 

	
	return $result;
}

function dbAffectedRows()
{
	global $dbConn;
	
	return mysql_affected_rows($dbConn);
}

function dbFetchArray($result, $resultType = MYSQL_BOTH) {
	return mysql_fetch_array($result, $resultType);
}

function dbFetchAssoc($result)
{
	return mysql_fetch_assoc($result);
}

function dbFetchRow($result) 
{
	return mysql_fetch_row($result);
}

function dbFreeResult($result)
{
	return mysql_free_result($result);
}

function dbNumRows($result)
{
	return mysql_num_rows($result);
}

function dbSelect($dbName)
{
	return mysql_select_db($dbName);
}

function dbInsertId()
{
	return mysql_insert_id();
}

function dbResultToArray($result)
{
	$resultArray = array();
	while($row=dbFetchArray($result,MYSQL_BOTH))
	{
	  $resultArray[] = $row;	
	}
	 return $resultArray;
}
?>