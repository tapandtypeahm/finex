<?php
require_once("cg.php");
require_once("city-functions.php");
require_once("loan-functions.php");
require_once("file-functions.php");
require_once("account-ledger-functions.php");
require_once("account-head-functions.php");
require_once("common.php");
require_once("bd.php");

function getBooksStartingDateForAgency($agency_id)
{
	if(checkForNumeric($agency_id))
	{
		$sql="SELECT ac_starting_date FROM fin_Ac_settings WHERE agency_id=$agency_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
		else
		return false;	
	}
	return false;
	
}

function getBooksStartingDateForOC($oc_id) // our_company
{
	if(checkForNumeric($oc_id))
	{
		$sql="SELECT ac_starting_date FROM fin_Ac_settings WHERE our_company_id=$oc_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
		else
		return false;	
	}
	return false;
	
}

function getAccountSettingsForAgency($agency_id)
{
	if(checkForNumeric($agency_id))
	{
		$sql="SELECT * FROM fin_Ac_settings WHERE agency_id=$agency_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else
		return false;	
	}
	return false;
	
	}
	
function getAccountSettingsForOC($oc_id)
{
	if(checkForNumeric($oc_id))
	{
		$sql="SELECT * FROM fin_Ac_settings WHERE our_company_id=$oc_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else
		return false;	
	}
	return false;
	
}

function getCurrentBalanceForLedger($ledger_id)
{
	if(checkForNumeric($ledger_id))
	{
		$sql="SELECT current_balance, current_balance_cd FROM fin_ac_ledgers WHERE ledger_id=$ledger_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else
		return false;
		}
}

function getCurrentBalanceForCustomer($ledger_id)
{
	if(checkForNumeric($ledger_id))
	{
		$sql="SELECT current_balance, current_balance_cd FROM fin_customer WHERE customer_id=$ledger_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else
		return false;
		}
}

function debitAccountingLedger($ledger_id,$amount) // cash or bank
{
	if(checkForNumeric($ledger_id,$amount))
	{
		$current_balance_array=getCurrentBalanceForLedger($ledger_id);
		$current_balance=$current_balance_array[0];
		$current_balance_cd=$current_balance_array[1];
		
		if($current_balance_cd==0) // if current balance is debit
		{
			$current_balance=$current_balance+$amount;
		}
		else if($current_balance_cd==1)
		{
			if($current_balance>$amount)
			{
				$current_balance=$current_balance-$amount;
				}
			else if($amount>$current_balance)	
			{
				$current_balance=$amount-$current_balance;
				$current_balance_cd=0; // debit
				}
			else if($current_balance==$amount)
			{
				$current_balance_cd=0;
				$current_balance=0;
				}	
		}	
		$sql="UPDATE fin_ac_ledgers SET current_balance = $current_balance , current_balance_cd = $current_balance_cd
		      WHERE ledger_id=$ledger_id";
		dbQuery($sql);
		return true;
		}
		else return false;
}	
function creditAccountingLedger($ledger_id,$amount) // cash or bank
{
	if(checkForNumeric($ledger_id,$amount))
	{
		$current_balance_array=getCurrentBalanceForLedger($ledger_id);
		$current_balance=$current_balance_array[0];
		$current_balance_cd=$current_balance_array[1];
		if($current_balance_cd==1)
		{
			$current_balance=$current_balance+$amount;
		}
		else if($current_balance_cd==0)
		{
			if($current_balance>$amount)
			{
				$current_balance=$current_balance-$amount;
				}
			else if($amount>$current_balance)	
			{
				$current_balance=$amount-$current_balance;
				$current_balance_cd=1;
				}
			else if($current_balance==$amount)
			{
				$current_balance_cd=0;
				$current_balance=0;
				}	
		}	
		$sql="UPDATE fin_ac_ledgers SET current_balance=$current_balance , current_balance_cd= $current_balance_cd
		      WHERE ledger_id=$ledger_id";
		dbQuery($sql);
		return true;
		}
		else return false;
}	

