<?php
require_once "../../../../lib/cg.php";
require_once "../../../../lib/bd.php";
require_once "../../../../lib/loan-functions.php";
require_once "../../../../lib/bank-functions.php";
require_once "../../../../lib/file-functions.php";
require_once "../../../../lib/customer-functions.php";
require_once "../../../../lib/vehicle-functions.php";
require_once "../../../../lib/currencyToWords.php";
require_once "../../../../lib/agency-functions.php";
require_once "../../../../lib/our-company-function.php";
unset($_SESSION['ack']);


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
		$showTitle=false; // to turn off the company title on the top of the page
		}
	else if($_GET['view']=='payments')
	{
		
		$content="payments.php";
		
		}	
	else if($_GET['view']=='edit')
	{
		$content="edit.php";
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
			
				$result=addPenaltyToLoan(0,$_POST["payment_date"],$_POST['mode'],0,$_POST['rasid_no'],$_POST['paid_by'],$_POST['loan_id'],$_POST['file_id'],$_POST['amount'],$_POST['rasid_type_id'],$_POST['paid'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_no'],$_POST['cheque_date']);			
			
				if($result!="error")
				{	
				$_SESSION['ack']['msg']="Payment successfully added!";
				$_SESSION['ack']['type']=1; // 1 for insert
				header("Location: ".WEB_ROOT."/admin/customer/payment/additional_charges/index.php?view=details&print_rasid=yes&id=".$_POST['file_id']."&state=".$result);
				exit;
				}
				else
				{	
				$_SESSION['ack']['msg']="Days More than Allowed OR Duplicate Entry!";
				$_SESSION['ack']['type']=4; // 4 for error
				header("Location: ".WEB_ROOT."admin/customer/payment/additional_charges/index.php?id=".$_POST['file_id']."&state=".$_POST['loan_id']);
				exit;
				}
			}
			else
			{	
					$_SESSION['ack']['msg']="Authentication Failed! Not enough access rights!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: ".WEB_ROOT."admin/customer/index.php?view=EMIdetails&id=".$_POST['file_id']."&state=".$_POST['emi_id']);
				exit;
			}
		}
	if($_GET['action']=='delete')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{	
				$result=deletePenalty($_GET["lid"]);
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Penalty deleted Successfuly!";
				$_SESSION['ack']['type']=3; // 3 for delete
				header("Location: ".WEB_ROOT."admin/customer/payment/additional_charges/index.php?view=payments&id=".$_GET['id']."&state=".$_GET['state']);
				exit;
				}
			}
			else
			{	
					$_SESSION['ack']['msg']="Authentication Failed! Not enough access rights!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: ".WEB_ROOT."admin/customer/index.php?view=penaltyDetails&id=".$_GET['id']."&state=".$_GET['state']);
				exit;
			}
		}
	if($_GET['action']=='edit')
	{
		
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
			{
				
				$result=editPenalty($_POST["lid"],0,$_POST["payment_date"],$_POST['mode'],0,$_POST['rasid_no'],$_POST['paid_by'],$_POST['loan_id'],$_POST['file_id'],$_POST['amount'],$_POST['rasid_type_id'],$_POST['paid'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_no'],$_POST['cheque_date']);
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Item updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				header("Location: ".WEB_ROOT."admin/customer/payment/additional_charges/index.php?view=payments&id=".$_POST['file_id']."&state=".$_POST['loan_id']);
				exit;
				}
				else{
					
				$_SESSION['ack']['msg']="Days More than Allowed OR Duplicate Entry!";
				$_SESSION['ack']['type']=4; // 4 for error
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=penaltyDetails&id=".$_POST['file_id']."&state=".$_POST['loan_id']);
				exit;
				}
			}
			else
			{	
					$_SESSION['ack']['msg']="Authentication Failed! Not enough access rights!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: ".WEB_ROOT."admin/customer/index.php?view=EMIdetails&id=".$_POST['file_id']."&state=".$_POST['emi_id']);
				exit;
			}
			
	}
	
	}
?>

<?php

$pathLinks=array("Home","Registration Form","Manage Locations");
$selectedLink="searchCustomer";
if(isset($link))
$selectedLink=$link;
$jsArray=array("jquery.validate.js","jquery-ui/js/jquery-ui.min.js","addInsuranceProof.js","customerDatePicker.js","validators/addPayment.js");
$cssArray=array("jquery-ui.css");

require_once "../../../../inc/template.php";
 ?>