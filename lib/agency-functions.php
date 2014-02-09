<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("our-company-function.php");
require_once("common.php");
require_once("bd.php");
require_once 'EMI-functions.php';
		
function listAgencies(){
	
	try
	{
		$sql="SELECT agency_id, agency_name, agency_prefix, sub_heading, agency_contact_name, agency_contact_no, agency_address, auto_pay, auto_pay_date, file_counter, rasid_counter
		      FROM fin_agency ORDER BY agency_name";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		return $resultArray; 
	}
	catch(Exception $e)
	{
	}
	
}	
function getTotalNoOfAgencies(){
	
	try
	{
		$sql="SELECT count(agency_id) FROM fin_agency ORDER BY agency_name";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		return $resultArray[0][0]; 
	}
	catch(Exception $e)
	{
	}
	
}	
function insertAgency($name,$prefix,$auto_pay,$contact_person,$contact_no,$address,$subheading,$auto_pay_date){
	
	try
	{
		if(strlen($auto_pay_date)==1)
		$auto_pay_date="0".$auto_pay_date;
		
		if($auto_pay==1)
		{
		if(!checkForNumeric($auto_pay_date))
		return "error";
		if($auto_pay_date<=0 && $auto_pay_date>30)
		return "error";
		}
		else
		$auto_pay_date=0;
		
		$name=clean_data($name);
		$prefix=clean_data($prefix);
		$contact_person=clean_data($contact_person);
		$address=clean_data($address);
		if(validateForNull($name,$prefix) && !checkForDuplicateAgency($name,$prefix,$contact_person,$contact_no,$address) && checkForAlphaNumeric($prefix) && strlen($prefix)<5)
		{
		
			if(!checkForNumeric($contact_no))
			$contact_no=0;	
			
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$name = ucwords(strtolower($name));
		$contact_person = ucwords(strtolower($contact_person));
		$prefix=strtoupper($prefix);
		
		
		
		if($contact_person=="" || $contact_person==null)
		{
			$contact_person="NA";
			}
		if(!checkForNumeric($contact_no))
		{
			$contact_no=0;
		}
		if($address=="" || $address==null)
		{
			$address="NA";
			}		
		$sql="INSERT INTO fin_agency  					    
		     (agency_name ,agency_prefix, sub_heading, agency_contact_name, agency_contact_no, agency_address, auto_pay, auto_pay_date, created_by, last_updated_by, date_added, date_modified)
			  VALUES
			  ('$name', '$prefix', '$subheading' , '$contact_person', '$contact_no', '$address', $auto_pay ,'$auto_pay_date', $admin_id, $admin_id, NOW(), NOW())";
		$result=dbQuery($sql);	 
		$agency_id=dbInsertId();
		if(getAccountsStatus())
		{
		insertAccountSettingsForAgency($agency_id);
		}
		return "success";
		}
		else
		{return "error";}
	}
	catch(Exception $e)
	{
	}
	
}	

function insertAccountSettingsForAgency($agency_id)
{
	$account_starting_date=ACCOUNT_STARTING_DATE;
	$include_penalty=INCLUDE_PENALTY;
	$include_ac=INCLUDE_AC;
	
	if(checkForNumeric($agency_id,$include_penalty,$include_ac) && validateForNull($account_starting_date) && ACCOUNT_STATUS==1 && !getAccountSettingsForAgencyID($agency_id))
	{
		
		$sql="INSERT INTO fin_ac_settings (agency_id,ac_starting_date,include_penalty,include_ac) VALUES ($agency_id,'$account_starting_date',$include_penalty,$include_ac)";
		$result=dbQuery($sql);
		return "success";
		}
}

function getAccountSettingsForAgencyID($agency_id)
{
	if(checkForNumeric($agency_id))
	{
		$sql="SELECT agency_id,ac_starting_date,include_penalty,include_ac FROM fin_ac_settings WHERE agency_id=$agency_id";
		$result=dbQuery($sql);
		if(dbNumRows($result)>0)
		{
			$resultArray=dbResultToArray($result);
			return $resultArray[0][0];
		}
		else
		return false;
	}
	return false;
}

