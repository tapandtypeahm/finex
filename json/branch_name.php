<?php require_once '../lib/cg.php';
require_once '../lib/bank-functions.php';
?>

<?php
if(isset($_GET['bank_name']))
{
  $bank_name=$_GET['bank_name'];
   $bank_id=getBankIdFromName($bank_name);
   if($bank_id!=false)
   {   
$sql = "SELECT branch_name FROM fin_bank_branch WHERE bank_id=$bank_id AND branch_name LIKE '%".$_REQUEST['term']."%' 
       ";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
foreach ($resultArray as $r) 
{
    $results[] = array('label' => $r['branch_name']);
}


	
echo json_encode($results); 

   }
}



?>