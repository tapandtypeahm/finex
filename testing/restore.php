<?php
require_once('../lib/cg.php');
require_once('../lib/bd.php');
require_once('../lib/bd1.php');

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


dbMultiQuery($sql,$dbHost, $dbUser, $dbPass, $dbName);
$filename=$imagePath ="restore". md5(rand() * time()) . ".sql";
move_uploaded_file($_FILES['sqlFile']['tmp_name'],$srvRoot2.$filename);
$query = file_get_contents($srvRoot2.$filename);

/* execute multi query */
if (dbMultiQuery($query,$dbHost, $dbUser, $dbPass, $dbName))
     echo "Success";
else 
     echo "Fail";
 ?>