function deleteAgency($id){
	
	try
	{
		if(!checkIfAgencyInUse($id))
		{
		$sql="DELETE FROM fin_agency 
			   WHERE agency_id=$id";
		dbQuery($sql);	
		return "success";
		}
		else
		{
			return "error";
			}
	}
	catch(Exception $e)
	{
	}
	
}	

function updateAgency($id,$name,$prefix,$auto_pay,$rasid_counter,$contact_person,$contact_no,$address,$subheading,$auto_pay_date){
	
	try
	{
		if(checkForNumeric($id) && validateForNull($name,$prefix,$auto_pay_date) && !checkForDuplicateAgency($name,$prefix,$contact_person,$contact_no,$address,$id))
		{
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$name = ucwords(strtolower($name));
		$contact_person = ucwords(strtolower($contact_person));
		$prefix=strtoupper($prefix);
		
		$sql="UPDATE fin_agency  					    
		      SET agency_name =  '$name', agency_prefix = '$prefix', agency_contact_name = '$contact_person', agency_contact_no = '$contact_no', agency_address = '$address', auto_pay=$auto_pay,  last_updated_by = $admin_id, date_modified = NOW(), sub_heading='$subheading', auto_pay_date='$auto_pay_date'
			  WHERE agency_id=$id";
		$result=dbQuery($sql);
		return "success";
		}
		else
		{
			return "error";
			}
		
	}
	catch(Exception $e)
	{
	}
	
}	

function getAgencyById($id){
	
	try
	{
		$sql="SELECT 
		      agency_id, agency_name ,agency_prefix, agency_contact_name, agency_contact_no, agency_address, auto_pay, auto_pay_date, sub_heading, rasid_counter
			  FROM fin_agency 
			  WHERE agency_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0];
			
		}	  
	}
	catch(Exception $e)
	{
	}	
}	

function getAgencyHeadingById($id){
	
	try
	{
		$sql="SELECT 
		      sub_heading FROM fin_agency 
			  WHERE agency_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0 && validateForNull($resultArray[0][0]))
		{
			
			return $resultArray[0][0];
			
		}
		else
		{
			return getOurCompanyNameByID($_SESSION['adminSession']['oc_id']);
			}	  
	}
	catch(Exception $e)
	{
	}	
}	


function checkForDuplicateAgency($name,$prefix,$contact_person,$contact_no,$address,$id=false)
{
	try
	{
		if(validateForNull($prefix))
		{
		$prefix=clean_data($prefix);
		$sql="SELECT 
		      agency_id
			  FROM fin_agency 
			  WHERE agency_prefix = '$prefix' ";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND agency_id!=$id ";
		$sql=$sql." UNION SELECT 
		      our_company_id
			  FROM fin_our_company 
			  WHERE our_company_prefix = '$prefix'";
			  		  
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			
			return true;
			
		}	
		else
		{
			return false;
			} 
		}
		else
		return false;
	}
	catch(Exception $e)
	{
	}	
	
	
	}	
	
function checkIfAgencyInUse($id)
{
	$sql="SELECT agency_id
		FROM fin_file
		WHERE agency_id=$id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	return true;
	else
	return false;	
	}	

function getAgencyPrefixFromAgencyId($id)
{
	$sql="SELECT agency_prefix FROM fin_agency Where
			agency_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];		
}	

function getRasidnoForAgencyId($id)
{
	$sql="SELECT agency_prefix,rasid_counter FROM fin_agency Where
			agency_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0].$resultArray[0][1];		
}	

function getRasidCounterForAgencyId($id)
{
	$sql="SELECT rasid_counter FROM fin_agency Where
			agency_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];		
}	

function incrementRasidCounterForAgency($id)
{
	$r=getRasidCounterForAgencyId($id);
	$r++;
	$sql="UPDATE fin_agency
	      SET rasid_counter=$r
		  WHERE agency_id=$id";
	dbQuery($sql);	
}

