<?php
require_once "../../../lib/cg.php";
require_once "../../../lib/bd.php";
require_once "../../../lib/common.php";
require_once "../../../lib/loan-functions.php";


if(isset($_SESSION['adminSession']['admin_rights']))
$admin_rights=$_SESSION['adminSession']['admin_rights'];

if(isset($_GET['view']))
{
	if($_GET['view']=='add')
	{
		$content="list_add.php";
	}
	else if($_GET['view']=='details')
	{
		$content="details.php";
		}
	else
	{
		$content="list_add.php";
	}	
}
else
{
		$content="emiCalculator.php";
}		
if(isset($_GET['action']))
{
	
	if($_GET['action']=='calculate')
	{
		$amount=$_POST['amount'];
		$duration=$_POST['duration'];
		$reducing_roi=$_POST['reducing_roi'];
		$roi=getFlatRateFromReducing($amount,$reducing_roi,$duration);
		$irr_reducing=getReducingRateAndIRRFromFlat($amount,$duration,$roi);
		$emi=getEMIFromReducing($amount,$irr_reducing[0],$duration);
		$princ_interest_table=getIntPrincBalanceTable($amount,$duration,$irr_reducing[0],$emi);
		$_SESSION['remiCalc']['emi_array']=$returnArray;
		
		$_SESSION['remiCalc']['amount']=$amount;
		$_SESSION['remiCalc']['duration']=$duration;
		$_SESSION['remiCalc']['reducing_roi']=$irr_reducing[0];
		$_SESSION['remiCalc']['irr']=$irr_reducing[1];
		$_SESSION['remiCalc']['roi']=$roi;
		$_SESSION['remiCalc']['emi']=$emi;
		$_SESSION['remiCalc']['princ_interest_table']=$princ_interest_table;
		header("Location: index.php");		
		exit;
			
		}
	
	
	}
?>

<?php

$pathLinks=array("Home","Registration Form","Manage Locations");
$selectedLink="calc";
$jsArray=array("jquery.validate.js","jquery-ui/js/jquery-ui.min.js","validators/searchCustomer.js","validators/addNewCustomer.js");
$cssArray=array("jquery-ui.css");
require_once "../../../inc/template.php";
 ?>