<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("vehicle-dealer-functions.php");
require_once("vehicle-model-functions.php");
require_once("common.php");
require_once("bd.php");
		
function listVehicleCompanies(){
	
	try
	{
		$sql="SELECT vehicle_company_id, company_name
			  FROM fin_vehicle_company ORDER BY company_name";
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


function insertVehicleCompany($name){
	try
	{
		$name=clean_data($name);
		$name = ucwords(strtolower($name));
		if(validateForNull($name) && !checkDuplicateVehicleCompany($name))
		{
			$sql="INSERT INTO 
				fin_vehicle_company(company_name)
				VALUES ('$name')";
		$result=dbQuery($sql);
		$company_id=dbInsertId();
		insertVehicleDealer("others","others",1,$company_id,9999999999);
		insertVehicleModel("others",$company_id);
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

function deleteVehicleCompany($id){
	
	try
	{
		if(!checkifVehicleCompanyInUse($id))
		{
		$sql="DELETE FROM fin_vehicle_company
		      WHERE vehicle_company_id=$id";
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

function updateVehicleCompany($id,$name){
	
	try
	{
		$name=clean_data($name);
		$name = ucwords(strtolower($name));
		if(validateForNull($name) && checkForNumeric($id) && !checkDuplicateVehicleCompany($name,$id))
		{
		$sql="UPDATE fin_vehicle_company
			  SET company_name='$name'
			  WHERE vehicle_company_id=$id";
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

function getVehicleCompanyById($id){
	
	try
	{
		if(checkForNumeric($id))
		{
		$sql="SELECT vehicle_company_id, company_name
			  FROM fin_vehicle_company
			  WHERE vehicle_company_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else
		return false;
		}
	}
	catch(Exception $e)
	{
	}
	
}

function getVehicleCompanyNameById($id){
	
	try
	{
		if(checkForNumeric($id))
		{
		$sql="SELECT company_name
			  FROM fin_vehicle_company
			  WHERE vehicle_company_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
		else
		return false;
		}
	}
	catch(Exception $e)
	{
	}
	
}

function checkDuplicateVehicleCompany($name,$id=false)
{
	if(validateForNull($name))
	{
		$sql="SELECT vehicle_company_id
			  FROM fin_vehicle_company
			  WHERE company_name='$name'";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND vehicle_company_id!=$id";		  
		$result=dbQuery($sql);	
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0][0]; //duplicate found
			} 
		else
		{
			return false;
			}
	}
}	

function checkifVehicleCompanyInUse($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT vehicle_company_id
	      FROM fin_vehicle
		  Where vehicle_company_id=$id";
	$result=dbQuery($sql);	  
	if(dbNumRows($result)>0)
	return true;
	else 
	return false;
	}
}		
?>