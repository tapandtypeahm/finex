<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("area-functions.php");
require_once("account-head-functions.php");
require_once("account-period-functions.php");
require_once("common.php");
require_once("bd.php");

function listLedgers(){
	
	try
	{
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$our_company_id=$_SESSION['adminSession']['oc_id'];
		$current_company=getCurrentCompanyForUser($admin_id);
		if($current_company[1]==0) // if current company is our company
		{
		$oc_id=$current_company[0];
		$agency_id="NULL";	
		}
		else if($current_company[1]==1) // if agency
		{
		$agency_id=$current_company[0];
		$oc_id="NULL";		
			}
		$sql="SELECT ledger_id, ledger_name,address,head_id,postal_name, fin_city.city_id, area_id, pincode, pan_no, sales_no,opening_balance, opening_date, opening_cd,notes, agency_id, oc_id, our_company_id,fin_ac_ledgers.date_added, fin_ac_ledgers.date_modified, fin_ac_ledgers.last_updated_by, city_name
		  FROM fin_ac_ledgers,fin_city
		  WHERE fin_ac_ledgers.city_id=fin_city.city_id  AND our_company_id=$our_company_id AND ";
		if($oc_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql." agency_id=$agency_id  ";
}
if($agency_id=="NULL" && is_numeric($oc_id))
{
	$sql=$sql." oc_id=$oc_id  ";
}  
		  $sql=$sql." ORDER BY ledger_name";
		 
		  
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray; 
		else
		return false;
	}
	catch(Exception $e)
	{
	}
	
}

function listNonAccountingLedgers() // normal ledgers without cash and bank
{
	
	try
	{
		$bank_head_id=getBankAccountsHeadId();
		$cash_head_id=getCashHeadId();
		$debtors_head_id=getSundryDebtorsId();
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$our_company_id=$_SESSION['adminSession']['oc_id'];
		$current_company=getCurrentCompanyForUser($admin_id);
		if($current_company[1]==0) // if current company is our company
		{
		$oc_id=$current_company[0];
		$agency_id="NULL";	
		}
		else if($current_company[1]==1) // if agency
		{
		$agency_id=$current_company[0];
		$oc_id="NULL";		
			}
		$sql="SELECT ledger_id as id, ledger_name as name,head_id, fin_city.city_id, opening_balance, opening_date, opening_cd, agency_id, oc_id, our_company_id,city_name
		  FROM fin_ac_ledgers,fin_city
		  WHERE fin_ac_ledgers.city_id=fin_city.city_id  AND
		  head_id!=$bank_head_id AND head_id!=$cash_head_id AND
		   our_company_id=$our_company_id AND ";
		if($oc_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql." agency_id=$agency_id  ";
}
if($agency_id=="NULL" && is_numeric($oc_id))
{
	$sql=$sql." oc_id=$oc_id  ";
}  
		  $sql=$sql."";
		 
		  
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray; 
		else
		return false;
	}
	catch(Exception $e)
	{
	}
	
}	

