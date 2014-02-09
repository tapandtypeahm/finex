<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("loan-functions.php");
require_once("agency-functions.php");
require_once("our-company-function.php");
require_once("common.php");
require_once("bd.php");
require_once("vehicle-functions.php");
require_once('EMI-functions.php');
require_once("customer-functions.php");
require_once("loan-functions.php");
require_once("bank-functions.php");
		
function listFilesForAgency($id){
	
	try
	{
		if(checkForNumeric($id))
		{
		$oc_id=$_SESSION['adminSession']['oc_id'];	
		$sql="SELECT fin_file.file_id, file_number, file_agreement_no, customer_id, customer_name, customer_address, city_id, area_id, opening_balance, opening_cd
		      FROM fin_file, fin_customer 
			  WHERE agency_id = $id  AND our_company_id = $oc_id AND fin_file.file_id = fin_customer.file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		for($i=0;$i<count($resultArray);$i++)
		{
			$re=$resultArray[$i];
			$customer_id=0;
			$customer_id=$re['customer_id'];
			$contact_nos=getCustomerContactNo($customer_id);
			$resultArray[$i]['contact_no'] = $contact_nos; 
			}	  
		return $resultArray;	
		}
	}
	catch(Exception $e)
	{
	}
	
}	

function listFilesForOurCompany($id){
	
	try
	{
		if(checkForNumeric($id))
		{
		$oc_id=$_SESSION['adminSession']['oc_id'];	
		$sql="SELECT fin_file.file_id, file_number, file_agreement_no, customer_id, customer_name, customer_address, city_id, area_id
		      FROM fin_file, fin_customer 
			  WHERE oc_id = $id  AND our_company_id = $oc_id AND fin_file.file_id = fin_customer.file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		for($i=0;$i<count($resultArray);$i++)
		{
			$re=$resultArray[$i];
			$customer_id=0;
			$customer_id=$re['customer_id'];
			$contact_nos=getCustomerContactNo($customer_id);
			$resultArray[$i]['contact_no'] = $contact_nos; 
			}	  
		return $resultArray;	
		}
	}
	catch(Exception $e)
	{
	}
	
}	

function insertFile($agency_id, $agreement_no, $file_number, $OC_id, $broker_id, $remarks=false, $remainder_date=false){
	
	try
	{
	
		$original_agency_id=$agency_id;
		$our_company_id=NULL;
		$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
$prefix=getAgencyPrefixFromAgencyId($agency_id);
$current_rasid_counter=getRasidCounterForAgencyId($agency_id);
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
$prefix=getPrefixFromOCId($our_company_id);
$current_rasid_counter=getRasidCounterForOCID($our_company_id);
}	


		
		$agreement_no=clean_data($agreement_no);
		$file_number=clean_data($file_number);
		$file_number=ltrim($file_number,'0');
		$file_number=$prefix.$file_number;
		$file_number=stripFileNo($file_number);
		if($remarks!=false)
		$remarks=clean_data($remarks);
		if($remainder_date!=false)
		$remainder_date=clean_data($remainder_date);
		if($agency_id!="NULL")
		{
			$agreement_no_validate=validateForNull($agreement_no);
			}
		else
		{
			$agreement_no_validate=true;
			}	
		
		if(checkForNumeric($OC_id,$broker_id) && $agreement_no_validate && $file_number!=NULL && $file_number!="" && !checkForDuplicateFile($original_agency_id, $agreement_no, $file_number, $OC_id))
		{
			
			$admin_id=$_SESSION['adminSession']['admin_id'];
			$file_status=1; //file is open ie.1 and 2 is for closed file
						
			$sql="INSERT INTO fin_file
				  (agency_id, oc_id, file_agreement_no, file_number, our_company_id, broker_id,created_by, last_updated_by, file_status, date_added, date_modified)
				  VALUES
				  ($agency_id, $our_company_id,  '$agreement_no', '$file_number', $OC_id, $broker_id, $admin_id, $admin_id, $file_status, NOW(), NOW())";
			dbQuery($sql);
			$file_id=dbInsertId();	 
			if($remarks!=false && $remainder_date!=false)
				{
				addRemarksToFile($file_id,$remainder_date,$remarks);
				} 
			release_rasid();	
			return $file_id;	
		}
		else
		{
			release_rasid();	
			return false;
		}
	}
	catch(Exception $e)
	{
	}
	
}	

