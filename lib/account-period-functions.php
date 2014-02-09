<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("area-functions.php");
require_once("account-head-functions.php");
require_once("our-company-function.php");
require_once("agency-functions.php");
require_once("adminuser-functions.php");
require_once("common.php");
require_once("bd.php");

$accounts=1;

function getPeriodForUser($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT period_id, from_period, to_period, last_updated FROM fin_ac_period_date WHERE admin_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return array($resultArray[0][1],$resultArray[0][2],$resultArray[0][3]);
	else
	return "error";
	}
}

function setPeriodForUser($id,$from,$to)
{
	if(checkForNumeric($id) && validateForNull($from,$to))
	{
	if(isset($from) && validateForNull($from))
		{
	$from = str_replace('/', '-', $from);
		$from=date('Y-m-d',strtotime($from));
		}
if(isset($to) && validateForNull($to))
		{
	$to = str_replace('/', '-', $to);
		$to=date('Y-m-d',strtotime($to));
		}
			
	if(getPeriodForUser($id)=="error")
	{	
	$sql="INSERT INTO fin_ac_period_date( from_period, to_period, admin_id, last_updated ) VALUES ('$from', '$to', $id, NOW())";
	$result=dbQuery($sql);
	return "success";
	}
	else
	{
		updatePeriodForUser($id,$from,$to);
		return "success";
		}
	return "error";	
		
}
}

function updatePeriodForUser($id,$from,$to)
{
	if(checkForNumeric($id) && validateForNull($from,$to))
	{
		if(isset($from) && validateForNull($from))
		{
	$from = str_replace('/', '-', $from);
		$from=date('Y-m-d',strtotime($from));
		}
if(isset($to) && validateForNull($to))
		{
	$to = str_replace('/', '-', $to);
		$to=date('Y-m-d',strtotime($to));
		}
		
	$sql="UPDATE fin_ac_period_date SET from_period='$from', to_period='$to', admin_id=$id, last_updated=NOW()  WHERE admin_id=$id";
	$result=dbQuery($sql);
	return "success";
	}
	return "error";
	
}
	
	

function getCurrentDateForUser($id)
{
	if(checkForNumeric($id))
	{
		
	$sql="SELECT curr_date FROM fin_ac_period_date WHERE admin_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	return "error";
	}
}

function setCurrentDateForUser($id,$curr_date)
{
	if(checkForNumeric($id) && validateForNull($curr_date))
	{
	if(isset($curr_date) && validateForNull($curr_date))
	{
	$curr_date = str_replace('/', '-', $curr_date);
		$curr_date=date('Y-m-d',strtotime($curr_date));
	}

		
	if(getCurrentDateForUser($id)=="error")
	{	
	$sql="INSERT INTO fin_ac_period_date( curr_date, admin_id, last_updated ) VALUES ('$curr_date', $id, NOW())";
	$result=dbQuery($sql);
	return "success";
	}
	else
	{
		updateCurrentDateForUser($id,$curr_date);
		return "success";
		}
	return "error";	
		
}
}
function updateCurrentDateForUser($id,$curr_date)
{
	if(checkForNumeric($id) && validateForNull($curr_date))
	{
	if(isset($curr_date) && validateForNull($curr_date))
	{
	$curr_date = str_replace('/', '-', $curr_date);
		$curr_date=date('Y-m-d',strtotime($curr_date));
	}	
	$sql="UPDATE fin_ac_period_date SET curr_date='$curr_date', last_updated=NOW()  WHERE admin_id=$id";
	$result=dbQuery($sql);
	return "success";
	}
	return "error";
	
}

function getCurrentCompanyForUser($id) // return an array 1 = > company or agency_id, 2 = >  0 if oc or 1 if agency
{
	if(checkForNumeric($id))
	{
	$sql="SELECT period_id, curr_company, company_type, last_updated FROM fin_ac_period_date WHERE admin_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return array($resultArray[0][1],$resultArray[0][2]);
	else
	return "error";
	}
	
	}
	
function setCurrentCompanyForUser($id,$curr_company,$company_type)
{
	if(checkForNumeric($id,$curr_company,$company_type))
	{
	
			
	if(getCurrentCompanyForUser($id)=="error")
	{	
	$sql="INSERT INTO fin_ac_period_date( curr_company, company_type, admin_id, last_updated ) VALUES ($curr_company, $company_type, $id, NOW())";
	$result=dbQuery($sql);
	return "success";
	}
	else
	{
		updateCurrentCompanyForUser($id,$curr_company,$company_type);
		return "success";
		}
	return "error";	
		
}
}

function updateCurrentCompanyForUser($id,$curr_company,$company_type)
{
	if(checkForNumeric($id,$curr_company,$company_type))
	{
	$sql="UPDATE fin_ac_period_date SET curr_company=$curr_company, company_type=$company_type , admin_id=$id, last_updated=NOW()  WHERE admin_id=$id";
	$result=dbQuery($sql);
	return "success";
	}
	return "error";
	
}	
?>