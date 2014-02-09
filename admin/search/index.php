<?php
require_once "../../lib/cg.php";
require_once "../../lib/bd.php";
require_once "../../lib/common.php";
require_once "../../lib/customer-functions.php";
require_once "../../lib/file-functions.php";
require_once "../../lib/vehicle-functions.php";

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
		$content="search.php";
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
	if($_GET['action']=='search')
	{
		$agreement_number=$_POST['agreementNo'];
		$file_number=$_POST['fileNumber'];
		$reg_number=$_POST['reg_no'];
		$mobile_number=$_POST['mobile_no'];
		$name=$_POST['name'];
		
		if(validateForNull($agreement_number) || validateForNull($file_number) || validateForNull($reg_number) || validateForNull($mobile_number) || validateForNull($name))
			{
				if(validateForNull($agreement_number))
				{
					$file_id=getFileIdFromAgreementNo($agreement_number);
					if(checkForNumeric($file_id))
					{
						unset($_SESSION['search']);
						header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$file_id);
						exit;
						}
					else if(is_array($file_id))
					{
						$file_id_array=$file_id;
						$_SESSION['search']['file_id_array']=$file_id_array;
						$_SESSION['search']['parameter']="Agreement Number";
						$_SESSION['search']['value']=$agreement_number;
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
					}
					else{
						unset($_SESSION['search']);
						$_SESSION['ack']['msg']="Invalid Agreement Number!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
						
						}	
					}
				else if(validateForNull($file_number))
				{
					$file_id=getFileIdFromFileNo($file_number);
					if(checkForNumeric($file_id))
					{
						unset($_SESSION['search']);
						header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$file_id);
						exit;
						}
					else if(is_array($file_id))
					{
						$file_id_array=$file_id;
						$_SESSION['search']['file_id_array']=$file_id_array;
						$_SESSION['search']['parameter']="File Number";
						$_SESSION['search']['value']=$agreement_number;
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
					}
					else{
						unset($_SESSION['search']);
						$_SESSION['ack']['msg']="Invalid File Number!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
						
						}	
					}
					else if(validateForNull($reg_number))
				{
					$reg_number=stripVehicleno($reg_number);
					$file_id=getFileIdFromRegNo($reg_number);
					if(checkForNumeric($file_id))
					{
						unset($_SESSION['search']);
						header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$file_id);
						exit;
						}
					else if(is_array($file_id))
					{
						$file_id_array=$file_id;
						$_SESSION['search']['file_id_array']=$file_id_array;
						$_SESSION['search']['parameter']="Registration Number";
						$_SESSION['search']['value']=$reg_number;
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
					}
					else{
						unset($_SESSION['search']);
						$_SESSION['ack']['msg']="Invalid Registration Number!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
						
						}	
					}		
					else if(validateForNull($name))
					{
					$file_id=getFileIdFromCustomerName($name);
					if(checkForNumeric($file_id))
					{
						unset($_SESSION['search']);
						header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$file_id);
						exit;
					}
					else if(is_array($file_id))
					{
						
						$file_id_array=$file_id;
						
						if(count($file_id_array)==1)
						{
						$_SESSION['search']['file_id_array']=$file_id_array;
						$_SESSION['search']['parameter']="Customer Name Like";
						$_SESSION['search']['value']=$name;
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;	
							}
						$_SESSION['search']['file_id_array']=$file_id_array;

						if($file_id_array['nameType']=="like")
						$_SESSION['search']['parameter']="Customer Name Like";
						else
						$_SESSION['search']['parameter']="Customer Name";
						$_SESSION['search']['value']=$name;
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
					}
					else{
						unset($_SESSION['search']);
						$_SESSION['ack']['msg']="Invalid Customer Name!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
						
						}	
					}
				else if(validateForNull($mobile_number))
				{
					$file_id=getFileIdFromCustomerNo($mobile_number);
					if(checkForNumeric($file_id))
					{
						unset($_SESSION['search']);
						header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$file_id);
						exit;
						}
					else if(is_array($file_id))
					{
						$file_id_array=$file_id;
						$_SESSION['search']['file_id_array']=$file_id_array;
						$_SESSION['search']['parameter']="Customer Name";
						$_SESSION['search']['value']=$mobile_number;
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
					}
					else{
						unset($_SESSION['search']);
						$_SESSION['ack']['msg']="Invalid Customer Name!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: ".$_SERVER['PHP_SELF']);
						exit;
						
						}	
					}
								
			}
			else
			{	
					$_SESSION['ack']['msg']="Minimum One Input is Required!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: ".$_SERVER['PHP_SELF']);
			exit;
			}
			
			
		}				
	}
?>

<?php

$pathLinks=array("Home","Registration Form","Manage Locations");
$selectedLink="searchCustomer";
$jsArray=array("jquery.validate.js","jquery-ui/js/jquery-ui.min.js","validators/searchCustomer.js");
$cssArray=array("jquery-ui.css");
require_once "../../inc/template.php";
 ?>