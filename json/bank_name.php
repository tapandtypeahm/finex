<?php require_once '../lib/cg.php';
?>

<?php

      
$sql = "SELECT bank_name FROM fin_bank WHERE  bank_name LIKE '%".$_REQUEST['term']."%' 
       ";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
foreach ($resultArray as $r) 
{
    $results[] = array('label' => $r['bank_name']);
}


	
echo json_encode($results); 

   




?>