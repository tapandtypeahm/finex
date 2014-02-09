<?php
require_once "../../lib/cg.php";
require_once "../../lib/bd.php";
require_once "../../lib/city-functions.php";
require_once "../../lib/agency-functions.php";
require_once "../../lib/our-company-function.php";
require_once "../../lib/customer-functions.php";
require_once "../../lib/guarantor-functions.php";
require_once "../../lib/bank-functions.php";
require_once "../../lib/file-functions.php";
require_once "../../lib/loan-functions.php";
require_once "../../lib/vehicle-functions.php";
require_once "../../lib/vehicle-insurance-functions.php";
require_once "../../lib/vehicle-dealer-functions.php";
require_once "../../lib/vehicle-company-functions.php";
require_once "../../lib/vehicle-type-functions.php";
require_once "../../lib/addNewCustomer-functions.php";

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
		$content="list_add.php";
}		
if(isset($_GET['action']))
{
	if($_GET['action']=='add')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(2,$admin_rights) || in_array(7,$admin_rights)))
			{
			
				$result=addNewCustomer($_POST["agency_id"],$_POST['agreementNo'],$_POST['fileNumber'],$_POST['customer_name'],$_POST['customer_address'],$_POST['customer_city_id'],$_POST['customer_pincode'],$_POST['customerContact'],$_POST['customerProofId'],$_POST['customerProofNo'],$_FILES['customerProofImg'],$_POST['guarantor_name'],$_POST['guarantor_address'],$_POST['guarantor_city_id'],$_POST['guarantor_pincode'],$_POST['guarantorContact'],$_POST['guarantorProofId'],$_POST['guarantorProofNo'],$_FILES['guarantorProofImg'],$_POST['amount'],$_POST['duration'],$_POST['roi'],$_POST['emi'],$_POST['approvalDate'],$_POST['startingDate'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_amount'],$_POST['cheque_date'],$_POST['cheque_no'],$_POST['axin_no']);
				
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Customer successfully added!";
				$_SESSION['ack']['type']=1; // 1 for insert
				}
				else{
					
				$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
				$_SESSION['ack']['type']=4; // 4 for error
				}
				
				header("Location: ".$_SERVER['PHP_SELF']);
				exit;
			}
			else
			{	
					$_SESSION['ack']['msg']="Authentication Failed! Not enough access rights!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: ".$_SERVER['PHP_SELF']);
			exit;
			}
		}
	if($_GET['action']=='delete')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{	
				deleteCity($_GET["lid"]);
				
				$_SESSION['ack']['msg']="Item deleted Successfuly!";
				$_SESSION['ack']['type']=3; // 3 for delete
				
				header("Location: ".$_SERVER['PHP_SELF']);
				exit;
			}
			else
			{	
					$_SESSION['ack']['msg']="Authentication Failed! Not enough access rights!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: ".$_SERVER['PHP_SELF']);
			exit;
			}
		}
	if($_GET['action']=='edit')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
			{
				editLocation($_POST["lid"],$_POST["location"]);
				
				$_SESSION['ack']['msg']="Item updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				
				header("Location: ".$_SERVER['PHP_SELF']);
				exit;
			}
			else
			{	
					$_SESSION['ack']['msg']="Authentication Failed! Not enough access rights!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: ".$_SERVER['PHP_SELF']);
			exit;
			}
			
		}			
	}
?>

<?php

$pathLinks=array("Home","Registration Form","Manage Locations");
$selectedLink="newCustomer";
$jsArray=array("jquery.validate.js","Ajax/prefixFromAgencyCustomer.js","jquery-ui/js/jquery-ui.min.js","customerDatePicker.js","generateContactNoCustomer.js","generateContactNoGuarantor.js","addCustomerProof.js","generateProofimgCustomer.js","addGuarantorProof.js","generateProofimgGuarantor.js","validators/addNewCustomer.js");
$cssArray=array("jquery-ui.css");
require_once "../../inc/template.php";
 ?>