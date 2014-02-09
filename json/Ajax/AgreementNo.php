<?php require_once '../../../lib/cg.php';
?>

<?php
$oc_id=$_SESSION['adminSession']['oc_id'];
$agency_id=$_GET['id'];
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
$sql = "SELECT file_agreement_no FROM fin_file WHERE our_company_id=$oc_id AND agency_id=$agency_id AND file_agreement_no='$value' AND file_status!=3
       ";
}
else if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql = "SELECT file_agreement_no FROM fin_file WHERE our_company_id=$oc_id AND oc_id=$our_company_id AND file_agreement_no ='$value' AND file_status!=3
       ";
	}
$result=dbQuery($sql);
if(dbnumrows($result)>0)	
echo "new String('failure')";
else
echo "new String('success')"; 
?>