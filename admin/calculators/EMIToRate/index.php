<?php
require_once "../../../lib/cg.php";
require_once "../../../lib/bd.php";
require_once "../../../lib/common.php";
require_once "../../../lib/customer-functions.php";
require_once "../../../lib/file-functions.php";
require_once "../../../lib/vehicle-functions.php";

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
	if($_GET['action']=='calc')
	{
		$start_amount=$_POST['start_amount'];
		$end_amount=$_POST['end_amount'];
		$amount_interval=$_POST['amount_interval'];
		$duration_interval=$_POST['month'];
		$roi=$_POST['roi'];
				$_SESSION['emiCalc']['start_amount']=$start_amount;
		$returnArray=array();
		$j=0;
		for($start_amount;$start_amount<=$end_amount;$start_amount=$start_amount+$amount_interval)
		{
			for($i=1;$i<11;$i++)
			{
				$duration=$duration_interval*$i;
				$division=$start_amount/$duration;
				$interest=$start_amount*$roi/(1200);
				$emi=$division+$interest;
				$emi=ceil($emi);
				
				$returnArray[$j][$i]=$emi;
				
			}
			$j++;
		}		
		$_SESSION['emiCalc']['emi_array']=$returnArray;
		
		$_SESSION['emiCalc']['end_amount']=$end_amount;
		$_SESSION['emiCalc']['amount_interval']=$amount_interval;
		$_SESSION['emiCalc']['duration_interval']=$duration_interval;
		$_SESSION['emiCalc']['roi']=$roi;
		header("Location: index.php");		
		exit;
			
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
$selectedLink="calc";
$jsArray=array("jquery.validate.js","jquery-ui/js/jquery-ui.min.js","validators/searchCustomer.js","validators/addNewCustomer.js");
$cssArray=array("jquery-ui.css");
require_once "../../../inc/template.php";
 ?>