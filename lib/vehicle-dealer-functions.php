<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("common.php");
require_once("bd.php");

function listVehicleDealers(){
	
	try
	{
		$sql="SELECT dealer_id, dealer_name, dealer_address, fin_city.city_id, city_name
		  FROM fin_vehicle_dealer,fin_city
		  WHERE fin_vehicle_dealer.city_id=fin_city.city_id
		  ORDER BY dealer_name";
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

function insertVehicleDealer($dealer_name,$dealer_address,$city_id,$company_id,$contact_no){
	
	try
	{
		$dealer_name=clean_data($dealer_name);
		$dealer_name = ucwords(strtolower($dealer_name));
		$dealer_address=clean_data($dealer_address);
		$duplcate=checkForDuplicateDealer($dealer_name,$city_id);
		if($dealer_name!=null && $dealer_name!=''  && checkForNumeric($city_id) && !checkForDuplicateDealer($dealer_name,$city_id))
			{
			$admin_id=$_SESSION['adminSession']['admin_id'];
			$sql="INSERT INTO fin_vehicle_dealer
					(dealer_name, dealer_address, city_id, created_by, last_updated_by, date_added, date_modified)
					VALUES
					('$dealer_name','$dealer_address',$city_id,$admin_id,$admin_id,NOW(),NOW())";
			dbQuery($sql);
			$dealer_id=dbInsertId();
			if(is_array($company_id))
			{
				
				foreach($company_id as $comp)
				{
					
					insertCompanyToDealer($dealer_id,$comp);
					}
				}
			else if(is_numeric($company_id))		
			insertCompanyToDealer($dealer_id,$company_id);
			
			addVehicleDealerContactNo($dealer_id,$contact_no);
			return "success";
			}
		else if($duplcate)
		{
			$dealer_id=$duplcate;
			if(is_array($company_id))
			{
				
				foreach($company_id as $comp)
				{
					
					insertCompanyToDealer($dealer_id,$comp);
					}
				}
			else if(is_numeric($company_id))		
			insertCompanyToDealer($dealer_id,$company_id);
			
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

function insertCompanyToDealer($dealer_id,$company_id)
{
	
	if(checkForNumeric($dealer_id,$company_id) && !checkForDuplicateCompanyToDealer($dealer_id,$company_id))
	{
		
		
	$sql="INSERT INTO fin_rel_company_dealer
	     (vehicle_company_id,dealer_id)
		 VALUES
		 ($company_id,$dealer_id)";
	dbQuery($sql);	 
	}
	
	}	

function deleteDealersForCompany($dealer_id,$company_id)
{
	
	if(checkForNumeric($dealer_id) && !checkIfCompanyAndDealerIsInUse($dealer_id,$company_id) )
	{
		$sql="DELETE FROM fin_rel_company_dealer WHERE dealer_id=$dealer_id AND vehicle_company_id=$company_id";
		dbQuery($sql);
		}
	}	

function checkIfCompanyAndDealerIsInUse($dealer_id,$company_id)
{
	
	$sql="SELECT vehicle_id FROM fin_vehicle WHERE vehicle_dealer_id=$dealer_id AND vehicle_company_id=$company_id";
	$result=dbQuery($sql);
	
	if(dbNumRows($result)>0)
	{	
	return true;
	}
	else
	return false;
	
	}	

function checkForDuplicateCompanyToDealer($dealer_id,$company_id)
{
	$sql="SELECT  company_dealer_id
	      FROM fin_rel_company_dealer
	     WHERE dealer_id=$dealer_id
		 AND vehicle_company_id=$company_id";
	
	$result=dbQuery($sql);	
	$resultArray=dbResultToArray($result);
	
	if(dbNumRows($result)>0)
	return true;
	else
	return false;
	}
function deleteVehicleDealer($id){
	
	try
	{
		if(checkForNumeric($id) && !checkIfDealerInUse($id))
		{
		$sql="DELETE FROM fin_vehicle_dealer WHERE dealer_id=$id";
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

function updateVehicleDealer($id,$dealer_name,$dealer_address,$city_id,$company_id,$contact_no){
	
	try
	{
		$dealer_name=clean_data($dealer_name);
		$dealer_name = ucwords(strtolower($dealer_name));
		$dealer_address=clean_data($dealer_address);
		if($dealer_name!=null && $dealer_name!=''  && checkForNumeric($city_id,$id) && !checkForDuplicateDealer($dealer_name,$city_id,$id))
			{
			
			$admin_id=$_SESSION['adminSession']['admin_id'];
			$sql="UPDATE fin_vehicle_dealer
					SET dealer_name = '$dealer_name', dealer_address ='$dealer_address', city_id = $city_id, last_updated_by=$admin_id, date_modified=NOW()
					WHERE dealer_id=$id";
					
			dbQuery($sql);
			$alCompanies=getVehicleCompanyIDsByDealerId($id);
			foreach($alCompanies as $comp_id)
				{
				deleteDealersForCompany($id,$comp_id);
				}
			 if(is_array($company_id))
			{
				
				foreach($company_id as $comp)
				{
					insertCompanyToDealer($id,$comp);
					}
				}
			else if(is_numeric($company_id))
			{	
			insertCompanyToDealer($id,$company_id);
			}
			deleteAllContactNoVehicleDealer($id);	
			addVehicleDealerContactNo($id,$contact_no);
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

function getVehicleDealerById($id){
	
	try
	{
		$sql="SELECT dealer_id, dealer_name, dealer_address, fin_city.city_id, city_name
		  FROM fin_vehicle_dealer,fin_city
		  WHERE fin_vehicle_dealer.city_id=fin_city.city_id
		  AND dealer_id=$id";
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

function getDealerNameFromDealerId($id)
{
try
	{
		$sql="SELECT  dealer_name
		  FROM fin_vehicle_dealer
		  WHERE dealer_id=$id";
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
	
function checkForDuplicateDealer($name,$city_id,$id=false)
{
	
	$sql="SELECT dealer_id
		  FROM fin_vehicle_dealer
		  WHERE dealer_name='$name'
		  AND city_id=$city_id";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND dealer_id!=$id";		  
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0]; 
		else
		return false;
	
}

function addVehicleDealerContactNo($dealer_id,$contact_no)
{
	try
	{
		if(is_array($contact_no))
		{
			foreach($contact_no as $no)
			{
				if($no!="" && $no!=null && is_numeric($no))
				{
				insertContactNoVehicleDealer($dealer_id,$no); 
				}
			}
		}
		else
		{
			if($contact_no!="" && $contact_no!=null && is_numeric($contact_no))
				{
				insertContactNoVehicleDealer($dealer_id,$contact_no); 
				}
			
		}
	}
	catch(Exception $e)
	{
	}
}

function insertContactNoVehicleDealer($id,$contact_no)
{
	try
	{
		if(checkForNumeric($id,$contact_no)==true && !checkForDuplicateContactNoDealer($id,$contact_no))
		{
		$sql="INSERT INTO fin_vehicle_dealer_contact_no
				      (dealer_contact_no, dealer_id)
					  VALUES
					  ('$contact_no', $id)";
				dbQuery($sql);	  
		}
	}
	catch(Exception $e)
	{}
	
	
}
function deleteContactNoVehicleDealer($id)
{
	try
	{
		$sql="DELETE FROM fin_vehicle_dealer_contact_no
			  WHERE dealer_contact_no_id=$id";
		dbQuery($sql);	  
	}
	catch(Exception $e)
	{}
	
	
	
	}
function deleteAllContactNoVehicleDealer($id)
{
	try
	{
		$sql="DELETE FROM fin_vehicle_dealer_contact_no
			  WHERE dealer_id=$id";
		dbQuery($sql);
	}
	catch(Exception $e)
	{}
	
	
	
	}	
function updateContactNoVehicleDealer($id,$contact_no)
{
	try
	{
		deleteAllContactNoVehicleDealer($id);
		addVehicleDealerContactNo($id,$contact_no);
	}
	catch(Exception $e)
	{}
	
	
	
	}	

function checkForDuplicateContactNoDealer($id,$contact_no)
{
	if(checkForNumeric($id,$contact_no))
	{
	$sql="SELECT dealer_contact_no_id
	      FROM fin_vehicle_dealer_contact_no
		  WHERE dealer_contact_no='$contact_no'
		  AND dealer_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	return false;	
	}
	}	
function getVehicleCompanyByDealerId($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT company_name
	      FROM fin_vehicle_company, fin_rel_company_dealer
		  WHERE fin_vehicle_company.vehicle_company_id=fin_rel_company_dealer.vehicle_company_id
		  AND fin_rel_company_dealer.dealer_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return false;	
	}
	}
function getVehicleCompanyIDsByDealerId($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT fin_vehicle_company.vehicle_company_id
	      FROM fin_vehicle_company, fin_rel_company_dealer
		  WHERE fin_vehicle_company.vehicle_company_id=fin_rel_company_dealer.vehicle_company_id
		  AND fin_rel_company_dealer.dealer_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$returnArray=array();
	if(dbNumRows($result)>0)
	{
		foreach($resultArray as $re)
		{
		$returnArray[]=$re[0];	
		}
		return $returnArray;
		}
	else
	return false;	
	}
	}	

	
function getDealerNumbersByDealerId($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT dealer_contact_no
	      FROM fin_vehicle_dealer_contact_no
		  WHERE fin_vehicle_dealer_contact_no.dealer_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return false;	
	}
	}	

function getDealersFromCompanyID($id)
{
	$sql="SELECT fin_rel_company_dealer.dealer_id,dealer_name
	      FROM fin_rel_company_dealer, fin_vehicle_dealer
		  WHERE vehicle_company_id=$id
		  AND fin_rel_company_dealer.dealer_id=fin_vehicle_dealer.dealer_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);	  
	if(dbNumRows($result)>0)
	return $resultArray;
	}	
function checkIfDealerInUse($id)
{
	$sql="SELECT vehicle_id FROM fin_vehicle WHERE vehicle_dealer_id=$id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	return true;
	else
	return false;
	
	}

?>