function debitAccountingCustomer($customer_id,$amount) // cash or bank
{
	
	if(checkForNumeric($customer_id,$amount))
	{
		$current_balance_array=getCurrentBalanceForCustomer($customer_id);
		$current_balance=$current_balance_array[0];
		$current_balance_cd=$current_balance_array[1];
		if($current_balance_cd==0) // if current balance is debit
		{
			$current_balance=$current_balance+$amount;
		}
		else if($current_balance_cd==1)
		{
			if($current_balance>$amount)
			{
				$current_balance=$current_balance-$amount;
				}
			else if($amount>$current_balance)	
			{
				$current_balance=$amount-$current_balance;
				$current_balance_cd=0; // debit
				}
			else if($current_balance==$amount)
			{
				$current_balance_cd=0;
				$current_balance=0;
				}	
		}	
		$sql="UPDATE fin_customer SET current_balance=$current_balance , current_balance_cd= $current_balance_cd
		      WHERE customer_id=$customer_id";
		dbQuery($sql);
		return true;
		}
		else return false;
}	
function creditAccountingCustomer($customer_id,$amount) // cash or bank
{
	
	if(checkForNumeric($customer_id,$amount))
	{
		$current_balance_array=getCurrentBalanceForCustomer($customer_id);
		$current_balance=$current_balance_array[0];
		$current_balance_cd=$current_balance_array[1];
		
		if($current_balance_cd==1)
		{
			$current_balance=$current_balance+$amount;
		}
		else if($current_balance_cd==0)
		{
			if($current_balance>$amount)
			{
				$current_balance=$current_balance-$amount;
				}
			else if($amount>$current_balance)	
			{
				$current_balance=$amount-$current_balance;
				$current_balance_cd=1;
				}
			else if($current_balance==$amount)
			{
				$current_balance_cd=0;
				$current_balance=0;
				}	
		}	
		
		$sql="UPDATE fin_customer SET current_balance=$current_balance , current_balance_cd= $current_balance_cd
		      WHERE customer_id=$customer_id";
		dbQuery($sql);
		return true;
		}
		else return false;
}	

function getCashLedgerIdForAgency($agency_id)
{
	if(checkForNumeric($agency_id))
	{
	$cash_head_id=getCashHeadId();
	$sql="SELECT ledger_id FROM fin_ac_ledgers WHERE head_id=$cash_head_id AND agency_id=$agency_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	{
		createCashLedgerForAgnecy($agency_id);
		return getCashLedgerIdForAgency($agency_id);
		}
	}
	return false;
}
function getCashLedgerIdForOC($oc_id)
{
	if(checkForNumeric($oc_id))
	{
	$cash_head_id=getCashHeadId();
	$sql="SELECT ledger_id FROM fin_ac_ledgers WHERE head_id=$cash_head_id AND oc_id=$oc_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	{
		
		$result=createCashLedgerForOC($oc_id);
		if($result=="success")
		{
		return getCashLedgerIdForOC($oc_id);
		}
		}
	}
	return false;	
}

function getAdvanceInterestLedgerIdForOC($oc_id)
{
	if(checkForNumeric($oc_id))
	{
			
	$unsecured_loans_head_id=getUnsecuredLoansId();
	$sql="SELECT ledger_id FROM fin_ac_ledgers WHERE head_id=$unsecured_loans_head_id AND oc_id=$oc_id AND ledger_name='Auto Interest'";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	{
		
		$result=createAutoInterestLedgerForOC($oc_id);
		if($result=="success")
		{
		return getAdvanceInterestLedgerIdForOC($oc_id);
		}
		}
	}
	return false;	
}

function getAdvanceInterestLedgerIdForAgency($agency_id)
{
	if(checkForNumeric($agency_id))
	{
	$unsecured_loans_head_id=getUnsecuredLoansId();
	$sql="SELECT ledger_id FROM fin_ac_ledgers WHERE head_id=$unsecured_loans_head_id AND agency_id=$agency_id AND ledger_name='Auto Interest'";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	{
		
		$result=createAutoInterestLedgerForAgency($agency_id);
		if($result=="success")
		{
		return getAdvanceInterestLedgerIdForAgency($agency_id);
		}
		}
	}
	return false;	
}