function deletetFile($id){
	
	try
	{
		if(checkForNumeric($id))
		{
			
			
			$sql="UPDATE fin_file SET file_status=3 WHERE file_id=$id";
			dbQuery($sql);
			return "success";
			}
		else 
		return "error";	
	}
	catch(Exception $e)
	{
	}
	
}	

function updateFile($id,$agency_id, $agreement_no, $file_number,$broker_id){
	
	try
	{
		$OC_id=$_SESSION['adminSession']['oc_id'];
		$original_agency_id=$agency_id;
		$our_company_id="NULL";
		$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
$prefix=getAgencyPrefixFromAgencyId($agency_id);
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
$prefix=getPrefixFromOCId($our_company_id);
}
		
		$agreement_no=clean_data($agreement_no);
		$file_number=clean_data($file_number);
		$file_number=$prefix.$file_number;
		$file_number=stripFileNo($file_number);
		if($agency_id!="NULL")
		{
			$agreement_no_validate=validateForNull($agreement_no);
			}
		else
		{
			$agreement_no_validate=true;
			}	
			
		if(checkForNumeric($broker_id) && $agreement_no_validate && $file_number!=NULL && $file_number!="" && !checkForDuplicateFile($original_agency_id, $agreement_no, $file_number, $OC_id, $id))
		{
		
		$admin_id=$_SESSION['adminSession']['admin_id'];		
		$sql="UPDATE fin_file
		     SET agency_id = $agency_id, oc_id=$our_company_id, file_agreement_no = '$agreement_no', file_number = '$file_number',  last_updated_by = $admin_id, date_modified = NOW(), broker_id=$broker_id
			 WHERE file_id=$id";
			dbQuery($sql);
		
		if($type=="oc")
		{
		$loan_id=getLoanIdFromFileId($id);
		deleteLoanSchemeAgency($loan_id);
		}
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

function getFileById($id){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function checkForDuplicateFile($agency_id, $agreement_no, $file_number, $OC_id, $id=false)
{
	try
	{
		
		$our_company_id=NULL;
		$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id=NULL;
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id=NULL;	
}

if(checkForNumeric($agency_id))
{
		
		$sql="SELECT file_id
		      FROM fin_file
			  WHERE ((file_number='$file_number' AND (!(agency_id=$agency_id AND file_agreement_no='$agreement_no'))) OR (agency_id=$agency_id AND file_agreement_no='$agreement_no'))
			  AND our_company_id=$OC_id
			  AND file_status!=3 ";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND file_id!=$id";
		
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		
		if(dbNumRows($result)>0)
		{
			
			$_SESSION['error']['submit_error']="Duplicate Entry!";
			return true;
			} 
		else
		{
			return false;
			}
}
else if(checkForNumeric($our_company_id))
{
	$sql="SELECT file_id
		      FROM fin_file
			  WHERE file_number='$file_number' AND oc_id=$our_company_id
			  AND our_company_id=$OC_id
			  AND file_status!=3 ";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND file_id!=$id";		  
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			$_SESSION['error']['submit_error']="Duplicate Entry!";
			return true;
			} 
		else
		{
			return false;
			}
	
	}
else
{
	return true;
	}	
	}
	catch(Exception $e)
	{
	}	
}



function addRemarksToFile($id,$dates,$descriptions)
{
	try
	{
		
		if(is_array($dates))
		{
			for($i=0;$i<count($dates);$i++)
			{
				$date=$dates[$i];
				$des=$descriptions[$i];
				insertRemark($id,$date,$des);  
			}
		}
		else
		{
			insertRemark($id,$dates,$descriptions);  
		}
	}
	catch(Exception $e)
	{
	}
	
}

function insertRemark($id,$date,$description)
{
	try
	{
			
		$description=clean_data($description);
		$admin_id=$_SESSION['adminSession']['admin_id'];
		if(($date!=NULL && $date!="") || ($description!=NULL && $description!=""))
		$sql="INSERT INTO fin_file_remarks
				      (file_id, reminder_date, description, created_by, last_updated_by, date_added, date_modified)
					  VALUES
					  ($id, '$date', '$description', $admin_id, $admin_id, NOW(), NOW())";
		dbQuery($sql);	   
	}
	catch(Exception $e)
	{}	
	
	}	

function deleteRemark($id)
{
	try
	{
		$sql="DELETE FROM fin_file_remarks
		      WHERE file_remark_id=$id";
		dbQuery($sql);	  
	}
	catch(Exception $e)
	{}	
}

function deleteAllRemarksForFile($id)
{
	try
	{
		$sql="DELETE FROM fin_file_remarks
		      WHERE file_id=$id";
		dbQuery($sql);	  
	}
	catch(Exception $e)
	{}	
}

function getRemarkById($id)
{
	try
	{
		$sql="SELECT file_remark_id, file_id, reminder_date, description
			  FROM fin_file_remarks
		      WHERE file_remark_id=$id";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0];
		}
	}
	catch(Exception $e)
	{}	
	
}

