<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("common.php");
require_once("bd.php");

			
function listOurCompanies(){
	
	$sql="SELECT our_company_id, our_company_name, our_company_address, our_company_pincode, fin_city.city_id, city_name, 	sub_heading,		     our_company_prefix
		  FROM fin_our_company,fin_city
		  WHERE fin_our_company.city_id=fin_city.city_id";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray; 
		else
		return false;
	}

function listOurCompaniesNames(){
	
	$sql="SELECT our_company_id, our_company_name
	      FROM fin_our_company";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray; 
		else
		return false;
	}


function insertOurCompany($name,$address,$pincode,$city_id,$company_prefix,$sub_heading,$contact_no=false) // name,pincode,city_id,prefix validations
{
	try
	{
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$name = strtoupper($name);
		$address=clean_data($address);
		$name=clean_data($name);
		$company_prefix=clean_data($company_prefix);
		$cond=checkForNumeric($pincode,$city_id);
		if($cond==true && validateForNull($name,$address,$company_prefix) && strlen($pincode)==6 && checkForAlphaNumeric($company_prefix) && strlen($company_prefix)<5 && !checkForDuplicateOurCompany($name,$address,$city_id,$pincode,$company_prefix))
		{
			
			$company_prefix=strtoupper($company_prefix);
			$sql="INSERT INTO 
			      fin_our_company(our_company_name, our_company_address, our_company_pincode, city_id, our_company_prefix, sub_heading, created_by, last_updated_by, date_added, date_modified)
				  VALUES
				  ('$name', '$address', '$pincode', $city_id, '$company_prefix', '$sub_heading' , $admin_id, $admin_id, NOW(), NOW())";
			$result=dbQuery($sql);	  
			$ourCompanyId=dbInsertId();
			if(getAccountsStatus())
			{
			insertAccountSettingsForOC($ourCompanyId);
			}
			if($contact_no!=false)
			{
			addContactNoOurCompany($ourCompanyId,$contact_no);
			}
			return "success";
		}
		else
		{
			return "error";
			
		}
		return "error";
		
	}
	catch(Exception $e)
	{}
	
}
function insertAccountSettingsForOC($oc_id,$account_starting_date,$include_penalty,$include_additional_charges)
{
	$account_starting_date=ACCOUNT_STARTING_DATE;
	$include_penalty=INCLUDE_PENALTY;
	$include_ac=INCLUDE_AC;
	
	if(checkForNumeric($oc_id,$include_penalty,$include_ac) && validateForNull($account_starting_date) && ACCOUNT_STATUS==1 && !getAccountSettingsForOCID($oc_id))
	{
		
		$sql="INSERT INTO fin_ac_settings (our_company_id,ac_starting_date,include_penalty,include_ac) VALUES ($oc_id,'$account_starting_date',$include_penalty,$include_ac)";
		$result=dbQuery($sql);
		return "success";
		}
	
	}

function getAccountSettingsForOCID($agency_id)
{
	if(checkForNumeric($agency_id))
	{
		$sql="SELECT our_company_id,ac_starting_date,include_penalty,include_ac FROM fin_ac_settings WHERE our_company_id=$agency_id";
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
	
function deleteOurCompany($id)
{
	try{
		
		$ourCompanies=listOurCompaniesNames();
		if(checkForNumeric($id) && !checkIfOurCompanyInUse($id) && count($ourCompanies)>1)
		{
		$sql="DELETE FROM
		      fin_our_company 
			  WHERE our_company_id=$id";
		dbQuery($sql);	  
		return "success";
		}
		else if(count($ourCompanies)==1)
		{
			return "error1";
			}
		else
		{
			return "error";
			}
		}
	catch(Exception $e)
	{}
	
	}
	
function updateOurCompany($id,$name,$address,$pincode,$city_id,$company_prefix,$subheading,$contact_no)
{
	try{
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$name = strtoupper($name);
		$address=clean_data($address);
		$name=clean_data($name);
		$company_prefix=clean_data($company_prefix);
		$cond=checkForNumeric($pincode,$city_id);
		
		if($cond==true && validateForNull($name,$address,$company_prefix) && $pincode>100000 && checkForAlphaNumeric($company_prefix) && strlen($company_prefix)<5 && !checkForDuplicateOurCompany($name,$address,$city_id,$pincode,$company_prefix,$id))
		{
			$company_prefix=strtoupper($company_prefix);
			$sql="UPDATE  
			      fin_our_company
				  SET our_company_name = '$name',
				      our_company_address = '$address',
					  our_company_pincode = $pincode,
					  city_id = $city_id,
					  our_company_prefix = '$company_prefix',
					  sub_heading = '$subheading',
					  last_updated_by = $admin_id,
					  date_modified = NOW()
				  WHERE our_company_id=$id";
			dbQuery($sql);	
			updateContactNoOurCompany($id,$contact_no);  
			return "success";
		}
		else
		{
			return "error";
			}
		
		}
	catch(Exception $e)
	{}
	
	}	


	
function getOurCompanyByID($id)
{
	$sql="SELECT our_company_id, sub_heading, our_company_name, our_company_address, our_company_pincode, sub_heading, fin_city.city_id, city_name, our_company_prefix
	      FROM fin_our_company,fin_city
		  WHERE our_company_id=$id
		  AND fin_our_company.city_id=fin_city.city_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0];
		}	  
	else
	{
		return false;
		}	
	}
	



			

