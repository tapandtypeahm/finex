<?php
require_once '../lib/cg.php';
require_once '../lib/bd.php';


$sql="SELECT max(insurance_expiry_date),file_id FROM fin_vehicle_insurance GROUP BY file_id";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
print_r($resultArray);
 ?>