function getAllRemarksForFile($id)
{
	try
	{
		$sql="SELECT file_remark_id, file_id, reminder_date, description
			  FROM fin_file_remarks
		      WHERE file_id=$id";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray;
		}
	}
	catch(Exception $e)
	{}	
	
}

function getFileDetailsByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$oc_id=$_SESSION['adminSession']['oc_id'];
		$sql="SELECT agency_id, oc_id, file_agreement_no, file_number, our_company_id, broker_id, created_by, file_status, date_added
		      FROM fin_file
			  WHERE file_id=$file_id
			  AND our_company_id=$oc_id
			  ";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0];
			}	  
		else
		{
			return "error";
			}	
		}

		
	
}

function getFullFileDetailsByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$oc_id=$_SESSION['adminSession']['oc_id'];
		$sql="SELECT  file_agreement_no, file_number, fin_file.broker_id, broker_name, fin_file.created_by, admin_username, file_status, fin_file.date_added
		      FROM fin_file
			  INNER JOIN fin_broker
			  on fin_file.broker_id=fin_broker.broker_id
			  INNER JOIN fin_admin
			  on fin_file.created_by=admin_id
			  WHERE file_id=$file_id
			  AND our_company_id=$oc_id
			  ";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0];
			}	  
		else
		{
			return "error";
			}	
		}
}
function getFileNumberByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$oc_id=$_SESSION['adminSession']['oc_id'];
		$sql="SELECT   file_number
		      FROM fin_file
			  WHERE file_id=$file_id
			  AND our_company_id=$oc_id
			  AND file_status!=3";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0][0];
			}	  
		else
		{
			return "error";
			}	
		}
	
}
function getFileIdFromAgreementNo($agreement_no)
{
	if(validateForNull($agreement_no))
	{
		$oc_id=$_SESSION['adminSession']['oc_id'];
		$agreement_no=clean_data($agreement_no);
		$sql="SELECT file_id
		      FROM fin_file
			  WHERE file_agreement_no='$agreement_no'
			  AND our_company_id=$oc_id
			  AND file_id!=3";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)==1)
		{
			return $resultArray[0][0];
		}
		else if(dbNumRows($result)>1)
		{
			return $resultArray;
		}
		else
		{
			return "error";
			}		  
		}
}

