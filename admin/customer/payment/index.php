<?php
require_once "../../../lib/cg.php";
require_once "../../../lib/bd.php";
require_once "../../../lib/loan-functions.php";
require_once "../../../lib/file-functions.php";
require_once "../../../lib/customer-functions.php";
require_once "../../../lib/vehicle-functions.php";
require_once "../../../lib/bank-functions.php";
require_once "../../../lib/currencyToWords.php";
require_once "../../../lib/agency-functions.php";
require_once "../../../lib/our-company-function.php";
require_once "../../../lib/account-ledger-functions.php";

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
	else if($_GET['view']=='search')
	{
		$content="search.php";
		
		}	
	else if($_GET['view']=='edit')
	{
		$content="edit.php";
		}	
	else if($_GET['view']=='addMultiple')
	{
		$content="add_multiple.php";
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
				if(isset($_POST['form_identifier']))
				{
					$form_identifier=$_POST['form_identifier'];
					if(in_array($form_identifier,$_SESSION['paymentSession']['identifiers']))
					{
						
						$result="error";
						}
					else
					{
						
						$_SESSION['paymentSession']['identifiers'][]=$_POST['form_identifier'];
						$result=insertPayment($_POST['emi_id'],$_POST["amount"],$_POST['mode'],$_POST['payment_date'],$_POST['rasid_no'],$_POST['remarks'],$_POST['remainder_date'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_no'],$_POST['cheque_date'],0,$_POST['bank_account'],$_POST['paid_by'],$_POST['auto_rasid_no']); // $cheque_return is 0 when inserting a payment			
				
						}	
					
					}
				
				if($result!="error")
				{
					
					/*if(isset($_POST['return']) && $_POST['return']==1)
					{
						$_SESSION['ack']['msg']="Payment successfully added!";
						$_SESSION['ack']['type']=1; // 1 for insert
						header("Location: ".WEB_ROOT."admin/customer/payment/index.php?view=details&id=".$_POST['file_id']."&state=".$_POST['emi_id']);
						exit;
						
					} */
					
					$_SESSION['ack']['msg']="Payment successfully added!";
					$_SESSION['ack']['type']=1; // 1 for insert
					$loan_id=getLoanIdFromEmiId($_POST['emi_id']);
					closeFileIfBalanceZero($loan_id);
					header("Location: ".WEB_ROOT."admin/customer/payment/index.php?view=details&print_rasid=yes&id=".$_POST['file_id']."&state=".$_POST['emi_id']."&lid=".$result);
					exit;
				}
				else
				{
					
				$_SESSION['ack']['msg']="Payment Exceeds the Balance OR Duplicate Entry! OR Duplicate Rasid No";
				$_SESSION['ack']['type']=4; // 4 for error
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=EMIdetails&id=".$_POST['file_id']."&state=".$_POST['emi_id']);
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
	if($_GET['action']=='addMultiple')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(2,$admin_rights) || in_array(7,$admin_rights)))
			{
				if(isset($_POST['form_identifier']))
				{
					$form_identifier=$_POST['form_identifier'];
					if(in_array($form_identifier,$_SESSION['paymentSession']['identifiers']))
					{
						$result="error";
						}
					else
					{
						$_SESSION['paymentSession']['identifiers'][]=$_POST['form_identifier'];
				$result=insertManyPayments($_POST['emi_id'],$_POST["amount"],$_POST['mode'],$_POST['payment_date'],$_POST['rasid_no'],$_POST['remarks'],$_POST['remainder_date'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_no'],$_POST['cheque_date'],0,0); // 0 for cheque_return while inserting payment			
					}
				}
				if($result=="success")
				{
					
					$loan_id=getLoanIdFromEmiId($_POST['emi_id']);
					closeFileIfBalanceZero($loan_id);
					if(isset($_POST['return']) && $_POST['return']==1)
					{
						
						$_SESSION['ack']['msg']="Payment successfully added!";
						$_SESSION['ack']['type']=1; // 1 for insert
						header("Location: ".WEB_ROOT."admin/customer/index.php?view=EMIdetails&id=".$_POST['file_id']."&state=".$_POST['emi_id']);
						exit;
						
					}
					$_SESSION['ack']['msg']="Payment successfully added!";
					$_SESSION['ack']['type']=1; // 1 for insert
					header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$_POST['file_id']);
					exit;
				}
				else
				{
					
				$_SESSION['ack']['msg']="Payment Exceeds the Balance OR Duplicate Entry! OR Duplicate Rasid No";
				$_SESSION['ack']['type']=4; // 4 for error
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=EMIdetails&id=".$_POST['file_id']."&state=".$_POST['emi_id']);
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
			$loan_id=getLoanIdFromEmiPaymentId($_GET['lid']);
				$result=deletePayment($_GET["lid"]);
				if($result=="success")
				{
					
				
				closeFileIfBalanceZero($loan_id);	
				$_SESSION['ack']['msg']="Payment deleted Successfuly!";
				$_SESSION['ack']['type']=3; // 3 for delete
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=EMIdetails&id=".$_GET['id']."&state=".$_GET['state']);
				exit;
				}
			}
			else
			{	
					$_SESSION['ack']['msg']="Authentication Failed! Not enough access rights!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: ".WEB_ROOT."admin/customer/index.php?view=EMIdetails&id=".$_GET['id']."&state=".$_GET['state']);
				exit;
			}
		}
	if($_GET['action']=='edit')
	{
		
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
			{
				
				
				$result=editPayment($_POST["lid"],$_POST['emi_id'],$_POST["amount"],$_POST['mode'],$_POST['payment_date'],$_POST['rasid_no'],$_POST['remarks'],$_POST['remainder_date'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_no'],$_POST['cheque_date'], $_POST["cheque_return"],$_POST['bank_account'],$_POST['paid_by']);
				if($result=="success")
				{
				$loan_id=getLoanIdFromEmiId($_POST['emi_id']);
					closeFileIfBalanceZero($loan_id);	
				$_SESSION['ack']['msg']="Item updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=EMIdetails&id=".$_POST['file_id']."&state=".$_POST['emi_id']);
				exit;
				}
				else{
					
				$_SESSION['ack']['msg']="Payment Exceeds the Balance OR Duplicate Entry!";
				$_SESSION['ack']['type']=4; // 4 for error
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=EMIdetails&id=".$_POST['file_id']."&state=".$_POST['emi_id']);
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

require_once "../../../inc/template.php";
 ?>