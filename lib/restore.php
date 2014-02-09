<?php
require_once('cg.php');
require_once('bd.php');
require_once('bd1.php');
require_once('backup.php');
require_once('vehicle-type-functions.php');
set_time_limit(0); 
$thisFile2 = str_replace('\\', '/', __FILE__);
$srvRoot2  = str_replace('lib/restore.php', '', $thisFile2);

define('RESTORE_ROOT', $srvRoot2);
function restore($file)
{

 global $dbHost, $dbUser, $dbPass, $dbName;

if(isset($file['sqlFile']['tmp_name']) && trim($file['sqlFile']['tmp_name'])!="")	
{

  if (trim($file['sqlFile']['tmp_name']) != '') 
  {
        // get the image extension
        $ext = substr(strrchr($file['sqlFile']['name'], "."), 1); 
 	}
  
if($ext=="sql")
{  

backup_tables('*','restore_checkpoint');
		
$sql1="SELECT table_name FROM INFORMATION_SCHEMA.TABLES
WHERE table_schema = '".$dbName."'";
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
$filename="restore". md5(rand() * time()).".sql";
move_uploaded_file($file['sqlFile']['tmp_name'],RESTORE_ROOT.$filename);
$query = file_get_contents(RESTORE_ROOT.$filename);

$query_array=explode(";",$query);
$total_no_queries=count($query_array);
$slabs=$total_no_queries/1000;
$ceil_slabs=ceil($slabs);
$slabs_last_part=1-($ceil_slabs-$slabs);
$slabs_front_part=floor($slabs);
$queries_arrays=array();

for($i=0;$i<$ceil_slabs;$i++)
{
	
	if($i<=($ceil_slabs-2))
	{
	$start_count=$i*1000;
	$end_count=(($i+1)*1000)-1;
	
	$query_array_part=array_slice($query_array,$start_count,1000);
	if($i==0)
	$sql_array="";
	else
	$sql_array="SET FOREIGN_KEY_CHECKS=0;";
	$sql_array=$sql_array.implode(";",$query_array_part).";";
	$queries_arrays[]=$sql_array;
	}
	else if($i==($ceil_slabs-1))
	{
		$start_count=$i*1000;
		$end_count=$total_no_queries-1;
		$query_array_part=array_slice($query_array,$start_count,$end_count);
	    if($i==0)
	$sql_array="";
	else
	$sql_array="SET FOREIGN_KEY_CHECKS=0;";
		$sql_array=$sql_array.implode(";",$query_array_part).";";
		$queries_arrays[]=$sql_array;
	}


	
}

foreach($queries_arrays as $queries_array)
{
	
	dbMultiQuery($queries_array,$dbHost, $dbUser, $dbPass, $dbName);
	sleep(4);
}

return "success";		
}

}

return "success";
}
 
?>