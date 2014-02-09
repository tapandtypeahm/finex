<?php require_once '../lib/cg.php';
?>

<?php
$oc_id=$_SESSION['adminSession']['oc_id'];

      
$sql = "SELECT file_agreement_no FROM fin_file WHERE our_company_id=$oc_id AND file_status!=3 AND file_agreement_no LIKE '%".ltrim($_REQUEST['term'], '0')."%' 
       ";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
foreach ($resultArray as $r) 
{
    $results[] = array('label' => $r['file_agreement_no']);
}


	
echo json_encode($results); 

?>