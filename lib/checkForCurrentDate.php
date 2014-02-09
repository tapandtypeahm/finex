<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("area-functions.php");
require_once("account-head-functions.php");
require_once("account-period-functions.php");
require_once("adminuser-functions.php");
require_once("common.php");
require_once("bd.php");

if(isset($_POST))
{
	$current_Date=$_POST['current_date'];
	
	$admin_id=$_SESSION['adminSession']['admin_id'];
	
	if(validateForDate($current_Date)  && checkForNumeric($admin_id))
	{
	setCurrentDateForUser($admin_id,$current_Date);	
	}
}

header("location: ".$_SERVER['HTTP_REFERER']);
exit;

?>