function listCustomerAndLedgers($like_term=false)
{
	try
	{
		$bank_head_id=getBankAccountsHeadId();
		$cash_head_id=getCashHeadId();
		$debtors_head_id=getSundryDebtorsId();
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$our_company_id=$_SESSION['adminSession']['oc_id'];
		$current_company=getCurrentCompanyForUser($admin_id);
		if($current_company[1]==0) // if current company is our company
		{
		$oc_id=$current_company[0];
		$agency_id="NULL";	
		}
		else if($current_company[1]==1) // if agency
		{
		$agency_id=$current_company[0];
		$oc_id="NULL";		
			}
		$sql="SELECT CONCAT('L',ledger_id) as id, ledger_name as name, fin_city.city_id, agency_id, oc_id, our_company_id,city_name
		  FROM fin_ac_ledgers,fin_city
		  WHERE fin_ac_ledgers.city_id=fin_city.city_id  AND
		  head_id!=$bank_head_id AND head_id!=$cash_head_id AND
		  our_company_id=$our_company_id AND ";
		if($oc_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql." agency_id=$agency_id  ";
}
if($agency_id=="NULL" && is_numeric($oc_id))
{
	$sql=$sql." oc_id=$oc_id  ";
} 
if($like_term!=false)
{
	$sql=$sql."AND ledger_name LIKE '%$like_term%' ";
}  
		  $sql=$sql." UNION 
		  SELECT CONCAT('C',fin_customer.customer_id) as id, CONCAT(customer_name,' ',file_number,' ',IFNULL(vehicle_reg_no,'')) as name, fin_city.city_id, agency_id, oc_id, our_company_id,city_name
		  FROM fin_customer
		  LEFT JOIN fin_city ON fin_customer.city_id=fin_city.city_id 
		  LEFT JOIN fin_file ON fin_customer.file_id=fin_file.file_id
		  LEFT JOIN fin_vehicle ON fin_vehicle.file_id=fin_customer.file_id
		  WHERE 
		  our_company_id=$our_company_id AND 
		  ";
		 if($oc_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql." agency_id=$agency_id  ";
}
if($agency_id=="NULL" && is_numeric($oc_id))
{
	$sql=$sql." oc_id=$oc_id  ";
}   
if($like_term!=false)
{
	$sql=$sql."AND (customer_name LIKE '%$like_term%' OR file_number LIKE '%$like_term%' OR vehicle_reg_no LIKE '%$like_term%')";
} 
		 		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray; 
		else
		return false;
	}
	catch(Exception $e)
	{
	}
	
	}

function listAccountingLedgers() // bank or cash
{
	
	try
	{
		$bank_head_id=getBankAccountsHeadId();
		$cash_head_id=getCashHeadId();
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$our_company_id=$_SESSION['adminSession']['oc_id'];
		$current_company=getCurrentCompanyForUser($admin_id);
		if($current_company[1]==0) // if current company is our company
		{
		$oc_id=$current_company[0];
		$agency_id="NULL";	
		}
		else if($current_company[1]==1) // if agency
		{
		$agency_id=$current_company[0];
		$oc_id="NULL";		
			}
		$sql="SELECT ledger_id, ledger_name,address,head_id,postal_name, fin_city.city_id, area_id, pincode, pan_no, sales_no,opening_balance, opening_date, opening_cd,notes, agency_id, oc_id, our_company_id,fin_ac_ledgers.date_added, fin_ac_ledgers.date_modified, fin_ac_ledgers.last_updated_by, city_name
		  FROM fin_ac_ledgers,fin_city
		  WHERE fin_ac_ledgers.city_id=fin_city.city_id  AND
		  (head_id=$bank_head_id OR head_id=$cash_head_id) AND
		   our_company_id=$our_company_id AND ";
		if($oc_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql." agency_id=$agency_id  ";
}
if($agency_id=="NULL" && is_numeric($oc_id))
{
	$sql=$sql." oc_id=$oc_id  ";
}  
		  $sql=$sql." ORDER BY ledger_name";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray; 
		else
		return false;
	}
	catch(Exception $e)
	{
	}
	
}	

function listAccountingLedgersForAgency($agency_id) //only bank 
{
	
	try
	{
		if(checkForNumeric($agency_id))
		{
		$bank_head_id=getBankAccountsHeadId();
		$cash_head_id=getCashHeadId();
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$our_company_id=$_SESSION['adminSession']['oc_id'];
		
		$sql="SELECT ledger_id, ledger_name,address,head_id,postal_name, fin_city.city_id, area_id, pincode, pan_no, sales_no,opening_balance, opening_date, opening_cd,notes, agency_id, oc_id, our_company_id,fin_ac_ledgers.date_added, fin_ac_ledgers.date_modified, fin_ac_ledgers.last_updated_by, city_name
		  FROM fin_ac_ledgers,fin_city
		  WHERE fin_ac_ledgers.city_id=fin_city.city_id  AND
		  head_id=$bank_head_id AND
		   our_company_id=$our_company_id AND ";
		
	$sql=$sql." agency_id=$agency_id  ";

	
  
		  $sql=$sql." ORDER BY ledger_name";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray; 
		else
		return false;
		}
	}
	catch(Exception $e)
	{
	}
	
}	

