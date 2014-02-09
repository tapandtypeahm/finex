<?php
require_once 'cg.php';
require_once 'bd.php';
require_once 'common.php';
require_once 'account-period-functions.php';
function addHead($headName,$parent_id=0)
{
	
	$headName=clean_data($headName);
	$headName=ucwords(strtolower($headName));
	if(validateForNull($headName) && checkForNumeric($parent_id) && !checkForDuplicateHead($headName,$parent_id))
	{
		$sql="INSERT INTO fin_ac_account_heads(head_name,parent_id) VALUES('$headName',$parent_id)";
		dbQuery($sql);
		return "success";
		}
	else
	return "error";	
	
}
function getBankAccountsHeadId()
{
	$sql="SELECT head_id FROM fin_ac_account_heads WHERE head_name='Bank Accounts'";
	$result=dbQuery($sql);
	$result_array=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $result_array[0][0];
	else
	return false;
	}

function getCashHeadId()
{
	$sql="SELECT head_id FROM fin_ac_account_heads WHERE head_name='Cash In Hand'";
	$result=dbQuery($sql);
	$result_array=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $result_array[0][0];
	else
	return false;
	}	

function getUnsecuredLoansId()
{
	$sql="SELECT head_id FROM fin_ac_account_heads WHERE head_name='Unsecured Loans'";
	$result=dbQuery($sql);
	$result_array=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $result_array[0][0];
	else
	return false;
	}	


function getIndirectIncomeId()
{
	$sql="SELECT head_id FROM fin_ac_account_heads WHERE head_name='Indirect Income'";
	$result=dbQuery($sql);
	$result_array=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $result_array[0][0];
	else
	return false;
	}	

function getSundryDebtorsId()
{
	$sql="SELECT head_id FROM fin_ac_account_heads WHERE head_name!='Sundry Debtors'";
	$result=dbQuery($sql);
	$result_array=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $result_array[0][0];
	else
	return false;
	}		

function updateHead($id,$headName,$parent_id=0)
{
	$headName=clean_data($headName);
	$headName=ucwords(strtolower($headName));
	if(validateForNull($headName) && checkForNumeric($parent_id) && !checkForDuplicateHead($headName,$parent_id,$id))
	{
		$sql="UPDATE fin_ac_account_heads
		      SET head_name='$headName', parent_id=$parent_id
			  WHERE head_id = $id";
		dbQuery($sql);
		return "success";
		}
	else
	return "error";	
	
	}	
function checkForDuplicateHead($headName,$parent_id,$id=false)
{
	$headName=clean_data($headName);
	if(validateForNull($headName) && checkForNumeric($parent_id))
	{
		$sql="SELECT head_id FROM fin_ac_account_heads WHERE head_name='$headName' AND parent_id=$parent_id";
		if($id!=false)
		$sql=$sql." AND head_id != $id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray;
		else
		return false;
		}
	
}

function deleteHead($id)
{
	if(checkForNumeric($id))
	{
		$sql="DELETE FROM fin_ac_account_heads WHERE head_id=$id OR parent_id=$id";
		dbQuery($sql);
		return "success";
	}
	else 
	return "error";
}	

function listSubHeads()
{
	$sql="SELECT head_id,head_name,parent_id FROM fin_ac_account_heads WHERE parent_id!=0";
	$result=dbQuery($sql);
	$result_array=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $result_array;
	else
	return false;
}
function listHeadsWithoutChild()
{
	$heads_array=listHeads();
	$returnArray=array();
	foreach($heads_array as $parent_head)
	{
		$head_id=null;
		$head_id=$parent_head['head_id'];
		$subheads=getSubHeadsOfHead($head_id);
		if($subheads==false)
		{
			$returnArray[]=$parent_head;
			}
		}
	return $returnArray;
}
	
function listHeads()
{
	$sql="SELECT head_id,head_name,parent_id FROM fin_ac_account_heads WHERE parent_id=0";
	$result=dbQuery($sql);
	$result_array=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $result_array;
	else
	return false;
	
	}	

function listAllHeads() // All heads and subheads without bank account WHERE head_name!='Bank Accounts' 
{
	$sql="SELECT head_id,head_name,parent_id FROM fin_ac_account_heads ORDER BY head_name";
	$result=dbQuery($sql);
	$result_array=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $result_array;
	else
	return false;
	
	}		

function getSubHeadsOfHead($id)
{
	$sql="SELECT head_id,head_name,parent_id FROM fin_ac_account_heads WHERE parent_id=$id";
	$result=dbQuery($sql);
	$result_array=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $result_array;
	else
	return false;
	
}

function getHeadById($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT head_id ,  head_name , parent_id FROM fin_ac_account_heads WHERE head_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		}	
		return false;
}	

 ?>