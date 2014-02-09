<?php 
require_once("cg.php");
require_once("bd.php");
require_once("common.php");

function listCities(){
	
	try
	{
		$sql="SELECT city_id,city_name
		      FROM fin_city ORDER BY city_name";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		return $resultArray; 
	}
	catch(Exception $e)
	{
	}
}

function listCitiesAlpha(){
	
	try
	{
		$sql="SELECT city_id,city_name
		      FROM fin_city
			  ORDER BY city_name";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		return $resultArray; 
	}
	catch(Exception $e)
	{
	}
}



function insertCity($name){
	
	try
	{
		$name=clean_data($name);
		$name = ucfirst(strtolower($name));
		$duplicate=checkForDuplicateCity($name);
		if(validateForNull($name) && !$duplicate)
		{
			$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="INSERT INTO
		      fin_city (city_name, created_by, last_updated_by, date_added, date_modified)
			  VALUES
			  ('$name', $admin_id, $admin_id, NOW(), NOW())";
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
function insertCityIfNotDuplicate($name)
{
	try
	{
		$name=clean_data($name);
		$name = ucfirst(strtolower($name));
		$duplicate=checkForDuplicateCity($name);
		if(validateForNull($name) && !$duplicate)
		{
			$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="INSERT INTO
		      fin_city (city_name, created_by, last_updated_by, date_added, date_modified)
			  VALUES
			  ('$name', $admin_id, $admin_id, NOW(), NOW())";
		$result=dbQuery($sql);	
			
		return "success";
		}
		else
		{
		 return $duplicate;
		}
	}
	catch(Exception $e)
	{
	}
	
	}

function deleteCity($id){
	
	try
	{
		if(checkForNumeric($id) && !checkIfCityInUse($id))
		{
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="DELETE FROM
			  fin_city
			  WHERE city_id=$id";
		dbQuery($sql);	
		return  "success";
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

function updateCity($id,$name){
	
	try
	{
		$name=clean_data($name);
		$name = ucfirst(strtolower($name));
		$duplicate=checkForDuplicateCity($name,$id);
		if(validateForNull($name) && checkForNumeric($id) && !$duplicate)
		{
			
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="UPDATE fin_city
			  SET city_name='$name', last_updated_by=$admin_id, date_modified=NOW()
			  WHERE city_id=$id";	  
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

function checkForDuplicateCity($name,$id=false)
{
	try{
		$sql="SELECT city_id 
			  FROM 
			  fin_city 
			  WHERE city_name='$name'";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND city_id!=$id";		  
		$result=dbQuery($sql);	
		
		if(dbNumRows($result)>0)
		{
			$resultArray=dbResultToArray($result);
			
			return $resultArray[0][0]; //duplicate found
			} 
		else
		{
			return false;
			}	 
		}
	catch(Exception $e)
	{
		
		}
	
	}

function getCityByID($id)
{
	$sql="SELECT city_id, city_name
			  FROM 
			  fin_city 
			  WHERE city_id=$id";
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
function checkIfCityInUse($id)
{
	$sql="SELECT city_id
	      FROM fin_our_company
		  WHERE city_id=$id LIMIT 0, 1";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		return true;
		}
	
		$sql="SELECT city_id
	      FROM fin_customer
		  WHERE city_id=$id LIMIT 0, 1";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		return true;
		}
	
	$sql="SELECT city_id
	      FROM fin_guarantor
		  WHERE city_id=$id LIMIT 0, 1";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		return true;
		}	
	
	$sql="SELECT city_id
	      FROM fin_vehicle_dealer
		  WHERE city_id=$id LIMIT 0, 1";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		return true;
		}		
		
		return false;
			
		  
	}	
?>