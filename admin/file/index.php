<?php
require_once "../../lib/cg.php";
require_once "../../lib/bd.php";
require_once "../../lib/common.php";
require_once "../../lib/file-functions.php";
require_once "../../lib/adminuser-functions.php";
require_once "../../lib/loan-functions.php";
require_once "../../lib/agency-functions.php";
require_once "../../lib/our-company-function.php";
require_once "../../lib/bank-functions.php";
require_once "../../lib/currencyToWords.php";

if(isset($_SESSION['adminSession']['admin_rights']))
$admin_rights=$_SESSION['adminSession']['admin_rights'];

if(isset($_GET['view']))
{
	if($_GET['view']=='closeFile')
	{
		$content="closeFile.php";
	}
	else if($_GET['view']=='closureDetails')
	{
		$showTitle=false;
		$content="closuredetails.php";
		}
	else if($_GET['view']=='editClosure')
	{
		$content="editClosure.php";
		}	
}
	
if(isset($_GET['action']))
{
	if($_GET['action']=='close')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(2,$admin_rights) || in_array(7,$admin_rights)))
			{
			    
				$result=closeFile($_POST["close_date"],$_POST['amount'],$_POST['file_id'],$_POST['mode'],$_POST['rasid_no'],$_POST['remarks'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_no'],$_POST['cheque_date'],$_POST['bank_account'],$_POST['auto_rasid_no']);
				
				if($result=="success")
				{
				$_SESSION['ack']['msg']="File closed successfully!";
				$_SESSION['ack']['type']=1; // 1 for insert
				}
				else{
					
				$_SESSION['ack']['msg']="Unable to Close File!";
				$_SESSION['ack']['type']=4; // 4 for error
				}
				
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$_POST['file_id']);
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
		
		if($_GET['action']=='editClosure')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,$admin_rights)))
			{
			    
				$result=editCloseFile($_POST["close_date"],$_POST['amount'],$_POST['mode'],$_POST['rasid_no'],$_POST['file_id'],$_POST['remarks'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_no'],$_POST['cheque_date'],$_POST['bank_account']);
				
				if($result=="success")
				{
				$_SESSION['ack']['msg']="File closure details edited successfully!";
				$_SESSION['ack']['type']=1; // 1 for insert
				}
				else{
					
				$_SESSION['ack']['msg']="Unable to Edit File closure details!";
				$_SESSION['ack']['type']=4; // 4 for error
				}
				
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$_POST['file_id']);
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
		
	if($_GET['action']=='deleteClosure')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,$admin_rights)))
			{
			    
				$result=deleteClosure($_GET['id']);
				
				if($result=="success")
				{
				$_SESSION['ack']['msg']="File closure details deleted successfully!";
				$_SESSION['ack']['type']=1; // 1 for insert
				}
				else{
					
				$_SESSION['ack']['msg']="Unable to Delete File closure details!";
				$_SESSION['ack']['type']=4; // 4 for error
				}
				
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$_GET['id']);
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
$selectedLink="settings";
$jsArray=array("jquery.validate.js","jquery-ui/js/jquery-ui.min.js","validators/closeFile.js","customerDatePicker.js");
$cssArray=array("jquery-ui.css");
require_once "../../inc/template.php";
 ?>