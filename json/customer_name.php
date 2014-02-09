<?php require_once '../lib/cg.php';
?>

<?php
$oc_id=$_SESSION['adminSession']['oc_id'];

      
$sql = "SELECT customer_name FROM fin_file,fin_customer WHERE our_company_id=$oc_id AND file_status!=3 AND  customer_name LIKE '%".$_REQUEST['term']."%' 
       AND fin_file.file_id=fin_customer.file_id ";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
foreach ($resultArray as $r) 
{
    $results[] = array('label' => $r['customer_name']);
}


	
echo json_encode($results); 

   




?>