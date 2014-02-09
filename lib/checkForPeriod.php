<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("area-functions.php");
require_once("account-head-functions.php");
require_once("account-period-functions.php");
require_once("our-company-function.php");
require_once("agency-functions.php");
require_once("adminuser-functions.php");
require_once("common.php");
require_once("bd.php");

if(isset($_POST))
{
	$from=$_POST['from_period'];
	$to=$_POST['to_period'];
	$admin_id=$_SESSION['adminSession']['admin_id'];
	
	if(validateForDate($from) && validateForDate($to) && checkForNumeric($admin_id))
	{
	setPeriodForUser($admin_id,$from,$to);	
	}
	
	$current_Date=$_POST['current_date'];
	
	if(validateForDate($current_Date)  && checkForNumeric($admin_id))
	{
	setCurrentDateForUser($admin_id,$current_Date);	
	}
	
	$agency_id=$_POST['agency_id'];
	
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
	$agency_id=substr($agency_id,2);
	if($type=="ag")
	{
	$company_type=1;
	}
	else if($type=="oc")
	{
	$company_type=0;
	}	
	if(validateForDate($current_Date)  && checkForNumeric($admin_id))
	{
	setCurrentCompanyForUser($admin_id,$agency_id,$company_type);	
	}
}

header("location: ".$_SERVER['HTTP_REFERER']);
exit;

?>