function getIncomeLedgerIdForOC($oc_id)
{
	if(checkForNumeric($oc_id))
	{
			
	$income_head_id=getIndirectIncomeId();
	$sql="SELECT ledger_id FROM fin_ac_ledgers WHERE head_id=$income_head_id AND oc_id=$oc_id AND ledger_name='Finance Income'";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	{
		
		$result=createIncomeLedgerForOC($oc_id);
		if($result=="success")
		{
		return getIncomeLedgerIdForOC($oc_id);
		}
		}
	}
	return false;	
}

function getIncomeLedgerIdForAgency($agency_id)
{
	if(checkForNumeric($agency_id))
	{
	$income_head_id=getIndirectIncomeId();
	$sql="SELECT ledger_id FROM fin_ac_ledgers WHERE head_id=$income_head_id AND agency_id=$agency_id AND ledger_name='Finance Income'";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	{
		$result=createIncomeLedgerForAgency($agency_id);
		if($result=="success")
		{
		return getIncomeLedgerIdForAgency($agency_id);
		}
		}
	}
	return false;	
}

function getAmountToBeCreditedForLoanId($loan_id) //FOR LOAN AMOUNT after the books starting date
{
	
	if(checkForNumeric($loan_id))
	{
		
		$file_id=getFileIdFromLoanId($loan_id);
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		if($agency_company_type=="agency")
		{
			$accounts_settings=getAccountSettingsForAgency($agency_company_type_id);
			$booksStartingDate=getBooksStartingDateForAgency($agency_company_type_id);
			$mercantile=$accounts_settings['mercantile'];
		}
		else
		{
			$accounts_settings=getAccountSettingsForOC($agency_company_type_id);
			$booksStartingDate=getBooksStartingDateForOC($agency_company_type_id);
			$mercantile=$accounts_settings['mercantile'];
		}
		
		$loan=getLoanById($loan_id);
		
		if(strtotime($loan['loan_approval_date'])>=strtotime($booksStartingDate))	
		{
			return $loan['loan_amount'];
		}	
		else return 0;
		
	}
}

function getInterestToBeCreditedForLoanId($loan_id) // after the books starting date
{
	
	if(checkForNumeric($loan_id))
	{
		
		$file_id=getFileIdFromLoanId($loan_id);
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		if($agency_company_type=="agency")
		{
			$accounts_settings=getAccountSettingsForAgency($agency_company_type_id);
			$booksStartingDate=getBooksStartingDateForAgency($agency_company_type_id);
			$mercantile=$accounts_settings['mercantile'];
		}
		else
		{
			$accounts_settings=getAccountSettingsForOC($agency_company_type_id);
			$booksStartingDate=getBooksStartingDateForOC($agency_company_type_id);
			$mercantile=$accounts_settings['mercantile'];
		}
		
		$loan=getLoanById($loan_id);
		
		if(strtotime($loan['loan_approval_date'])>=strtotime($booksStartingDate))	
		{
			return getTotalInterestForLoan($loan_id);
		}	
		else return 0;
		
	}
}

function getPrincipalAmountToBeCreditedForEMIPaymentId($payment_id) //FOR PAYMENT AMOUNT after the books starting date
{
	
	if(checkForNumeric($payment_id))
	{
		
		$amount=getTotalAmountForPaymentId($payment_id);
		$emis_paid=getTotalEmisPaidForEMIPaymentId($payment_id);
		
		$payment=getPaymentDetailsForEmiPaymentId($payment_id);
		$loan_id=getLoanIdFromEmiPaymentId($payment_id);
		$file_id=getFileIdFromLoanId($loan_id);
		
		$interestPerEMI=getInterestPerEMIForLoan($loan_id);
		
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		if($agency_company_type=="agency")
		{
			$accounts_settings=getAccountSettingsForAgency($agency_company_type_id);
			$booksStartingDate=getBooksStartingDateForAgency($agency_company_type_id);
			$mercantile=$accounts_settings['mercantile'];
		}
		else
		{
			$accounts_settings=getAccountSettingsForOC($agency_company_type_id);
			$booksStartingDate=getBooksStartingDateForOC($agency_company_type_id);
			$mercantile=$accounts_settings['mercantile'];
		}
		
		if(strtotime($payment['payment_date'])>=strtotime($booksStartingDate))	
		{
			return $amount-($emis_paid*$interestPerEMI);
		}	
		else return 0;
		
	}
}
?>