function getFileIdFromFileNo($file_no)
{
	
	if(validateForNull($file_no))
	{
		$oc_id=$_SESSION['adminSession']['oc_id'];
		$file_no=clean_data($file_no);
		$sql="SELECT file_id
		      FROM fin_file
			  WHERE file_number='$file_no'
			  AND our_company_id=$oc_id
			  AND file_status!=3";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)==1)
		{
			
			return $resultArray[0][0];
		}
		else if(dbNumRows($result)>1)
		{
			return $resultArray;
		}
		else
		{
			return "error";
			}		  
		}
	}

function getFileSearchResultDetailsFromFileId($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT file_agreement_no,file_number
		      FROM fin_file
			  WHERE file_id=$id
			  AND file_status!=3";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			$file=$resultArray[0];
			$customer=getCustomerDetailsByFileId($id);
			$reg_no=getRegNoFromFileID($id);
			$returnArray=array();
			$returnArray['file_array']=$file;
			$returnArray['customer_array']=$customer;
			$returnArray['reg_no']=$reg_no;
			return $returnArray;
			}
		}
	}
	
function getAgencyNameFromFileId($id)
{
	
	$sql="SELECT agency_id,oc_id FROM fin_file WHERE file_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		$agency_id=$resultArray[0][0];
		$oc_id=$resultArray[0][1];
		
		if(is_numeric($agency_id))
		{
			$agency=getAgencyById($agency_id);
			return $agency['agency_name'];
			}
		else if(is_numeric($oc_id))
		{
			return getOurCompanyNameByID($oc_id);
			}
				
		}
		return false;
}

function getAgencyOrCompanyIdFromFileId($id)
{
	
	$sql="SELECT agency_id,oc_id FROM fin_file WHERE file_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		$agency_id=$resultArray[0][0];
		$oc_id=$resultArray[0][1];
		
		if(is_numeric($agency_id))
		return array('agency',$agency_id);
		else if(is_numeric($oc_id))
		return array('oc',$oc_id);
				
		}
		return false;
}
	
function addRemainder($file_id,$date,$remarks)
{
	if(!validateForNull($date))
	{
		$date='1970-01-01';
		}
	$remarks=clean_data($remarks);	
	if(checkForNumeric($file_id) && validateForNull($date,$remarks))
	{
	$date = str_replace('/', '-', $date);
			$date=date('Y-m-d',strtotime($date));	
	$sql="INSERT INTO fin_file_remainder(file_id,date,remarks) VALUE ($file_id,'$date','$remarks')";
	$result=dbQuery($sql);
	return "success";
	
	}
	else
	return "error";
}
	
function listRemainderForFile($file_id)
{
	
	if(checkForNumeric($file_id))
	{
		$sql="SELECT remainder_id,date,remarks,remainder_status FROM fin_file_remainder WHERE file_id=$file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		
		if(dbNumRows($result)>0)
		return $resultArray;
		else return false;
	}			
}

function listRemarksForFile($file_id)
{
	
	if(checkForNumeric($file_id))
	{
		$sql="SELECT remainder_id,date,remarks,remainder_status FROM fin_file_remainder WHERE file_id=$file_id AND remainder_status=0 ORDER BY date";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		
		if(dbNumRows($result)>0)
		return $resultArray;
		else return false;
	}			
}

function listOnlyRemaindersForFile($file_id)
{
	
	if(checkForNumeric($file_id))
	{
		$sql="SELECT remainder_id,date,remarks,remainder_status FROM fin_file_remainder WHERE file_id=$file_id AND (date!='1970-01-01' AND date!='0000-00-00')";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		
		if(dbNumRows($result)>0)
		return $resultArray;
		else return false;
	}			
}


function getRemainderById($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT remainder_id,date,remarks,file_id FROM fin_file_remainder WHERE remainder_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else return false;
	}		
	
	
}

