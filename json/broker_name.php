<?php require_once '../lib/cg.php';
?>

<?php

      
$sql = "SELECT broker_name FROM fin_broker WHERE  broker_name LIKE '%".$_REQUEST['term']."%' 
       ";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
foreach ($resultArray as $r) 
{
    $results[] = array('label' => $r['broker_name']);
}


	
echo json_encode($results); 

   




?>