function listAccountingLedgersForOC($oc_id) //only bank
{
	
	try
	{
		if(checkForNumeric($oc_id))
		{
		$bank_head_id=getBankAccountsHeadId();
		$cash_head_id=getCashHeadId();
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$our_company_id=$_SESSION['adminSession']['oc_id'];
		
		$sql="SELECT ledger_id, ledger_name,address,head_id,postal_name, fin_city.city_id, area_id, pincode, pan_no, sales_no,opening_balance, opening_date, opening_cd,notes, agency_id, oc_id, our_company_id,fin_ac_ledgers.date_added, fin_ac_ledgers.date_modified, fin_ac_ledgers.last_updated_by, city_name
		  FROM fin_ac_ledgers,fin_city
		  WHERE fin_ac_ledgers.city_id=fin_city.city_id  AND
		  head_id=$bank_head_id  AND
		  our_company_id=$our_company_id AND ";
	$sql=$sql." oc_id=$oc_id  ";  
		  $sql=$sql." ORDER BY ledger_name";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray; 
		else
		return false;
		}
	}
	catch(Exception $e)
	{
	}
	
}	


function insertLedger($name,$postal_name,$address,$city_id,$area,$pincode,$head_id,$contact_no,$pan_no,$sales_no,$notes,$opening_balance,$opening_balance_cd,$agency_id=null,$oc_id=null){
	
	try
	{
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$our_company_id=$_SESSION['adminSession']['oc_id'];
		if(checkForNumeric($agency_id))
		{
			$oc_id="NULL";
			}
		else if(checkForNumeric($oc_id))
		{
			$agency_id="NULL";
			}
		if(!(checkForNumeric($agency_id) || checkForNumeric($oc_id)))
		{		
			$current_company=getCurrentCompanyForUser($admin_id);
			if($current_company[1]==0) // if current company is our company
			{
			$oc_id=$current_company[0];
			$agency_id="NULL";	
			}
			else if($current_company[1]==1) // if agency
			{
			$agency_id=$current_company[0];
			$oc_id="NULL";		
				}
		}
		$name=clean_data($name);
		$name = ucwords(strtolower($name));
		$address=clean_data($address);
		if(!checkForNumeric($opening_balance))
		$opening_balance=0;
		
		if(!validateForNull($pan_no))
		{
			$pan_no='0';
			}
		if(!validateForNull($sales_no))
		{
			$sales_no='0';
			}	
		if(!validateForNull($postal_name))
		{
			$postal_name="NA";
			}	
		if(!validateForNull($address))
		{
			$address="NA";
			}	
			
		if(!validateForNull($city_id) || $city_id==-1)
		{
			$city_id=insertCityIfNotDuplicate("NA");
			}	
		
		if(!validateForNull($pincode))
		{
			$pincode=111111;
			}		
			
		if(!validateForNull($area))
		{
			
			$area_id=insertArea("NA",$city_id);
			}
		else
		{
			$area_id=insertArea($area,$city_id);
			}		
		
		if(!validateForNull($notes))
		{
			$notes="NA";
			}	
				
		if($name!=null && $name!=''  && checkForNumeric($head_id) && strlen($pincode)==6)
			{
			
			
			$curr_date=getCurrentDateForUser($admin_id);
			$opening_date=date('Y-04-01',strtotime($curr_date));
			$sql="INSERT INTO fin_ac_ledgers
					(ledger_name, head_id, postal_name,  address, city_id, area_id,pincode, pan_no, sales_no,opening_balance,opening_cd, opening_date, notes, agency_id, oc_id, our_company_id, created_by, last_updated_by, date_added, date_modified)
					VALUES
					('$name',$head_id,'$postal_name','$address',$city_id,$area_id,$pincode,'$pan_no','$sales_no',$opening_balance, $opening_balance_cd, '$opening_date','$notes', $agency_id , $oc_id, $our_company_id ,$admin_id,$admin_id,NOW(),NOW())";
			
			dbQuery($sql);
			$ledger_id=dbInsertId();
			addledgerContactNo($ledger_id,$contact_no);
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



function deleteLedger($ledger_id)
{
	
	if(checkForNumeric($ledger_id))
	{
		$sql="DELETE FROM fin_ac_ledgers WHERE ledger_id=$ledger_id";
		dbQuery($sql);
		return "success";
		}
	}	

function updateLedger($id,$name,$postal_name,$address,$city_id,$area,$pincode,$head_id,$contact_no,$pan_no,$sales_no,$notes,$opening_balance,$opening_balance_cd){
	
	try
	{
		
		$name=clean_data($name);
		$name = ucwords(strtolower($name));
		$address=clean_data($address);
		
		if(!checkForNumeric($opening_balance))
		$opening_balance=0;
		
		if(!validateForNull($pan_no))
		{
			$pan_no='0';
			}
		if(!validateForNull($sales_no))
		{
			$sales_no='0';
			}	
		if(!validateForNull($postal_name))
		{
			$postal_name="NA";
			}	
		if(!validateForNull($address))
		{
			$address="NA";
			}		
		if(!validateForNull($city_id) || $city_id==-1)
		{
			$city_id=insertCityIfNotDuplicate("NA");
			}	
		if(!validateForNull($pincode))
		{
			$pincode=111111;
			}		
		if(!validateForNull($area))
		{
			$area_id=insertArea("NA",$city_id);
			}
		else
		{
			$area_id=insertArea($area,$city_id);
			}		
		
		if(!validateForNull($notes))
		{
			$notes="NA";
			}				
		if($name!=null && $name!=''  && checkForNumeric($id,$head_id))
			{
			
			$admin_id=$_SESSION['adminSession']['admin_id'];
			$sql="UPDATE fin_ac_ledgers
					SET ledger_name = '$name', head_id=$head_id, postal_name='$postal_name', address ='$address', city_id = $city_id, area_id=$area_id, pincode=$pincode, pan_no='$pan_no',sales_no='$sales_no',opening_balance=$opening_balance, opening_cd=$opening_balance_cd, notes='$notes' ,last_updated_by=$admin_id, date_modified=NOW()
					WHERE ledger_id=$id";
					
			dbQuery($sql);
			deleteAllContactNoLedger($id);	
			addLedgerContactNo($id,$contact_no);
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

function getLedgerById($id){
	
	try
	{
		$sql="SELECT ledger_id, ledger_name,address,head_id,postal_name, fin_city.city_id, area_id, pincode, pan_no, sales_no,opening_balance,opening_cd,opening_date,notes,fin_ac_ledgers.date_added, fin_ac_ledgers.date_modified, fin_ac_ledgers.last_updated_by, city_name
		  FROM fin_ac_ledgers,fin_city
		  WHERE fin_ac_ledgers.city_id=fin_city.city_id
		  AND ledger_id=$id";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0]; 
		else
		return false;
	}
	catch(Exception $e)
	{
	}
	
}

function getLedgerNameFromLedgerId($id)
{
try
	{
		$sql="SELECT  ledger_name
		  FROM fin_ac_ledgers
		  WHERE ledger_id=$id";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0]; 
		else
		return false;
	}
	catch(Exception $e)
	{
	}	
}
	


function addLedgerContactNo($ledger_id,$contact_no)
{
	try
	{
		if(is_array($contact_no))
		{
			foreach($contact_no as $no)
			{
				if($no!="" && $no!=null && is_numeric($no))
				{
				insertContactNoLedger($ledger_id,$no); 
				}
			}
		}
		else
		{
			if($contact_no!="" && $contact_no!=null && is_numeric($contact_no))
				{
				insertContactNoLedger($ledger_id,$contact_no); 
				}
			
		}
	}
	catch(Exception $e)
	{
	}
}

function insertContactNoLedger($id,$contact_no)
{
	try
	{
		if(checkForNumeric($id,$contact_no)==true && !checkForDuplicateContactNoledger($id,$contact_no))
		{
		$sql="INSERT INTO fin_ac_ledgers_contact_no
				      (contact_no, ledger_id)
					  VALUES
					  ('$contact_no', $id)";
				dbQuery($sql);	  
		}
	}
	catch(Exception $e)
	{}
	
	
}
function deleteContactNoLedger($id)
{
	try
	{
		$sql="DELETE FROM fin_ac_ledgers_contact_no
			  WHERE ledger_contact_no_id=$id";
		dbQuery($sql);	  
	}
	catch(Exception $e)
	{}
	
	
	
	}
function deleteAllContactNoLedger($id)
{
	try
	{
		$sql="DELETE FROM fin_ac_ledgers_contact_no
			  WHERE ledger_id=$id";
		dbQuery($sql);
	}
	catch(Exception $e)
	{}
	
	
	
	}	
function updateContactNoVehicleledger($id,$contact_no)
{
	try
	{
		deleteAllContactNoLedger($id);
		addLedgerContactNo($id,$contact_no);
	}
	catch(Exception $e)
	{}
	
	
	
	}	

function checkForDuplicateContactNoledger($id,$contact_no)
{
	if(checkForNumeric($id,$contact_no))
	{
	$sql="SELECT ledger_contact_no_id
	      FROM fin_ac_ledgers_contact_no
		  WHERE contact_no='$contact_no'
		  AND ledger_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	return false;	
	}
	}	

	
function getledgerNumbersByledgerId($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT contact_no
	      FROM fin_ac_ledgers_contact_no
		  WHERE fin_ac_ledgers_contact_no.ledger_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	return false;	
	}
	}	
	
function createCashLedgerForAgnecy($agency_id)
{
	
	$cash_head_id=getCashHeadId();
	if(checkForNumeric($agency_id))
	{
		
		$result=insertLedger('Cash','','',null,null,'',$cash_head_id,'','','','',0,0,$agency_id);
		if($result=="success")
		return true;
		else
		return false;
		}
	return false;	
}

function createCashLedgerForOC($oc_id)
{
	
	$cash_head_id=getCashHeadId();
	if(checkForNumeric($oc_id))
	{
		
		$result=insertLedger('Cash','','',null,null,'',$cash_head_id,'','','','',0,0,null,$oc_id);
		if($result=="success")
		return true;
		else
		return false;
		}
	return false;	
}

function createAutoInterestLedgerForAgency($agency_id)
{
	
	$unsecured_loans_head_id=getUnsecuredLoansId();
	if(checkForNumeric($agency_id))
	{
		
		$result=insertLedger('Auto Interest','','',null,null,'',$unsecured_loans_head_id,'','','','',0,0,$agency_id);
		if($result=="success")
		return true;
		else
		return false;
		}
	return false;	
}

function createAutoInterestLedgerForOC($oc_id)
{
	
	$unsecured_loans_head_id=getUnsecuredLoansId();
	if(checkForNumeric($oc_id))
	{
		
		$result=insertLedger('Auto Interest','','',null,null,'',$unsecured_loans_head_id,'','','','',0,0,null,$oc_id);
		if($result=="success")
		return true;
		else
		return false;
		}
	return false;	
}

function createIncomeLedgerForAgency($agency_id)
{
	
	$unsecured_loans_head_id=getIndirectIncomeId();
	if(checkForNumeric($agency_id))
	{
		
		$result=insertLedger('Finance Income','','',null,null,'',$unsecured_loans_head_id,'','','','',0,0,$agency_id);
		if($result=="success")
		return true;
		else
		return false;
		}
	return false;	
}

function createIncomeLedgerForOC($oc_id)
{
	
	$unsecured_loans_head_id=getIndirectIncomeId();
	if(checkForNumeric($oc_id))
	{
		
		$result=insertLedger('Finance Income','','',null,null,'',$unsecured_loans_head_id,'','','','',0,0,null,$oc_id);
		if($result=="success")
		return true;
		else
		return false;
		}
	return false;	
}
?>