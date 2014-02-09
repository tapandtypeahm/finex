<?php require_once '../../../lib/cg.php';
require_once '../../../lib/bd.php';

$oc_id=$_SESSION['adminSession']['oc_id'];
$agency_id=$_GET['id'];
if(isset($_GET['fid']))
$file_id=$_GET['fid'];
$value=$_GET['value'];


$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
if($our_company_id=="NULL" && is_numeric($agency_id))
{      
$sql = "SELECT file_agreement_no FROM fin_file WHERE our_company_id=$oc_id AND agency_id=$agency_id AND file_agreement_no='$value' AND file_status!=3";
if(isset($file_id) && is_numeric($file_id))
$sql=$sql." AND file_id!=$file_id ";	
}
else if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql = "SELECT file_agreement_no FROM fin_file WHERE our_company_id=$oc_id AND oc_id=$our_company_id AND file_agreement_no ='$value' AND file_status!=3"; 
     if(isset($file_id) && is_numeric($file_id))
	$sql=$sql." AND file_id!=$file_id ";	  
	}	

$result=dbQuery($sql);
echo dbNumRows($result);
?>