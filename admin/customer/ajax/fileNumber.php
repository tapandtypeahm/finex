<?php require_once '../../../lib/cg.php';
require_once '../../../lib/bd.php';
require_once '../../../lib/agency-functions.php';
require_once '../../../lib/our-company-function.php';
require_once '../../../lib/file-functions.php';
?>

<?php
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
$prefix=getAgencyPrefixFromAgencyId($agency_id);
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
$prefix=getPrefixFromOCId($our_company_id);
}
$value=ltrim($value,'0');
$value=$prefix.$value;
$value=stripFileNo($value);

	$sql = "SELECT file_number FROM fin_file WHERE our_company_id=$oc_id AND  file_number ='$value' AND file_status!=3";
	if(isset($file_id) && is_numeric($file_id))
	$sql=$sql." AND file_id!=$file_id ";	
	$result=dbQuery($sql);


echo dbNumRows($result);
?>