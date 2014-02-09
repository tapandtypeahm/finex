<?php
require_once "../../lib/cg.php";
require_once "../../lib/bd.php";
require_once "../../lib/city-functions.php";
require_once "../../lib/area-functions.php";
require_once "../../lib/agency-functions.php";
require_once "../../lib/our-company-function.php";
require_once "../../lib/customer-functions.php";
require_once "../../lib/guarantor-functions.php";
require_once "../../lib/bank-functions.php";
require_once "../../lib/file-functions.php";
require_once "../../lib/loan-functions.php";
require_once "../../lib/vehicle-functions.php";
require_once "../../lib/vehicle-insurance-functions.php";
require_once "../../lib/insurance-company-functions.php";
require_once "../../lib/vehicle-model-functions.php";
require_once "../../lib/vehicle-dealer-functions.php";
require_once "../../lib/vehicle-company-functions.php";
require_once "../../lib/vehicle-type-functions.php";
require_once "../../lib/addNewCustomer-functions.php";
require_once "../../lib/adminuser-functions.php";
require_once "../../lib/broker-functions.php";

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
		$link="searchCustomer";
		}
	else if($_GET['view']=='EMIdetails')
	{
		$content="emiDetails.php";
		$link="searchCustomer";
		}	
	else if($_GET['view']=='fileDetails')
	{
		$content="fileDetails.php";
		$link="searchCustomer";
		}
	else if($_GET['view']=='editFile')
	{
		$content="fileEdit.php";
		$link="searchCustomer";
		}			
	else if($_GET['view']=='customerDetails')
	{
		$content="customerDetails.php";
		$link="searchCustomer";
		}	
	else if($_GET['view']=='editCustomer')
	{
		$content="edit.php";
		$link="searchCustomer";
		}				
	else if($_GET['view']=='guarantorDetails')
	{
		$content="guarantorDetails.php";
		$link="searchCustomer";
		}	
	else if($_GET['view']=='editGuarantor')
	{
		$content="guarantorEdit.php";
		$link="searchCustomer";
		}
	else if($_GET['view']=='addGuarantor')
	{
		$content="guarantorAdd.php";
		$link="searchCustomer";
		}		
	else if($_GET['view']=='editLoan')
	{
		$content="loanEdit.php";
		$link="searchCustomer";
		}		
	else if($_GET['view']=='loanDetails')
	{
		$content="loanDetails.php";
		$link="searchCustomer";
		}	
	else if($_GET['view']=='insuranceDetails')
	{
		$content="emiDetails.php";
		$link="searchCustomer";
		}	
	else if($_GET['view']=='penaltyDetails')
	{
		$content="penaltyDetails.php";
		$link="searchCustomer";
		}	
	else if($_GET['view']=='additionalPaymentDetails')
	{
		$content="additionalPaymentDetails.php";
		$link="searchCustomer";
		}		
	else if($_GET['view']=='addRemainder')
	{
		$content="addRemainder.php";
		$link="searchCustomer";
		}
	else if($_GET['view']=='editRemainder')
	{
		
		$content="remainderEdit.php";
		$link="searchCustomer";
		}	
	else if($_GET['view']=='addCompanyPaidDate')
	{
		$content="addCompanyPaidDate.php";
		$link="searchCustomer";
		}
	else if($_GET['view']=='editCompanyPaidDate')
	{
		
		$content="CompanyPaidDateEdit.php";
		$link="searchCustomer";
		}		
	else if($_GET['view']=='chequeReturnDetails')
	{
		
		$content="chequeReturnDetails.php";
		$link="searchCustomer";
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
				if(isset($_POST['customerProofImg']))
				{
					$scanCustomerProof=$_POST['customerProofImg'];
					}
				else
				{
					$scanCustomerProof=false;
					}
				if(isset($_POST['guarantorProofImg']))
				{
					$scanGuarantorProof=$_POST['guarantorProofImg'];
					}
				else
				{
					$scanGuarantorProof=false;
					}	
				
				if(isset($_FILES['customerProofImg']))
				{
					$CustomerProof=$_FILES['customerProofImg'];
					}
				else
				{
					$CustomerProof=false;
					}
				if(isset($_FILES['guarantorProofImg']))
				{
					$GuarantorProof=$_FILES['guarantorProofImg'];
					}
				else
				{
					$GuarantorProof=false;
					}		
				if(isset($_POST['loan_scheme']))
				{
					if($_POST['loan_scheme']==1)
					{
					$emi=$_POST['emi'];	
					$duration=$_POST['duration'];	
						}
					else if($_POST['loan_scheme']==2)
					{
					$emi=$_POST['emi_uneven'];	
					$duration=$_POST['duration_uneven'];	
						}	
				}	
				$result=addNewCustomer($_POST["agency_id"],$_POST['agreementNo'],$_POST['fileNumber'],$_POST['broker_id'],$_POST['customer_name'],$_POST['customer_address'],$_POST['customer_city_id'],$_POST['customer_area'],$_POST['customer_pincode'],$_POST['customerContact'],$_POST['customerProofId'],$_POST['customerProofNo'],$CustomerProof,$scanCustomerProof,$_POST['guarantor_name'],$_POST['guarantor_address'],$_POST['guarantor_city_id'],$_POST['guarantor_area'],$_POST['guarantor_pincode'],$_POST['guarantorContact'],$_POST['guarantorProofId'],$_POST['guarantorProofNo'], $GuarantorProof , $guarantorProofImg,$_POST['amount'],$_POST['loan_amount_type'],$duration,$_POST['loan_type'],$_POST['loan_scheme'],$_POST['roi'],$emi,$_POST['approvalDate'],$_POST['startingDate'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_amount'],$_POST['cheque_date'],$_POST['cheque_no'],$_POST['axin_no'],$_POST['bank_account'],$_POST['agency_amount'],$_POST['agency_emi'],$_POST['agency_duration'],$_POST['duration_unit']);
				
				if(is_numeric($result))
				{
				$_SESSION['ack']['msg']="Customer successfully added!";
				$_SESSION['ack']['type']=1; // 1 for insert
				header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$result);
				exit;
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
	if($_GET['action']=='editFile')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
			{
				$result=updateFile($_POST["lid"],$_POST["agency_id"],$_POST['agreementNo'],$_POST['fileNumber'],$_POST['broker_id']);
				if($result=="success")
				{
				$_SESSION['ack']['msg']="File updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=fileDetails&id=".$_POST["lid"]);
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
	if($_GET['action']=='deleteFile')
	{
		
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{
				
				$result=deletetFile($_GET["id"]);
				if($result=="success")
				{
				$_SESSION['ack']['msg']="File Deleted Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
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
	if($_GET['action']=='editCustomer')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
			{
				$result=updateCustomer($_POST["lid"],$_POST['customer_name'],$_POST['customer_address'],$_POST['customer_city_id'],$_POST['customer_area'],$_POST['customer_pincode'],$_POST['customerContact'],$_POST['customerProofId'],$_POST['customerProofNo'],$_FILES['customerProofImg'],$_POST['customerProofImg'],$_POST['paid_by']);
	
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Customer updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=customerDetails&id=".$_POST["file_id"]);
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
	if($_GET['action']=='delCustomerProof')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{
				$result=deleteCustomerProof($_GET["state"]);
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Customer Proof Deleted Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Unable to delete Customer Proof!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=customerDetails&id=".$_GET["id"]);
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
	if($_GET['action']=='editGuarantor')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
			{
				
				$result=updateGuarantor($_POST["lid"],$_POST['guarantor_name'],$_POST['guarantor_address'],$_POST['guarantor_city_id'],$_POST['guarantor_area'],$_POST['guarantor_pincode'],$_POST['guarantorContact'],$_POST['guarantorProofId'],$_POST['guarantorProofNo'],$_FILES['guarantorProofImg'],$_POST['guarantorProofImg']);
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Guarantor updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=guarantorDetails&id=".$_POST["file_id"]);
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
	if($_GET['action']=='addGuarantor')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
			{
				if(isset($_POST['guarantorProofImg']))
				$scanImage=$_POST['guarantorProofImg'];
				else
				$scanImage=false;
				$result=insertGuarantor($_POST['guarantor_name'],$_POST['guarantor_address'],$_POST['guarantor_city_id'],$_POST['guarantor_area'],$_POST['guarantor_pincode'],$_POST['file_id'],$_POST['customer_id'],$_POST['guarantorContact'],$_POST['guarantorProofId'],$_POST['guarantorProofNo'],$_FILES['guarantorProofImg'],$scanImage);
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Guarantor added Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=guarantorDetails&id=".$_POST["file_id"]);
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
	if($_GET['action']=='delGuarantorProof')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{
				$result=deleteGuarantorProof($_GET["state"]);
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Guarantor Proof Deleted Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Unable to delete Guarantor Proof!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=guarantorDetails&id=".$_GET["id"]);
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
		if($_GET['action']=='editLoan')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,$admin_rights)))
			{
				if(isset($_POST['loan_scheme']))
				{
					if($_POST['loan_scheme']==1)
					{
					$emi=$_POST['emi'];	
					$duration=$_POST['duration'];	
						}
					else if($_POST['loan_scheme']==2)
					{
					$emi=$_POST['emi_uneven'];	
					$duration=$_POST['duration_uneven'];	
						}	
					}	
				
				$amount=$_POST['amount'];
				$result=updateLoan($_POST["lid"],$amount,$_POST['loan_amount_type'],$duration,$_POST['loan_type'],$_POST['loan_scheme'],$_POST['roi'],$emi,$_POST['approvalDate'],$_POST['startingDate'],$_POST['bank_name'],$_POST['branch_name'],$_POST['cheque_amount'],$_POST['cheque_date'],$_POST['cheque_no'],$_POST['axin_no'],$_POST['bank_account'],$_POST['agency_amount'],$_POST['agency_emi'],$_POST['agency_duration'],$_POST['duration_unit']);
	
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Loan updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else if($result="paymentError")
				{
					$_SESSION['ack']['msg']="Cannot Change Amount,ROI,EMI,Duration,etc! Please Delete All Payments to make changes to loan!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=loanDetails&id=".$_POST["file_id"]);
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
		if($_GET['action']=='addRemainder')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(2,$admin_rights) || in_array(7,					$admin_rights)))
			{
			
				$result=addRemainder($_POST["lid"],$_POST['remainderDate'],$_POST['remarks']);
	
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Remainder Added Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=addRemainder&id=".$_POST["lid"]);
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
		if($_GET['action']=='editRemainder')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
			{
			
				$result=editRemainDer($_POST["lid"],$_POST['remainderDate'],$_POST['remarks']);
	
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Remainder Updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=addRemainder&id=".$_POST["file_id"]);
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
		if($_GET['action']=='deleteRemainder')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,$admin_rights) || in_array(8,$admin_rights)))
			{
			
				$result=deleteRemainder($_GET["lid"]);
				
				if($result=="success")
				{	
				$_SESSION['ack']['msg']="Remainder Deleted Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=addRemainder&id=".$_GET["id"]);
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
		if($_GET['action']=='addCompanyPaidDate')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(2,$admin_rights) || in_array(7,					$admin_rights)))
			{
			
				$result=addCompanyPaymentDate($_POST["lid"],$_POST['remainderDate']);
	
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Remainder Added Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				if(isset($_POST['return']) && $_POST['return']=='emiDetails')
				{
					
					header("Location: ".$_SERVER['PHP_SELF']."?view=EMIdetails&id=".$_POST["file_id"]."&state=".$_POST['lid']);
					exit;
				}
				else
				{	
				header("Location: ".$_SERVER['PHP_SELF']."?view=details&id=".$_POST["file_id"]);
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
		if($_GET['action']=='editCompanyPaidDate')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
			{
			
				$result=addCompanyPaymentDate($_POST["lid"],$_POST['remainderDate']);
	
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Company Paid Date Updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				if(isset($_POST['return']) && $_POST['return']=='emiDetails')
				{
					header("Location: ".$_SERVER['PHP_SELF']."?view=EMIdetails&id=".$_POST["file_id"]."&state=".$_POST['lid']);
					exit;
				}
				else
				{	
				header("Location: ".$_SERVER['PHP_SELF']."?view=details&id=".$_POST["file_id"]);
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
		if($_GET['action']=='deleteCompanyPaidDate')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{
			
				$result=DeleteCompanyPaymentDate($_GET["lid"]);
				
				if($result=="success")
				{	
				$_SESSION['ack']['msg']="Company Paid Date Deleted Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				if(isset($_GET['return']) && $_GET['return']=='emiDetails')
				{
					header("Location: ".$_SERVER['PHP_SELF']."?view=EMIdetails&id=".$_GET["id"]."&state=".$_GET['lid']);
					exit;
				}
				else
				{	
				header("Location: ".$_SERVER['PHP_SELF']."?view=details&id=".$_GET["id"]);
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
		if($_GET['action']=='doneRemainderGeneral')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{
			
				$result=setDoneRemainderGeneral($_GET["lid"]);
				
				if($result=="success")
				{	
				$_SESSION['ack']['msg']="Remainder Updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=addRemainder&id=".$_GET["id"]);
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
		if($_GET['action']=='unDoneRemainderGeneral')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{
			
				$result=setUnDoneRemainderGeneral($_GET["lid"]);
				
				if($result=="success")
				{	
				$_SESSION['ack']['msg']="Remainder Updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
			
				header("Location: ".$_SERVER['PHP_SELF']."?view=addRemainder&id=".$_GET["id"]);
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
		if($_GET['action']=='doneRemainderPayment')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{
			
				$result=setDoneRemainderPayment($_GET["lid"]);
				
				if($result=="success")
				{	
				$_SESSION['ack']['msg']="Remainder Updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				header("Location: ".$_SERVER['PHP_SELF']."?view=EMIdetails&id=".$_GET["id"]."&state=".$_GET['state']);
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
		if($_GET['action']=='unDoneRemainderPayment')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{
			
				$result=setUnDoneRemainderPayment($_GET["lid"]);
				
				if($result=="success")
				{	
				$_SESSION['ack']['msg']="Remainder Updated Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				
				header("Location: ".$_SERVER['PHP_SELF']."?view=EMIdetails&id=".$_GET["id"]."&state=".$_GET['state']);
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
		if($_GET['action']=='deleteChequeReturn')
		{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
			{
			    
				$result=deleteChequeReturnById($_GET["lid"]);
				
				if($result=="success")
				{	
				$_SESSION['ack']['msg']="Cheque Return Deleted Successfuly!";
				$_SESSION['ack']['type']=2; // 2 for update
				}
				else
				{
					$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
					$_SESSION['ack']['type']=4; // 4 for error
					
					}
				if(isset($_GET['return']) && $_GET['return']=='emiDetails')
				{
					header("Location: ".$_SERVER['PHP_SELF']."?view=EMIdetails&id=".$_GET["id"]."&state=".$_GET['lid']);
					exit;
				}
				else
				{	
				header("Location: ".$_SERVER['PHP_SELF']."?view=chequeReturnDetails&id=".$_GET["id"]);
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
}
?>

<?php

$pathLinks=array("Home","Registration Form","Manage Locations");
$selectedLink="newCustomer";
if(isset($link))
$selectedLink=$link;
$jsArray=array("jquery.validate.js","dropDown.js","scanProof.js","generateEMIDuration.js","checkAvailability.js","Ajax/prefixFromAgencyCustomer.js","Ajax/calculatePenalty.js","jquery-ui/js/jquery-ui.min.js","customerDatePicker.js","generateContactNoCustomer.js","generateContactNoGuarantor.js","addCustomerProof.js","generateProofimgCustomer.js","addGuarantorProof.js","generateProofimgGuarantor.js","validators/addNewCustomer.js");
$cssArray=array("jquery-ui.css");
require_once "../../inc/template.php";
 ?>