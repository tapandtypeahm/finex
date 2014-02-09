<?php 
require_once("cg.php");
require_once("common.php");
require_once("bd.php");
		
function listVehicleModels(){
	
	try
	{
		$sql="SELECT model_id, model_name, fin_vehicle_model.vehicle_company_id, company_name
			  FROM fin_vehicle_model,fin_vehicle_company
			  WHERE fin_vehicle_model.vehicle_company_id = fin_vehicle_company.vehicle_company_id
			  ORDER BY model_name";
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


function insertVehicleModel($name,$vehicle_company_id){
	try
	{
		$name=clean_data($name);
		$name = ucwords(strtolower($name));
		$duplicate=checkDuplicateVehicleModel($name,$vehicle_company_id);
		if(validateForNull($name) && checkForNumeric($vehicle_company_id) && !$duplicate)
		{
			$sql="INSERT INTO 
				fin_vehicle_model(model_name, vehicle_company_id)
				VALUES ('$name',$vehicle_company_id)";
		$result=dbQuery($sql);
		return dbInsertId();
		}
		else if($duplicate!==false)
		{
			return $duplicate;
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

function deleteVehicleModel($id){
	
	try
	{
		if(!checkifVehicleModelInUse($id))
		{
		$sql="DELETE FROM fin_vehicle_model
		      WHERE model_id=$id";
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

function updateVehicleModel($id,$name,$vehicle_company_id){
	
	try
	{
		$name=clean_data($name);
		$name = ucwords(strtolower($name));
		if(validateForNull($name) && checkForNumeric($id,$vehicle_company_id) && !checkDuplicateVehicleModel($name,$vehicle_company_id,$id))
		{
		$sql="UPDATE fin_vehicle_model
			  SET model_name='$name', vehicle_company_id=$vehicle_company_id
			  WHERE model_id=$id";
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

function getVehicleModelById($id){
	
	try
	{
		if(checkForNumeric($id))
		{
		$sql="SELECT model_id, model_name, fin_vehicle_model.vehicle_company_id, company_name
			  FROM fin_vehicle_model,fin_vehicle_company
			  WHERE fin_vehicle_model.vehicle_company_id = fin_vehicle_company.vehicle_company_id
			  AND model_id=$id";
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

function getModelNameById($id){
	
	try
	{
		if(checkForNumeric($id))
		{
		$sql="SELECT  model_name
			  FROM fin_vehicle_model
			  WHERE model_id=$id";
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

function checkDuplicateVehicleModel($name,$vehicle_company_id,$id=false)
{
	
	if(validateForNull($name))
	{
		$sql="SELECT model_id
			  FROM fin_vehicle_model
			  WHERE model_name='$name'
			  AND vehicle_company_id=$vehicle_company_id";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND model_id!=$id";		  
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

function checkifVehicleModelInUse($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT model_id
	      FROM fin_vehicle
		  Where model_id=$id";
	$result=dbQuery($sql);	  
	if(dbNumRows($result)>0)
	return true;
	else 
	return false;
	}
}	

function getModelsFromCompanyID($id)
{
	
	if(checkForNumeric($id))
		{
			
		$sql="SELECT model_id, model_name
			  FROM fin_vehicle_model
			  WHERE vehicle_company_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray;
		else
		return false;
		}
	}	
?>