function editRemainDer($id,$date,$remarks)
{
	$remarks=clean_data($remarks);
	if(checkForNumeric($id))
	{
		$date = str_replace('/', '-', $date);
			$date=date('Y-m-d',strtotime($date));
		$sql="UPDATE fin_file_remainder SET date='$date', remarks='$remarks' WHERE remainder_id=$id";
		$result=dbQuery($sql);
		return "success";
	}		
	else
	return "error";
	
}

function deleteRemainder($id)
{
	if(checkForNumeric($id))
	{
		
		$sql="DELETE FROM fin_file_remainder WHERE remainder_id=$id";
		$result=dbQuery($sql);
		return "success";
	}		
	else
	return "error";
	
}

function setDoneRemainderGeneral($id)
{
	if(checkForNumeric($id))
	{
		$sql="UPDATE fin_file_remainder SET remainder_status=1 WHERE remainder_id=$id";
		$result=dbQuery($sql);
		return "success";
		}
	else return "error";	
	
	}

function setUnDoneRemainderGeneral($id)
{
	if(checkForNumeric($id))
	{
		$sql="UPDATE fin_file_remainder SET remainder_status=0 WHERE remainder_id=$id";
		$result=dbQuery($sql);
		return "success";
		}
	else return "error";	
	
	}	

function setDoneRemainderPayment($id)
{
	if(checkForNumeric($id))
	{
		$rasid_identifier=getRasidIdentifierForPaymentId($id);
		$sql="UPDATE fin_loan_emi_payment SET remainder_status=1";
		if(is_numeric($rasid_identifier) && $rasid_identifier==0)
		$sql=$sql." WHERE emi_payment_id=$id OR rasid_identifier=$id";
		else if(is_numeric($rasid_identifier) && $rasid_identifier!=0)
		$sql=$sql." WHERE emi_payment_id=$rasid_identifier OR rasid_identifier=$rasid_identifier";
		$result=dbQuery($sql);
		return "success";
		}
	else return "error";	
	
	}

function setUnDoneRemainderPayment($id)
{
	if(checkForNumeric($id))
	{
		$rasid_identifier=getRasidIdentifierForPaymentId($id);
		$sql="UPDATE fin_loan_emi_payment SET remainder_status=0";
		if(is_numeric($rasid_identifier) && $rasid_identifier==0)
		$sql=$sql." WHERE emi_payment_id=$id OR rasid_identifier=$id";
		else if(is_numeric($rasid_identifier) && $rasid_identifier!=0)
		$sql=$sql." WHERE emi_payment_id=$rasid_identifier OR rasid_identifier=$rasid_identifier";
		$result=dbQuery($sql);
		return "success";
		}
	else return "error";	
	
}		

