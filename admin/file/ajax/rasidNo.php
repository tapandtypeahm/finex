<?php require_once '../../../lib/cg.php';
require_once '../../../lib/bd.php';
require_once '../../../lib/agency-functions.php';
require_once '../../../lib/our-company-function.php';
require_once '../../../lib/loan-functions.php';
require_once '../../../lib/file-functions.php';
?>

<?php

$ourcompany_id=$_SESSION['adminSession']['oc_id'];
$reset_date=$_SESSION['adminSession']['reset_date'];
$agency_id=$_GET['agency_id'];
$oc_id=$_GET['oc_id'];
$file_id=$_GET['file_id'];
$value=$_GET['value']; // rasid_no
$old=$_GET['old']; // old_rasid_no for edit

$ag_id_array=getAgencyOrCompanyIdFromFileId($file_id);
if($ag_id_array[0]=='agency')
		{
			$agency_id=$ag_id_array[1];
			$oc_id=null;
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			$reset_date=getRasidResetDateAgnecy();
			}
		else if($ag_id_array[0]=='oc')
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_prefix=getPrefixFromOCId($oc_id);
			$reset_date=getRasidResetDateAgnecy();
			}
			
$value=$rasid_prefix.$value;
if($old!=$value)
{
	$sql = "SELECT emi_payment_id FROM fin_loan_emi_payment,fin_file,fin_loan,fin_loan_emi WHERE
	        fin_file.file_id=fin_loan.file_id
			AND fin_loan.loan_id=fin_loan_emi.loan_id
			AND fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
	 		AND our_company_id=$ourcompany_id AND rasid_no='$value' ";
	if(isset($reset_date) && validateForNull($reset_date) && $reset_date!=false)
	$sql=$sql."AND payment_date>='$reset_date'";
	else
	$sql=$sql."))";	
	$result=dbQuery($sql);
	echo dbNumRows($result);
}
else
echo 0;

?>