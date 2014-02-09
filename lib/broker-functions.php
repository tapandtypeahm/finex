<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("common.php");
require_once("bd.php");

function listBrokers(){
	
	try
	{
		$sql="SELECT broker_id, broker_name, broker_address, broker_contact_no
		  FROM fin_broker ORDER BY broker_name";
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

function insertBroker($broker_name,$address='',$contact_no=''){
	
	try
	{
		$broker_name=clean_data($broker_name);
		$broker_name = ucwords(strtolower($broker_name));
		$address=clean_data($address);
		if($address==null || $address=="")
		{
			$address="NA";
			}
		if($contact_no==null || $contact_no=="")
		{
			$contact_no="NA";
			}	
		if(validateForNull($broker_name))
			{
			$admin_id=$_SESSION['adminSession']['admin_id'];
			$sql="INSERT INTO fin_broker
					(broker_name, broker_address,broker_contact_no ,created_by, last_updated_by, date_added, date_modified)
					VALUES
					('$broker_name','$address','$contact_no',$admin_id,$admin_id,NOW(),NOW())";
					
			dbQuery($sql);
			$dealer_id=dbInsertId();
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

	



function checkIfBrokerIsInUse($broker_id)
{
	
	$sql="SELECT broker_id FROM fin_file WHERE broker_id=$broker_id";
	$result=dbQuery($sql);
	
	if(dbNumRows($result)>0)
	{	
	return true;
	}
	else
	return false;
	
	}	

function deleteBroker($id){
	
	try
	{
		if(checkForNumeric($id) && !checkIfBrokerIsInUse($id))
		{
		$sql="DELETE FROM fin_broker WHERE broker_id=$id";
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

function updateBroker($id,$broker_name,$address,$contact_no){
	
	try
	{
		$broker_name=clean_data($broker_name);
		$broker_name = ucwords(strtolower($broker_name));
		$address=clean_data($address);
		if($address==null || $address=="")
		{
			$address="NA";
			}
		if($contact_no==null || $contact_no=="")
		{
			$contact_no="NA";
			}	
		if(validateForNull($broker_name)  && checkForNumeric($id))
			{
			
			$admin_id=$_SESSION['adminSession']['admin_id'];
			$sql="UPDATE fin_broker
					SET broker_name = '$broker_name', broker_address ='$address', broker_contact_no='$contact_no', last_updated_by=$admin_id, date_modified=NOW()
					WHERE broker_id=$id";
			
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

function getBrokerById($id){
	
	try
	{
		$sql="SELECT broker_id, broker_name, broker_address, broker_contact_no
		  FROM fin_broker
		  WHERE broker_id=$id";
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

function getBrokerNameFromBrokerId($id)
{
try
	{
		$sql="SELECT  broker_name
		  FROM fin_broker
		  WHERE broker_id=$id";
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
?>