function closeFile($close_date,$amount,$file_id,$mode,$rasid_no,$remarks="NA",$bank_name=false,$branch_name=false,$cheque_no=false,$cheque_date=false,$ledger_id=0,$auto_rasid_no=0)
{
	
		while(check_rasid()==false)
		{
			sleep(1);
			}	
		lock_rasid();	
	if(!validateForNull($remarks))
	$remarks="";
	if(validateForNull($close_date) && checkForNumeric($amount,$file_id,$mode) && !checkForDuplicateClosedFile($file_id))
	{
		if(!validateForNull($rasid_no))
		$rasid_no="NA";
		else
		{
		$ag_id_array=getAgencyOrCompanyIdFromFileId($file_id);
		if($ag_id_array[0]=='agency')
		{
		$agency_id=$ag_id_array[1];
		$oc_id=null;
		$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
		$current_rasid_counter=getRasidCounterForAgencyId($agency_id);
		}
		else if($ag_id_array[0]=='oc')
		{				
		$oc_id=$ag_id_array[1];
		$agency_id=null;
		$rasid_prefix=getPrefixFromOCId($oc_id);
		$current_rasid_counter=getRasidCounterForOCID($oc_id);
		}
		if($auto_rasid_no==$rasid_no)
		{
			
			$rasid_no=$current_rasid_counter;
			}		
		$or_rasid_no=$rasid_no;		
		$rasid_no=$rasid_prefix.$rasid_no;
		}
		$close_date = str_replace('/', '-', $close_date);
		$close_date=date('Y-m-d',strtotime($close_date));	
		$loan=getLoanDetailsByFileId($file_id);
		$loan_id=$loan['loan_id'];
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="INSERT INTO fin_file_closed(file_close_date,amount_paid,mode,rasid_no,file_id,loan_id,remarks,date_closed,closed_by,last_updated_by,date_modified)
		VALUES ('$close_date',$amount,$mode,'$rasid_no',$file_id,$loan_id,'$remarks',NOW(),$admin_id,$admin_id,NOW())";
		dbQuery($sql);
		$file_close_id=dbInsertId();
		
		$file_status=getFileStatusforFile($file_id);
		if($file_status==1)
		{
		$sql="UPDATE fin_file SET file_status=4 WHERE file_id=$file_id";
		dbQuery($sql);
		}
		else if($file_status==2)
		{
			$sql="UPDATE fin_file SET file_status=4 WHERE file_id=$file_id";
		dbQuery($sql);
			}
		
		if(checkForNumeric($cheque_no) && validateForNull($cheque_date) && $mode==2 && $bank_name!=false && $bank_name!="" && $bank_name!=null && $branch_name!=false && $branch_name!="" && $branch_name!=null)
					{
						
						$bank_array=insertIfNotDuplicateBank($bank_name,$branch_name);
						$bank_id=$bank_array[0];
						$branch_id=$bank_array[1];
						
						insertClosureCheuqe($file_close_id,$bank_id,$branch_id,$cheque_no,$cheque_date,$ledger_id);
						
					}
		if($rasid_no!="NA")
		{
		if($ag_id_array[0]=='agency')
						{
						$rasid_counter=getRasidCounterForAgencyId($agency_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidCounterForAgency($agency_id);
					}
					else if($ag_id_array[0]=='oc')
						{
					$rasid_counter=getRasidCounterForOCID($oc_id);
					if($rasid_counter==$or_rasid_no)
					incrementRasidNoForOCID($oc_id);
					}
		}
		release_rasid();
		return "success";
		}
	else
	{
	release_rasid();	
	return "error";	
	}
}

function getFileStatusforFile($file_id)
{
	$sql="SELECT file_status FROM fin_file WHERE file_id=$file_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	return $resultArray[0][0];
}

function insertClosureCheuqe($file_close_id,$bank_id,$branch_id,$cheque_no,$cheque_date,$ledger_id)	
{
	
	if(checkForNumeric($bank_id,$branch_id,$cheque_no,$file_close_id) && $cheque_date!=null && $cheque_date!="")
		{
		
		$closure_cheque_details=getClosureChequeDetails($file_close_id);	
		if($closure_cheque_details)
		{
			editClosureCheuqe($file_close_id,$bank_id,$branch_id,$cheque_no,$cheque_date,$ledger_id);
			return;
			}
		$cheque_date = str_replace('/', '-', $cheque_date);
		$cheque_date=date('Y-m-d',strtotime($cheque_date));			
		$sql="INSERT INTO fin_file_closed_cheque
		      (bank_id, branch_id, cheque_no, cheque_date, file_closed_id, ledger_id)
			  VALUES
			  ($bank_id, $branch_id, $cheque_no, '$cheque_date', $file_close_id, $ledger_id)";
		dbQuery($sql);	  
		}
	
	}

function editClosureCheuqe($file_close_id,$bank_id,$branch_id,$cheque_no,$cheque_date,$ledger_id=0)	
{
	if(checkForNumeric($bank_id,$branch_id,$cheque_no,$file_close_id) && $cheque_date!=null && $cheque_date!="")
		{
		$cheque_date = str_replace('/', '-', $cheque_date);
			$cheque_date=date('Y-m-d',strtotime($cheque_date));			
		$sql="UPDATE fin_file_closed_cheque
		      SET bank_id=$bank_id, branch_id=$branch_id, cheque_no=$cheque_no, cheque_date='$cheque_date', ledger_id=$ledger_id
			  WHERE file_closed_id=$file_close_id";
		dbQuery($sql);	  
		}
	
	}


function getClosureChequeDetails($file_close_id)
{
	if(checkForNumeric($file_close_id))
	{
		$sql="SELECT bank_id, branch_id, cheque_no, cheque_date, file_closed_id
		      FROM fin_file_closed_cheque
			  WHERE file_closed_id=$file_close_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];	
		else return false;  
		}
	
	}	

