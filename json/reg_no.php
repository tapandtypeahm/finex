<?php require_once '../lib/cg.php';
 require_once '../lib/vehicle-functions.php';
?>

<?php
$oc_id=$_SESSION['adminSession']['oc_id'];

$regno=$_REQUEST['term'];
$regno=stripVehicleno($regno);   
$sql = "SELECT vehicle_reg_no FROM fin_file,fin_vehicle WHERE our_company_id=$oc_id AND vehicle_reg_no LIKE '%".$regno."%' 
       AND fin_file.file_id=fin_vehicle.file_id AND file_status!=3";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
foreach ($resultArray as $r) 
{
    $results[] = array('label' => $r['vehicle_reg_no']);
}


	
echo json_encode($results); 

   




?>