<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("area-functions.php");
require_once("account-head-functions.php");
require_once("account-period-functions.php");
require_once("account-ledger-functions.php");
require_once("common.php");
require_once("bd.php");

function getAllReceipts()
{
	$sql="SELECT receipt_id,amount,from_ledger_id,to_ledger_id,to_customer_id,trans_date,created_by,last_updated_by,date_added,date_modified
			  FROM fin_ac_receipt";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 
	}
function getReceiptById($id)
{
	
	if(checkForNumeric($id))
	{
		$sql="SELECT receipt_id,amount,from_ledger_id,to_ledger_id,to_customer_id,trans_date,created_by,last_updated_by,date_added,date_modified
			  FROM fin_ac_receipt
			  WHERE receipt_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else
		return "error"; 
		
		}
	}

function insertReceipt($amount,$trans_date,$to_ledger,$from_ledger,$remarks)
{
	$admin_id=$_SESSION['adminSession']['admin_id'];
	
	if(substr($to_ledger, 0, 1) === 'L')
	{
		$to_ledger=str_replace('L','',$to_ledger);
		$to_ledger=intval($to_ledger);
		$to_customer="NULL";
		}
	else if(substr($to_ledger, 0, 1) === 'C')
	{
		$to_ledger=str_replace('C','',$to_ledger);
		$to_customer=intval($to_ledger);
		$to_ledger="NULL";
		}	
		
	if(!(checkForNumeric($to_ledger) || checkForNumeric($to_customer)))
	{
		return "ledger_error";
		}
	if(checkForNumeric($amount,$from_ledger,$admin_id) && $from_ledger>0 && validateForNull($trans_date))
	{
			if(isset($trans_date) && validateForNull($trans_date))
			{
		$trans_date = str_replace('/', '-', $trans_date);
			$trans_date=date('Y-m-d',strtotime($trans_date));
			}
			$sql="INSERT INTO fin_ac_receipt (amount,from_ledger_id,to_ledger_id,to_customer_id,trans_date,remarks,created_by,last_updated_by,date_added,date_modified)
			VALUES ($amount,$from_ledger,$to_ledger,$to_customer,'$trans_date','$remarks',$admin_id,$admin_id,NOW(),NOW())";
			$result=dbQuery($sql);
			return "success";
	}
	return "error";	
}

function deleteReceipt($id)
{
	if(checkForNumeric($id))
	{
		$sql="DELETE FROM fin_ac_receipt where receipt_id=$id";
		dbQuery($sql);
		return "success";
		}
		return "error";
	}
	
function updateReceipt($id,$amount,$trans_date,$to_ledger,$from_ledger,$remarks)
{
	$admin_id=$_SESSION['adminSession']['admin_id'];
	if(!(checkForNumeric($to_ledger) || checkForNumeric($to_customer)))
	{
		return "ledger_error";
		}
	if(substr($to_ledger, 0, 1) === 'L')
	{
		$to_ledger=str_replace('L','',$to_ledger);
		$to_ledger=intval($to_ledger);
		$to_customer="NULL";
		}
	else if(substr($to_ledger, 0, 1) === 'C')
	{
		$to_ledger=str_replace('C','',$to_ledger);
		$to_customer=intval($to_ledger);
		$to_ledger="NULL";
		}	
		
	if(checkForNumeric($amount,$from_ledger,$admin_id,$id) && validateForNull($trans_date))
	{
			if(isset($trans_date) && validateForNull($trans_date))
			{
			$trans_date = str_replace('/', '-', $trans_date);
			$trans_date=date('Y-m-d',strtotime($trans_date));
			}
			$sql="UPDATE fin_ac_receipt SET amount=$amount, from_ledger_id=$from_ledger, to_ledger_id=$to_ledger, to_customer_id=$to_customer, trans_date='$trans_date', remarks='$remarks', last_updated_by=$admin_id, date_modified=NOW()
			WHERE receipt_id=$id";
			$result=dbQuery($sql);
			return "success";
	}
	return "error";	
	
	}	
	
function getReceiptsForNonAccountingLedgerId($ledger_id) // receipts for accounting ledgers
{
	$sql="SELECT receipt_id,amount,from_ledger_id,to_ledger_id,trans_date,created_by,last_updated_by,date_added,date_modified
		  FROM fin_ac_receipt
		  WHERE to_ledger_id=$ledger_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 
	
	}

function getReceiptsForCustomerLedgerId($ledger_id) // receipts for customer
{
	$sql="SELECT receipt_id,amount,from_ledger_id,to_customer_id,trans_date,created_by,last_updated_by,date_added,date_modified
		  FROM fin_ac_receipt
		  WHERE to_customer_id=$ledger_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 
	
	}


function getReceiptsForAccountingLedgerId($ledger_id) // receipts for bank or cash
{
	$sql="SELECT receipt_id,amount,from_ledger_id,to_ledger_id,trans_date,created_by,last_updated_by,date_added,date_modified
			  FROM fin_ac_receipt
			  WHERE from_ledger_id=$ledger_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 
	
	}


function getReceiptsForLedgerIdMonthWise()
{
	
	$sql="SELECT receipt_id,amount,from_ledger_id,to_ledger_id,to_customer_id,trans_date, Datename(month, trans_date) + ' - ' + Datename(year, trans_date) AS month_year,created_by,last_updated_by,date_added,date_modified
			  FROM fin_ac_receipt";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 	
	
	}

function getReceiptsForLedgerIdForMonth()
{
	
	}			
?>