function editCloseFile($close_date,$amount,$mode,$rasid_no,$file_id,$remarks="NA",$bank_name=false,$branch_name=false,$cheque_no=false,$cheque_date=false,$ledger_id=0)
{
	if(!validateForNull($rasid_no))
		$rasid_no="NA";
		else
		{
		$ag_id_array=getAgencyOrCompanyIdFromFileId($file_id);
		if($ag_id_array[0]=='agency')
		{
		$agency_id=$ag_id_array[1];
		$oc_id=null;
		$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
		}
		else if($ag_id_array[0]=='oc')
		{
						
		$oc_id=$ag_id_array[1];
		$agency_id=null;
		$rasid_prefix=getPrefixFromOCId($oc_id);
		}
		$or_rasid_no=$rasid_no;		
		$rasid_no=$rasid_prefix.$rasid_no;
		}
	if(!validateForNull($remarks))
	$remarks="";
	if(validateForNull($close_date,$rasid_no) && checkForNumeric($amount,$file_id,$mode))
	{
		
		
		$close_date = str_replace('/', '-', $close_date);
		$close_date=date('Y-m-d',strtotime($close_date));	
		
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="UPDATE fin_file_closed SET file_close_date='$close_date',amount_paid=$amount,mode=$mode,rasid_no='$rasid_no',remarks='$remarks',last_updated_by=$admin_id,date_modified=NOW()
	          WHERE file_id=$file_id";
		dbQuery($sql);
		
		$sql="UPDATE fin_file SET file_status=4 WHERE file_id=$file_id";
		dbQuery($sql);
		
		if($mode==1)
		{
			$closure_datesil=getPrematureClosureDetails($file_id);
			$file_closed_id=$closure_datesil['file_closed_id'];
			$sql="DELETE FROM fin_file_closed_cheque WHERE file_closed_id=$file_closed_id";
			
			dbQuery($sql);
			}
		else if($mode==2)
		{
			
			if(checkForNumeric($cheque_no) && validateForNull($cheque_date) && $mode==2 && $bank_name!=false && $bank_name!="" && $bank_name!=null && $branch_name!=false && $branch_name!="" && $branch_name!=null)
					{
						$closure_datesil=getPrematureClosureDetails($file_id);
			            $file_closed_id=$closure_datesil['file_closed_id'];
						$bank_array=insertIfNotDuplicateBank($bank_name,$branch_name);
						$bank_id=$bank_array[0];
						$branch_id=$bank_array[1];
				
						insertClosureCheuqe($file_closed_id,$bank_id,$branch_id,$cheque_no,$cheque_date,$ledger_id);
						
					}
			
			}	
		if($rasid_no!="NA")
		{	
		if($ag_id_array[0]=='agency')
						{
						$rasid_counter=getRasidCounterForAgencyId($agency_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidCounterForAgency($agency_id);
					}
					else if($ag_id_array[0]=='oc')
						{
					$rasid_counter=getRasidCounterForOCID($oc_id);
					if($rasid_counter==$or_rasid_no)
					incrementRasidNoForOCID($oc_id);
					}	
		}
		
		
		return "success";
		}
	else
	return "error";	
	
	}

function checkForDuplicateClosedFile($file_id)
{
	$sql="SELECT file_closed_id FROM fin_file_closed WHERE file_id=$file_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return true;
		}
	else 
	return false;	
	}	
	