function getAllAutoPaidAgencies()
{
	$sql="SELECT agency_id,auto_pay_date FROM fin_agency WHERE auto_pay=1";
	$rsult=dbQuery($sql);
	$rsultArray=dbResultToArray($rsult);
	if(dbNumRows($rsult)>0)
	return $rsultArray;
	else return false;
	}
	
function resetAllRasidCounters()
{
		$sql="UPDATE fin_agency SET rasid_reset_date=NOW(), rasid_counter=1";
		dbQuery($sql);
		return "success";
		}
	
function resetRasidCounterForAgency($agency_id)
{
	$sql="UPDATE fin_agency SET rasid_reset_date=NOW(), rasid_counter=1 WHERE agency_id=$agency_id";
		dbQuery($sql);
		return "success";
	}
function getRasidResetDateAgnecy()
{
	$sql="SELECT rasid_reset_date FROM fin_agency";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
	}	
	
function getAgecnyIdOrOCidNameFromAgnecySelectInput($agency_id) // input:: example ag2 or oc3 first ag stands for agency or our company and suffix stands for repective id agency or our comapny
{
	$type=substr($agency_id,0,2);
	$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$agnecy=getAgencyById($agency_id);
	return $agnecy['agency_name'];
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$oc=getOurCompanyByID($our_company_id);
	return $oc['our_company_name'];
}
	
	}	
	
function insertSettleFile($file_id,$amount,$settle_date,$receipt_no,$payment_mode,$noc_date,$remarks,$bank_name=false,$branch_name=false,$cheque_date=false,$cheque_no=false)
{
	
	$settle_date = str_replace('/', '-', $settle_date);
	$settle_date=date('Y-m-d',strtotime($settle_date));	
	
	$noc_date = str_replace('/', '-', $noc_date);
	$noc_date=date('Y-m-d',strtotime($noc_date));			
	if(checkForNumeric($file_id,$amount,$payment_mode) && validateForNull($settle_date,$receipt_no) && !checkForDuplicateSettleFile($file_id))
	{
		$admin_id=$_SESSION['adminSession']['admin_id']; 
		$sql="INSERT INTO fin_agency_settle(settle_amount,settle_date,receipt_no,payment_mode,noc_received_date,remarks,file_id,created_by,last_updated_by,date_added,date_modified)
		VALUES ($amount,'$settle_date','$receipt_no',$payment_mode,'$noc_date','$remarks',$file_id,$admin_id,$admin_id,NOW(),NOW())";
		dbQuery($sql);
		$settle_id=dbInsertId();
		if($payment_mode==2) // if cheque payment
		{
			insertSettleCheque($settle_id,$bank_name,$branch_name,$cheque_date,$cheque_no);
			}
		
		return "success";	
		}
		return "error";
}

function editSettleFile($settle_id,$amount,$settle_date,$receipt_no,$payment_mode,$noc_date,$remarks,$bank_name=false,$branch_name=false,$cheque_date=false,$cheque_no=false){
	
	
	if(checkForNumeric($settle_id,$amount,$payment_mode) && validateForNull($settle_date,$receipt_no))
	{
		$settle_date = str_replace('/', '-', $settle_date);
	$settle_date=date('Y-m-d',strtotime($settle_date));	
	
	$noc_date = str_replace('/', '-', $noc_date);
	$noc_date=date('Y-m-d',strtotime($noc_date));	
		
		$admin_id=$_SESSION['adminSession']['admin_id']; 
		$sql="UPDATE fin_agency_settle
		SET settle_amount = $amount, settle_date = '$settle_date', receipt_no = $receipt_no , payment_mode = $payment_mode ,noc_received_date = '$noc_date',remarks = '$remarks', last_updated_by = $admin_id, date_modified = NOW()
		WHERE settle_id = $settle_id";
		dbQuery($sql);
		deleteSettleCheque($settle_id);
		if($payment_mode==2) // if cheque payment
		{
			insertSettleCheque($settle_id,$bank_name,$branch_name,$cheque_date,$cheque_no);
			}
		
		return "success";	
		}
		return "error";
	
	}
