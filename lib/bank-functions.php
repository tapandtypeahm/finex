<?php 
require_once("cg.php");
require_once("bd.php");
require_once("common.php");

		
function listBanks(){
	
	try
	{
		$sql="SELECT bank_id,bank_name
		      FROM fin_bank";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		$returnArray=array();
		$i=0;
		foreach($resultArray as $resultItem)
		{
			$returnArray[++$i]['bank_id']=$resultItem['bank_id'];
			$returnArray[$i]['bank_name']=$resultItem['bank_name'];
		$returnArray[$i]['branchArray']=getBranchesForBank($resultItem['bank_id']);
		
		}
		
		return $returnArray; 
	}
	catch(Exception $e)
	{
	}
}

function insertBank($name,$branch){
	
	try
	{
		$name=clean_data($name);
		$name = ucwords(strtolower($name));
		$branch=clean_data($branch);
		$branch = ucwords(strtolower($branch));
		$duplicate=checkForDuplicateBank($name);
		if(validateForNull($name,$branch) && !$duplicate)
		{
			
			$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="INSERT INTO
		      fin_bank (bank_name, created_by, last_updated_by, date_added, date_modified)
			  VALUES
			  ('$name',  $admin_id, $admin_id, NOW(), NOW())";
		$result=dbQuery($sql);	
		$bank_id = dbInsertId();	
		$duplicateBranch=insertBankBranch($bank_id,$branch); // $duplicateBranch = branch_id
			if($duplicateBranch==false)
		 		return "error";
				else
		return array($bank_id,$duplicateBranch); 		
		}
		else if($duplicate)
		{	
			
			$bank_id=$duplicate;
			$duplicateBranch=insertBankBranch($bank_id,$branch);
			if($duplicateBranch==false)
		 		return "error";
				else
			return array($bank_id,$duplicateBranch); 	
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

function insertBankBranch($bank_id,$branch)
{
	try
	{
		
		$branch=clean_data($branch);
		$branch = ucwords(strtolower($branch));
		$duplicate=checkForDuplicateBankBranch($bank_id,$branch);
		
		if(validateForNull($branch) && !$duplicate && checkForNumeric($bank_id))
		{
			
			$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="INSERT INTO
		      fin_bank_branch (branch_name, bank_id)
			  VALUES
			  ('$branch',  $bank_id)";
		$result=dbQuery($sql);	
		return dbInsertId();	
		}
		else if($duplicate)
		{	
		return $duplicate;
		}
	}
	catch(Exception $e)
	{
	}
	
	}

function deleteBank($id){
	
	try
	{
		$inUse=checkIfBankInUse($id);
		if($inUse==false)
		{
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="DELETE FROM
			  fin_bank
			  WHERE bank_id=$id";
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

function updateBank($id,$name){
	
	try
	{
		$name=clean_data($name);
		$name = ucfirst(strtolower($name));
		$duplicate=checkForDuplicateBank($name,$id);
		if(validateForNull($name) && checkForNumeric($id) && !$duplicate)
		{
			$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="UPDATE 
			  fin_bank
			  SET bank_name='$name', last_updated_by=$admin_id, date_modified=NOW()
			  WHERE bank_id=$id";
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

function checkForDuplicateBank($name,$id=false)
{
	try{
		
	$name=clean_data($name);
	$name = ucwords(strtolower($name));
		$sql="SELECT bank_id 
			  FROM 
			  fin_bank 
			  WHERE bank_name='$name'";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND bank_id!=$id";		  
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
	catch(Exception $e)
	{
		
		}
}
	
function checkForDuplicateBankBranch($bank_id,$branch,$id=false)
{
	try{
	
	$branch=clean_data($branch);
	$branch = ucwords(strtolower($branch));
		$sql="SELECT branch_id 
			  FROM 
			  fin_bank_branch 
			  WHERE branch_name='$branch'
			  AND bank_id=$bank_id";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND branch_id!=$id";		  
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
	catch(Exception $e)
	{
		
	}
}

function getBankNameByID($id)
{
	
	$sql="SELECT  bank_name
		  FROM 
		  fin_bank
		  WHERE fin_bank.bank_id=$id";
		$result=dbQuery($sql);	
		$resultArray=dbResultToArray($result);
		
	if(dbNumRows($result)>0)
	{
		return $resultArray[0][0];
	}
	}

function getBankByID($id)
{
	$sql="SELECT fin_bank.bank_id, bank_name
		  FROM 
		  fin_bank
		  WHERE fin_bank.bank_id=$id";
		$result=dbQuery($sql);	
		$resultArray=dbResultToArray($result);
		$returnArray=array();
	if(dbNumRows($result)>0)
	{
		$sql="SELECT branch_id,branch_name
		      FROM 
			  fin_bank_branch
			  WHERE bank_id=$id";
		$res=dbQuery($sql);
		$branchArray=dbResultToArray($res);
		$returnArray[0]['bank_id']=$resultArray[0]['bank_id'];	 
		$returnArray[0]['bank_name']=$resultArray[0]['bank_name'];	 
		$returnArray[0]['branch_array']=$branchArray;
		return $returnArray[0];
		}
	else
	{
		return false;
		}
	}


	
function insertIfNotDuplicateBank($name,$branch){
	
	return insertBank($name,$branch);
	
}	

function getBranchesForBank($bank_id)
{
	$sql="SELECT  branch_id,branch_name
		  FROM 
		  fin_bank_branch 
		  WHERE bank_id=$bank_id";
		$result=dbQuery($sql);	
	
		$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray;
		}
	else
	{
		return false;
		}
	
	
}

function checkIfBankInUse($bankId)
{
	$sql="SELECT bank_id 
		  FROM fin_loan_cheque
		  WHERE bank_id=$bankId LIMIT 0, 1";
	$result=dbQuery($sql);
	
	if(dbNumRows($result)>0)
	return true;
	
	$sql="SELECT bank_id 
		  FROM fin_loan_penalty_cheque
		  WHERE bank_id=$bankId LIMIT 0, 1";
	$result1=dbQuery($sql);
	
	if(dbNumRows($result1)>0)
	return true;
	
	$sql="SELECT bank_id 
		  FROM fin_agency_settle_cheque
		  WHERE bank_id=$bankId LIMIT 0, 1";
	$result1=dbQuery($sql);
	
	if(dbNumRows($result1)>0)
	return true;
	
	$sql="SELECT bank_id 
		  FROM fin_loan_emi_payment_cheque
		  WHERE bank_id=$bankId LIMIT 0, 1";
	$result=dbQuery($sql);
	
	if(dbNumRows($result)>0)
	return true;
	else
	return false;
	
	
		  
	}

function updateBranch($id,$branch)
{
	if(validateForNull($branch))
	{
	$sql="UPDATE fin_bank_branch
	      SET branch_name='$branch'
		  WHERE branch_id=$id";
	dbQuery($sql);	  
	}
	}
function getBranchhById($id)
{
	
	if(checkForNumeric($id))
	{
		
		$sql="SELECT branch_name
		      FROM fin_bank_branch
			  WHERE branch_id=$id";
		$result=dbQuery($sql);
		
		$resultArray=dbResultToArray($result);
		
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
		else
		return false;	  
		}
	
	}	
function deleteBankBranch($id)
{
	if(checkForNumeric($id) && !checkIfBranchInUse($id))
	{
		$sql="DELETE FROM fin_bank_branch
		      WHERE branch_id=$id";
		$result=dbQuery($sql);	
		return "success";  
		}
	else
	{
		return "error";
		}	
	}

function checkIfBranchInUse($id)
{
	$sql="SELECT branch_id 
		  FROM fin_loan_cheque
		  WHERE branch_id=$id LIMIT 0, 1";
	$result=dbQuery($sql);
	
	if(dbNumRows($result)>0)
	return true;
	
	$sql="SELECT branch_id 
		  FROM fin_loan_penalty_cheque
		  WHERE branch_id=$id LIMIT 0, 1";
	$result1=dbQuery($sql);
	
	if(dbNumRows($result1)>0)
	return true;
	
	$sql="SELECT branch_id 
		  FROM fin_agency_settle_cheque
		  WHERE branch_id=$id LIMIT 0, 1";
	$result1=dbQuery($sql);
	
	if(dbNumRows($result1)>0)
	return true;
	
	$sql="SELECT branch_id 
		  FROM fin_loan_emi_payment_cheque
		  WHERE branch_id=$id LIMIT 0, 1";
	$result2=dbQuery($sql);
	
	if(dbNumRows($result2)>0)
	return true;
	else
	return false;
	}
function getBankIdFromName($name)
{
	$sql="SELECT bank_id FROM fin_bank WHERE bank_name='$name'";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0][0];
		}
	else
	{return false;
		}	
	}				
?>