<?php
require_once "../../../lib/cg.php";
require_once "../../../lib/bd.php";
require_once "../../../lib/city-functions.php";
require_once "../../../lib/area-functions.php";
require_once "../../../lib/agency-functions.php";
require_once "../../../lib/our-company-function.php";
require_once "../../../lib/customer-functions.php";
require_once "../../../lib/guarantor-functions.php";
require_once "../../../lib/bank-functions.php";
require_once "../../../lib/file-functions.php";
require_once "../../../lib/loan-functions.php";
require_once "../../../lib/vehicle-functions.php";
require_once "../../../lib/vehicle-insurance-functions.php";
require_once "../../../lib/insurance-company-functions.php";
require_once "../../../lib/vehicle-model-functions.php";
require_once "../../../lib/vehicle-dealer-functions.php";
require_once "../../../lib/vehicle-company-functions.php";
require_once "../../../lib/vehicle-type-functions.php";
require_once "../../../lib/addNewCustomer-functions.php";
require_once "../../../lib/adminuser-functions.php";

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
		$link="searchEMI";
		}
	else if($_GET['view']=='search')
	{
		$content="search.php";
		$link="searchEMI";
		}	
	else if($_GET['view']=='interest_certificate')
	{
		$showTitle=false;
		$content="interest_certificate.php";
		$link="searchEMI";
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
				
				$result=insertInsurance($_POST["issue_date"],$_POST['exp_date'],$_POST['idv'],$_POST['premium'],$_POST['insurance_company_id'],$_POST['vehicle_id'],$_POST['file_id'],$_POST['customer_id'],$_FILES['insuranceImg']);
				
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Insurance successfully added!";
				$_SESSION['ack']['type']=1; // 1 for insert
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$_POST['file_id']);
				exit;
				}
				else{
					
				$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
				$_SESSION['ack']['type']=4; // 4 for error
				header("Location: ".WEB_ROOT."admin/customer/EMI/index.php?id=".$_POST['file_id']."&state=".$_POST['customer_id']);
				exit;
				}
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
						unset($_SESSION['searchEMI']);
						header("Location: ".WEB_ROOT."admin/customer/EMI/index.php?view=details&id=".$file_id);
						exit;
						}
					else if(is_array($file_id))
					{
						$file_id_array=$file_id;
						$_SESSION['searchEMI']['file_id_array']=$file_id_array;
						$_SESSION['searchEMI']['parameter']="Agreement Number";
						$_SESSION['searchEMI']['value']=$agreement_number;
						header("Location: index.php?view=search");
						exit;
					}
					else{
						unset($_SESSION['searchEMI']);
						$_SESSION['ack']['msg']="Invalid Agreement Number!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: index.php?view=search");
						exit;
						
						}	
					}
				else if(validateForNull($file_number))
				{
					$file_id=getFileIdFromFileNo($file_number);
					if(checkForNumeric($file_id))
					{
						unset($_SESSION['searchEMI']);
						header("Location: ".WEB_ROOT."admin/customer/EMI/index.php?view=details&id=".$file_id);
						exit;
						}
					else if(is_array($file_id))
					{
						$file_id_array=$file_id;
						$_SESSION['searchEMI']['file_id_array']=$file_id_array;
						$_SESSION['searchEMI']['parameter']="File Number";
						$_SESSION['searchEMI']['value']=$agreement_number;
						header("Location: index.php?view=search");
						exit;
					}
					else{
						unset($_SESSION['searchEMI']);
						$_SESSION['ack']['msg']="Invalid File Number!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: index.php?view=search");
						exit;
						
						}	
					}
					else if(validateForNull($reg_number))
				{
					$file_id=getFileIdFromRegNo($reg_number);
					if(checkForNumeric($file_id))
					{
						unset($_SESSION['searchEMI']);
						header("Location: ".WEB_ROOT."admin/customer/EMI/index.php?view=details&id=".$file_id);
						exit;
						}
					else if(is_array($file_id))
					{
						$file_id_array=$file_id;
						$_SESSION['searchEMI']['file_id_array']=$file_id_array;
						$_SESSION['searchEMI']['parameter']="Registration Number";
						$_SESSION['searchEMI']['value']=$reg_number;
						header("Location: index.php?view=search");
						exit;
					}
					else{
						unset($_SESSION['searchEMI']);
						$_SESSION['ack']['msg']="Invalid Registration Number!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: index.php?view=search");
						exit;
						
						}	
					}		
				else if(validateForNull($name))
				{
					$file_id=getFileIdFromCustomerName($name);
					if(checkForNumeric($file_id))
					{
						unset($_SESSION['searchEMI']);
						header("Location: ".WEB_ROOT."admin/customer/EMI/index.php?view=details&id=".$file_id);
						exit;
						}
					else if(is_array($file_id))
					{
						
						$file_id_array=$file_id;
						$_SESSION['searchEMI']['file_id_array']=$file_id_array;
						if($file_id_array['nameType']=="like")
						$_SESSION['searchEMI']['parameter']="Customer Name Like";
						else
						$_SESSION['searchEMI']['parameter']="Customer Name";
						$_SESSION['searchEMI']['value']=$name;
						header("Location: index.php?view=search");
						exit;
					}
					else{
						unset($_SESSION['searchEMI']);
						$_SESSION['ack']['msg']="Invalid Customer Name!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: index.php?view=search");
						exit;
						
						}	
					}
				
				else if(validateForNull($mobile_number))
				{
					$file_id=getFileIdFromCustomerNo($mobile_number);
					if(checkForNumeric($file_id))
					{
						unset($_SESSION['searchEMI']);
						header("Location: ".WEB_ROOT."admin/customer/EMI/index.php?view=details&id=".$file_id);
						exit;
						}
					else if(is_array($file_id))
					{
						$file_id_array=$file_id;
						$_SESSION['searchEMI']['file_id_array']=$file_id_array;
						$_SESSION['searchEMI']['parameter']="Customer Name";
						$_SESSION['searchEMI']['value']=$mobile_number;
						header("Location: index.php?view=search");
						exit;
					}
					else{
						unset($_SESSION['searchEMI']);
						$_SESSION['ack']['msg']="Invalid Customer Name!";
						$_SESSION['ack']['type']=5; // 5 for access
						header("Location: index.php?view=search");
						exit;
						
						}	
					}
								
			}
			else
			{	
					$_SESSION['ack']['msg']="Minimum One Input is Required!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: index.php?view=search");
			exit;
			}

		}							
	}
?>

<?php
$pathLinks=array("Home","Registration Form","Manage Locations");
$selectedLink="searchEMI";
if(isset($link))
$selectedLink=$link;
$jsArray=array("jquery.validate.js","jquery-ui/js/jquery-ui.min.js","validators/searchCustomer.js","addInsuranceProof.js","customerDatePicker.js","validators/addNewInsurance.js");
$cssArray=array("jquery-ui.css","interest.css");
require_once "../../../inc/template.php";
?>