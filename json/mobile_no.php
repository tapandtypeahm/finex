<?php require_once '../lib/cg.php';
?>

<?php
$oc_id=$_SESSION['adminSession']['oc_id'];

      
$sql = "SELECT customer_contact_no FROM fin_file,fin_customer,fin_customer_contact_no WHERE our_company_id=$oc_id AND customer_contact_no LIKE '%".$_REQUEST['term']."%' 
       AND fin_file.file_id=fin_customer.file_id AND fin_customer.customer_id=fin_customer_contact_no.customer_id AND file_status!=3";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
foreach ($resultArray as $r) 
{
    $results[] = array('label' => $r['customer_contact_no']);
}


	$results=array_unique($results);
echo json_encode($results); 

?>