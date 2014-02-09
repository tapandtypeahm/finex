<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("area-functions.php");
require_once("account-head-functions.php");
require_once("account-period-functions.php");
require_once("account-ledger-functions.php");
require_once("common.php");
require_once("bd.php");

function getAllPayments()
{
	$sql="SELECT payment_id,amount,from_ledger_id,to_ledger_id,from_customer_id,trans_date,remarks,created_by,last_updated_by,date_added,date_modified
			  FROM fin_ac_payment";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 
	}
function getPaymentById($id)
{
	
	if(checkForNumeric($id))
	{
		$sql="SELECT payment_id,amount,from_ledger_id,to_ledger_id,from_customer_id,trans_date,remarks,created_by,last_updated_by,date_added,date_modified
			  FROM fin_ac_payment
			  WHERE payment_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else
		return "error"; 
		
		}
	}

function addPayment($amount,$trans_date,$to_ledger,$from_ledger,$remarks)
{
	$admin_id=$_SESSION['adminSession']['admin_id'];

	if(substr($from_ledger, 0, 1) === 'L')
	{
		$from_ledger=str_replace('L','',$from_ledger);
		$from_ledger=intval($from_ledger);
		$from_customer="NULL";
		}
	else if(substr($from_ledger, 0, 1) === 'C')
	{
		$from_ledger=str_replace('C','',$from_ledger);
		$from_customer=intval($from_ledger);
		$from_ledger="NULL";
		}	
		
	if(!(checkForNumeric($from_ledger) || checkForNumeric($from_customer)))
	{
		return "ledger_error";
		}
	if(checkForNumeric($amount,$to_ledger,$admin_id) && $to_ledger>0 && validateForNull($trans_date))
	{
			if(isset($trans_date) && validateForNull($trans_date))
			{
		$trans_date = str_replace('/', '-', $trans_date);
			$trans_date=date('Y-m-d',strtotime($trans_date));
			}
			$sql="INSERT INTO fin_ac_payment (amount,from_ledger_id,from_customer_id,to_ledger_id,trans_date,remarks,created_by,last_updated_by,date_added,date_modified)
			VALUES ($amount,$from_ledger,$from_customer,$to_ledger,'$trans_date','$remarks',$admin_id,$admin_id,NOW(),NOW())";
			$result=dbQuery($sql);
			return "success";
	}
	return "error";	
}

function removePayment($id)
{
	if(checkForNumeric($id))
	{
		$sql="DELETE FROM fin_ac_payment where payment_id=$id";
		dbQuery($sql);
		return "success";
		}
		return "error";
	}
	
function updatePayment($id,$amount,$trans_date,$to_ledger,$from_ledger,$remarks)
{
	$admin_id=$_SESSION['adminSession']['admin_id'];
	if(!(checkForNumeric($from_ledger) || checkForNumeric($from_customer)))
	{
		return "ledger_error";
		}
	if(substr($from_ledger, 0, 1) === 'L')
	{
		$from_ledger=str_replace('L','',$from_ledger);
		$from_ledger=intval($from_ledger);
		$from_customer="NULL";
		}
	else if(substr($from_ledger, 0, 1) === 'C')
	{
		$from_ledger=str_replace('C','',$from_ledger);
		$from_customer=intval($from_ledger);
		$from_ledger="NULL";
		}	
		
	if(checkForNumeric($amount,$to_ledger,$admin_id,$id) && validateForNull($trans_date))
	{
			if(isset($trans_date) && validateForNull($trans_date))
			{
			$trans_date = str_replace('/', '-', $trans_date);
			$trans_date=date('Y-m-d',strtotime($trans_date));
			}
			$sql="UPDATE fin_ac_payment SET amount=$amount, from_ledger_id=$from_ledger, to_ledger_id=$to_ledger, from_customer_id=$from_customer, trans_date='$trans_date', remarks='$remarks', last_updated_by=$admin_id, date_modified=NOW()
			WHERE payment_id=$id";
			$result=dbQuery($sql);
			return "success";
	}
	return "error";	
	
	}	
	
function getPaymentsForNonAccountingLedgerId($ledger_id) // Payments for accounting ledgers
{
	$sql="SELECT payment_id,amount,from_ledger_id,from_customer_id,to_ledger_id,trans_date,remarks,created_by,last_updated_by,date_added,date_modified
		  FROM fin_ac_payment
		  WHERE from_ledger_id=$ledger_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 
	
	}

function getPaymentsForCustomerLedgerId($ledger_id) // receipts for customer
{
	$sql="SELECT payment_id,amount,from_ledger_id,from_customer_id,to_customer_id,trans_date,created_by,last_updated_by,date_added,date_modified
		  FROM fin_ac_payment
		  WHERE from_customer_id=$ledger_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 
	
	}
	
function getPaymentsForAccountingLedgerId($ledger_id) // receipts for bank or cash
{
	$sql="SELECT payment_id,amount,from_ledger_id,from_customer_id,to_ledger_id,trans_date,created_by,last_updated_by,date_added,date_modified
			  FROM fin_ac_payment
			  WHERE from_ledger_id=$ledger_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 
	
	}

function getPaymentsForLedgerIdMonthWise()
{
	
	$sql="SELECT payment_id,amount,from_ledger_id,to_ledger_id,from_customer_id,trans_date,Datename(month, trans_date) + ' - ' + Datename(year, trans_date) AS month_year,created_by,last_updated_by,date_added,date_modified
			  FROM fin_ac_payment";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return "error"; 	
	}

function getPaymentsForLedgerIdForMonth()
{
	
	}			
?>