<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("common.php");
require_once("bd.php");
		
function listVehicleTypes(){
	
	try
	{
		$sql="SELECT vehicle_type_id, vehicle_type
		      FROM fin_vehicle_type
			  ORDER BY vehicle_type";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		return $resultArray;	  
	}
	catch(Exception $e)
	{
	}
	
}	

function getNumberOfVehicleTypes()
{
	$sql="SELECT count(vehicle_type_id)
		      FROM fin_vehicle_type
			  ORDER BY vehicle_type";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		return $resultArray[0][0];	
	
	}
function insertVehicleType($vehicle_type){
	
	try
	{
		$vehicle_type=clean_data($vehicle_type);
		$vehicle_type = ucwords(strtolower($vehicle_type));
		if(validateForNull($vehicle_type) && !checkForDuplicateVehicleType($vehicle_type))
		{
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="INSERT INTO fin_vehicle_type
		      (vehicle_type, created_by, last_updated_by, date_added, date_modified)
			  VALUES
			  ('$vehicle_type', $admin_id, $admin_id, NOW(), NOW())";
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

function deleteVehicleType($id){
	
	try
	{
		if(checkForNumeric($id) && !checkIfVehicleTypeInUse($id))
		{
		$sql="DELETE FROM fin_vehicle_type
		      WHERE vehicle_type_id=$id";
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

function updateVehicleType($id,$type){
	
	try
	{
		$type=clean_data($type);
		$type = ucwords(strtolower($type));
		if(checkForNumeric($id) && validateForNull($type) && !checkForDuplicateVehicleType($type,$id))
		{
			
		$sql="UPDATE fin_vehicle_type
		      SET vehicle_type='$type'
			  WHERE vehicle_type_id=$id";
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

function getVehicleTypeById($id){
	
	try
	{
		$sql="SELECT vehicle_type_id, vehicle_type
		      FROM fin_vehicle_type
			  WHERE vehicle_type_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];	 
	}
	catch(Exception $e)
	{
	}
	
}	
function getVehicleTypeNameById($id){
	
	try
	{
		$sql="SELECT vehicle_type_id, vehicle_type
		      FROM fin_vehicle_type
			  WHERE vehicle_type_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][1];	 
	}
	catch(Exception $e)
	{
	}
	
}	

function checkForDuplicateVehicleType($vehicle_type,$id=false)
{
	    if(validateForNull($vehicle_type))
		{
		$sql="SELECT vehicle_type_id
		      FROM fin_vehicle_type
			  WHERE vehicle_type='$vehicle_type'";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND vehicle_type_id!=$id";		  	  
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return true;
		else
		return false;
		}
	}	
function checkIfVehicleTypeInUse($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT vehicle_id FROM
			fin_vehicle
			WHERE vehicle_type_id=$id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	return true;
	else
	return false;		
	}
	
	}	
?>