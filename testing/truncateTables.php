<?php
require_once('../lib/cg.php');
require_once('../lib/bd.php');



$sql1="SELECT table_name FROM INFORMATION_SCHEMA.TABLES
WHERE table_schema = 'finex'";
$result=dbQuery($sql1);
$resultArray=dbResultToArray($result);

$sql="SET FOREIGN_KEY_CHECKS=0;";
foreach($resultArray as $re)
{
	$table_name=$re[0];
	$sql.="
TRUNCATE $table_name;
";
}
$sql.="SET FOREIGN_KEY_CHECKS=1;";

$mysqli = new mysqli("127.0.0.1", "tapandtype", "Iamtnt12@gmail", "finex1");
$mysqli->multi_query($sql);

$query = file_get_contents("shop.sql");

/* execute multi query */
$mysqli->multi_query($query); ?>