function getPrematureClosureAmount($file_id)
{
	$sql="SELECT amount_paid FROM fin_file_closed WHERE file_id=$file_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0][0];
		}
	else 
	return 0;	
	
	}
	
function getPrematureClosureDetails($file_id)
{
	$sql="SELECT file_closed_id,file_close_date,amount_paid,mode,rasid_no,file_id,remarks,date_closed,closed_by,last_updated_by,date_modified FROM fin_file_closed WHERE file_id=$file_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0];
		}
	else 
	return false;	
	
	}	

function deleteClosure($file_id)
{
	if(checkForNumeric($file_id))
	{
	$loan=getLoanDetailsByFileId($file_id);
	$loan_id=$loan['loan_id'];
	
	
	$sql="DELETE FROM fin_file_closed WHERE file_id=$file_id";
	dbQuery($sql);
	
	$balance=getBalanceForLoan($loan_id);
	
	if($balance<0)
	{
		$loan_ending_date=$loan['loan_ending_date'];
		$today=date('Y-m-d');
		
		if(strtotime($loan_ending_date)<strtotime($today))
		{
			
			$sql="UPDATE fin_file SET file_status=2 WHERE file_id=$file_id";
		dbQuery($sql);
			}
		else
		{
			
			$sql="UPDATE fin_file SET file_status=1 WHERE file_id=$file_id";
		dbQuery($sql);
			}	
		}
	else if($balance>=0)
	{
		
		$sql="UPDATE fin_file SET file_status=2 WHERE file_id=$file_id";
		dbQuery($sql);
		}
	}
	return "success";
}	

function stripFileNo($file_no)
{
$string=$file_no;
preg_match('#[0-9]+$#', $string, $match);
$end_number=$match[0]; // Output: 8271
if(is_numeric($end_number) && validateForNull($end_number))
{
$pos = strrpos($string, $end_number);

    if($pos !== false)
    {
        $start_string = substr_replace($string, "", $pos, strlen($end_number));
    }

$new_number=$str = ltrim($end_number, '0');
$new_file_no=$start_string.$new_number;
return $new_file_no;
}
return $file_no;
	}	
function getFileIdFromLoanId($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="SELECT file_id FROM fin_loan WHERE loan_id=$loan_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		return $resultArray[0][0];
		}
	
	}

function updateFileNos()
{
	$sql="SELECT file_id,file_number FROM fin_file";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		foreach($resultArray as $re)
		{
			$file_id=$re[0];
			$file_number=$re[1];
			$file_number=stripFileNo($file_number);
			
			$sql="UPDATE fin_file SET file_number='$file_number' WHERE file_id=$file_id";
			dbQuery($sql);
			}
		
		}
	}				
function updateAgreementNos()
{
	$sql="SELECT file_id, file_number, file_agreement_no FROM fin_file
	       WHERE agency_id=1";
	$result = dbQuery($sql);
	$resultArray=dbResultToArray($result);	
	return $resultArray;
}		


function updateAgreementNosForFileIds($file_array, $agreementId_array)
{
	if(is_array($file_array) && is_array($agreementId_array))
	{
		for($i=0; $i<count($file_array); $i++)
		{
			$file_id=$file_array[$i];
			$agrrement_no=$agreementId_array[$i];
			
			$sql="UPDATE fin_file SET file_agreement_no = '$agrrement_no'
	              WHERE file_id = $file_id";
	        $result = dbQuery($sql);
	
			
		}
	}
}

function getPaidByForFileID($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT paid_by from fin_customer WHERE file_id=$file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if($resultArray[0][0]!="NA" && validateForNull($resultArray[0][0]))
		return $resultArray[0][0];
		}
	}			
?>