function checkForDuplicateOurCompany($name,$address,$city,$pincode,$prefix,$id=false) // duplicate if name,address,pincode,city are all same or company prefix is same
{
	try{
		
		$sql="SELECT our_company_id
		       FROM fin_our_company
			   WHERE 
			   ((((our_company_name='$name'
			   AND our_company_address='$address'
			   AND our_company_pincode=$pincode
			   AND city_id=$city)
			   OR (our_company_prefix='$prefix')))
			   ";	   
		if($id==false)
		$sql=$sql.")";
		else
		$sql=$sql." AND (our_company_id!=$id))";	   
		$result=dbQuery($sql);	
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
	catch(Exception $e)
	{}
	
	}	
function addContactNoOurCompany($id,$contact_no)
{
	try
	{
		if(is_array($contact_no))
		{
			
			foreach($contact_no as $no)
			{
				
				insertContactNoOurCompany($id,$no);
			}
			
		}
		else
		{

			insertContactNoOurCompany($id,$contact_no);
		}
	}
	catch(Exception $e)
	{}
	
}

function insertContactNoOurCompany($id,$contact_no)
{
	try
	{
		if(checkForNumeric($id)==true && checkForNumeric($contact_no))
		{
		$sql="INSERT INTO 
			  fin_our_company_contact_no(our_company_contact_no, our_company_id)
			  VALUES
			  ('$contact_no', $id)";
		dbQuery($sql);
		}
	}
	catch(Exception $e)
	{}
	
	
}
function deleteContactNoOurCompany($id)
{
	try
	{
		$sql="DELETE FROM fin_our_company_contact_no
			  WHERE our_company_contact_no_id=$id";
		dbQuery($sql);	  
	}
	catch(Exception $e)
	{}
	
	
	
	}
function deleteAllContactNoOurCompany($id)
{
	try
	{
		$sql="DELETE FROM fin_our_company_contact_no
			  WHERE our_company_id=$id";
		dbQuery($sql);
	}
	catch(Exception $e)
	{}
	
	
	
	}	
function updateContactNoOurCompany($id,$contact_no)
{
	try
	{
		deleteAllContactNoOurCompany($id);
		addContactNoOurCompany($id,$contact_no);
	}
	catch(Exception $e)
	{}
	
	
	
	}
function getContactNoForOurCompany($id)
{
	$sql="SELECT our_company_id,our_company_contact_no
			FROM fin_our_company_contact_no
			WHERE our_company_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);		
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return false;
}	

function checkIfOurCompanyInUse($id)
{
	
	$sql="SELECT our_company_id
			FROM fin_file
			WHERE our_company_id=$id OR oc_id=$id
			LIMIT 0, 1";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return true;
	else
	return false;		
	
	}

function getPrefixFromOCId($id)
{
	$sql="SELECT our_company_prefix FROM
		   fin_our_company
		   WHERE our_company_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];	   
	
	}	
function getRasidNoForOCID($id)
{
	$sql="SELECT our_company_prefix,rasid_counter FROM
		   fin_our_company
		   WHERE our_company_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0].$resultArray[0][1];	
}

function getRasidCounterForOCID($id)
{
	$sql="SELECT our_company_prefix,rasid_counter FROM
		   fin_our_company
		   WHERE our_company_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][1];	
}

function incrementRasidNoForOCID($id)
{
	$r=getRasidCounterForOCID($id);
	$r++;
	$sql="UPDATE fin_our_company
	      SET rasid_counter=$r
		  WHERE our_company_id=$id";
	dbQuery($sql);	  
	
	}	
function resetAllRasidCountersOC()
{
		$sql="UPDATE fin_our_company SET rasid_reset_date=NOW(), rasid_counter=1";
		dbQuery($sql);
		return "success";
		}

function getRasidResetDateOC($oc_id)
{
	$sql="SELECT rasid_reset_date FROM fin_our_company WHERE our_company_id=$oc_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
	}	

function resetRasidCounterForOC($oc_id)
{
	$sql="UPDATE fin_our_company SET rasid_reset_date=NOW(), rasid_counter=1 WHERE our_company_id=$oc_id";
		dbQuery($sql);
		return "success";
	}			
?>