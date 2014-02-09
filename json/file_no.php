<?php require_once '../lib/cg.php';
require_once '../lib/common.php';
require_once '../lib/file-functions.php';
?>

<?php
$oc_id=$_SESSION['adminSession']['oc_id'];
$file_no=stripFileNo($_REQUEST['term']);
$file_no=clean_data($file_no);
  
$sql = "SELECT file_number FROM fin_file WHERE our_company_id=$oc_id AND file_status!=3 AND file_number LIKE '%".$file_no."%' 
       ORDER BY file_number";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
foreach ($resultArray as $r) 
{
    $results[] = array('label' => $r['file_number']);
}


	
echo json_encode($results); 

   




?>