<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("common.php");
require_once("bd.php");

		
function listInsuranceCompanies(){
	
	try
	{
		$sql="SELECT insurance_company_id, insurance_company_name
			  FROM fin_insurance_company
			  ORDER BY insurance_company_name";
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


function insertInsuranceCompany($name){
	try
	{
		$name=clean_data($name);
		$name = ucwords(strtolower($name));
		if(validateForNull($name) && !checkDuplicateInsuranceCompany($name))
		{
			$admin_id=$_SESSION['adminSession']['admin_id'];	
			$sql="INSERT INTO 
				fin_insurance_company(insurance_company_name,created_by,last_updated_by,date_added,date_modified)
				VALUES ('$name',$admin_id,$admin_id,NOW(),NOW())";
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

function deleteInsuranceCompany($id){
	
	try
	{
		if(!checkifInsuranceCompanyInUse($id))
		{
		$sql="DELETE FROM fin_insurance_company
		      WHERE insurance_company_id=$id";
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

function updateInsuranceCompany($id,$name){
	
	try
	{
		if(validateForNull($name) && checkForNumeric($id) && !checkDuplicateInsuranceCompany($name,$id))
		{
		$admin_id=$_SESSION['adminSession']['admin_id'];		
		$sql="UPDATE fin_insurance_company
			  SET insurance_company_name='$name', date_modified=NOW(), last_updated_by=$admin_id
			  WHERE insurance_company_id=$id";
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

function getInsuranceCompanyById($id){
	
	try
	{
		if(checkForNumeric($id))
		{
		$sql="SELECT insurance_company_id, insurance_company_name
			  FROM fin_insurance_company
			  WHERE insurance_company_id=$id";
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

function getInsuranceCompanyNameById($id){
	
	try
	{
		if(checkForNumeric($id))
		{
		$sql="SELECT insurance_company_id, insurance_company_name
			  FROM fin_insurance_company
			  WHERE insurance_company_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][1];
		else
		return false;
		}
	}
	catch(Exception $e)
	{
	}
	
}

function checkDuplicateInsuranceCompany($name,$id=false)
{
	if(validateForNull($name))
	{
		$sql="SELECT insurance_company_id
			  FROM fin_insurance_company
			  WHERE insurance_company_name='$name'";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND insurance_company_id!=$id";		  
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

function checkifInsuranceCompanyInUse($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT insurance_company_id
	      FROM fin_vehicle_insurance
		  Where insurance_company_id=$id";
	$result=dbQuery($sql);	  
	if(dbNumRows($result)>0)
	return true;
	else 
	return false;
	}
}	
?>