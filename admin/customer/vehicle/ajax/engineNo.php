<?php require_once '../../../../lib/cg.php';
require_once '../../../../lib/bd.php';
require_once '../../../../lib/vehicle-functions.php';
?>

<?php
$oc_id=$_SESSION['adminSession']['oc_id'];
$value=$_GET['value'];

if(isset($_GET['vid']))
$vid=$_GET['vid'];
else
$vid=-1;


				
	$sql = "SELECT vehicle_id FROM fin_vehicle WHERE fin_vehicle.file_id=fin_file.file_id
		  AND file_status=1 AND vehicle_engine_no ='$value' ";	
	if($vid!=-1)
		$sql=$sql." AND vehicle_id!=$vid";
	$result=dbQuery($sql);


echo dbNumRows($result);
?>