function deleteSettleFile($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="DELETE FROM fin_agency_settle WHERE file_id=$file_id";
		dbQuery($sql);
		return "success";
		}
		return "error";
	
	}

function getSettleFileDetails($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT settle_id,settle_amount,settle_date,receipt_no,payment_mode,noc_received_date,remarks,file_id,created_by,last_updated_by,date_added,date_modified
		      FROM fin_agency_settle
			  WHERE file_id=$file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(isset($resultArray[0]))
		return $resultArray[0];
		else
		return "error";	  
		}
	
	}	

function checkForDuplicateSettleFile($file_id)
{
	if(checkForNumeric($file_id))
	{
		$settle_file=getSettleFileDetails($file_id);
		if($settle_file!="error")
		return true;
		else return false;
	}
	
	}	

function insertSettleCheque($settle_id,$bank_name,$branch_name,$cheque_date,$cheque_no)
{
	try
	{
		
		$admin_id=$_SESSION['adminSession']['admin_id'];
		if(validateForNull($bank_name,$branch_name))
		{
		$bankArray=insertIfNotDuplicateBank($bank_name,$branch_name);
		$bank_id=$bankArray[0];
		$branch_id=$bankArray[1];
		
			if(checkForNumeric($bank_id,$branch_id,$cheque_no,$settle_id) && validateForNull($cheque_date))
			{
			
			$cheque_date = str_replace('/', '-', $cheque_date);
			$cheque_date=date('Y-m-d',strtotime($cheque_date));	
				
			$sql="INSERT INTO fin_agency_settle_cheque
				  (bank_id, branch_id, cheque_date, cheque_no, settle_id)
				  VALUES
				  ($bank_id, $branch_id, '$cheque_date', '$cheque_no' , $settle_id)";
			$result=dbQuery($sql);
			
				return "success";	
			}
			else
			{
				return "error";
				}
		}
		else
		{
			return "error";
			}
	}
	catch(Exception $e)
	{
	}
}
	
function getSettleChequeBySettleId($settle_id)
{
	if(checkForNumeric($settle_id))
	{
	$sql="SELECT settle_cheque_id,bank_id, branch_id, cheque_date, cheque_no, settle_id FROM fin_agency_settle_cheque WHERE settle_id=$settle_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0];
	else 
	return false;
	}
}	
	
function updateSettleCheque($settle_cheque_id,$bank_name,$branch_name,$cheque_date,$cheque_no)
{
	
	try
	{
		
		$admin_id=$_SESSION['adminSession']['admin_id'];
		if(validateForNull($bank_name,$branch_name))
		{
		$bankArray=insertIfNotDuplicateBank($bank_name,$branch_name); // if bank and its branch is not in the database insert bank and branch
		$bank_id=$bankArray[0];
		$branch_id=$bankArray[1];
		
			if(checkForNumeric($bank_id,$branch_id,$cheque_no,$settle_cheque_id) && validateForNull($cheque_date))
			{
			
			$cheque_date = str_replace('/', '-', $cheque_date);
			$cheque_date=date('Y-m-d',strtotime($cheque_date));	
				
			$sql="UPDATE fin_agency_settle_cheque
				  SET bank_id = $bank_id, branch_id = $branch_id, cheque_date = '$cheque_date', cheque_no = '$cheque_no' 
				  WHERE settle_cheque_id=$settle_cheque_id";
			$result=dbQuery($sql);
			
				return "success";	
			}
			else
			{
				return "error";
				}
		}
		else
		{
			return "error";
			}
	}
	catch(Exception $e)
	{
	}
	
	
	}	
	
function deleteSettleCheque($settle_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="DELETE FROM fin_agency_settle_cheque WHERE settle_id=$settle_id";
		dbQuery($sql);
		}
}					
?>