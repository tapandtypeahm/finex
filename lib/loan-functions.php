<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("agency-functions.php");
require_once("our-company-function.php");
require_once('EMI-functions.php');
require_once("common.php");
require_once("bd.php");
require_once("bank-functions.php");
require_once("file-functions.php");
require_once("account-ledger-functions.php");
require_once("account-functions.php");

/* loan Functions */

function listLoans(){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	
function deleteAllPaymentsForFileId($file_id)
{
	$loan_id=getLoanIdFromFileId($file_id);
	$emi_ids=getAllEmiIDsForLoan($loan_id);
	foreach($emi_ids as $emi_id)
	{
		$loan_emi_id=$emi_id['loan_emi_id'];
		$sql="DELETE FROM fin_loan_emi_payment WHERE loan_emi_id=$loan_emi_id";
		dbQuery($sql);
		}
	}

function getParticipationDetailsForLoanId($loan_id)
{
	$sql="SELECT agency_emi,agency_duration FROM fin_loan_scheme_agency WHERE loan_id=$loan_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0];
	}

function insertLoan($amount,$loan_amount_type,$duration,$loan_type,$loan_scheme,$roi,$emi,$loan_approval_date,$loan_starting_date,$file_id,$customer_id,$agency_amount=0,$agency_emi=0,$agency_duration=0,$duration_unit=1)
{
	
	try
	{
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		if($agency_company_type=="agency")
		{
			if(is_array($agency_duration) || $agency_duration==0)
			{
				if(!is_numeric($agency_duration[0]) || $agency_duration==0)
				{
					if($loan_scheme==1) // if loan is even
					$agency_duration=array($duration); // duration is not an array but itself the duration
					else $agency_duration=array($duration[0]); // first element of the duration array 
				}
			}
			
			if(is_array($agency_emi) || $agency_emi==0) 
			{
				if(!is_numeric($agency_emi[0]) || $agency_emi==0)
				{
					if($loan_scheme==1) // if loan is even
					$agency_emi=array($emi);
					else $agency_emi=array($emi[0]);
				}
			}
			
		}
		else
		{
			$agency_amount=0;
			}
		
		$original_duration=0; // for irregular (uneven) loan type to store original duration array
		if($loan_scheme==1 && !checkForNumeric($emi,$duration)) // if even loan validate emi and duration
		{
			return "error";
		}
		else if($loan_scheme==2 && !checkForNumeric($emi[0],$duration[0])) //// if uneven loan validate emi[0] and duration[0]
		{
			return "error";
		}
		if($loan_scheme==2) // if irregular loan
		{
				
				$original_duration=$duration; // copy duration array into $original_duration array
				$original_emi=$emi;  // copy emi array into $original_emi array
				$emi=0;
				$duration=0;
				$total_collection=0;
				for($j=0;$j<count($original_duration);$j++) // this for loop calculates the total collection and the total duration for uneven loan
				{
					$dura=$original_duration[$j];
					$emii=$original_emi[$j];
					if(checkForNumeric($dura))
					{	
					$duration=$duration+$dura;
					$total_collection=$total_collection+($dura*$emii);
					}
				}
				$emi=$total_collection/$duration; // calculate average emi for the uneven loan and insert the avg emi in the fin_loan table
				$loan_type=1;
		}	
		$loan_ending_date=getEndingDateForLoan($loan_starting_date,$duration,$duration_unit); // get the ending date of loan i.e the date of the last emi from the loan_starting_date i.e first emi date and duration of loan	
		
		if(checkForNumeric($amount,$file_id,$customer_id,$loan_type,$loan_amount_type,$loan_scheme,$agency_amount) && $loan_approval_date!=null && $loan_approval_date!="" && $loan_starting_date!=null && $loan_starting_date!="" && $loan_ending_date!=null && $loan_ending_date!="" && !checkForDuplicateLoan($file_id)) // impratant validation && checks for duplicate loan for same file_id
		{
			
			
			if($loan_scheme==2 && (count($original_emi)==2)) // if irregular loan has only one type of emi then it is even loan $emi[1] and $duration[1] are null from input used for regeneration
			{
				$loan_scheme=1;
				}		
			
			if($loan_type==1) // if loan is given ny flat rate of interest
			{
				$reducing_rate_IRR=getReducingRateAndIRRFromFlat($amount,$duration,$roi,$duration_unit); // returns an array of reducing rate and irr from flat rate of interest
				$reducing_rate=$reducing_rate_IRR[0];
				$IRR=$reducing_rate_IRR[1];
				}
			else if($loan_type==2)  // if loan is given ny reducing rate of interest
			{
				$reducing_rate=$roi;
				$roi=getFlatRateFromReducing($amount,$reducing_rate,$duration); // return flast rate of interest i.e roi from reducing rate of interest
				$reducing_rate_IRR=getReducingRateAndIRRFromFlat($amount,$duration,$roi); // returns an array of reducing rate and irr from flat rate of interest
				$IRR=$reducing_rate_IRR[1];
			}	
			$loan_starting_date = str_replace('/', '-', $loan_starting_date);// converts dd/mm/yyyy to dd-mm-yyyy
			$loan_starting_date=date('Y-m-d',strtotime($loan_starting_date)); // converts date to Y-m-d format
			
			$loan_approval_date = str_replace('/', '-', $loan_approval_date);// converts dd/mm/yyyy to dd-mm-yyyy
			$loan_approval_date=date('Y-m-d',strtotime($loan_approval_date));// converts date to Y-m-d format
			
			$admin_id=$_SESSION['adminSession']['admin_id']; // gets admin_id from session to use for created by or last_update_by
			
			$sql="INSERT INTO fin_loan
				  (loan_amount, loan_amount_type, loan_duration, duration_unit, loan_type, loan_scheme, roi, reducing_roi, IRR, emi, loan_approval_date, loan_starting_date, loan_ending_date, agency_loan_amount,  file_id, customer_id, created_by, last_updated_by, date_added, date_modified)
				  VALUES
				  ($amount, $loan_amount_type, $duration, $duration_unit, $loan_type, $loan_scheme, $roi, $reducing_rate, $IRR, $emi, '$loan_approval_date', '$loan_starting_date', '$loan_ending_date', $agency_amount,  $file_id, $customer_id, $admin_id, $admin_id, NOW(), NOW())";
			dbQuery($sql);	  
			$loan_id=dbInsertId();
			
			if(is_array($original_emi) && is_array($original_duration)) // if irregular loan
			{
				
				$total_dur=0;	
				for($i=0;$i<count($original_duration);$i++) // for every emi and duration
				{
					$em=$original_emi[$i];
					$dur=$original_duration[$i];
					
					
					if(checkForNumeric($em) && checkForNumeric($dur))
					{
						if($i==0)
						{
						insertEMIsForLoan($loan_id,$dur,$loan_starting_date,$em,$duration_unit);
						}
						else
						{
							$start_date=getEndingDateForLoan($loan_starting_date,$total_dur+1,$duration_unit);
							insertEMIsForLoan($loan_id,$dur,$start_date,$em,$duration_unit);
						}
						insertLoanScheme($loan_id,$em,$dur);
					}
					$total_dur=$total_dur+$dur;
				}
				
			}
			else
			{		
			insertEMIsForLoan($loan_id,$duration,$loan_starting_date,$emi,$duration_unit);
			}// inserts entries of emi in the fin_loan_emi table corrsponding to each emi
			setCorrectReducingRateFromPrinInterestTableForLoan($loan_id);
			setCorrectIrrForLoan($loan_id);	
			
			
			if($agency_company_type=="agency")
			{
				if(is_array($agency_emi) && is_array($agency_duration))
				{
					for($k=0;$k<count($agency_emi);$k++)
					{
						$agency_em=$agency_emi[$k];
						$agency_dur=$agency_duration[$k];
						if(checkForNumeric($agency_em,$agency_dur))
						{
							insertLoanSchemeAgency($loan_id,$agency_em,$agency_dur);
							}
						}
					}
			}
			else
			{
				deleteLoanSchemeAgency($loan_id);
				}
			// ACCOUNTING PART 
			
			if(getAccountsStatus())
			{
				if($agency_company_type=="agency")
				{
					
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForAgency($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForAgency($agency_company_type_id);
				}
				else
				{
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForOC($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForOC($agency_company_type_id);
				}
				
				if($account_settings && strtotime($books_starting_date)<=strtotime($loan_approval_date)) // account_settings is true if accounts_status for agency or oc is set to 1 in database
				{
					if($loan_amount_type==1) // if loan given by cash
					{
						if($account_settings['mercantile']==0)
						{
						creditAccountingLedger($cash_ledger_id,$amount);
						debitAccountingCustomer($customer_id,$amount);
						}
						else if($account_settings['mercantile']==2)
						{
							$total_collection=getTotalCollectionForLoan($loan_id);
							$total_interest=$total_collection-$amount;
							
							creditAccountingLedger($cash_ledger_id,$amount);
							debitAccountingCustomer($customer_id,$amount);
							creditAccountingLedger($auto_interest_ledger,$total_interest);
							debitAccountingCustomer($customer_id,$total_interest);
							}
					}
					
				}
				
			}
			return $loan_id;
		}
		
	}
	catch(Exception $e)
	{
	}
	
}	

function insertLoanScheme($loan_id,$emi,$duration)
{
	if(checkForNumeric($emi,$duration))
	{
		$sql="INSERT INTO fin_loan_scheme(duration,emi,loan_id) VALUES ($duration,$emi,$loan_id)";
		dbQuery($sql);
		return "success";
		}
	return "error";	
	
	}
function deleteLoanScheme($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="DELETE FROM fin_loan_scheme WHERE loan_id=$loan_id";
		dbQuery($sql);
		return "success";
		}
	return "error";	
	
	}	

function getLatestPaymentDateForLoan($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT max(Payment_date) FROM fin_loan_emi_payment,fin_loan_emi Where fin_loan_emi_payment.loan_emi_id=fin_loan_emi.loan_emi_id AND fin_loan_emi.loan_id=$id";
		$result=dbQuery($sql);
		if(dbNumRows($result)>0)
		{
		$resultArray=dbResultToArray($result);
		return $resultArray[0][0];
		}
		else return "1970-01-01";
		}
	
	}	

function insertLoanSchemeAgency($loan_id,$emi,$duration)
{
	if(checkForNumeric($emi,$duration))
	{
		$sql="INSERT INTO fin_loan_scheme_agency(agency_duration,agency_emi,loan_id) VALUES ($duration,$emi,$loan_id)";
		dbQuery($sql);
		return "success";
		}
	return "error";	
	
	}
function deleteLoanSchemeAgency($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="DELETE FROM fin_loan_scheme_agency WHERE loan_id=$loan_id";
		dbQuery($sql);
		return "success";
		}
	return "error";	
	
	}		

function deletetLoan($id){
	
	try
	{
		$sql="DELETE FROM fin_loan 
			  WHERE loan_id=$id";
		dbQuery($sql);
	}
	catch(Exception $e)
	{
	}
	
}	
function getInterestPaidForLoan($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$total_collection=getTotalCollectionForLoan($loan_id);
		$loan=getLoanById($loan_id);
		$total_interest=$total_collection-$loan['loan_amount'];
		$interest_per_emi=$total_interest/$loan['loan_duration'];
		$emis_paid=getTotalEmiPaidForLoan($loan_id);
		if(checkForNumeric($interest_per_emi,$emis_paid))
		return $emis_paid*$interest_per_emi;
		else
		return 0;
		}	
	
}

function getTotalInterestForLoan($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$total_collection=getTotalCollectionForLoan($loan_id);
		$loan=getLoanById($loan_id);
		$total_interest=$total_collection-$loan['loan_amount'];
		if(checkForNumeric($total_interest))
		return $total_interest;
		else
		return 0;
		}	
	
}

function getInterestPerEMIForLoan($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$total_collection=getTotalCollectionForLoan($loan_id);
		$loan=getLoanById($loan_id);
		$total_interest=$total_collection-$loan['loan_amount'];
		if(checkForNumeric($total_interest))
		return $total_interest/$loan['loan_duration'];
		else
		return 0;
		}	
	
}

function getTotalEmisPaidForEMIPaymentId($payment_id)
{
	if(checkForNumeric($payment_id))
	{
		$allPayments=getAllPaymentsForPaymentId($payment_id);
		$amount=$allPayments['total_paid'];
		$allPaymentsDetails=$allPayments['payment_details'];
		$emi_paid=0;
		foreach($allPayments as $payment)
		{
			$emi_amount=0;
			$payment_amount=0;
			$emi_amount=$payment['emi_amount'];
			$payment_amount=$payment['payment_amount'];
			if(checkForNumeric($emi_amount,$payment_amount) && $emi_amount>0 && $payment_amount>0)
			{
				$emi_paid=$emi_paid+($payment_amount/$emi_amount);
			}
		}
		
		}
	
	}
function updateLoan($id,$amt,$loan_amount_type,$duration,$loan_type,$loan_scheme,$roi,$emi,$loan_approval_date,$loan_starting_date,$bank_name=false,$branch_name=false,$cheque_amount=false,$cheque_date=false,$cheque_no=false,$axin_no=false,$ledger_id=0,$agency_amount=0,$agency_emi=0,$agency_duration=0,$duration_unit=1){
	
	try
	{
		
		$original_duration=0; // for irregular loan type to store original duration array
		$file_id=getFileIdFromLoanId($id);
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		if($agency_company_type=="agency")
		{
			if(is_array($agency_duration) || $agency_duration==0)
			{
				if(!is_numeric($agency_duration[0]) || $agency_duration==0)
				{
					if($loan_scheme==1)
					$agency_duration=array($duration);
					else $agency_duration=array($duration[0]);
				}
			}
			if(is_array($agency_emi) || $agency_emi==0) 
			{
				if(!is_numeric($agency_emi[0]) || $agency_emi==0)
				{
					if($loan_scheme==1)
					$agency_emi=array($emi);
					else $agency_emi=array($emi[0]);
				}
			}
			
		}
		else
		{
			$agency_amount=0;
			
		}
		if($loan_scheme==1 && !checkForNumeric($emi,$duration)) // if even loan validate emi and duration
		{
			return "error";
		}
		else if($loan_scheme==2 && !checkForNumeric($emi[0],$duration[0])) //// if uneven loan validate emi[0] and duration[0]
		{
			return "error";
		}
		if($loan_scheme==2) // if irregular loan
		{
				
				$original_duration=$duration; // copy duration array into $original_duration array
				$original_emi=$emi;           // copy emi array into $original_emi[
				$emi=0;
				$duration=0;
				$total_collection=0;
				for($j=0;$j<count($original_duration);$j++)
				{
					
					$dura=$original_duration[$j];
					$emii=$original_emi[$j];
					
					if(checkForNumeric($dura) && checkForNumeric($emii))
					{	
					$duration=$duration+$dura; // total duration
					$total_collection=$total_collection+($dura*$emii); // total collection
					}
				}
				$emi=$total_collection/$duration; // avg emi
		}	
		
		$loan_ending_date=getEndingDateForLoan($loan_starting_date,$duration,$duration_unit); // get the ending date of loan i.e the date of the last emi from the loan_starting_date i.e first emi date and duration of loan	
		// only update loan if no payments has been done yet or the amount,emi,roi,loan_type,duration are same
		$amount=$amt;
		$payment=getTotalPaymentForLoan($id); // gets Total payment done for th loan i.e total of emi_payment_id corresponding to the loan
		$loanDetails=getLoanById($id);
		 /* ACCOUNTS */
		
		 if(getAccountsStatus())
			{
				
				if($agency_company_type=="agency")
				{
					
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForAgency($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForAgency($agency_company_type_id); //  account that is credited for the whole interest of loan
					
				}
				else
				{
					
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForOC($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForOC($agency_company_type_id); //  account that is credited for the whole interest of loan
				
				}
			if($account_settings) // account_settings is true if accounts_status for agency or oc is set to 1 in database
				{
					
					$amount_to_be_debited = getAmountToBeCreditedForLoanId($id); // it ts the old loan amount for the reverse entry in the accounts
					$old_total_interest_to_be_debited = getInterestToBeCreditedForLoanId($id);
				}
			}
				
		/* ACCOUNTS COMPLETED */
		$old_emi=$loanDetails['emi'];
		$old_loan_scheme=$loanDetails['loan_scheme'];
		if($old_loan_scheme==2) // if old loan is uneven get loan structure
		{
			$loan_structure=getLoanScheme($id);
			if($loan_structure=="error")
			return "error";
			$emi_array=array();
			$duration_array=array();
			foreach($loan_structure as $struct)
			{
				$emi_array[]=$struct['emi'];
				$duration_array[]=$struct['duration'];
			}
			$ori_emi=array();
			$ori_dur=array();
			foreach($original_emi as $or_emi)
			{
				if(checkForNumeric($or_emi))
				$ori_emi[]=$or_emi;
				}
			foreach($original_duration as $or_dura)
			{
				if(checkForNumeric($or_dura))
				$ori_dur[]=$or_dura;
				}	
			$original_emi=$ori_emi;
			$original_duration=$ori_dur;	
		}
	
		if(checkForNumeric($id,$amount,$loan_amount_type,$duration,$loan_type,$roi,$emi,$agency_amount) && validateForNull($loan_approval_date,$loan_starting_date) && (($loan_scheme==1 && ($payment==0 || ($loanDetails['loan_amount']==$amount && $loanDetails['loan_duration']==$duration && $loanDetails['loan_type']==$loan_type && $loanDetails['roi']==$roi && $loanDetails['emi']==$emi))) || ($loan_scheme==2 && ($payment==0 || ($loanDetails['loan_amount']==$amount && $duration_array===$original_duration && $loanDetails['loan_type']==$loan_type && $loanDetails['roi']==$roi && $emi_array===$original_emi))))) // if payments are not done or the loan amount, emi, roi, duration are same if payments are done
		{
			
			if($loan_scheme==2 && (count($original_emi)==1)) // if irregular loan has only one type of emi then it is even loan 
			{
				$loan_scheme=1;
				}	
			
			if($loan_type==1)
				{
					
					$reducing_rate_IRR=getReducingRateAndIRRFromFlat($amount,$duration,$roi,$duration_unit); // reducing and irr from flat
					$reducing_rate=$reducing_rate_IRR[0];
					$IRR=$reducing_rate_IRR[1];
				}
			else if($loan_type==2)
				{
					$reducing_rate=$roi;
					$roi=getFlatRateFromReducing($amount,$reducing_rate,$duration);
					$reducing_rate_IRR=getReducingRateAndIRRFromFlat($amount,$duration,$roi);
					$IRR=$reducing_rate_IRR[1];
				}	
			
			$loan=getLoanById($id);
			$admin_id=$_SESSION['adminSession']['admin_id']; // admin_id from session
			$old_duration=$loan['loan_duration'];
			$old_starting_date=$loan['loan_starting_date'];
			$loan_ending_date=getEndingDateForLoan($loan_starting_date,$duration,$duration_unit);
		
			$loan_starting_date = str_replace('/', '-', $loan_starting_date);
			$loan_starting_date=date('Y-m-d',strtotime($loan_starting_date));
			
			$loan_approval_date = str_replace('/', '-', $loan_approval_date);
			$loan_approval_date=date('Y-m-d',strtotime($loan_approval_date));
			$old_duration_unit=$loan['duration_unit'];
			$sql="UPDATE fin_loan 
				  SET loan_amount=$amount, loan_amount_type=$loan_amount_type, loan_duration=$duration, loan_type=$loan_type, loan_scheme=$loan_scheme, roi=$roi, reducing_roi=$reducing_rate, IRR=$IRR, emi=$emi, loan_approval_date='$loan_approval_date', loan_starting_date='$loan_starting_date', loan_ending_date= '$loan_ending_date', agency_loan_amount=$agency_amount, last_updated_by=$admin_id, date_modified=NOW() ";
				  if($payment==0)
				  $sql=$sql." ,duration_unit=$duration_unit ";
				  
				  $sql=$sql." WHERE loan_id=$id ";
			  
			$result=dbQuery($sql);
				
			closeFileIfBalanceZero($id); // if loan_ending date is changed change the file status according to current date and balance of the loan
			if($loan_scheme==1)
			{
				
				
				
				if($old_loan_scheme==2)
				{
					deleteEMIsForLoan($id);
					insertEMIsForLoan($id,$duration,$loan_starting_date,$emi,$duration_unit);
					}	
				else if($old_duration!=$duration) // if no payments has been done yet and duration is changed delete emis from fin_loan_emi and reinsert 
				{
					deleteEMIsForLoan($id);
					insertEMIsForLoan($id,$duration,$loan_starting_date,$emi,$duration_unit);
				}
				else if($old_starting_date!=$loan_starting_date && $old_duration==$duration) // if no payments has been done yet and duration is not changed update date in emis in fin_loan_emi 
				{
					
					if($payment==0)
					updateEMIsForLoan($id,$duration,$loan_starting_date,$emi,$duration_unit);
					else
					updateEMIsForLoan($id,$duration,$loan_starting_date,false,$duration_unit);
				}	
				else if($old_emi!=$emi)
				{
					if($payment==0)
					updateEMIsForLoan($id,$duration,$loan_starting_date,$emi,$duration_unit);
					}
				else if($old_duration_unit!==$duration_unit && $payment==0)
				{
					deleteEMIsForLoan($id);
					insertEMIsForLoan($id,$duration,$loan_starting_date,$emi,$duration_unit);
					}	
			}
			else if($loan_scheme==2)
			{
				if(is_array($original_emi) && is_array($original_duration)) // if irregular loan
				{
					if($payment==0)
					{
						deleteEMIsForLoan($id);
						$total_dur=0;
						deleteLoanScheme($id);	
						for($i=0;$i<count($original_duration);$i++) // for every emi and duration
						{
							$em=$original_emi[$i];
							$dur=$original_duration[$i];
							
							
								if(checkForNumeric($em) && checkForNumeric($dur))
								{
										if($i==0)
										{
										insertEMIsForLoan($id,$dur,$loan_starting_date,$em,$duration_unit);
										}
										else
										{
											$start_date=getEndingDateForLoan($loan_starting_date,$total_dur+1,$duration_unit);
											insertEMIsForLoan($id,$dur,$start_date,$em,$duration_unit);
										}
									
									insertLoanScheme($id,$em,$dur);
								}
							$total_dur=$total_dur+$dur;
						}
					}
					else
					{
						updateEMIsForLoan($id,$duration,$loan_starting_date,false,$duration_unit);
						}
					
				}
				
			}
		 		
		}
		else if($payment!=0) // if payments are done return error that loan cannot be updated
		{
			
			$max_emi_id=getEmiIdForLatestPaymentForLoan($id); // get the max emi_id for which payment is done
			$emi_ids_for_loan=getAllEmiIDsForLoan($id);
			$loan=getLoanById($id);
			if($max_emi_id!=0)
			{
			$max_emi_no_payment_done=getLoanNoFromEMIIdForLoan($max_emi_id); // get the emi no for the max emi_id	
			
			$paid_scheme=getPaidEmiSchemeForLoan($id);
			if($loan_scheme==2) // if loan scheme is uneven average the paid and unpaid emi and add the paid and unpaid duration as duration and emi arrays donot contain paid emis info
			{
				$avg_paid_emi=0;
				$paid_duration=0;
				if($paid_scheme!="error")
				{
					
					$total_paid_collection=0;
					foreach($paid_scheme as $paid_part_scheme)
					{
						$paid_duration=$paid_duration+$paid_part_scheme['duration'];
						$total_paid_collection=$total_paid_collection+$paid_part_scheme['emi'];
						}
					$avg_paid_emi=$total_paid_collection/$paid_duration;	
				}
				if($avg_paid_emi>0)
				{
					$total_future_collection=$emi*$duration;
					$duration=$duration+$paid_duration;
					$total_collection_1=$total_future_collection+$total_paid_collection;
					$emi=$total_collection_1/$duration;
					}
			}
			if($loan_type==1)
				{
					
					$reducing_rate_IRR=getReducingRateAndIRRFromFlat($amount,$duration,$roi); // reducing and irr from flat
					$reducing_rate=$reducing_rate_IRR[0];
					$IRR=$reducing_rate_IRR[1];
				}
			else if($loan_type==2)
				{
					$reducing_rate=$roi;
					$roi=getFlatRateFromReducing($amount,$reducing_rate,$duration);
					$reducing_rate_IRR=getReducingRateAndIRRFromFlat($amount,$duration,$roi);
					$IRR=$reducing_rate_IRR[1];
				}	
			
			
			
			$admin_id=$_SESSION['adminSession']['admin_id']; // admin_id from session
			$old_duration=$loan['loan_duration'];
			$old_starting_date=$loan['loan_starting_date'];
			$old_loan_scheme=$loan['loan_scheme'];
			if($loan_scheme==$old_loan_scheme && $old_loan_scheme==1)
			{
				if($duration<$max_emi_no_payment_done)
				return "duration_error";
				if($emi!=$loan['emi'])
				return "emi_error";
			}
			else if($loan_scheme==1 && $old_loan_scheme==2)
			{
				
				$loan_structure=getLoanScheme($id);
				$first_slab_duration=$loan_structure[0]['duration'];
				$first_slab_emi=$loan_structure[0]['emi'];
				if($max_emi_no_payment_done>$first_slab_duration)
				{
					return "duration_error";
					}
				if($duration<$max_emi_no_payment_done)
				{
					return "duration_error";
					}	
				if($emi!=$first_slab_emi)
				{
					return "emi_error";
					}	
			}
			$loan_ending_date=getEndingDateForLoan($loan_starting_date,$duration,$duration_unit);
		
			$loan_starting_date = str_replace('/', '-', $loan_starting_date);
			$loan_starting_date=date('Y-m-d',strtotime($loan_starting_date));
			
			$loan_approval_date = str_replace('/', '-', $loan_approval_date);
			$loan_approval_date=date('Y-m-d',strtotime($loan_approval_date));
		
			$sql="UPDATE fin_loan 
				  SET loan_amount=$amount, loan_amount_type=$loan_amount_type, loan_duration=$duration, loan_type=$loan_type, loan_scheme=$loan_scheme, roi=$roi, reducing_roi=$reducing_rate, IRR=$IRR, emi=$emi, loan_approval_date='$loan_approval_date', loan_starting_date='$loan_starting_date', loan_ending_date= '$loan_ending_date', agency_loan_amount=$agency_amount, last_updated_by=$admin_id, date_modified=NOW()
				  WHERE loan_id=$id ";
			  
			$result=dbQuery($sql);
				
			closeFileIfBalanceZero($id);
				if($loan_scheme==1)
				{
				updateEMIsForLoan($id,$old_duration,$loan_starting_date,false,$duration_unit);
				$new_starting_date=getEndingDateForLoan($loan_starting_date,$max_emi_no_payment_done+1);
				deleteEMIsForLoan($id,$max_emi_id);
				insertEMIsForLoan($id,$duration-$max_emi_no_payment_done,$new_starting_date,$emi,$duration_unit);
				}
				else if($loan_scheme==2)
				{
					
					updateEMIsForLoan($id,$old_duration,$loan_starting_date,false,$duration_unit);
					if(is_array($original_emi) && is_array($original_duration)) // if irregular loan
					{
						$new_starting_date_emi_id=$emi_ids_for_loan[$max_emi_no_payment_done]['loan_emi_id'];
						$new_starting_date_emi=getEmiDetailsByEmiId($new_starting_date_emi_id);
						$new_starting_date=$new_starting_date_emi['loanDetails']['actual_emi_date'];
						deleteEMIsForLoan($id,$max_emi_id);
						$total_dur=0;
						deleteLoanScheme($id);	
						
						if($paid_scheme!="error")
						{
							 
							
							foreach($paid_scheme as $part_scheme)
							{
								insertLoanScheme($id,$part_scheme['emi'],$part_scheme['duration']);
								}
								
							}
						for($i=0;$i<count($original_duration);$i++) // for every emi and duration
						{
							$em=$original_emi[$i];
							$dur=$original_duration[$i];
							
								if(checkForNumeric($em) && checkForNumeric($dur))
								{
										if($i==0)
										{
										insertEMIsForLoan($id,$dur,$new_starting_date,$em,$duration_unit);
										}
										else
										{
											$start_date=getEndingDateForLoan($new_starting_date,$total_dur+1,$duration_unit);
											insertEMIsForLoan($id,$dur,$start_date,$em,$duration_unit);
										}
									
									insertLoanScheme($id,$em,$dur);
								}
							$total_dur=$total_dur+$dur;
						}
					}
				 setCorrectLoanScheme($id);
				}
			}
		}
	
		$customer_id=getCustomerIdFromLoanId($id);
		if($loan_amount_type==2) // if loan is given through cheque update loan cheuqe details or insert if necessary
		{
			
				$loan_cheque=getLoanChequeByLoanId($id);
				
				if(isset($loan_cheque['loan_cheque_id']) && $loan_cheque!=false)
				{
					
					$cheque_id=$loan_cheque['loan_cheque_id'];
					updateLoanCheque($cheque_id,$bank_name,$branch_name,$cheque_amount,$cheque_date,$cheque_no,$axin_no,$ledger_id);
					 /* ACCOUNTS */
		 		
					 if(getAccountsStatus())
						{
						if(isset($account_settings) && $account_settings) // account_settings is true if accounts_status for agency or oc is set to 1 in database
							{
								$amount_to_be_credited = getAmountToBeCreditedForLoanId($loan['loan_id']);
								
								if($account_settings['mercantile']==0)
								{
								debitAccountingLedger($loan_cheque['ledger_id'],$amount_to_be_debited); // amount_to_be_debited is the old loan amount, debit the ledger from which the old cheque was given
								creditAccountingLedger($ledger_id,$amount_to_be_credited); // credit the ledger from which the new cheque is given
								
								creditAccountingCustomer($customer_id,$amount_to_be_debited); // credit the customer for the old loan amount
								debitAccountingCustomer($customer_id,$amount_to_be_credited); // debit the new loan amount
								}
								else if($account_settings['mercantile']==2)
								{
								$total_collection=getTotalCollectionForLoan($id);
								$total_interest=getInterestToBeCreditedForLoanId($id);
								
								debitAccountingLedger($loan_cheque['ledger_id'],$amount_to_be_debited);
								creditAccountingLedger($ledger_id,$amount_to_be_credited);
								
								creditAccountingCustomer($customer_id,$amount_to_be_debited);
								debitAccountingCustomer($customer_id,$amount_to_be_credited);
								
								creditAccountingCustomer($customer_id,$old_total_interest_to_be_debited); // reverse entry of old interest if eligible
								debitAccountingLedger($auto_interest_ledger,$old_total_interest_to_be_debited);
								
								creditAccountingLedger($auto_interest_ledger,$total_interest);
								debitAccountingCustomer($customer_id,$total_interest);
								}
							}
						}
					/* ACCOUNTS COMPLETED */
				}
				else
				{
					insertLoanCheque($id,$bank_name,$branch_name,$cheque_amount,$cheque_date,$cheque_no,$axin_no,$ledger_id);
					/* ACCOUNTS */
		 			
					 if(getAccountsStatus())
						{
						if(isset($account_settings) && $account_settings) // account_settings is true if accounts_status for agency or oc is set to 1 in database
							{
								$amount_to_be_credited = getAmountToBeCreditedForLoanId($loan['loan_id']);
								if($account_settings['mercantile']==0)
								{
								debitAccountingLedger($cash_ledger_id,$amount_to_be_debited);
							//	creditAccountingLedger($ledger_id,$amount_to_be_credited); this entries happen in insertLoanCheque
								
								creditAccountingCustomer($customer_id,$amount_to_be_debited);
							//	debitAccountingCustomer($customer_id,$amount_to_be_credited); 
								}
								else if($account_settings['mercantile']==2)
								{
								$total_collection=getTotalCollectionForLoan($id);
								$total_interest=getInterestToBeCreditedForLoanId($id);
								
								debitAccountingLedger($cash_ledger_id,$amount_to_be_debited); 
							//	creditAccountingLedger($ledger_id,$amount_to_be_credited); entries happen in insertLoanCheuque
								
								creditAccountingCustomer($customer_id,$amount_to_be_debited);
							//	debitAccountingCustomer($customer_id,$amount_to_be_credited);
								
								creditAccountingCustomer($customer_id,$old_total_interest_to_be_debited); // reverse entry of old interest if eligible
								debitAccountingLedger($auto_interest_ledger,$old_total_interest_to_be_debited);
								
								creditAccountingLedger($auto_interest_ledger,$total_interest);
								debitAccountingCustomer($customer_id,$total_interest);
								}
							}
						}
					/* ACCOUNTS COMPLETED */
				}
		}
		else if($loan_amount_type==1)
		{
				$loan_cheque=getLoanChequeByLoanId($id);
				if(isset($loan_cheque['loan_cheque_id']) && $loan_cheque!=false)
				{
				deleteLoanCheque($id);
				/* ACCOUNTS */
		 
					 if(getAccountsStatus())
						{
						if(isset($account_settings) && $account_settings) // account_settings is true if accounts_status for agency or oc is set to 1 in database
							{
								$amount_to_be_credited = getAmountToBeCreditedForLoanId($loan['loan_id']);
								if($account_settings['mercantile']==0)
								{
								debitAccountingLedger($loan_cheque['ledger_id'],$amount_to_be_debited);
								creditAccountingLedger($cash_ledger_id,$amount_to_be_credited);
								creditAccountingCustomer($customer_id,$amount_to_be_debited);
								debitAccountingCustomer($customer_id,$amount_to_be_credited);
								}
								else if($account_settings['mercantile']==2)
								{
								$total_collection=getTotalCollectionForLoan($id);
								$total_interest=getInterestToBeCreditedForLoanId($id);
								
								debitAccountingLedger($loan_cheque['ledger_id'],$amount_to_be_debited);
								creditAccountingLedger($cash_ledger_id,$amount_to_be_credited);
								
								creditAccountingCustomer($customer_id,$amount_to_be_debited);
								debitAccountingCustomer($customer_id,$amount_to_be_credited);
								
								creditAccountingCustomer($customer_id,$old_total_interest_to_be_debited); // reverse entry of old interest if eligible
								debitAccountingLedger($auto_interest_ledger,$old_total_interest_to_be_debited);
								
								creditAccountingLedger($auto_interest_ledger,$total_interest);
								debitAccountingCustomer($customer_id,$total_interest);
								}
							}
						}
					/* ACCOUNTS COMPLETED */
				}
				else
				{
					/* ACCOUNTS */
		 			
					 if(getAccountsStatus())
						{
						if(isset($account_settings) && $account_settings) // account_settings is true if accounts_status for agency or oc is set to 1 in database
							{
								
								$amount_to_be_credited = getAmountToBeCreditedForLoanId($loan['loan_id']);
								
								if($account_settings['mercantile']==0)
								{
								debitAccountingLedger($cash_ledger_id,$amount_to_be_debited);
								creditAccountingLedger($cash_ledger_id,$amount_to_be_credited);
								creditAccountingCustomer($customer_id,$amount_to_be_debited);
								debitAccountingCustomer($customer_id,$amount_to_be_credited);
								}
								else if($account_settings['mercantile']==2)
								{
									
								$total_collection=getTotalCollectionForLoan($id);
								$total_interest=getInterestToBeCreditedForLoanId($id);
							
								debitAccountingLedger($cash_ledger_id,$amount_to_be_debited);
								creditAccountingLedger($cash_ledger_id,$amount_to_be_credited);
								
								creditAccountingCustomer($customer_id,$amount_to_be_debited);
								debitAccountingCustomer($customer_id,$amount_to_be_credited);
								
								creditAccountingCustomer($customer_id,$old_total_interest_to_be_debited); // reverse entry of old interest if eligible
								debitAccountingLedger($auto_interest_ledger,$old_total_interest_to_be_debited);
								
								creditAccountingLedger($auto_interest_ledger,$total_interest);
								debitAccountingCustomer($customer_id,$total_interest);
								}
							}
						}
					/* ACCOUNTS COMPLETED */
				}
		}	
				setCorrectReducingRateFromPrinInterestTableForLoan($id);
				setCorrectIrrForLoan($id);			
		if($agency_company_type=="agency")
			{
				deleteLoanSchemeAgency($id);
				if(is_array($agency_emi) && is_array($agency_duration))
				{
					for($k=0;$k<count($agency_emi);$k++)
					{
						$agency_em=$agency_emi[$k];
						$agency_dur=$agency_duration[$k];
						if(checkForNumeric($agency_em,$agency_dur))
						{
							insertLoanSchemeAgency($id,$agency_em,$agency_dur);
							}
						}
					}
			}
			else
			{
				deleteLoanSchemeAgency($id);
				}		
		return "success";	
	}
	catch(Exception $e)
	{
}
	
}	



function setCorrectLoanScheme($id)
{
	if(checkForNumeric($id))
	{
		
		$sql="SELECT emi_amount,loan_emi_id FROM fin_loan_emi WHERE loan_id=$id ORDER BY actual_emi_date ";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		$returnArray=array();
		if(dbNumRows($result)>0)
		{
			
			$previous_emi=-1;
			$i=-1;
			foreach($resultArray as $re)
			{
					$current_emi=$re['emi_amount'];
					if($current_emi==$previous_emi)
					{
						
						$returnArray[$i]['duration']=$returnArray[$i]['duration']+1;
						}
					else
					{
						$returnArray[++$i]['emi']=$current_emi;
						$returnArray[$i]['duration']=1;
						}	
					$previous_emi=$current_emi;	
			}
		/*if($current_loan_scheme!="error" && is_array($current_loan_scheme))
		{
			$new_scheme=array();
			$previous_emi=-1;
			$previous_duration=-1;
			foreach($current_loan_scheme as $part_of_scheme)
			{
				$current_part_emi=$part_of_scheme['emi'];
				$current_part_duration=$part_of_scheme['duration'];
				
				if($previous_emi>0 && $current_part_emi==$previous_emi)
				{
					if($previous_duration>0)
					{
						$new_scheme[$current_part_emi]=$new_scheme[$current_part_emi]+$current_part_duration;
						
					}
				}
				else
				{
					$new_scheme[$current_part_emi]=$current_part_duration;
					
					}
				$previous_emi=$current_part_emi;
				$previous_duration=$current_part_duration;	
				
			} */
			deleteLoanScheme($id);
			
			foreach($returnArray as $ret)
			{
				
				insertLoanScheme($id,$ret['emi'],$ret['duration']);
				}
		 
		}
	}
}

function checkForDuplicateLoan($file_id,$id=false) 
{
	try
	{
		// checks if the loan is already inserted for file_id
		$sql="SELECT loan_id
			  FROM fin_loan
			  WHERE file_id=$file_id";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND loan_id!=$id";		  
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			$_SESSION['error']['submit_error']="Duplicate Entry!";
			return true;
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

function getEmiIdForLatestPaymentForLoan($loan_id)
{
	if(checkForNumeric($loan_id))
	{
	$sql="SELECT MAX(fin_loan_emi_payment.loan_emi_id) as max_emi_id FROM fin_loan_emi_payment, fin_loan_emi WHERE 
	      fin_loan_emi_payment.loan_emi_id = fin_loan_emi.loan_emi_id
		  AND loan_id=$loan_id
		  GROUP BY loan_id";
	$result=dbQuery($sql);	  
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else return 0;
	}
}

function getLoanById($id){
	
	try
	{
		$sql="SELECT loan_id,loan_amount, loan_duration, loan_type,loan_scheme, roi, reducing_roi, IRR, emi, loan_approval_date, loan_starting_date, loan_ending_date, agency_loan_amount, file_id, customer_id, created_by
		      FROM fin_loan
			  WHERE loan_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0];
			}	  
		else
		return false;	
	}
	catch(Exception $e)
	{
	}
	
}	

function getLoanAmountById($id){
	
	try
	{
		$sql="SELECT loan_amount
		      FROM fin_loan
			  WHERE loan_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0][0];
			}	  
		else
		return false;	
	}
	catch(Exception $e)
	{
	}
	
}	

function getCustomerIdFromLoanId($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="SELECT customer_id FROM fin_loan WHERE loan_id=$loan_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result))
		return $resultArray[0][0];
		}
}

function insertLoanCheque($loan_id,$bank_name,$branch_name,$amount,$cheque_date,$cheque_no,$axin_no,$ledger_id=0)
{
	if(!validateForNull($axin_no))
	$axin_no="NA";
	
	try
	{
		$file_id=getFileIdFromLoanId($loan_id);
		$admin_id=$_SESSION['adminSession']['admin_id'];
		if(validateForNull($bank_name,$branch_name))
		{
		$bankArray=insertIfNotDuplicateBank($bank_name,$branch_name);
		$bank_id=$bankArray[0];
		$branch_id=$bankArray[1];
		
			if(checkForNumeric($bank_id,$branch_id,$amount,$cheque_no,$loan_id,$ledger_id) && validateForNull($cheque_date))
			{
			
			$cheque_date = str_replace('/', '-', $cheque_date);
			$cheque_date=date('Y-m-d',strtotime($cheque_date));	
				
			$sql="INSERT INTO fin_loan_cheque
				  (bank_id, branch_id, loan_cheque_amount, loan_cheque_date, loan_cheque_no, loan_cheque_axin_no, loan_id, created_by, last_updated_by, date_added, date_modified, ledger_id)
				  VALUES
				  ($bank_id, $branch_id, $amount, '$cheque_date', '$cheque_no' , '$axin_no', $loan_id, $admin_id, $admin_id, NOW(), NOW(), $ledger_id)";
			$result=dbQuery($sql);
			
			
			if(getAccountsStatus())
			{
				$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
				$agency_company_type=$agency_company_type_array[0];
				$agency_company_type_id=$agency_company_type_array[1];
				if($agency_company_type=="agency")
				{
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
				}
				else
				{
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
				}
				$customer_id=getCustomerIdFromLoanId($loan_id);
				if($account_settings) // account_settings is true if accounts_status for agency or oc is set to 1 in database
				{
						creditAccountingLedger($ledger_id,$amount);
						debitAccountingCustomer($customer_id,$amount);
				}
				
			}
			
			
				return "success";	
			}
			else
			{
				return "error";
				}
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
	
function getLoanChequeByLoanId($loan_id)
{
	if(checkForNumeric($loan_id))
	{
	$sql="SELECT loan_cheque_id,bank_id, branch_id, loan_cheque_amount, loan_cheque_date, loan_cheque_no, loan_cheque_axin_no, ledger_id FROM fin_loan_cheque WHERE loan_id=$loan_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0];
	else 
	return false;
	}
}	
	
function updateLoanCheque($cheque_id,$bank_name,$branch_name,$amount,$cheque_date,$cheque_no,$axin_no,$ledger_id=0)
{
	if(!validateForNull($axin_no))
	$axin_no="NA";
	
	try
	{
		
		$admin_id=$_SESSION['adminSession']['admin_id'];
		if(validateForNull($bank_name,$branch_name))
		{
		$bankArray=insertIfNotDuplicateBank($bank_name,$branch_name); // if bank and its branch is not in the database insert bank and branch
		$bank_id=$bankArray[0];
		$branch_id=$bankArray[1];
		
			if(checkForNumeric($bank_id,$branch_id,$amount,$cheque_no,$cheque_id,$ledger_id) && validateForNull($cheque_date))
			{
			
			$cheque_date = str_replace('/', '-', $cheque_date);
			$cheque_date=date('Y-m-d',strtotime($cheque_date));	
				
			$sql="UPDATE fin_loan_cheque
				  SET bank_id = $bank_id, branch_id = $branch_id, loan_cheque_amount = $amount, loan_cheque_date = '$cheque_date', loan_cheque_no = '$cheque_no' ,  loan_cheque_axin_no = '$axin_no', last_updated_by = $admin_id,  date_modified = NOW(), ledger_id=$ledger_id
				  WHERE loan_cheque_id=$cheque_id";
			$result=dbQuery($sql);
			
				return "success";	
			}
			else
			{
				return "error";
				}
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
	
function deleteLoanCheque($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="DELETE FROM fin_loan_cheque WHERE loan_id=$loan_id";
		dbQuery($sql);
		}
}


function getEndingDateForLoan($starting_date,$duration,$duration_unit=1)
{
	
	$starting_date = str_replace('/', '-', $starting_date);
	$starting_date=date('Y-m-d',strtotime($starting_date));		
	    $i=($duration*$duration_unit)-$duration_unit;
		
		$monthToAdd = $i;
		
		$d1 = DateTime::createFromFormat('Y-m-d', $starting_date);
		
		$year = $d1->format('Y');
		$month = $d1->format('n');
		$day = $d1->format('d');
		
		$year += floor($monthToAdd/12);
		$monthToAdd = $monthToAdd%12;
		$month += $monthToAdd;
		if($month > 12) {
			$year ++;
			$month = $month % 12;
			if($month === 0)
				$month = 12;
		}
		
		if(!checkdate($month, $day, $year)) {
			$d2 = DateTime::createFromFormat('Y-n-j', $year.'-'.$month.'-1');
			$d2->modify('last day of');
		}else {
			$d2 = DateTime::createFromFormat('Y-n-d', $year.'-'.$month.'-'.$day);
		}
		$d2->setTime($d1->format('H'), $d1->format('i'), $d1->format('s'));
		$return_date=$d2->format('Y-m-d');
		return $return_date;
		
}


function getEmiForLoanId($id) // get emi amount for loan_id AND loan scheme FOR Iregular Loan
{
	$loan=getLoanById($id);
	if($loan['loan_scheme']==1)
	{
	$sql="SELECT loan_id,emi
	      FROM fin_loan
		  WHERE loan_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][1];
	else
	return 0;	
	}
	else
	{
		$scheme=getLoanScheme($id);
		return $scheme;
		}
}
	
function getLoanDetailsByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT loan_id,loan_amount,loan_amount_type, loan_duration, duration_unit,loan_type, loan_scheme, roi, reducing_roi, IRR, emi, loan_approval_date, loan_starting_date, loan_ending_date, agency_loan_amount, file_id, customer_id
		      FROM fin_loan
			  WHERE file_id=$file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0];
			}	  
		else
		{
			return "error";
			}		  
			  
		
		}
	
	}	

function getLoanDetailsByCustomerId($customer_id)
{
	if(checkForNumeric($customer_id))
	{
		$sql="SELECT loan_id,loan_amount,loan_amount_type, loan_duration,loan_type, loan_scheme, roi, reducing_roi, IRR, emi, loan_approval_date, loan_starting_date, loan_ending_date, file_id, customer_id
		      FROM fin_loan
			  WHERE customer_id=$customer_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0];
			}	  
		else
		{
			return "error";
			}		  
			  
		
		}
	
}

function getLoanTableForLoanId($id) 
{
	if(checkForNumeric($id))
	{
	//get the whole loan table shown in the customer details	
	$sql="SELECT loan_emi_id,actual_emi_date,emi_amount,company_paid_date
	      FROM fin_loan_emi
		  WHERE loan_id=$id";
	$result=dbQuery($sql);	  
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		$returnArray=array();
		for($i=0;$i<count($resultArray);$i++)
		{
			$returnArray[$i]['loanDetails']=$resultArray[$i];
			
			$returnArray[$i]['paymentDetails']=getPaymentDetailsForEmiId($resultArray[$i]['loan_emi_id']);
		}
		return $returnArray;
		}
	}
}



function getTotalPenaltyForLoan($id,$today=false)
{
	if($today==false)
	{
		$today=date('Y-m-d');
		}
	else
	{
			
			$today = str_replace('/', '-', $today);
			$today=date('Y-m-d',strtotime($today));	
		}	
	$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$id AND actual_emi_date<='$today'"; // actual_emi_date<today as penalty has penalty has to be calculated up to today
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
	
		$totalDays=0;
		foreach($resultArray as $re)
		{
			$totalDays=$totalDays+getPenaltyDaysFroEmiId($re[0],$today); 
		}
		return $totalDays;
		}
	return 0;	
}	
function getTotalPenaltyPaidDaysForLoan($id)
{
	$sql="SELECT SUM(days_paid) FROM fin_loan_penalty WHERE loan_id=$id GROUP BY loan_id=$id and rasid_type_id=1";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
	
		
		return $resultArray[0][0];
		}
	return 0;	
	}	

function getPenaltyDetailsForLoan($id)
{
	
	$sql="SELECT penalty_id, days_paid, paid_date, payment_mode, amount_per_day, total_amount, fin_loan_penalty.rasid_type_id,  paid,file_id, rasid_no, paid_by FROM fin_loan_penalty WHERE loan_id=$id AND rasid_type_id=1";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray;
		}
	return false;	
	
	
}		

function getAdditionalPaymentDetailsForLoan($id)
{
	
	$sql="SELECT penalty_id, days_paid, paid_date, payment_mode, amount_per_day, total_amount, fin_loan_penalty.rasid_type_id, rasid_type_name, paid,file_id, rasid_no, paid_by FROM fin_loan_penalty, fin_rasid_type WHERE loan_id=$id AND fin_loan_penalty.rasid_type_id=fin_rasid_type.rasid_type_id AND fin_loan_penalty.rasid_type_id!=1";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray;
		}
	return false;	
	
	
}				


function getFileIdForClosingFile($che=false) 
{
	// this is for the daily check done fro the files which are supposed to be closed because the loan ending date has reached 
	// if $che is set it has to be a date then result is restricted to che < loan_ending_date < today so less results and more perfomance
	$today=date('Y-m-d');
	$sql="SELECT fin_file.file_id From fin_loan,fin_file WHERE loan_ending_date<='$today'
	AND fin_file.file_id=fin_loan.file_id
	AND file_status=1";
	if($che!=false && $che!="NOTDONE")
	$sql=$sql." AND loan_ending_date>'$che'";
	$result=dbQuery($sql);
	$returnArray=array();
	$returnArray=getOpenLoansWithZeroBalance();
	if(dbNumRows($result)>0)
	{
		
		$resultArray=dbResultToArray($result);
		foreach($resultArray as $re)
		{
			$returnArray[]=$re[0];
		}
		return $returnArray;
		}
	else
	return $returnArray;	
}

function getOpenLoansWithZeroBalance() // gets loan which are open though thier balance ae zero
{
	$sql="SELECT loan_id,fin_file.file_id FROM fin_loan INNER JOIN fin_file ON fin_file.file_id=fin_loan.file_id WHERE file_status=1";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$returnArray=array();
	if(is_array($resultArray) && count($resultArray)>0)
	foreach($resultArray as $re)
	{
		$loan_id=$re[0];
		$bal=getBalanceForLoan($loan_id);
		if($bal>=0)
		{
			$returnArray[]=$re[1];
			}
		}
	return $returnArray;	
	
	}	



function getFullPenaltyTableForLoanId($loan_id,$today=false) // returns a full penalty table responding to each and every emi
{
	// we union full penalty table of all emis and join them to build for whole loan
	if($today==false)
	{
		$today=date('Y-m-d');
		}
	else
	{
			
			$today = str_replace('/', '-', $today);
			$today=date('Y-m-d',strtotime($today));	
		}	
		
	$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$loan_id AND actual_emi_date<= '$today' ";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	
	$returnArray=array();
	if(dbNumRows($result)>0)
	{
		foreach($resultArray as $re)
		{
		$loan_emi_id=$re[0];
		$penaltyDetails=getFullPenaltyTableForLoanEmiId($loan_emi_id,$today);
		if(isset($penaltyDetails) && !empty($penaltyDetails))
		{
		$returnArray[]=$penaltyDetails;
		}
		}
		
		return $returnArray;
	}
	else return false;
	return false;
}	

function getTotalPaymentForLoan($loan_id)
{
	// we sums up payment of all emis to give total payment for laon
	if(checkForNumeric($loan_id))
	{
	$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$loan_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$total=0;
	$returnArray=array();
	if(dbNumRows($result)>0)
	{
		foreach($resultArray as $re)
		{
		$loan_emi_id=$re[0];
		
		$balance=getTotalPaymetnsForEmi($loan_emi_id);
		
		$total=$total+$balance;
		}
		
		return $total;
	}
	else return false;
	return false;
	}
}	


function getTotalEmiPaidForLoan($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$loan=getLoanById($loan_id);
		if($loan['loan_scheme']==1)
		{
			$totalPayment=getTotalPaymentForLoan($loan['loan_id']);
			$totalEMIsPaid=number_format($totalPayment/$loan['emi'],1);
			return $totalEMIsPaid;
		}
		else if($loan['loan_scheme']==2)
		{
			
			$loan=getLoanById($loan_id);
			
			$totalPayment = getTotalPaymentForLoan($loan['loan_id']);
			
			$loan_scheme=getLoanScheme($loan_id);
			$total_amount=0;
			$loan_division_amounts=array(); // contains total amounts corresponding to each part of loan
			foreach($loan_scheme as $loan_part)
			{
				$emi=$loan_part['emi'];
				$duration=$loan_part['duration'];
				$total_amount=$total_amount+($emi*$duration);
				$loan_division_amounts[]=$total_amount; // loan_division_amounts is an array containg total amount to be paid to reach that part of loan 
			}
			
			for($i=0;$i<count($loan_division_amounts);$i++)
			{
				$loan_division_amount=$loan_division_amounts[$i];
				if($totalPayment<=$loan_division_amount)
				break;	
				}
			
			$extra_payment_remaining_after_slab=$totalPayment-$loan_division_amounts[$i-1];
			$emi_for_payment_after_slab=$loan_scheme[$i]['emi'];
			$no_of_emis_after_slab=$extra_payment_remaining_after_slab/$emi_for_payment_after_slab;
			
			$no_of_emis_befor_slab=0;
			for($j=0;$j<$i;$j++)
			{
				$no_of_emis_befor_slab=$no_of_emis_befor_slab+$loan_scheme[$j]['duration'];
				}
			$totalEMIsPaid=$no_of_emis_after_slab+$no_of_emis_befor_slab;		
			return $totalEMIsPaid;
		}
	}
	
}

function getBalanceForLoan($loan_id)
{
	//gets the remaining amount for the loan IMP it is NEGATIVE
	if(checkForNumeric($loan_id))
	{
	$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$loan_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$total=0;
	$returnArray=array();
	if(dbNumRows($result)>0)
	{
		foreach($resultArray as $re)
		{
		$loan_emi_id=$re[0];
		$balance=getBalanceForEmi($loan_emi_id);
		$total=$total+$balance;
		}
		
		return $total;
	}
	else return false;
	return false;
	}
}	

function getPaymentForLoanUptoDate($loan_id,$date) // Y-m-d
{
	//returns total payment for loan upto the corresponding date 
	if(checkForNumeric($loan_id))
	{
	$sql="SELECT SUM(payment_amount) FROM fin_loan_emi_payment
	      INNER JOIN fin_loan_emi
		  ON fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
		  INNER JOIN fin_loan
		  ON fin_loan_emi.loan_id=fin_loan.loan_id
		  WHERE fin_loan.loan_id=$loan_id AND payment_date<'$date'
		  GROUP BY fin_loan.loan_id";
	$result=dbQuery($sql);
	
	$resultArray=dbResultToArray($result);
	if(isset($resultArray[0][0]))
	return $resultArray[0][0];
	else return 0;
	}
}

function getOpeningBalanceForLaonAtDate($loan_id,$date)
{
	
	return -(getIdealPaymentsForLoanIdUptoDate($loan_id,$date)-getPaymentForLoanUptoDate($loan_id,$date));
	
	}
	


function getNoOfEmiBeforeDateForLoanId($loan_id,$date)
{
	$sql="SELECT count(loan_emi_id) FROM fin_loan_emi WHERE loan_id=$loan_id AND actual_emi_date<'$date'";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(isset($resultArray[0][0]))
	return $resultArray[0][0];
	else 
	return 0;
	}	
function getEmiIdsBeforeDateForLoanId($loan_id,$date)
{
	$sql="SELECT loan_emi_id,emi_amount FROM fin_loan_emi WHERE loan_id=$loan_id AND actual_emi_date<'$date'";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(isset($resultArray[0][0]))
	return $resultArray;
	else 
	return 0;
	}	
function getBucketDetailsForLoan($loan_id)
{
	$loan=getLoanById($loan_id);
	if($loan['loan_scheme']==1)
	{
		$totalEMIsPaid=getTotalEmiPaidForLoan($loan_id);
		 $actualEMis=getNoOfEmiBeforeDateForLoanId($loan['loan_id'],date('Y-m-d'));
		$bucket=$actualEMis-$totalEMIsPaid;
		if($bucket>0)  return array($loan['emi'] => number_format($bucket,1)); 
		else return array($loan['emi'] => 0);
		}
	else
	{
		$returnArray=array();
		$emi_ids=getEmiIdsBeforeDateForLoanId($loan_id,date('Y-m-d'));
		foreach($emi_ids as $emi_id)
		{
			$loan_emi_id=$emi_id['loan_emi_id'];
			$balance=getBalanceForEmi($loan_emi_id);
			if($balance<0)
			{
				$emi=getEmiForLoanEmiId($loan_emi_id);
				$bucket=-$balance/$emi;
				$bucket=number_format($bucket,2);
				if(!isset($returnArray[$emi]))
				{
					$returnArray[$emi]=$bucket;
					}
				else
				{
					$returnArray[$emi]=$returnArray[$emi]+$bucket;
					}	
			}
		}
		if(empty($returnArray))
		return 0.0;
		return $returnArray;
	}	
	
}

function getBucketDetailsForEMIIDs($emi_ids)
{
	
		$returnArray=array();
		foreach($emi_ids as $emi_id)
		{
			$loan_emi_id=$emi_id['loan_emi_id'];
			$balance=getBalanceForEmi($loan_emi_id);
			if($balance<0)
			{
				$emi=getEmiForLoanEmiId($loan_emi_id);
				$bucket=-$balance/$emi;
				$bucket=number_format($bucket,2);
				if(!isset($returnArray[$emi]))
				{
					$returnArray[$emi]=$bucket;
					}
				else
				{
					$returnArray[$emi]=$returnArray[$emi]+$bucket;
					}	
			}
		}
		if(empty($returnArray))
		return 0;
		return $returnArray;
}	

function getBucketForLoan($loan_id)
{
		$loan=getLoanById($loan_id);
		$totalEMIsPaid=getTotalEmiPaidForLoan($loan_id);
		$actualEMis=getNoOfEmiBeforeDateForLoanId($loan['loan_id'],date('Y-m-d'));
		$bucket=$actualEMis-$totalEMIsPaid;
		if($bucket>0)  return number_format($bucket,2); 
		else return  0.0;
		
	
}		
function getNoOfEmiBetweenDatesForLoanId($loan_id,$from_date,$to_date)
{
	$sql="SELECT count(loan_emi_id) FROM fin_loan_emi WHERE loan_id=$loan_id AND actual_emi_date>='$from_date' AND actual_emi_date<='$to_date'";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(isset($resultArray[0][0]))
	return $resultArray[0][0];
	else 
	return 0;
	}	

function getEmiIdsBetweenDatesForLoanId($loan_id,$from_date,$to_date)
{
	$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$loan_id AND actual_emi_date>='$from_date' AND actual_emi_date<='$to_date'";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(isset($resultArray[0][0]))
	return $resultArray;
	else 
	return 0;
	}		


function getIdealPaymentsForLoanIdUptoDate($loan_id,$date)
{
	$emis_id=getEmiIdsBeforeDateForLoanId($loan_id,$date);
	$ideal_payment=0;
	if(is_array($emis_id))
	{
		foreach($emis_id as $emi_id)
		{
			$emi=getEmiForLoanEmiId($emi_id[0]);
			$ideal_payment=$ideal_payment+$emi;
			}
	}
	
	return $ideal_payment;
	}

function getIdealPaymentsForLoanIdBetweenDates($loan_id,$from_date,$to_date)
{
	$emis_id=getEmiIdsBetweenDatesForLoanId($loan_id,$from_date,$to_date);
	$ideal_payment=0;
	if(is_array($emis_id))
	{
		foreach($emis_id as $emi_id)
		{
			$emi=getEmiForLoanEmiId($emi_id[0]);
			$ideal_payment=$ideal_payment+$emi;
			}
	}
	
	return $ideal_payment;
}			
	
function getPaymentsWithinDates($loan_id,$from_date,$to_date)
{
	if(checkForNumeric($loan_id))
	{
		
	$sql="SELECT SUM(payment_amount) FROM fin_loan_emi_payment
	      INNER JOIN fin_loan_emi
		  ON fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
		  INNER JOIN fin_loan
		  ON fin_loan_emi.loan_id=fin_loan.loan_id
		  WHERE fin_loan.loan_id=$loan_id AND payment_date>='$from_date' AND payment_date<='$to_date'
		  GROUP BY fin_loan.loan_id";
		  
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(isset($resultArray[0][0]))
	return $resultArray[0][0];
	else return 0;
	}
}	
	
function getAllPaymentsWithinDates($loan_id,$from_date,$to_date)
{
	if(checkForNumeric($loan_id))
	{
		
	$sql="SELECT fin_loan.loan_id,fin_loan_emi.loan_emi_id,SUM(payment_amount) as amount FROM fin_loan_emi_payment
	      INNER JOIN fin_loan_emi
		  ON fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
		  INNER JOIN fin_loan
		  ON fin_loan_emi.loan_id=fin_loan.loan_id
		  WHERE fin_loan.loan_id=$loan_id AND payment_date>='$from_date' AND payment_date<='$to_date'
		  GROUP BY fin_loan_emi.loan_emi_id";
		  
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(isset($resultArray[0][0]))
	return $resultArray;
	else return 0;
	}
}	

function getFirstEmiIdForLoan($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$id ORDER BY loan_emi_id LIMIT 0,1";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
		
		}
	
	}
	


function getUnPaidEmisUptillTodayForLoan($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$today=date('Y-m-d');
		$oldest_emi_id=getOldestUnPaidEmi($loan_id);
		if($oldest_emi_id!=false && is_numeric($oldest_emi_id))
		{
			$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$loan_id AND loan_emi_id>=$oldest_emi_id AND actual_emi_date<'$today'";
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
		
		}
	}
	
function getUnPaidEmisUptillDateForLoanForCompanyPaidReports($loan_id,$date)
{
	if(checkForNumeric($loan_id))
	{
		$date = str_replace('/', '-', $date);
		$date=date('Y-m-d',strtotime($date));
		$oldest_emi_id=getOldestUnPaidEmi($loan_id);
		if($oldest_emi_id!=false && is_numeric($oldest_emi_id))
		{
			$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$loan_id AND loan_emi_id>=$oldest_emi_id AND actual_emi_date<'$date' AND company_paid_date IS NOT NULL";
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
		
		}
	}	

		

function getTotalCollectionForLoan($id)
{
	$total_collection=0;
	$sql="SELECT loan_duration*emi as total_collection,loan_id,loan_scheme  FROM fin_loan WHERE loan_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		
		if($resultArray[0]['loan_scheme']==1)
		return $resultArray[0][0];
		else
		{
			
			$loan_scheme=getLoanScheme($id);
			
			if($loan_scheme!="error")
			{
				foreach($loan_scheme as $scheme)
				{
					$duration=0;
					$emi=0;
					$duration=$scheme['duration'];
					$emi=$scheme['emi'];
					$collection=$emi*$duration;
					$total_collection=$total_collection+$collection;
				}
			return $total_collection;	
			}
		}
	}
}	

function getLoanScheme($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="SELECT scheme_id,duration,emi,loan_id FROM fin_loan_scheme WHERE loan_id=$loan_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray;
			}
		else return "error";	
		
		}
	
	}

function getProfitForLoan($id)
{
	$total_collection=getTotalCollectionForLoan($id);
	$loan=getLoanById($id);
	$amount=$loan['loan_amount'];
	$profit=$total_collection-$amount;
	return $profit;
}

function setCorrectReducingRateFromPrinInterestTableForLoan($id)
{
	
	$table=getIntPrincBalanceTableForLoan($id);
	
	$last_element=count($table)-1;
	$balance=$table[$last_element]['balance'];
	$loan=getLoanById($id);
	$reducing_rate=$loan['reducing_roi'];
	
	$step=1;
	while($balance!=0)
	{
		while($balance<0)
		{
		$reducing_rate=$reducing_rate+$step;
		
		$sql="UPDATE fin_loan SET reducing_roi=$reducing_rate WHERE loan_id=$id";
		dbQuery($sql);
		$balance=getLastBalanceFromPrinInterestTableForLoan($id);
		if($balance<=10 && $balance>=-10)
		{
			
			return;
			}
		}
		if($balance<=10 && $balance>=-10)
		{
			
			return;
			}
		$step=$step/2;	
		while($balance>0)
		{
		$reducing_rate=$reducing_rate-$step;
		
		$sql="UPDATE fin_loan SET reducing_roi=$reducing_rate WHERE loan_id=$id";
		dbQuery($sql);
		$balance=getLastBalanceFromPrinInterestTableForLoan($id);
		if($balance<=10 && $balance>=-10)
		{
			
			return;
			}
		
		}
		if($balance<=10 && $balance>=-10)
		{
			
			return;
			}
		$step=$step/2;
	}
	
}

function setCorrectIrrForLoan($id)
{
	$loan=getLoanById($id);
	$loan_emi_id_emi_amount_array=getLoanEmiIdAndEmiAmountForLoan($id);
	$loan_amount=$loan['loan_amount'];
	$reducing_roi=$loan['reducing_roi'];
	$reducing_roi=$reducing_roi/100;
	$duration=$loan['loan_duration'];
	$one_month_in_year=1/12;
	$total_inflow=0;
	$step=0.03;
	
	for($i=1;$i<=$duration;$i++)
	{
		$emi=$loan_emi_id_emi_amount_array[$i-1]['emi_amount'];
		$current_month=$one_month_in_year*$i;
		$inflow=($emi/pow(1+$reducing_roi,$current_month));
		$total_inflow=$total_inflow+$inflow;
		}
	$net_flow=$total_inflow-$loan_amount;
	$net_flow=number_format($net_flow,2);
	while($net_flow!=0)
	{
		
		while($net_flow>0)
		{
			$total_inflow=0;
			$reducing_roi=$reducing_roi+$step;
			for($j=1;$j<=$duration;$j++)
			{
				$emi=$loan_emi_id_emi_amount_array[$j-1]['emi_amount'];
				$current_month=$one_month_in_year*$j;
				$inflow=($emi/pow(1+$reducing_roi,$current_month));
				$total_inflow=$total_inflow+$inflow;
				}
			$net_flow=$total_inflow-$loan_amount;
			$net_flow=number_format($net_flow,2);
			
		}
		$step=$step/1.25;
		while($net_flow<0)
		{
			$total_inflow=0;
			$reducing_roi=$reducing_roi-$step;
			for($k=1;$k<=$duration;$k++)
			{
				$emi=$loan_emi_id_emi_amount_array[$k-1]['emi_amount'];
				$current_month=$one_month_in_year*$k;
				$inflow=($emi/pow(1+$reducing_roi,$current_month));
				$total_inflow=$total_inflow+$inflow;
				}
			$net_flow=$total_inflow-$loan_amount;
			$net_flow=number_format($net_flow,2);
			
			}
		$step=$step/1.25;
		
	}	
	
	 $irr=$reducing_roi*100;
	 $sql="UPDATE fin_loan SET irr=$irr WHERE loan_id=$id";
	 $result=dbQuery($sql);
}
function getLastBalanceFromPrinInterestTableForLoan($id)
{
	$table=getIntPrincBalanceTableForLoan($id);
	$last_element=count($table)-1;
	$balance=$table[$last_element]['balance'];
	return $balance;
	}

function getFlatRateFromReducing($principal,$reducing_rate,$duration_in_months)
{
	$intr=$reducing_rate/1200;
	$yrs=$duration_in_months/12;
	$emi = ceil($principal * $intr / (1 - (pow(1/(1 + $intr), $duration_in_months))));
    $total_amount = $emi * $duration_in_months;
    $tot_interest = $total_amount - $principal;
    $yearly_interest = $tot_interest /$yrs;
	$flat_rate_per_annum = $yearly_interest / $principal * 100;
	return number_format($flat_rate_per_annum,2);
}

function getEMIFromReducing($principal,$reducing_rate,$duration_in_months)
{
	$intr=$reducing_rate/1200;
	$yrs=$duration_in_months/12;
	$emi = $principal * $intr / (1 - (pow(1/(1 + $intr), $duration_in_months)));
	return ceil($emi);
	}		

function getReducingRateAndIRRFromFlat($amount,$duration_in_months,$interest,$duration_unit=1)
{
$interest=$interest/100;
$tenure=$duration_in_months*$duration_unit;
$pay=$amount*(1+$tenure/12*$interest);
$strt=($pay-$amount)/$amount/$tenure;
$emi =$pay/$tenure;
$r=1/(1+$strt);
$res= $emi*((pow($r,$tenure)-1)/($r-1))*$r-$amount;
if($res>0)
{
	do {
	$strt=$strt+0.00001;
	$r=1/(1+$strt);
	$res=$emi*((pow($r,$tenure)-1)/($r-1))*$r-$amount;
	}while($res > 0);

}
$eirr1=(pow(1+$strt,12)-1)*10000;
$eirr=(($strt)*12)*10000;
$eirr=round($eirr); // reducing rate
$eirr1=round($eirr1); // IRR
$eirr/=100;
$eirr1/=100;
$emi=$eirr;
$irr=$eirr1;
return array($eirr,$eirr1); // first element reducing Rate, second element is IRR
}

function getLoanEmiIdAndEmiAmountForLoan($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT loan_emi_id,emi_amount FROM fin_loan_emi WHERE loan_id=$id ORDER BY actual_emi_date";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		return $resultArray;
		}
	
}

function getIntPrincBalanceTableForLoan($id)
{
	if(checkForNumeric($id))
	{
	$loan=getLoanById($id);
	$amount=$loan['loan_amount'];
	$dutation=$loan['loan_duration'];
	$roi=$loan['reducing_roi'];
	$returnArray=array();
	$emi_date_array=getActualDatesForLoan($id);
	$loan_emi_id_emi_amount_array=getLoanEmiIdAndEmiAmountForLoan($id);
	if(checkForNumeric($amount,$dutation,$roi))
	{
	
		for($i=0;$i<count($emi_date_array);$i++){
			$emi=$loan_emi_id_emi_amount_array[$i]['emi_amount'];
			$intr=round((($amount*$dutation*$roi)/(1200*($dutation/12)))/12); //interest per month
			$princ_paid=$emi-$intr;
			$amount=$amount-$princ_paid;
			$returnArray[$i]['emi']=$emi;
			$returnArray[$i]['actual_emi_date']=$emi_date_array[$i]['actual_emi_date'];
			$returnArray[$i]['interest']=$intr;
			$returnArray[$i]['principal']=$princ_paid;
			$returnArray[$i]['balance']=$amount;
		};
		
		return $returnArray;
		
	}
	
	}
}

function getIntPrincBalanceTable($amount,$dutation,$roi,$emi)
{
	if(checkForNumeric($amount,$dutation,$roi))
	{
	$returnArray=array();	
		for($i=0;$i<$dutation;$i++){
			$intr=round((($amount*$dutation*$roi)/(1200*($dutation/12)))/12); //interest per month
			$princ_paid=$emi-$intr;
			$amount=$amount-$princ_paid;
			$returnArray[$i]['emi']=$emi;
			$returnArray[$i]['interest']=$intr;
			$returnArray[$i]['principal']=$princ_paid;
			$returnArray[$i]['balance']=$amount;
			
		}
	
	return $returnArray;	
		
	}
}
function updateAllFiles()
{
	$sql="SELECT loan_id,loan_amount,loan_duration,roi FROM fin_loan";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	
	foreach($resultArray as $re)
	{
		$loan_id=$re['loan_id'];
		$amount=$re['loan_amount'];
		$loan_duration=$re['loan_duration'];
		$roi=$re['roi'];
		
		$reducing_rate_irr=getReducingRateAndIRRFromFlat($amount,$loan_duration,$roi);
		$reducing_rate=$reducing_rate_irr[0];
		$irr=$reducing_rate_irr[1];
		
		$emi=getEMIFromReducing($amount,$reducing_rate,$loan_duration);
		
		$sql="UPDATE fin_loan SET  reducing_roi=$reducing_rate, IRR=$irr
		      WHERE loan_id=$loan_id";
		dbQuery($sql);	  
		}
	}

function getActualDatesForLoan($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT actual_emi_date FROM fin_loan_emi WHERE loan_id=$id ORDER BY actual_emi_date";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		return $resultArray;
		}
}

function getFileIdsAndLoanIdsForAgencyId($agency_id)
{
	if(checkForNumeric($agency_id))
	{
		$sql="SELECT fin_file.file_id, loan_id FROM fin_file, fin_loan WHERE fin_file.file_id= fin_loan.file_id AND agency_id=$agency_id AND file_status!=3 AND file_status!=4";
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
	
}	
	
function getFileIdsAndLoanIdsForOCId($agency_id)
{
	
	if(checkForNumeric($agency_id))
	{
		$sql="SELECT fin_file.file_id, loan_id FROM fin_file, fin_loan WHERE fin_file.file_id= fin_loan.file_id AND oc_id=$agency_id AND file_status!=3 AND file_status!=4";
		
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
	
	}		
	

/* installment Functions */	

function insertEMIsForLoan($id,$duration,$starting_date,$emi,$duration_unit=1)
{
	try
	{
		if($duration>0 && $starting_date!=null && $starting_date!="" && checkForNumeric($emi))
		{
		$starting_date = str_replace('/', '-', $starting_date);
		$starting_date=date('Y-m-d',strtotime($starting_date));		
		$datesArray=getArrayOfDatesForEMI($starting_date,$duration,$duration_unit);
			foreach($datesArray as $emi_date)
			{	
			$sql="INSERT INTO fin_loan_emi
				  (actual_emi_date,emi_amount,company_paid_date,loan_id)
				  VALUES
				  ('$emi_date',$emi,null,$id)";
				  dbQuery($sql);
			}
		}
	}
	catch(Exception $e)
	{
		
	}
}

function updateEMIsForLoan($id,$duration,$starting_date,$emi=false,$duration_unit=1)
{
	try
	{
		if($duration>0 && $starting_date!=null && $starting_date!="")
		{
		$emiIDs=getAllEmiIDsForLoan($id);	
		$starting_date = str_replace('/', '-', $starting_date);
		$starting_date=date('Y-m-d',strtotime($starting_date));		
		$datesArray=getArrayOfDatesForEMI($starting_date,$duration,$duration_unit);
		if(count($emiIDs)==count($datesArray))
		{
		for($d=0;$d<count($datesArray);$d++)
			{	
			$emi_date=$datesArray[$d];
			$emi_id=$emiIDs[$d]['loan_emi_id'];
			$sql="UPDATE fin_loan_emi
				  SET actual_emi_date='$emi_date'";
			if($emi!=false && checkForNumeric($emi))
			$sql=$sql." , emi_amount=$emi ";	  
				  $sql=$sql."WHERE loan_emi_id=$emi_id";
				  dbQuery($sql);
			}
		}
		}
	}
	catch(Exception $e)
	{
	}
	
}

function getAllEmiIDsForLoan($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$loan_id ORDER BY actual_emi_date";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray;
		else return false;
		}
}

function getArrayOfDatesForEMI($starting_date,$duration,$duration_unit=1)
{
	$returnArray=array();
	$starting_date = str_replace('/', '-', $starting_date);
	$starting_date=date('Y-m-d',strtotime($starting_date));	

	for($i=0;$i<$duration*$duration_unit;$i=$i+$duration_unit)
		{
			
		$monthToAdd = $i;
		
		$d1 = DateTime::createFromFormat('Y-m-d', $starting_date);
		
		$year = $d1->format('Y');
		$month = $d1->format('n');
		$day = $d1->format('d');
		
		$year += floor($monthToAdd/12);
		$monthToAdd = $monthToAdd%12;
		$month += $monthToAdd;
		if($month > 12) {
			$year ++;
			$month = $month % 12;
			if($month === 0)
				$month = 12;
		}
		
		if(!checkdate($month, $day, $year)) {
			$d2 = DateTime::createFromFormat('Y-n-j', $year.'-'.$month.'-1');
			$d2->modify('last day of');
		}else {
			$d2 = DateTime::createFromFormat('Y-n-d', $year.'-'.$month.'-'.$day);
		}
		$d2->setTime($d1->format('H'), $d1->format('i'), $d1->format('s'));
		
		$returnArray[]=$d2->format('Y-m-d');
		}
	return $returnArray;	
}

function getActualDateForLoanEMIId($id)
{
	$sql="SELECT loan_id,actual_emi_date
	      FROM fin_loan_emi
		  WHERE loan_emi_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][1];
	else
	return 0;	  
}

function getEmiForLoanEmiId($id)
{
	
	$sql="SELECT emi_amount
		  FROM fin_loan_emi
		  WHERE loan_emi_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
		{
			
			return $resultArray[0][0];
		}
	else
	{
		return 0;
		}	
	}
	
function getAgnecyIdFromEmiId($id)
{
	$sql="SELECT agency_id,oc_id FROM fin_file,fin_loan_emi,fin_loan
	      WHERE loan_emi_id=$id
		  AND fin_loan_emi.loan_id=fin_loan.loan_id
		  AND fin_file.file_id=fin_loan.file_id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		$resultArray=dbResultToArray($result);
		return $resultArray[0];
		}
		  
	}	
	
	function getAgnecyIdFromLoanId($id)
{
	$sql="SELECT agency_id,oc_id FROM fin_file,fin_loan
	      WHERE loan_id=$id
		  AND fin_file.file_id=fin_loan.file_id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		$resultArray=dbResultToArray($result);
		return $resultArray[0];
		}
		  
	}	

function getTotalPaymetnsForEmi($id)
{
	try{
		$sql="SELECT SUM(payment_amount) AS payment
			  FROM fin_loan_emi_payment 
			  WHERE loan_emi_id=$id";
			  
		$result=dbQuery($sql);
		
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return 	$resultArray[0][0];
		else
		return 0;
		}
	catch(Exception $e)
	{}
	}
	
function getBalanceForEmi($loan_emi_id) // gives negative value
{
		$emi=getEmiForLoanEmiId($loan_emi_id);
		
		$payment=getTotalPaymetnsForEmi($loan_emi_id);
		
		$balance= $payment-$emi;
	return $balance;
	}	

function getPaymentDetailsForEmiId($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT emi_payment_id, payment_amount, payment_mode, payment_date, rasid_no, paid_by, remarks, remainder_date, remainder_status
		      FROM fin_loan_emi_payment
			  WHERE loan_emi_id=$id ORDER BY payment_date";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray;
		else
		return false;
		}
	}	

		

function getEmiDetailsByEmiId($id)
{
	$sql="SELECT loan_emi_id,actual_emi_date,company_paid_date
	      FROM fin_loan_emi
		  WHERE loan_emi_id=$id";
	$result=dbQuery($sql);	  
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		$returnArray=array();
		
			$returnArray[0]['loanDetails']=$resultArray[0];
			
			$returnArray[0]['paymentDetails']=getPaymentDetailsForEmiId($resultArray[0]['loan_emi_id']);
		
		return $returnArray[0];
		}
	
	}


function getTotalPaymentFroEmiIds($emi_ids)
{
	
	$payment=0;
	
	if(is_array($emi_ids) && count($emi_ids)>0)
	{
		
	foreach($emi_ids as $emi_id)
	{
		if(is_numeric($emi_id))
		{
		$sql="SELECT sum(payment_amount)
		      FROM fin_loan_emi_payment
			  WHERE loan_emi_id=$emi_id
			  GROUP BY loan_emi_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		$payment=$payment+$resultArray[0][0];
		}
		}
	return $payment;	
		
	}
}

function getPenaltyDaysFroEmiId($id,$today=false)
{
	$balance=getBalanceForEmi($id);
	if($today==false)
	{
		$today=date('Y-m-d');
		}
	else
	{
			
			$today = str_replace('/', '-', $today);
			$today=date('Y-m-d',strtotime($today));	
		}	
	$daylen = 60*60*24;
$sql="SELECT actual_emi_date
	      FROM fin_loan_emi
		  WHERE loan_emi_id=$id";
	$result=dbQuery($sql);	  
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		
		$actual_date=$resultArray[0][0];
		
		if($balance<0)
 		{
		return (strtotime($today)-strtotime($actual_date))/$daylen; 
	 	}
 		 else if($balance==0)
  		{
	  	  $sql="SELECT MAX(payment_date)
	      FROM fin_loan_emi_payment
		  WHERE loan_emi_id=$id
		  GROUP BY loan_emi_id";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
	  	if(dbNumRows($result)>0)
		{
		$last_payment_date = $resultArray[0][0];
		if(strtotime($last_payment_date)>strtotime($actual_date))
		return (strtotime($last_payment_date)-strtotime($actual_date))/$daylen;
		else
		return 0;
		}
	else
	return 0;
	  
	  }
	}
	
 
  return 0;
	
}	


	
	
function getPaymentUptoDateForEmiId($payment_id,$emi_id,$date)
{
	
	$sql="SELECT SUM(payment_amount),MAX(payment_date)
	      FROM fin_loan_emi_payment
		  WHERE payment_date<'$date'
		  AND loan_emi_id=$emi_id
		  AND emi_payment_id!=$payment_id
		  GROUP BY loan_emi_id";
		  
	 $result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
	  	if(dbNumRows($result)>0)
		{
			return $resultArray[0];
			} 
	}	
				
function getLoanIdFromEmiId($emi_id)
{
	
	if(checkForNumeric($emi_id))
	{
	$sql="SELECT loan_id from fin_loan_emi WHERE loan_emi_id=$emi_id";
	$result=dbQuery($sql);
	$returnArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		
		return $returnArray[0][0];
	}
	return false;
	}
}


function getNextValidEmisWithBalance($emi_id,$balance)
{
	
	
	if(checkForNumeric($emi_id))
	{
		$loan_id=getLoanIdFromEmiId($emi_id);
		if(checkForNumeric($loan_id))
		{
			$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_emi_id!=$emi_id AND loan_id=$loan_id";
			$result=dbQuery($sql);
			$resultArray=dbResultToArray($result);
			$returnArray=array();
			if(dbNumRows($result)>0)
			{
				$no=0;
				foreach($resultArray as $result)
				{
					$id=$result[0];
					$emiBalance=getBalanceForEmi($id);
					
					if($emiBalance<0)
					{
						$balance=$balance+$emiBalance;
						if($balance<=0)
						{
							$returnArray[$no]['emi_id']=$id;
							$returnArray[$no]['emi_balance']=$emiBalance-$balance;
							return $returnArray;
						}
						else if($balance>0)
						{
							$returnArray[$no]['emi_id']=$id;
							$returnArray[$no]['emi_balance']=$emiBalance;
						}
					}
					$no++;
				}
				
				if(count($returnArray)>0)
				return $returnArray;
				
			}
			return false;
		}
	}
	return false;
}	

function getFullPenaltyTableForLoanEmiId($loan_emi_id,$today=false)
{
	if($today==false)
	{
		$today=date('Y-m-d');
		}
	else
	{
			
			$today = str_replace('/', '-', $today);
			$today=date('Y-m-d',strtotime($today));	
		}	
		
	$daylen = 60*60*24;
	$sql="SELECT GROUP_CONCAT(emi_payment_id) FROM fin_loan_emi_payment WHERE loan_emi_id=$loan_emi_id GROUP BY payment_date ORDER BY payment_date";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$returnArray=array();
	if(dbNumRows($result)>0)
	{
		
		foreach($resultArray as $re)
		{
			$emi_payment_id=$re[0];	
			
			$penaltyDetails=getPenaltyDaysForPaymentId($emi_payment_id);
			$returnArray['paidDetails'][]=$penaltyDetails;
			$notExpired=checkIfEMIExpired($loan_emi_id,$today);
			
			if($notExpired)
			{
				$balance=getBalanceForEmi($loan_emi_id);
				$emi=getEmiForLoanEmiId($loan_emi_id);
				$actual_date=getActualDateForLoanEMIId($loan_emi_id);
				
				
				if($balance<0 && strtotime($actual_date)<=strtotime($today))
				{
					
					$returnArray['UnPaidDetails']['days']=(strtotime($today)-strtotime($penaltyDetails['payment_date']))/$daylen;
					$returnArray['UnPaidDetails']['amount']=-$balance;
					$returnArray['UnPaidDetails']['emi']=$emi;
					$returnArray['UnPaidDetails']['actual_date']=$actual_date;
				}
			
			}
			
		
		}
		
		return $returnArray;
	}
	else
	{
		
		$notExpired=checkIfEMIExpired($loan_emi_id,$today);
			
			if($notExpired)
			{
				
				$balance=getBalanceForEmi($loan_emi_id);
				$emi=getEmiForLoanEmiId($loan_emi_id);
				$actual_date=getActualDateForLoanEMIId($loan_emi_id);
				if($balance<0)
				{
					
					$returnArray['UnPaidDetails']['days']=$notExpired;
					$returnArray['UnPaidDetails']['amount']=-$balance;
					$returnArray['UnPaidDetails']['emi']=$emi;
					$returnArray['UnPaidDetails']['actual_date']=$actual_date;
				}
			   return $returnArray;
			}	
		
	}
}

function checkIfEMIExpired($loan_emi_id,$today=false)
{
	
	if(checkForNumeric($loan_emi_id))
	{
	$daylen = 60*60*24;
	if($today==false)
	{
		$today=date('Y-m-d');
		}
	else
	{
			
			$today = str_replace('/', '-', $today);
			$today=date('Y-m-d',strtotime($today));	
		}	
	$sql="SELECT actual_emi_date FROM fin_loan_emi WHERE loan_emi_id=$loan_emi_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		$actual_date=$resultArray[0][0];
		$penalty_days=(strtotime($today)-strtotime($actual_date))/$daylen;	
		if($penalty_days>0)
		return $penalty_days;
		else return false;
	}
	return false;
	}
}


function getOldestUnPaidEmi($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="SELECT loan_emi_id FROM fin_loan_emi WHERE loan_id=$loan_id";
		$result=dbQuery($sql);
		
		$resultArray=dbResultToArray($result);
		foreach($resultArray as $row)
		{
		   $loan_emi_id=$row['loan_emi_id'];
		   $balance=getBalanceForEmi($loan_emi_id);
		   if($balance<0)
		   return $loan_emi_id;
		}
		
		return false;
		
		}
}

function getOldestUnPaidEmiDate($loan_id)
{
	if(checkForNumeric($loan_id))
	{
		$sql="SELECT loan_emi_id,actual_emi_date FROM fin_loan_emi WHERE loan_id=$loan_id";
		$result=dbQuery($sql);
		
		$resultArray=dbResultToArray($result);
		foreach($resultArray as $row)
		{
		   $loan_emi_id=$row['loan_emi_id'];
		   $balance=getBalanceForEmi($loan_emi_id);
		   if($balance<0)
		   return $row;
		}
		
		return false;
		
		}
}
				
function deleteEMIsForLoan($id,$after_emi_id=false)
{
	if(checkForNumeric($id))
	{
	$sql="DELETE FROM fin_loan_emi WHERE loan_id=$id";
	if($after_emi_id!=false)
	$sql=$sql." AND loan_emi_id>$after_emi_id";
	dbQuery($sql);
	}
}

/* payment functions */

function insertPayment($id,$amount,$payment_mode,$payment_date,$rasid_no,$remarks=false,$remainder_date=false,$bank_name=false,$branch=false,$cheque_no=false,$cheque_date=false,$cheque_return=0,$ledger_id=0,$paid_by="NA",$auto_rasid_no=0)
{
	try{
		
		while(check_rasid()==false)
		{
			sleep(1);
			}	
		lock_rasid();	
		
		if($remainder_date=="" || $remainder_date==false || $remainder_date==null)
		{
		$remainder_date="1970-01-01";
		}
		else
		{
			$remainder_date = str_replace('/', '-', $remainder_date);
			$remainder_date=date('Y-m-d',strtotime($remainder_date));	
			}
		$amount=trim($amount);	
		$remarks=clean_data($remarks);	
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$payment_date = str_replace('/', '-', $payment_date);
		$payment_date=date('Y-m-d',strtotime($payment_date));		
		$balance=getBalanceForEmi($id);
		$ag_id_array=getAgnecyIdFromEmiId($id);
		$loan_id=getLoanIdFromEmiId($id);
		$file_id=getFileIdFromLoanId($loan_id);
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		/* Accounts Start */
		if(getAccountsStatus())
			{
				if($agency_company_type=="agency")
				{
					
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForAgency($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForAgency($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForAgency($agency_company_type_id);
				}
				else
				{
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForOC($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForOC($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForOC($agency_company_type_id);
				}
			}
		/* Accounts Stop */
		
		if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			$current_rasid_counter=getRasidCounterForAgencyId($agency_id);
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_prefix=getPrefixFromOCId($oc_id);
			$current_rasid_counter=getRasidCounterForOCID($oc_id);
			}
			
		if($auto_rasid_no==$rasid_no)
		{
			
			$rasid_no=$current_rasid_counter;
			}	
		if(!isset($date_added) || ($date_added==null || $date_added==false) )
		{

			$sql="SELECT NOW()";
			$result= dbQuery($sql);
			$resultArray=dbResultToArray($result);
			$date_added = $resultArray[0][0];
			}	
		$or_rasid_no=$rasid_no;		
		
		$rasid_no=$rasid_prefix.$rasid_no;		
	
		
		if(checkForNumeric($id,$amount) && $payment_date!=null && $payment_date!="" && ($balance+$amount<=0) && $amount>0 && $balance<0)
		{
				
			if($payment_mode==2)
			{
				
			$sql="INSERT INTO 
				  fin_loan_emi_payment(payment_amount, payment_mode, payment_date, rasid_no, paid_by, remarks, remainder_date, loan_emi_id, created_by, last_updated_by , date_added, date_modified)
				  VALUES
				  ($amount, $payment_mode , '$payment_date', '$rasid_no', '$paid_by' , '$remarks', '$remainder_date', $id, $admin_id, $admin_id, '$date_added', NOW())";
				 
				  dbQuery($sql);
				  $emi_payment_id=dbInsertId();
				  
				  if(is_numeric($ag_id_array[0]))
					{
						$rasid_counter=getRasidCounterForAgencyId($agency_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidCounterForAgency($agency_id);
					}
					else if(is_numeric($ag_id_array[1]))
					{
					$rasid_counter=getRasidCounterForOCID($oc_id);
					if($rasid_counter==$or_rasid_no)
					incrementRasidNoForOCID($oc_id);
					}
					if(checkForNumeric($cheque_no,$emi_payment_id) && $payment_mode==2 && $bank_name!=false && $bank_name!="" && $bank_name!=null && $branch!=false && $branch!="" && $branch!=null)
					{
						
						$bank_array=insertIfNotDuplicateBank($bank_name,$branch);
						$bank_id=$bank_array[0];
						$branch_id=$bank_array[1];
						insertChequePayment($bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id,$cheque_return,$ledger_id);
						/* accounts start */
						if(getAccountsStatus())
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							if(strtotime($payment_date)>=strtotime($books_starting_date))
							{
								if($account_settings['mercantile']==2)
								{
									//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									//$interest_amount=$amount-$principal_amount;
									
									debitAccountingLedger($ledger_id,$amount); // debit bank account
								    creditAccountingCustomer($customer_id,$amount); // credit customer
									
								}
								else if($account_settings['mercantile']==0)
								{
									$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									$interest_amount=$amount-$principal_amount;
									
									debitAccountingLedger($ledger_id,$amount); // debit bank account
								    creditAccountingCustomer($customer_id,$principal_amount); // credit customer
									creditAccountingLedger($income_ledger,$interest_amount);
									}
							}
						}
						/* accounts stop */
						
						release_rasid();
						return $emi_payment_id;
					}
			}
			else
			{
				$sql="INSERT INTO 
				  fin_loan_emi_payment(payment_amount, payment_mode, payment_date, rasid_no, paid_by, remarks, remainder_date, loan_emi_id, created_by, last_updated_by , date_added, date_modified)
				  VALUES
				  ($amount, $payment_mode , '$payment_date', '$rasid_no', '$paid_by' , '$remarks', '$remainder_date', $id, $admin_id, $admin_id, '$date_added', NOW())";
				  dbQuery($sql);
				  $emi_payment_id=dbInsertId();
				  /* accounts start */
						if(getAccountsStatus())
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							if(strtotime($payment_date)>=strtotime($books_starting_date))
							{
								if($account_settings['mercantile']==2)
								{
									//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									//$interest_amount=$amount-$principal_amount;
									
									debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
								    creditAccountingCustomer($customer_id,$amount); // credit customer
									
								}
								else if($account_settings['mercantile']==0)
								{
									$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									$interest_amount=$amount-$principal_amount;
									
									debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
								    creditAccountingCustomer($customer_id,$principal_amount); // credit customer
									creditAccountingLedger($income_ledger,$interest_amount);
									}
							}
						}
						/* accounts stop */
				    if(is_numeric($ag_id_array[0]))
					{
						$rasid_counter=getRasidCounterForAgencyId($agency_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidCounterForAgency($agency_id);
					}
					else if(is_numeric($ag_id_array[1]))
					{
					$rasid_counter=getRasidCounterForOCID($oc_id);
					if($rasid_counter==$or_rasid_no)
					incrementRasidNoForOCID($oc_id);
					}
					
					release_rasid();
					return $emi_payment_id;
			}
		}
		else if($balance+$amount>0 && $balance<0 && $amount>0 && !checkForDuplicateRasidNo($rasid_no,$loan_id))
		{
			$remainingBalance=$amount+$balance;
			
			$emisArray=getNextValidEmisWithBalance($id,$remainingBalance);
			 
				 $rasid_identifier=insertMultiplePayment($id,-$balance,$payment_mode,$payment_date,$or_rasid_no,$remarks,$remainder_date,$bank_name,$bank_name,$cheque_no,$cheque_date,$cheque_return,$ledger_id,$date_added,0,false,$paid_by);
				  if($payment_mode==1)
				 $rasid_identifier=dbInsertId();

				if(is_array($emisArray) && count($emisArray)>0)
				{
				foreach($emisArray as $emis)
				{

					 insertMultiplePayment($emis['emi_id'],-$emis['emi_balance'],$payment_mode,$payment_date,$or_rasid_no,$remarks,$remainder_date,$bank_name,$bank_name,$cheque_no,$cheque_date,$cheque_return,$ledger_id,$date_added,$rasid_identifier,true,$paid_by);
					
				}
				
				/* accounts start */
						if($payment_mode==2)
						{
							if(getAccountsStatus())
							{
								$customer_id=getCustomerIdFromLoanId($loan_id);
								if(strtotime($payment_date)>=strtotime($books_starting_date))
								{
									if($account_settings['mercantile']==2)
									{
										//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										//$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$amount); // credit customer
										
									}
									else if($account_settings['mercantile']==0)
									{
										$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$principal_amount); // credit customer
										creditAccountingLedger($income_ledger,$interest_amount);
										}
								}
							}
						}
						else if($payment_mode==1)
						{
							if(getAccountsStatus())
							{
								$customer_id=getCustomerIdFromLoanId($loan_id);
								if(strtotime($payment_date)>=strtotime($books_starting_date))
								{
									if($account_settings['mercantile']==2)
									{
										//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										//$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$amount); // credit customer
										
									}
									else if($account_settings['mercantile']==0)
									{
										$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$principal_amount); // credit customer
										creditAccountingLedger($income_ledger,$interest_amount);
										}
								}
							}
							
						}
						/* accounts stop */
				
				}
				  if(is_numeric($ag_id_array[0]))
					{
						$rasid_counter=getRasidCounterForAgencyId($agency_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidCounterForAgency($agency_id);
					}
					else if(is_numeric($ag_id_array[1]))
					{
					$rasid_counter=getRasidCounterForOCID($oc_id);
					if($rasid_counter==$or_rasid_no)
					incrementRasidNoForOCID($oc_id);
					}
				release_rasid();	
				return $rasid_identifier;
				
		}
		else
		{
			release_rasid();
			return "error";
			
			}	
		}
	catch(Exception $e)
	{}
}

function lock_rasid()
{
	$sql="UPDATE fin_loan_settings SET rasid_lock=1 , lock_time=NOW()";
	dbQuery($sql);
	return true;
	}

function release_rasid()
{
	$sql="UPDATE fin_loan_settings SET rasid_lock=0";
	dbQuery($sql);
	return true;
	}	

function check_rasid()
{
	$sql="SELECT rasid_lock,lock_time FROM fin_loan_settings";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$lock=$resultArray[0][0];
	if($lock['rasid_lock']==0)
	{
		return true;
		}	
	else
	{
		$lock_time=$lock['lock_time'];
		$sql="SELECT NOW()";
		
		$result2=dbQuery($sql);
		$result2Array=dbResultToArray($result2);
		$current_date_time=$result2Array[0][0];
		if(strtotime($current_date_time)-strtotime($lock_time)>60)
		{
		release_rasid();	
		return true;
		}
		else
		return false;
		}	
	}	

function insertManyPayments($id,$amounta,$payment_modea,$payment_datea,$rasid_noa,$remarksa=false,$remainder_datea=false,$bank_namea=false,$brancha=false,$cheque_noa=false,$cheque_datea=false,$cheque_return=0,$ledger_id=0)
{
	try{
		$countArray=array_count_values($rasid_noa);
		
		for($no=0;$no<count($amounta);$no++)
		{
			
		if(checkForNumeric($amounta[$no],$payment_modea[$no],$rasid_noa[$no],$id) && validateForNull($payment_datea[$no],$amounta[$no],$payment_modea[$no],$rasid_noa[$no],$id))
		{
			$amount=$amounta[$no];
			$payment_mode=$payment_modea[$no];
			$rasid_no=$rasid_noa[$no];
			$payment_date=$payment_datea[$no];
			$remarks=$remarksa[$no];
			$remainder_date=$remainder_datea[$no];
			$bank_name=$bank_namea[$no];
			$branch=$brancha[$no];
			$cheque_date=$cheque_datea[$no];
			$cheque_no=$cheque_noa[$no];
			$count=$countArray[$rasid_no];
			if($count>1)
			sleep(1);
		if($remainder_date=="" || $remainder_date==false || $remainder_date==null)
		{
		$remainder_date="1970-01-01";
		}
		else
		{
			$remainder_date = str_replace('/', '-', $remainder_date);
			$remainder_date=date('Y-m-d',strtotime($remainder_date));	
			}
		$remarks=clean_data($remarks);	
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$payment_date = str_replace('/', '-', $payment_date);
		$payment_date=date('Y-m-d',strtotime($payment_date));		
		$ag_id_array=getAgnecyIdFromEmiId($id);
		$loan_id=getLoanIdFromEmiId($id);
		
		$file_id=getFileIdFromLoanId($loan_id);
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		/* Accounts Start */
		if(getAccountsStatus())
			{
				if($agency_company_type=="agency")
				{
					
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForAgency($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForAgency($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForAgency($agency_company_type_id);
				}
				else
				{
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForOC($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForOC($agency_company_type_id);
				}
			}
		/* Accounts Stop */
		
		$id=getOldestUnPaidEmi($loan_id);	
		$balance=getBalanceForEmi($id);
		
		if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_prefix=getPrefixFromOCId($oc_id);
			}
		
			$sql="SELECT NOW()";
			$result= dbQuery($sql);
			$resultArray=dbResultToArray($result);
			$date_added = $resultArray[0][0];
			
		$or_rasid_no=$rasid_no;		
		$rasid_no=$rasid_prefix.$rasid_no;	
		if(checkForDuplicateRasidNo($rasid_no,$loan_id))
		break;
		if(checkForNumeric($id,$amount) && $payment_date!=null && $payment_date!="" && ($balance+$amount<=0) && $amount>0 && $balance<0 && !checkForDuplicateRasidNo($rasid_no,$loan_id))
		{
			
			if($payment_mode==2 && validateForNull($bank_name,$branch,$cheque_no,$cheque_date))
			{
				
			$sql="INSERT INTO 
				  fin_loan_emi_payment(payment_amount, payment_mode, payment_date, rasid_no, remarks, remainder_date, loan_emi_id, created_by, last_updated_by , date_added, date_modified)
				  VALUES
				  ($amount, $payment_mode , '$payment_date', '$rasid_no', '$remarks', '$remainder_date', $id, $admin_id, $admin_id, '$date_added', NOW())";
				  dbQuery($sql);
				  $emi_payment_id=dbInsertId();
				  
				   if(is_numeric($ag_id_array[0]))
					{
						$rasid_counter=getRasidCounterForAgencyId($agency_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidCounterForAgency($agency_id);
					}
					else if(is_numeric($ag_id_array[1]))
					{
					$rasid_counter=getRasidCounterForOCID($oc_id);
					if($rasid_counter==$or_rasid_no)
					incrementRasidNoForOCID($oc_id);
					}
				  
					if(checkForNumeric($cheque_no,$emi_payment_id) && $payment_mode==2 && $bank_name!=false && $bank_name!="" && $bank_name!=null && $branch!=false && $branch!="" && $branch!=null)
					{
						
						$bank_array=insertIfNotDuplicateBank($bank_name,$branch);
						$bank_id=$bank_array[0];
						$branch_id=$bank_array[1];
						insertChequePayment($bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id,$cheque_return,$ledger_id);
						/*acount start */
						if(getAccountsStatus())
							{
								$customer_id=getCustomerIdFromLoanId($loan_id);
								if(strtotime($payment_date)>=strtotime($books_starting_date))
								{
									if($account_settings['mercantile']==2)
									{
										//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										//$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$amount); // credit customer
										
									}
									else if($account_settings['mercantile']==0)
									{
										$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$principal_amount); // credit customer
										creditAccountingLedger($income_ledger,$interest_amount);
										}
								}
							}
						/*acount stop */
						
						
					}
			}
			else if($payment_mode==1)
			{
				
				$sql="INSERT INTO 
				  fin_loan_emi_payment(payment_amount, payment_mode, payment_date, rasid_no, remarks, remainder_date, loan_emi_id, created_by, last_updated_by , date_added, date_modified)
				  VALUES
				  ($amount, $payment_mode , '$payment_date', '$rasid_no', '$remarks', '$remainder_date', $id, $admin_id, $admin_id, '$date_added', NOW())";
				  dbQuery($sql);
				  $emi_payment_id=dbInsertId();
				  
				  /*acount start */
				  if(getAccountsStatus())
							{
								$customer_id=getCustomerIdFromLoanId($loan_id);
								if(strtotime($payment_date)>=strtotime($books_starting_date))
								{
									if($account_settings['mercantile']==2)
									{
										//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										//$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$amount); // credit customer
										
									}
									else if($account_settings['mercantile']==0)
									{
										$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$principal_amount); // credit customer
										creditAccountingLedger($income_ledger,$interest_amount);
										}
								}
							}
					/*acount stop */		
				    if(is_numeric($ag_id_array[0]))
					{
						$rasid_counter=getRasidCounterForAgencyId($agency_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidCounterForAgency($agency_id);
					}
					else if(is_numeric($ag_id_array[1]))
					{
					$rasid_counter=getRasidCounterForOCID($oc_id);
					if($rasid_counter==$or_rasid_no)
					incrementRasidNoForOCID($oc_id);
					}
					
			}
		}
		else if($balance+$amount>0 && $balance<0 && $amount>0 && !checkForDuplicateRasidNo($rasid_no,$loan_id))
		{
			$remainingBalance=$amount+$balance;
			
			$emisArray=getNextValidEmisWithBalance($id,$remainingBalance);
				 
				 $rasid_identifier=insertMultiplePayment($id,-$balance,$payment_mode,$payment_date,$or_rasid_no,$remarks,$remainder_date,$bank_name,$bank_name,$cheque_no,$cheque_date,$cheque_return,$ledger_id,$date_added,0);
				  if($payment_mode==1)
				 $rasid_identifier=dbInsertId();
				
				if(is_array($emisArray) && count($emisArray)>0)
				{
				foreach($emisArray as $emis)
				{
					 insertMultiplePayment($emis['emi_id'],-$emis['emi_balance'],$payment_mode,$payment_date,$or_rasid_no,$remarks,$remainder_date,$bank_name,$bank_name,$cheque_no,$cheque_date,$cheque_return,$ledger_id,$date_added,$rasid_identifier,true);
					
				}
				/* accounts start */
						if($payment_mode==2)
						{
							if(getAccountsStatus())
							{
								$customer_id=getCustomerIdFromLoanId($loan_id);
								if(strtotime($payment_date)>=strtotime($books_starting_date))
								{
									if($account_settings['mercantile']==2)
									{
										//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										//$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$amount); // credit customer
										
									}
									else if($account_settings['mercantile']==0)
									{
										$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$principal_amount); // credit customer
										creditAccountingLedger($income_ledger,$interest_amount);
										}
								}
							}
						}
						else if($payment_mode==1)
						{
							if(getAccountsStatus())
							{
								$customer_id=getCustomerIdFromLoanId($loan_id);
								if(strtotime($payment_date)>=strtotime($books_starting_date))
								{
									if($account_settings['mercantile']==2)
									{
										//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										//$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$amount); // credit customer
										
									}
									else if($account_settings['mercantile']==0)
									{
										$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$principal_amount); // credit customer
										creditAccountingLedger($income_ledger,$interest_amount);
										}
								}
							}
							
						}
						/* accounts stop */
				
				}
				  if(is_numeric($ag_id_array[0]))
					{
						$rasid_counter=getRasidCounterForAgencyId($agency_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidCounterForAgency($agency_id);
					}
					else if(is_numeric($ag_id_array[1]))
					{
					$rasid_counter=getRasidCounterForOCID($oc_id);
					if($rasid_counter==$or_rasid_no)
					incrementRasidNoForOCID($oc_id);
					}
		}
		else
		{
			
			break;
			}
		}
	}
	
return "success";	
		}
	catch(Exception $e)
	{}
}

function insertMultiplePayment($id,$amount,$payment_mode,$payment_date,$rasid_no,$remarks=false,$remainder_date=false,$bank_name=false,$branch=false,$cheque_no=false,$cheque_date=false,$cheque_return=0,$ledger_id=0,$date_added=false,$rasid_identifier=0,$skip_rasid_check=false,$paid_by="NA")
{
	try{
		
		if($remainder_date=="" || $remainder_date==false || $remainder_date==null)
		{
		$remainder_date="1970-01-01";
		}
		else
		{
			$remainder_date = str_replace('/', '-', $remainder_date);
			$remainder_date=date('Y-m-d',strtotime($remainder_date));	
		}
		if(!validateForNull($date_added) || $date_added==false)
		{
			$sql="SELECT NOW()";
			$result= dbQuery($sql);
			$resultArray=dbResultToArray($result);
			$date_added = $resultArray[0][0];
			}
			
		$loan_id=getLoanIdFromEmiId($id);
		$admin_id=$_SESSION['adminSession']['admin_id'];
		
		$balance=getBalanceForEmi($id);
		
		$ag_id_array=getAgnecyIdFromEmiId($id);
		$remarks=clean_data($remarks);	
		
		$payment_date = str_replace('/', '-', $payment_date);
		$payment_date=date('Y-m-d',strtotime($payment_date));	
		if($rasid_no==null || $rasid_no=="")
		{
			$rasid_no=-1;
		}
		if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_prefix=getPrefixFromOCId($oc_id);
			}
		$or_rasid_no=$rasid_no;	
		$rasid_no=$rasid_prefix.$rasid_no;
		
		if(checkForNumeric($id,$amount) && $payment_date!=null && $payment_date!=""   && ($balance+$amount<=0) && $amount>0 && $balance<0 && !checkForDuplicateRasidNo($rasid_no,$loan_id,$skip_rasid_check))
		{
			
			if($payment_mode==2)
			{
				
			$sql="INSERT INTO 
				  fin_loan_emi_payment(payment_amount, payment_mode, payment_date, rasid_no, rasid_identifier, paid_by,  remarks, remainder_date, loan_emi_id, created_by, last_updated_by , date_added, date_modified)
				  VALUES
				  ($amount, $payment_mode , '$payment_date', '$rasid_no', $rasid_identifier, '$paid_by' , '$remarks', '$remainder_date', $id, $admin_id, $admin_id, '$date_added', NOW())";
				
				  
				  dbQuery($sql);
				 
				  $emi_payment_id=dbInsertId();
				  
				 
				  
					if(checkForNumeric($cheque_no,$emi_payment_id) && $payment_mode==2 && $bank_name!=false && $bank_name!="" && $bank_name!=null && $branch!=false && $branch!="" && $branch!=null)
					{
						
						$bank_array=insertIfNotDuplicateBank($bank_name,$branch);
						$bank_id=$bank_array[0];
						$branch_id=$bank_array[1];
						
						insertChequePayment($bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id,$cheque_return,$ledger_id);
							
					}
					
					return $emi_payment_id;
			}
			else
			{
				$sql="INSERT INTO 
				  fin_loan_emi_payment(payment_amount, payment_mode, payment_date, rasid_no, rasid_identifier, paid_by , remarks, remainder_date, loan_emi_id, created_by, last_updated_by , date_added, date_modified)
				  VALUES
				  ($amount, $payment_mode , '$payment_date', '$rasid_no', $rasid_identifier , '$paid_by' ,'$remarks', '$remainder_date', $id, $admin_id, $admin_id, '$date_added', NOW())";
				 
				  dbQuery($sql);
				  $emi_payment_id=dbInsertId();
				  
					
			}
		}
		else if($balance+$amount>0 && $amount>0 && $balance<0 && !checkForDuplicateRasidNo($rasid_no,$loan_id))
		{
			
			$remainingBalance=$amount+$balance;
			
			$emisArray=getNextValidEmisWithBalance($id,$remainingBalance);
				 
				 $rasid_identifier=insertMultiplePayment($id,-$balance,$payment_mode,$payment_date,$or_rasid_no,$remarks,$remainder_date,$bank_name,$bank_name,$cheque_no,$cheque_date,$cheque_return,$ledger_id,$date_added,0,false,$paid_by);
				 if($payment_mode==1)
				 $rasid_identifier=dbInsertId();
				
				if(is_array($emisArray) && count($emisArray)>0)
				{
				foreach($emisArray as $emis)
				{
					
					 insertMultiplePayment($emis['emi_id'],-$emis['emi_balance'],$payment_mode,$payment_date,$or_rasid_no,$remarks,$remainder_date,$bank_name,$bank_name,$cheque_no,$cheque_date,$cheque_return,$ledger_id,$date_added,$rasid_identifier,true,$paid_by);
					
				}
				
				}
				
		}
		else
		{
			
			return "error";
			
			}	
		}
	catch(Exception $e)
	{}
}

function checkForDuplicateRasidNo($rasid_no,$loan_id,$skipRasidCheck=false,$old_rasid_no=false,$reset_date=false)
{
	return false;
	if($rasid_no==$old_rasid_no || $skipRasidCheck)
	{
		return false;
	}
	else
	{
	$reset_date=getRasidResetDateAgnecy();
	$OC_id=$_SESSION['adminSession']['oc_id'];	
	$sql = "SELECT emi_payment_id FROM fin_loan_emi_payment,fin_file,fin_loan,fin_loan_emi WHERE
	        fin_file.file_id=fin_loan.file_id
			AND fin_loan.loan_id=fin_loan_emi.loan_id
			AND fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
	 		AND our_company_id=$OC_id AND rasid_no='$rasid_no' ";
	if(isset($reset_date) && validateForNull($reset_date) && $reset_date!=false)
	$sql=$sql."AND payment_date>='$reset_date'";
	
	$sql =$sql." UNION SELECT penalty_id FROM fin_loan_penalty,fin_file,fin_loan WHERE
	        fin_file.file_id=fin_loan.file_id
			AND fin_loan.loan_id=fin_loan_penalty.loan_id
	 		AND our_company_id=$OC_id AND rasid_no='$rasid_no' ";
	if(isset($reset_date) && validateForNull($reset_date) && $reset_date!=false)
	$sql=$sql."AND paid_date>='$reset_date'";
	
	$sql =$sql." UNION SELECT file_closed_id FROM fin_file_closed,fin_file,fin_loan WHERE
	        fin_file.file_id=fin_loan.file_id
			AND fin_loan.loan_id=fin_file_closed.loan_id
	 		AND our_company_id=$OC_id AND rasid_no='$rasid_no' ";
	if(isset($reset_date) && validateForNull($reset_date) && $reset_date!=false)
	$sql=$sql."AND file_close_date>='$reset_date'";
	
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	return true;
	else return false;
	}
	
}


	
function insertChequePayment($bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id,$cheque_return=0,$ledger_id=0){
	
	try{
		if(checkForNumeric($bank_id,$branch_id,$cheque_no,$emi_payment_id,$ledger_id) && $cheque_date!=null && $cheque_date!="")
		{
		$cheque_date = str_replace('/', '-', $cheque_date);
			$cheque_date=date('Y-m-d',strtotime($cheque_date));			
		$sql="INSERT INTO fin_loan_emi_payment_cheque
		      (bank_id, branch_id, cheque_no, cheque_date,cheque_return, emi_payment_id, ledger_id)
			  VALUES
			  ($bank_id, $branch_id, '$cheque_no','$cheque_date',$cheque_return, $emi_payment_id, $ledger_id)";  
		dbQuery($sql);	
		
		}
		}
	catch(Exception $e)
	{}
	
	
}	


function checkForDuplicateChequePayment($cheque_no,$id=false)
{
	$sql="SELECT payment_cheque_id
		  FROM fin_loan_emi_payment_cheque
		  WHERE cheque_no='$cheque_no'";
	if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND payment_cheque_id!=$id";		  
		$result=dbQuery($sql);	
		
		if(dbNumRows($result)>0)
		{
			return true; //duplicate found
			} 
		else
		{
			return false;
			}	 	  
	
	}	

function checkForDuplicateChequePaymentWithEmiPaymentId($cheque_no,$id=false)
{
	$sql="SELECT payment_cheque_id
		  FROM fin_loan_emi_payment_cheque
		  WHERE cheque_no='$cheque_no'";
	if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND emi_payment_id!=$id";		  
		$result=dbQuery($sql);	
		
		if(dbNumRows($result)>0)
		{
			return true; //duplicate found
			} 
		else
		{
			return false;
			}	 	  
	
	}	



function getPaymentDetailsForEmiPaymentId($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT emi_payment_id, payment_amount, payment_mode, payment_date, rasid_no, paid_by, remarks, remainder_date
		      FROM fin_loan_emi_payment
			  WHERE emi_payment_id=$id ORDER BY payment_date";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else
		return false;
		}
	}

	
function deletePayment($id)
{

	if(checkForNumeric($id))
	{
	$payment=getPaymentDetailsForEmiPaymentId($id);
	$payment_date=$payment['payment_date'];
	$amount=getTotalAmountForPaymentId($id);
	$loan_emi_id=getEMIIDFromPaymentId($id);
	$loan_id=getLoanIdFromEmiId($loan_emi_id);
	
		$file_id=getFileIdFromLoanId($loan_id);
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		/* Accounts Start */
		if(getAccountsStatus())
			{
				if($agency_company_type=="agency")
				{
					
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForAgency($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForAgency($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForAgency($agency_company_type_id);
				}
				else
				{
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForOC($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForOC($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForOC($agency_company_type_id);
				}
			}
		/* Accounts Stop */
	
	if($payment['payment_mode']==2)
	{
		$cheque_details=getChequePaymentDetailsFromEMiPaymentId($id);		
		$ledger_id=$cheque_details['ledger_id'];
		/* accounts start */
						if(getAccountsStatus())
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							if(strtotime($payment_date)>=strtotime($books_starting_date))
							{
								if($account_settings['mercantile']==2)
								{
									//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									//$interest_amount=$amount-$principal_amount;
									
									creditAccountingLedger($ledger_id,$amount); // debit bank account
								    debitAccountingCustomer($customer_id,$amount); // credit customer
									
								}
								else if($account_settings['mercantile']==0)
								{
									$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
									$interest_amount=$amount-$principal_amount;
									creditAccountingLedger($ledger_id,$amount); // debit bank account
								    debitAccountingCustomer($customer_id,$principal_amount); // credit customer
									debitAccountingLedger($income_ledger,$interest_amount);
									}
							}
						}
						/* accounts stop */
		
		if($cheque_details['cheque_return']==1)
		{
		$sql="SELECT file_id FROM fin_loan,fin_loan_emi WHERE fin_loan.loan_id=fin_loan_emi.loan_id AND loan_emi_id=$loan_emi_id";
		$res=dbQuery($sql);
		$resArray=dbResultToArray($res);
		$file_id=$resArray[0][0];
		$cheque_amount=getTotalAmountForPaymentId($id);
		insertChequeReturn($cheque_details['bank_id'],$cheque_details['branch_id'],$cheque_details['cheque_no'],$cheque_details['cheque_date'],$file_id,$loan_emi_id,$cheque_amount,$id,$cheque_details['payment_cheque_id']);
		}
	}
	$rasid_identifier=getRasidIdentifierForPaymentId($id);	
	$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
	$sql="DELETE FROM fin_loan_emi_payment WHERE ";
	if($rasid_identifier==0)
	$sql=$sql." emi_payment_id=$id OR rasid_identifier=$id";
	else if($rasid_identifier!=0)
	$sql=$sql." emi_payment_id=$rasid_identifier OR rasid_identifier=$rasid_identifier";
	dbQuery($sql);
	
	
	if($payment['payment_mode']==1)
	{
	/* accounts start */
						if(getAccountsStatus())
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							if(strtotime($payment_date)>=strtotime($books_starting_date))
							{
								if($account_settings['mercantile']==2)
								{
									//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									//$interest_amount=$amount-$principal_amount;
									
									creditAccountingLedger($cash_ledger_id,$amount); // debit bank account
								    debitAccountingCustomer($customer_id,$amount); // credit customer
									
								}
								else if($account_settings['mercantile']==0)
								{
									
									
									$interest_amount=$amount-$principal_amount;
									
									creditAccountingLedger($cash_ledger_id,$amount); // debit bank account
								    debitAccountingCustomer($customer_id,$principal_amount); // credit customer
									debitAccountingLedger($income_ledger,$interest_amount);
									}
							}
						}
						/* accounts stop */
	
	}
	return "success";
	}
	else
	{return "error";}
}	

function getRasidNoForPayment($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT rasid_no FROM fin_loan_emi_payment WHERE emi_payment_id=$id";
		$result=dbQuery($sql);	  
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		
		
		return $resultArray[0][0];
		}
	}
}		
function editPayment($id,$emi_id,$amount,$payment_mode,$payment_date,$rasid_no,$remarks=false,$remainder_date=false,$bank_name=false,$branch=false,$cheque_no=false,$cheque_date=false,$cheque_return=0,$ledger_id=0,$paid_by="NA")
{
	
	
	if(checkForNumeric($id))
	{
		if($remainder_date=="" || $remainder_date==false || $remainder_date==null)
		{
		$remainder_date="1970-01-01";
		}
		else
		{
			$remainder_date = str_replace('/', '-', $remainder_date);
			$remainder_date=date('Y-m-d',strtotime($remainder_date));	
			}
		$remarks=clean_data($remarks);		
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$payment_date = str_replace('/', '-', $payment_date);
		$payment_date=date('Y-m-d',strtotime($payment_date));	
		$balance=getBalanceForEmi($emi_id);
		$old_payment_details=getPaymentDetailsForEmiPaymentId($id);
		$old_payment_mode=$old_payment_details['payment_mode'];
		if($old_payment_mode==2)
		{
			$old_cheque_details=getChequeDetailsFromEmiPaymentId($id);
			$old_ledger_id=$old_cheque_details['ledger_id'];
			}
		$loan_id=getLoanIdFromEmiId($emi_id);
		$file_id=getFileIdFromLoanId($loan_id);
		$old_rasid_no=getRasidNoForPayment($id);
		$balance=$balance-getAmountForPaymentId($id);
		$ag_id_array=getAgnecyIdFromEmiId($emi_id);
		if($rasid_no==null || $rasid_no=="")
		{
			$rasid_no=-1;
		}
		if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_prefix=getPrefixFromOCId($oc_id);
			}
		$or_rasid_no=$rasid_no;	
		$rasid_no=$rasid_prefix.$rasid_no;	
		
		if($rasid_no==null || $rasid_no=="")
		{
			$rasid_no=-1;
		}
		
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		/* Accounts Start */
		if(getAccountsStatus())
			{
				if($agency_company_type=="agency")
				{
					
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForAgency($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForAgency($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForAgency($agency_company_type_id);
				}
				else
				{
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForOC($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForOC($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForOC($agency_company_type_id);
				}
			}
		/* Accounts Stop */
		
		
		if(checkForNumeric($id,$amount) && $payment_date!=null && $payment_date!="")
		{
			
			$totalPayment=getTotalAmountForRasidNo($old_rasid_no,$loan_id,$id);
			$totalBalance=getBalanceForLoan(getLoanIdFromEmiPaymentId($id));
			$newBalance=$totalBalance-$totalPayment;
			$difference=$amount+$newBalance;
			
			$old_principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
			$old_interest_amount=$totalPayment-$old_principal_amount;
			
			if($difference<=0)
			{
				
				if($amount!=$totalPayment) // if amount of rasid is changed
				{
					
					$loan_emi_id=getFirstLoanEmiIdFromRasidNo($old_rasid_no,$loan_id,$id);
					deletePayment($id);
					insertMultiplePayment($loan_emi_id,$amount,$payment_mode,$payment_date,$or_rasid_no,$remarks,$remainder_date,$bank_name,$branch,$cheque_no,$cheque_date,$cheque_return,$ledger_id);
					
						/* accounts start */
						if(getAccountsStatus())
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							if(strtotime($payment_date)>=strtotime($books_starting_date))
							{
								 if($payment_mode==1)
								{
									
									if($account_settings['mercantile']==2)
									{
										//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
										//$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$amount); // credit customer
										
									}
									else if($account_settings['mercantile']==0)
									{
										$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
										$interest_amount=$amount-$principal_amount;
										
										debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
										creditAccountingCustomer($customer_id,$principal_amount); // credit customer
										creditAccountingLedger($income_ledger,$interest_amount);
									}
									
									
								}
								else if($payment_mode==2)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													
													debitAccountingLedger($ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$amount); // credit customer
													
												}
												else if($account_settings['mercantile']==0)
												{
													$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
													$interest_amount=$amount-$principal_amount;
													
													debitAccountingLedger($ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$principal_amount); // credit customer
													creditAccountingLedger($income_ledger,$interest_amount);
													}
											}
							}
						}
						/* accounts stop */
					
					return "success";
					
				}
			}
			$rasidIdentifier=getRasidIdentifierForPaymentId($id);		
			if($payment_mode==2) // amount is static
			{
					
			$sql="UPDATE 
				  fin_loan_emi_payment
				  SET payment_mode=$payment_mode, payment_date='$payment_date',rasid_no='$rasid_no', paid_by='$paid_by', remarks='$remarks', remainder_date='$remainder_date', last_updated_by=$admin_id , date_modified=NOW()
				  WHERE ";
				  if($rasidIdentifier==0)
				  $sql=$sql."emi_payment_id=$id OR rasid_identifier=$id";
				  else if($rasidIdentifier!=0)
				  $sql=$sql."emi_payment_id=$rasidIdentifier OR rasid_identifier=$rasidIdentifier";
				  dbQuery($sql);
				  $emi_payment_id=$id;
				  
				if(checkForNumeric($cheque_no,$emi_payment_id) && $payment_mode==2 && $bank_name!=false && $bank_name!="" && $bank_name!=null && $branch!=false && $branch!="" && $branch!=null)
				{
					
					$bank_array=insertIfNotDuplicateBank($bank_name,$branch);
					$bank_id=$bank_array[0];
					$branch_id=$bank_array[1];
					checkChequePayment($bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id,$cheque_return,$ledger_id);
						/* accounts start */
						if(getAccountsStatus())
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							
							if(strtotime($old_payment_details['payment_date'])>=strtotime($books_starting_date))
							{
								if($old_payment_mode==1 && $payment_mode==2)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$totalPayment); // debit customer for the old amount
													creditAccountingLedger($cash_ledger_id,$totalPayment); // credit cash the old amount 
													
												
													
												}
												else if($account_settings['mercantile']==0)
												{
													$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
													$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$old_principal_amount); // debit customer for the old amount
													creditAccountingLedger($cash_ledger_id,$totalPayment); // credit cash the old amount 
													debitAccountingLedger($income_ledger,$old_interest_amount);
													}
											}
											else if($old_payment_mode==2 && $payment_mode==2)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$totalPayment); // debit customer for the old amount
													creditAccountingLedger($old_ledger_id,$totalPayment); // credit cash the old amount 
													
												
													
												}
												else if($account_settings['mercantile']==0)
												{
													$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
													$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$old_principal_amount); // debit customer for the old amount
													creditAccountingLedger($old_ledger_id,$totalPayment); // credit cash the old amount 
													debitAccountingLedger($income_ledger,$old_interest_amount);
													}
											}
							}
							
							if(strtotime($payment_date)>=strtotime($books_starting_date))
							{
								if($old_payment_mode==1 && $payment_mode==2)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													debitAccountingLedger($ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$amount); // credit customer
													
												}
												else if($account_settings['mercantile']==0)
												{
													$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
													$interest_amount=$amount-$principal_amount;
												
													debitAccountingLedger($ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$principal_amount); // credit customer
													creditAccountingLedger($income_ledger,$interest_amount);
													}
											}
											else if($old_payment_mode==2 && $payment_mode==2)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													debitAccountingLedger($ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$amount); // credit customer
													
												}
												else if($account_settings['mercantile']==0)
												{
													$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
													$interest_amount=$amount-$principal_amount;
												
													debitAccountingLedger($ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$principal_amount); // credit customer
													creditAccountingLedger($income_ledger,$interest_amount);
													}
											}
							}
						}
						/* accounts stop */		
					
				}
			}
			else
			{
				
				$sql="UPDATE 
				  fin_loan_emi_payment
				  SET payment_mode=$payment_mode, payment_date='$payment_date', rasid_no='$rasid_no', paid_by='$paid_by', remarks='$remarks', remainder_date='$remainder_date', last_updated_by=$admin_id , date_modified=NOW()
				  WHERE  ";
				  if($rasidIdentifier==0)
				  $sql=$sql."emi_payment_id=$id OR rasid_identifier=$id";
				  else if($rasidIdentifier!=0)
				  $sql=$sql."emi_payment_id=$rasidIdentifier OR rasid_identifier=$rasidIdentifier";
				  dbQuery($sql);
				  
				  
				  
				  /* accounts start */
						if(getAccountsStatus())
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							
							if(strtotime($old_payment_details['payment_date'])>=strtotime($books_starting_date))
							{
								 if($old_payment_mode==1 && $payment_mode==1)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$totalPayment); // debit customer for the old amount
													creditAccountingLedger($cash_ledger_id,$totalPayment); // credit cash the old amount 
													
												}
												else if($account_settings['mercantile']==0)
												{
													
													debitAccountingCustomer($customer_id,$old_principal_amount); // debit customer for the old amount
													creditAccountingLedger($cash_ledger_id,$totalPayment); // credit cash the old amount 
													debitAccountingLedger($income_ledger,$old_interest_amount);
													
													}
											}
											else if($old_payment_mode==2 && $payment_mode==1)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$totalPayment); // debit customer for the old amount
													creditAccountingLedger($old_ledger_id,$totalPayment); // credit cash the old amount 
													
												
												}
												else if($account_settings['mercantile']==0)
												{
												
													debitAccountingCustomer($customer_id,$old_principal_amount); // debit customer for the old amount
													creditAccountingLedger($old_ledger_id,$totalPayment); // credit cash the old amount 
													debitAccountingLedger($income_ledger,$old_interest_amount);
													
												}
											}
											
							}
							
							if(strtotime($payment_date)>=strtotime($books_starting_date))
							{
								 if($old_payment_mode==1 && $payment_mode==1)
											{
												if($account_settings['mercantile']==2)
												{
												
													debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$amount); // credit customer
													
												}
												else if($account_settings['mercantile']==0)
												{
													$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
													$interest_amount=$amount-$principal_amount;
												
													debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$principal_amount); // credit customer
													creditAccountingLedger($income_ledger,$interest_amount);
													}
											}
											else if($old_payment_mode==2 && $payment_mode==1)
											{
												if($account_settings['mercantile']==2)
												{
												
													debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$amount); // credit customer
													
												}
												else if($account_settings['mercantile']==0)
												{
													$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($id);
													$interest_amount=$amount-$principal_amount;	
													debitAccountingLedger($cash_ledger_id,$amount); // debit bank account
													creditAccountingCustomer($customer_id,$principal_amount); // credit customer
													creditAccountingLedger($income_ledger,$interest_amount);
													}
											}
											
							}
						}
						/* accounts stop */	
				  
				  deleteChequePaymentByPaymentId($id);  
				   
			}
			
			
			return "success";
			
		}
		else
		{
			
			return "error";
			
			}	
	}
	
}	


function getFirstLoanEmiIdFromRasidNo($rasid_no,$loan_id,$emi_payment_id)
{
	if(validateForNull($rasid_no,$loan_id,$emi_payment_id))
	{
		$rasid_identifier=getRasidIdentifierForPaymentId($emi_payment_id);
		$sql="SELECT min(fin_loan_emi.loan_emi_id) FROM fin_loan_emi_payment,fin_loan_emi WHERE fin_loan_emi_payment.loan_emi_id=fin_loan_emi.loan_emi_id AND loan_id=$loan_id AND rasid_no='$rasid_no'";
		if($rasid_identifier==0)
		$sql=$sql." AND (rasid_identifier=$emi_payment_id OR emi_payment_id=$emi_payment_id)";
		else if($rasid_identifier!=0)
		$sql=$sql." AND (rasid_identifier=$rasid_identifier OR emi_payment_id=$rasid_identifier)";
		$sql=$sql." GROUP BY rasid_no";
		
		$result=dbQuery($sql);
		if(dbNumRows($result))
		{
			$resultArray=dbResultToArray($result);
			return $resultArray[0][0];
			}
		}
	}	
	
function checkChequePayment($bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id,$cheque_return=0,$ledger_id=0)	
{
	
	$cheque_date = str_replace('/', '-', $cheque_date);
		$cheque_date=date('Y-m-d',strtotime($cheque_date));
	$sql="SELECT payment_cheque_id FROM fin_loan_emi_payment_cheque WHERE emi_payment_id=$emi_payment_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	
	if(dbNumRows($result)>0)
	{
		$id=$resultArray[0][0];
		updateChequePayment($id,$bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id,$cheque_return,$ledger_id);	
		}
	else
	{
		
		insertChequePayment($bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id,0,$ledger_id);
		}	
}



function updateChequePayment($id,$bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id,$cheque_return,$ledger_id=0)	
{
	try{
		$cheque_date = str_replace('/', '-', $cheque_date);
		$cheque_date=date('Y-m-d',strtotime($cheque_date));
		if(checkForNumeric($id,$bank_id,$branch_id,$cheque_no,$emi_payment_id,$ledger_id) && $cheque_date!=null && $cheque_date!="")
		{
		
		$sql="UPDATE fin_loan_emi_payment_cheque
		      SET bank_id = $bank_id, branch_id = $branch_id, cheque_no = '$cheque_no', cheque_date = '$cheque_date', cheque_return=$cheque_return , ledger_id=$ledger_id
			 WHERE payment_cheque_id=$id";
		dbQuery($sql);	  
		}
		}
	catch(Exception $e)
	{}
	
	
	
	}	
	
function deleteChequePaymentByPaymentId($id)	
{
	try{
		
		if(checkForNumeric($id))
		{
		
		$sql="DELETE FROM fin_loan_emi_payment_cheque
			 WHERE emi_payment_id=$id";	 
		dbQuery($sql);	 
		
		}
		}
	catch(Exception $e)
	{}
	
	
	
	}	

function getEMIIDFromPaymentId($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT loan_emi_id FROM fin_loan_emi_payment WHERE emi_payment_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0][0];
			}
		}	
}	

function getAmountForPaymentId($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT payment_amount FROM fin_loan_emi_payment WHERE emi_payment_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0][0];
			}
		}
	}	

function getTotalAmountForPaymentId($id)
{
	if(checkForNumeric($id))
	{
		$rasid_identifier=getRasidIdentifierForPaymentId($id);
		$sql="SELECT SUM(payment_amount) FROM fin_loan_emi_payment WHERE ";
		if($rasid_identifier==0)
		$sql=$sql."emi_payment_id=$id OR rasid_identifier=$id";
		else if($rasid_identifier!=0)
		$sql=$sql."emi_payment_id=$rasid_identifier OR rasid_identifier=$rasid_identifier";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0][0];
			}
		}
	}		

function getChequePaymentDetailsFromEMiPaymentId($emi_payment_id)
{
		$sql="SELECT payment_cheque_id,bank_id,branch_id,cheque_no,cheque_date,cheque_return, ledger_id FROM fin_loan_emi_payment_cheque WHERE emi_payment_id=$emi_payment_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0];
		
	}
	else
	{
		return "error";
		}
	
	}	

function insertChequeReturn($bank_id,$branh_id,$cheque_no,$cheque_date,$file_id,$loan_emi_id,$amount,$payment_id,$cheque_id)
{
	if(checkForNumeric($bank_id,$branh_id,$cheque_no,$file_id,$loan_emi_id,$amount) && validateForNull($cheque_date))
	{	
	
	$admin_id=$_SESSION['adminSession']['admin_id'];	
	$sql="INSERT INTO fin_loan_emi_payment_cheque_return (bank_id,branch_id,cheque_no,cheque_date,file_id,loan_emi_id,date_added,created_by,cheque_amount)
		  VALUES($bank_id,$branh_id,'$cheque_no','$cheque_date',$file_id,$loan_emi_id,NOW(),$admin_id,$amount)";
	dbQuery($sql);
	return "success";	  
	}
	return "error";
}

function getPenaltyDaysForPaymentId($id)
{
	if(checkForNumeric($id))
	{}
	else
	{
		if(is_string($id))
		{
		$paymentIdsString=$id;	
		$paymentIdsArray=explode(",",$id);
		$id=$paymentIdsArray[0];
		}
	}
	$daylen = 60*60*24;
	
		  $sql="SELECT actual_emi_date,fin_loan_emi.loan_emi_id
	      FROM fin_loan_emi,fin_loan_emi_payment
		  WHERE emi_payment_id=$id
		  AND fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id";
	$result=dbQuery($sql);	  
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		
		$actual_date=$resultArray[0][0];
		$loan_emi_id=$resultArray[0][1];
		
		$emi=getEmiForLoanEmiId($loan_emi_id);
		
	
	if(isset($paymentIdsArray) && count($paymentIdsArray)>1)
	{
		$sql="SELECT payment_date,SUM(payment_amount)
	      FROM fin_loan_emi_payment
		  WHERE emi_payment_id IN ($paymentIdsString)
		  GROUP BY payment_date";
	}
	else
	{
	$sql="SELECT payment_date,payment_amount
	      FROM fin_loan_emi_payment
		  WHERE emi_payment_id=$id";
	}
	$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		$returnArray=array();
	  	if(dbNumRows($result)>0)
		{
		$last_payment_date = $resultArray[0][0];
		$payment_amount = $resultArray[0][1];
		$payment_array=getPaymentUptoDateForEmiId($id,$loan_emi_id,$last_payment_date);
		if(isset($payment_array) && count($payment_array)>0)
		{
		$payment=$payment_array[0];
		$payment_date=$payment_array[1];
		}
		else
		{
			$payment=0;
			$payment_date=false;
			}
		$penalty_amount=$emi-$payment;
		if(isset($payment_date) && $payment_date!="" && $payment_date!=null && $payment_date!=false)
		$penalty_days=(strtotime($last_payment_date)-strtotime($payment_date))/$daylen;
		else
		$penalty_days=(strtotime($last_payment_date)-strtotime($actual_date))/$daylen;	
		if($penalty_days>0)
		{
		$returnArray['days']=$penalty_days;
		$returnArray['amount']=$penalty_amount;
		$returnArray['actual_date']=$actual_date;
		$returnArray['payment_date']=$last_payment_date;
		$returnArray['emi']=$emi;
		$returnArray['payment_amount']=$payment_amount;
		}
		else
		{
		$returnArray['days']=0;
		$returnArray['amount']=0;
		$returnArray['actual_date']=$actual_date;
		$returnArray['payment_date']=$last_payment_date;
		$returnArray['emi']=$emi;
		$returnArray['payment_amount']=$payment_amount;
			}
		return $returnArray;
		}
		else
		return 0;	
	}
	}


	
function getLoanIdFromEmiPaymentId($emi_payment_id)
{
	
	if(checkForNumeric($emi_payment_id))
	{
	$sql="SELECT loan_id from fin_loan_emi,fin_loan_emi_payment WHERE emi_payment_id=$emi_payment_id AND fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id";
	$result=dbQuery($sql);
	$returnArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		
		return $returnArray[0][0];
	}
	return false;
	}
	}	


function getPaymentsForRasidno($rasid_no,$loan_id,$emi_payment_id=false)
{
	if(validateForNull($rasid_no,$loan_id))
	{
	$or_rasid_identifier=0;	
	
	
	if(checkForNumeric($emi_payment_id))
	{
		$rasid_identifier=getRasidIdentifierForPaymentId($emi_payment_id);
		if(validateForNull($rasid_identifier) && $rasid_identifier!=0)
		$or_rasid_identifier=$rasid_identifier;
		}	
		
	$sql="SELECT emi_payment_id,payment_amount,fin_loan_emi.loan_emi_id,actual_emi_date
	      FROM fin_loan_emi,fin_loan_emi_payment
		  WHERE rasid_no= '$rasid_no'
		  AND fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
		  AND loan_id=$loan_id";
	if($emi_payment_id!=false && is_numeric($emi_payment_id) && $or_rasid_identifier==0)
	$sql=$sql." AND emi_payment_id!=$emi_payment_id
	AND rasid_identifier=$emi_payment_id";
	if($emi_payment_id!=false && is_numeric($emi_payment_id) && $or_rasid_identifier!=0)
	$sql=$sql." AND emi_payment_id!=$emi_payment_id
	AND (rasid_identifier=$or_rasid_identifier OR emi_payment_id=$or_rasid_identifier)";

	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$returnArray=array();
	$total=0;
	if(dbNumRows($result)>0)
	{
		foreach($resultArray as $re)
		{
			$total=$total+$re['payment_amount'];
			
			}
		$returnArray['total_paid']=$total;	
		$returnArray['payment_details']=$resultArray;
		return $returnArray;
		}
	else return false;	
	
	}
}
function getAllPaymentsForRasidno($rasid_no,$loan_id,$emi_payment_id)
{
	if(validateForNull($rasid_no,$loan_id))
	{
	$or_rasid_identifier=0;	
	if(checkForNumeric($emi_payment_id))
	{
		$rasid_identifier=getRasidIdentifierForPaymentId($emi_payment_id);
		if(validateForNull($rasid_identifier) && $rasid_identifier!=0)
		$or_rasid_identifier=$rasid_identifier;
		}	
	$sql="SELECT emi_payment_id,payment_amount,fin_loan_emi.loan_emi_id,actual_emi_date,emi_amount
	      FROM fin_loan_emi,fin_loan_emi_payment
		  WHERE rasid_no= '$rasid_no'
		  AND fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
		  AND loan_id=$loan_id";
	if($or_rasid_identifier==0)
	$sql=$sql." AND (emi_payment_id=$emi_payment_id
	OR rasid_identifier=$emi_payment_id)";
	if($or_rasid_identifier!=0)
	$sql=$sql." AND (emi_payment_id=$or_rasid_identifier OR
	rasid_identifier=$or_rasid_identifier)";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$returnArray=array();
	$total=0;
	if(dbNumRows($result)>0)
	{
		foreach($resultArray as $re)
		{
			$total=$total+$re['payment_amount'];
			}
		$returnArray['total_paid']=$total;	
		$returnArray['payment_details']=$resultArray;
		return $returnArray;
		}
	else return false;	
	
	}
}

function getAllPaymentsForPaymentId($emi_payment_id)
{
	
	$or_rasid_identifier=0;	
	if(checkForNumeric($emi_payment_id))
	{
		$loan_id=getLoanIdFromEmiPaymentId($emi_payment_id);
		$rasid_identifier=getRasidIdentifierForPaymentId($emi_payment_id);
		if(validateForNull($rasid_identifier) && $rasid_identifier!=0)
		$or_rasid_identifier=$rasid_identifier;
		}	
	$sql="SELECT emi_payment_id,payment_amount,fin_loan_emi.loan_emi_id,actual_emi_date,emi_amount
	      FROM fin_loan_emi,fin_loan_emi_payment
		  WHERE fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
		  AND loan_id=$loan_id";
	if($or_rasid_identifier==0)
	$sql=$sql." AND (emi_payment_id=$emi_payment_id
	OR rasid_identifier=$emi_payment_id)";
	if($or_rasid_identifier!=0)
	$sql=$sql." AND (emi_payment_id=$or_rasid_identifier OR
	rasid_identifier=$or_rasid_identifier)";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$returnArray=array();
	$total=0;
	if(dbNumRows($result)>0)
	{
		foreach($resultArray as $re)
		{
			$total=$total+$re['payment_amount'];
			}
		$returnArray['total_paid']=$total;	
		$returnArray['payment_details']=$resultArray;
		return $returnArray;
		}
	else return false;	
	
	
}
function getRasidIdentifierForPaymentId($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT rasid_identifier FROM fin_loan_emi_payment WHERE emi_payment_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
		}
	
	}
	

function getTotalAmountForRasidNo($rasid_no,$loan_id,$emi_payment_id)
{
	if(validateForNull($rasid_no))
	{
	$rasid_identifier=getRasidIdentifierForPaymentId($emi_payment_id);	
	$sql="SELECT SUM(payment_amount)
	      FROM fin_loan_emi_payment,fin_loan_emi
		  WHERE rasid_no= '$rasid_no'
		  AND fin_loan_emi_payment.loan_emi_id=fin_loan_emi.loan_emi_id
		  AND loan_id=$loan_id";
	if($rasid_identifier==0)
	$sql=$sql." AND (emi_payment_id=$emi_payment_id OR rasid_identifier=$emi_payment_id)";	 
	else if($rasid_identifier!=0)
	{
	$sql=$sql." AND (emi_payment_id=$rasid_identifier OR rasid_identifier=$rasid_identifier)";	 	
		} 
	$sql=$sql."	GROUP BY rasid_no";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0][0];
	}
	else return false;
	}
}	
	
function addCompanyPaymentDate($emi_id,$date)
{
	
		$date = str_replace('/', '-', $date);
		$date=date('Y-m-d',strtotime($date));	
		
		if(checkForNumeric($emi_id))
		{
			$sql="UPDATE fin_loan_emi SET company_paid_date='$date' WHERE loan_emi_id=$emi_id";
			dbQuery($sql);
			return "success";
			}
		else
		return "error";	
}

function DeleteCompanyPaymentDate($emi_id)
{
		if(checkForNumeric($emi_id))
		{
			$sql="UPDATE fin_loan_emi SET company_paid_date=NULL WHERE loan_emi_id=$emi_id";
			dbQuery($sql);
			return "success";
			}
		else
		return "error";	
}

function getCompanyPaidDateById($emi_id)
{
	if(checkForNumeric($emi_id))
		{
			$sql="SELECT  company_paid_date FROM fin_loan_emi WHERE loan_emi_id=$emi_id";
			$result=dbQuery($sql);
			$resultArray=dbResultToArray($result);
			if(dbNumRows($result)>0)
			return date('d/m/Y',strtotime($resultArray[0][0]));
			}
			
	}

/* penalty functions */
	


function insertChequePaymentPenalty($bank_id,$branch_id,$cheque_no,$cheque_date,$penalty_id,$ledger_id){
	
	try{
		if(checkForNumeric($bank_id,$branch_id,$cheque_no,$penalty_id,$ledger_id) && $cheque_date!=null && $cheque_date!="")
		{
			$cheque_date = str_replace('/', '-', $cheque_date);
			$cheque_date=date('Y-m-d',strtotime($cheque_date));
		$sql="INSERT INTO fin_loan_penalty_cheque
		      (bank_id, branch_id, cheque_no, cheque_date, penalty_id, ledger_id)
			  VALUES
			  ($bank_id, $branch_id, '$cheque_no', '$cheque_date', $penalty_id, $ledger_id)";
		dbQuery($sql);	  
		}
		}
	catch(Exception $e)
	{}
	
	
}	
	


function checkChequePaymentPenalty($bank_id,$branch_id,$cheque_no,$cheque_date,$penalty_id, $ledger_id)	
{
	$cheque_date = str_replace('/', '-', $cheque_date);
	$cheque_date=date('Y-m-d',strtotime($cheque_date));
	$sql="SELECT penalty_cheque_id FROM fin_loan_penalty_cheque WHERE penalty_id=$penalty_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	
	if(dbNumRows($result)>0)
	{
		$id=$resultArray[0][0];
		updateChequePaymentPenalty($id,$bank_id,$branch_id,$cheque_no,$cheque_date,$penalty_id,$ledger_id);	
		}
	else
	{
		
		insertChequePaymentPenalty($bank_id,$branch_id,$cheque_no,$cheque_date,$penalty_id,$ledger_id);
		}	
}

	

function deleteChequePaymentByPaymentIdPenalty($id)	
{
	try{
		
		if(checkForNumeric($id))
		{
		
		$sql="DELETE FROM fin_loan_penalty_cheque
			 WHERE penalty_id=$id";
		dbQuery($sql);	  
		}
		}
	catch(Exception $e)
	{}
	
	
	
	}			
	
function updateChequePaymentPenalty($id,$bank_id,$branch_id,$cheque_no,$cheque_date,$penalty_id,$ledger_id)	
{
	try{
		
		if(checkForNumeric($id,$bank_id,$branch_id,$cheque_no,$penalty_id) && $cheque_date!=null && $cheque_date!="")
		{
		$cheque_date = str_replace('/', '-', $cheque_date);
		$cheque_date=date('Y-m-d',strtotime($cheque_date));
		$sql="UPDATE fin_loan_penalty_cheque
		      SET bank_id = $bank_id, branch_id = $branch_id, cheque_no = '$cheque_no', cheque_date = '$cheque_date', ledger_id=$ledger_id
			 WHERE penalty_cheque_id=$id";
		dbQuery($sql);	  
		}
		}
	catch(Exception $e)
	{}
	
	
	
	}	

function addPenaltyToLoan($days_paid, $paid_date, $payment_mode , $amount_per_day, $or_rasid_no , $paid_by,  $loan_id,$file_id,$total_amount,$rasid_type=1,$paid=1,$bank_name=false,$branch=false,$cheque_no=false,$cheque_date=false,$ledger_id=0,$auto_rasid_no=0)
{
	while(check_rasid()==false)
		{
			sleep(1);
			}	
		lock_rasid();	
	$admin_id=$_SESSION['adminSession']['admin_id'];
	if(checkForNumeric($loan_id,$file_id))
	{
		
		$total_Penalty_days=getTotalPenaltyForLoan($loan_id);
		$total_days_paid=getTotalPenaltyPaidDaysForLoan($loan_id);
		$days_left=$total_Penalty_days-$total_days_paid;
		$ag_id_array=getAgnecyIdFromLoanId($loan_id);
		$paid_date = str_replace('/', '-', $paid_date);
		$paid_date=date('Y-m-d',strtotime($paid_date));
		
		
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		/* Accounts Start */
		if(getAccountsStatus())
			{
				if($agency_company_type=="agency")
				{
					
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForAgency($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForAgency($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForAgency($agency_company_type_id);
				}
				else
				{
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForOC($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForOC($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForOC($agency_company_type_id);
				}
			}
		/* Accounts Stop */
		
		
		
		if($rasid_type==1)
		{
			$total_amount=$amount_per_day*$days_paid;
			}	
		if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$prefix=getAgencyPrefixFromAgencyId($agency_id);
			$current_rasid_counter=getRasidCounterForAgencyId($agency_id);
			
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$prefix=getPrefixFromOCId($oc_id);
			$current_rasid_counter=getRasidCounterForOCID($oc_id);
			
			} 
		if($auto_rasid_no==$or_rasid_no)
		{
			
			$or_rasid_no=$current_rasid_counter;
			}		
		$rasid_no=$prefix.$or_rasid_no;	
	if($days_paid<=$days_left)
	{
	if(checkForNumeric($amount_per_day) && $paid_date!=null && $paid_date!="")
		{
			$sql="INSERT INTO fin_loan_penalty (days_paid, paid_date, payment_mode,  amount_per_day, total_amount, rasid_type_id, paid, rasid_no, paid_by, loan_id, file_id, created_by, last_updated_by, date_added, date_modified)
	      VALUES ($days_paid, '$paid_date', $payment_mode, $amount_per_day, $total_amount, $rasid_type, $paid, '$rasid_no' , '$paid_by', $loan_id, $file_id, $admin_id, $admin_id, NOW(), NOW())";
		  $result=dbQuery($sql);
		  $penalty_id=dbInsertId();
		  
		  /* accounts start */
						if(getAccountsStatus() && $account_settings['include_penalty']==1 && $payment_mode==1)
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							if(strtotime($paid_date)>=strtotime($books_starting_date))
							{
								if($account_settings['mercantile']==2)
								{
									//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									//$interest_amount=$amount-$principal_amount;
									
									debitAccountingLedger($cash_ledger_id,$total_amount); // debit bank account
								    creditAccountingCustomer($customer_id,$total_amount); // credit customer
									
								}
								else if($account_settings['mercantile']==0)
								{
									
									
									debitAccountingLedger($cash_ledger_id,$total_amount); // debit bank account
								    creditAccountingCustomer($customer_id,$total_amount); // credit customer
								
									}
							}
						}
						/* accounts stop */
					
		  
			  if(is_numeric($ag_id_array[0]))
					{
						$rasid_counter=getRasidCounterForAgencyId($agency_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidCounterForAgency($agency_id);
					}
					else if(is_numeric($ag_id_array[1]))
					{
						$rasid_counter=getRasidCounterForOCID($oc_id);
						if($rasid_counter==$or_rasid_no)
						incrementRasidNoForOCID($oc_id);
					} 
			if($payment_mode==2)
			{
				if(checkForNumeric($cheque_no,$penalty_id) && $payment_mode==2 && $bank_name!=false && $bank_name!="" && $bank_name!=null && $branch!=false && $branch!="" && $branch!=null)
				{
					
					$bank_array=insertIfNotDuplicateBank($bank_name,$branch);
					$bank_id=$bank_array[0];
					$branch_id=$bank_array[1];
					insertChequePaymentPenalty($bank_id,$branch_id,$cheque_no,$cheque_date,$penalty_id,$ledger_id);
					
					/* accounts start */
						if(getAccountsStatus() && $account_settings['include_penalty']==1 && $payment_mode==2)
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							if(strtotime($paid_date)>=strtotime($books_starting_date))
							{
								if($account_settings['mercantile']==2)
								{
									//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									//$interest_amount=$amount-$principal_amount;
									
									debitAccountingLedger($ledger_id,$total_amount); // debit bank account
								    creditAccountingCustomer($customer_id,$total_amount); // credit customer
									
								}
								else if($account_settings['mercantile']==0)
								{
									
									
									debitAccountingLedger($ledger_id,$total_amount); // debit bank account
								    creditAccountingCustomer($customer_id,$total_amount); // credit customer
								
									}
							}
						}
						/* accounts stop */
					
					return $penalty_id;
				}
			}
			else
			{
				release_rasid();
				return $penalty_id;
				}
		}
	release_rasid();
	return $penalty_id;
		}
		else
		release_rasid();
		return "error";
	}
	else
	{
		release_rasid();
		return "error";
		}	  
}

function editPenalty($id,$days_paid, $paid_date, $payment_mode, $amount_per_day, $or_rasid_no,$paid_by, $loan_id,$file_id,$total_amount,$rasid_type=1,$paid=1,$bank_name=false,$branch=false,$cheque_no=false,$cheque_date=false,$ledger_id=0)
{
	$admin_id=$_SESSION['adminSession']['admin_id'];
	if(checkForNumeric($loan_id))
	{
	$total_Penalty_days=getTotalPenaltyForLoan($loan_id);
	$total_days_paid=getTotalPenaltyPaidDaysForLoan($loan_id);
	$days_left=$total_Penalty_days-$total_days_paid;
	$penalty=getPenaltyById($id);
	$old_payment_mode=$penalty['payment_mode'];
	$old_amount=$penalty['total_amount'];
	$old_payment_date=$penalty['paid_date'];
	
	
	if($old_payment_mode==2)
	{
		$old_cheque_details=getChequeDetailsPenalty($id);
		$old_ledger_id=$old_cheque_details['ledger_id'];
		}
	$penalty_days=$penalty['days_paid'];
	$days_left=$days_left+$penalty_days;
	$paid_date = str_replace('/', '-', $paid_date);
	$paid_date=date('Y-m-d',strtotime($paid_date));
	$ag_id_array=getAgnecyIdFromLoanId($loan_id);
	if($rasid_type==1)
	{
		$total_amount=$amount_per_day*$days_paid;
		}
		if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$prefix=getAgencyPrefixFromAgencyId($agency_id);
			$rasid_no=$prefix.$or_rasid_no;
			
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$prefix=getPrefixFromOCId($oc_id);
			$rasid_no=$prefix.$or_rasid_no;
			} 
			
	    $agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		/* Accounts Start */
		if(getAccountsStatus())
			{
				if($agency_company_type=="agency")
				{
					
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForAgency($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForAgency($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForAgency($agency_company_type_id);
				}
				else
				{
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForOC($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForOC($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForOC($agency_company_type_id);
				}
			}
		/* Accounts Stop */
		
			
	if(checkForNumeric($id,$days_paid,$amount_per_day) && $days_paid<=$days_left)
	{
		
		$sql="UPDATE fin_loan_penalty 
	     SET days_paid = $days_paid, paid_date = '$paid_date', payment_mode=$payment_mode, amount_per_day = $amount_per_day , total_amount=$total_amount, rasid_type_id=$rasid_type, paid=$paid, rasid_no='$rasid_no',paid_by= '$paid_by', date_modified=NOW(), last_updated_by=$admin_id
		 WHERE penalty_id = $id";

		  $result=dbQuery($sql);
		  
		  
		  /* accounts start */
						if(getAccountsStatus() && $account_settings['include_penalty']==1)
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							
							if(strtotime($old_payment_date)>=strtotime($books_starting_date))
							{
								if($old_payment_mode==1 && $payment_mode==1)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$old_amount); // debit customer for the old amount
													creditAccountingLedger($cash_ledger_id,$old_amount); // credit cash the old amount 
													
													
													
												}
												else if($account_settings['mercantile']==0)
												{
													
													
													debitAccountingCustomer($customer_id,$old_amount); // debit customer for the old amount
													creditAccountingLedger($cash_ledger_id,$old_amount); // credit cash the old amount 
													
													}
											}
											else if($old_payment_mode==2 && $payment_mode==1)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$old_amount); // debit customer for the old amount
													creditAccountingLedger($old_ledger_id,$old_amount); // credit cash the old amount 
													
												
													
												}
												else if($account_settings['mercantile']==0)
												{
													
													debitAccountingCustomer($customer_id,$old_amount); // debit customer for the old amount
													creditAccountingLedger($old_ledger_id,$old_amount); // credit cash the old amount 
													
													}
											}
							}
							
							if(strtotime($paid_date)>=strtotime($books_starting_date))
							{
								if($old_payment_mode==1 && $payment_mode==1)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													debitAccountingLedger($cash_ledger_id,$total_amount); // debit bank account
													creditAccountingCustomer($customer_id,$total_amount); // credit customer
													
												}
												else if($account_settings['mercantile']==0)
												{
													
													debitAccountingLedger($cash_ledger_id,$total_amount); // debit bank account
													creditAccountingCustomer($customer_id,$total_amount); // credit customer
													
													}
											}
											else if($old_payment_mode==2 && $payment_mode==1)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													debitAccountingLedger($ledger_id,$total_amount); // debit bank account
													creditAccountingCustomer($customer_id,$total_amount); // credit customer
													
												}
												else if($account_settings['mercantile']==0)

												{
													debitAccountingLedger($ledger_id,$total_amount); // debit bank account
													creditAccountingCustomer($customer_id,$total_amount); // credit customer
													}
											}
							}
						}
						/* accounts stop */	
		  

		if($payment_mode==2)
			{
				if(checkForNumeric($cheque_no,$id,$ledger_id) && $payment_mode==2 && $bank_name!=false && $bank_name!="" && $bank_name!=null && $branch!=false && $branch!="" && $branch!=null)
				{
					
					$bank_array=insertIfNotDuplicateBank($bank_name,$branch);
					$bank_id=$bank_array[0];
					$branch_id=$bank_array[1];
					checkChequePaymentPenalty($bank_id,$branch_id,$cheque_no,$cheque_date,$id,$ledger_id);
					
					/* accounts start */
						if(getAccountsStatus() && $account_settings['include_penalty']==1)
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							
							if(strtotime($old_payment_date)>=strtotime($books_starting_date))
							{
								if($old_payment_mode==1 && $payment_mode==2)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$old_amount); // debit customer for the old amount
													creditAccountingLedger($cash_ledger_id,$old_amount); // credit cash the old amount 
													
												
													
												}
												else if($account_settings['mercantile']==0)
												{
													
													
													debitAccountingCustomer($customer_id,$old_amount); // debit customer for the old amount
													creditAccountingLedger($cash_ledger_id,$old_amount); // credit cash the old amount 
													
													}
											}
											else if($old_payment_mode==2 && $payment_mode==2)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													
													debitAccountingCustomer($customer_id,$old_amount); // debit customer for the old amount
													creditAccountingLedger($old_ledger_id,$old_amount); // credit cash the old amount 
													
												
													
												}
												else if($account_settings['mercantile']==0)
												{
													
													debitAccountingCustomer($customer_id,$old_amount); // debit customer for the old amount
													creditAccountingLedger($old_ledger_id,$old_amount); // credit cash the old amount 
													
													}
											}
							}
							
							if(strtotime($paid_date)>=strtotime($books_starting_date))
							{
								if($old_payment_mode==1 && $payment_mode==2)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													debitAccountingLedger($ledger_id,$total_amount); // debit bank account
													creditAccountingCustomer($customer_id,$total_amount); // credit customer
													
												}
												else if($account_settings['mercantile']==0)
												{
													
													debitAccountingLedger($ledger_id,$total_amount); // debit bank account
													creditAccountingCustomer($customer_id,$total_amount); // credit customer
													
													}
											}
											else if($old_payment_mode==2 && $payment_mode==2)
											{
												if($account_settings['mercantile']==2)
												{
													//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
													//$interest_amount=$amount-$principal_amount;
													debitAccountingLedger($ledger_id,$total_amount); // debit bank account
													creditAccountingCustomer($customer_id,$total_amount); // credit customer
													
												}
												else if($account_settings['mercantile']==0)

												{
													debitAccountingLedger($ledger_id,$total_amount); // debit bank account
													creditAccountingCustomer($customer_id,$total_amount); // credit customer
													}
											}
							}
						}
						/* accounts stop */		
					
					return "success";
				}
			}
			else
			{
			
				dbQuery($sql);
				deleteChequePaymentByPaymentIdPenalty($id);
				return "success";
			}
	
	}
	else
	{
		return "error";
		}	
	}
}

function deletePenalty($id){
	
	if(checkForNumeric($id))
	{
	$penalty=getPenaltyById($id);
	$file_id=$penalty['file_id'];
	$loan_id=$penalty['loan_id'];
	$paid_date=$penalty['paid_date'];
	$total_amount=$penalty['total_amount'];
	$payment_mode=$penalty['payment_mode'];
	if($penalty['payment_mode']==2)
	{
	$cheque_details=getChequeDetailsPenalty($id);
	$ledger_id=$cheque_details['ledger_id'];
	}
	
		$agency_company_type_array=getAgencyOrCompanyIdFromFileId($file_id);
		$agency_company_type=$agency_company_type_array[0];
		$agency_company_type_id=$agency_company_type_array[1];
		/* Accounts Start */
		if(getAccountsStatus())
			{
				if($agency_company_type=="agency")
				{
					
					$account_settings=getAccountsSettingsForAgency($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForAgency($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForAgency($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForAgency($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForAgency($agency_company_type_id);
				}
				else
				{
					$account_settings=getAccountsSettingsForOC($agency_company_type_id);
					$cash_ledger_id=getCashLedgerIdForOC($agency_company_type_id);
					$books_starting_date=getBooksStartingDateForOC($agency_company_type_id);
					$auto_interest_ledger=getAdvanceInterestLedgerIdForOC($agency_company_type_id);
					$income_ledger=getIncomeLedgerIdForOC($agency_company_type_id);
				}
			}
		/* Accounts Stop */	
		
	$sql="DELETE FROM fin_loan_penalty WHERE penalty_id=$id";
	dbQuery($sql);
	
	 /* accounts start */
						if(getAccountsStatus() && $account_settings['include_penalty']==1 && $payment_mode==1)
						{
							
							$customer_id=getCustomerIdFromLoanId($loan_id);
							if(strtotime($paid_date)>=strtotime($books_starting_date))
							{
								if($account_settings['mercantile']==2)
								{
									//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									//$interest_amount=$amount-$principal_amount;
									
									debitAccountingCustomer($customer_id,$total_amount); // debit bank account
								    creditAccountingLedger($cash_ledger_id,$total_amount); // credit customer
									
								}
								else if($account_settings['mercantile']==0)
								{
									
									
									debitAccountingCustomer($customer_id,$total_amount); // debit bank account
								    creditAccountingLedger($cash_ledger_id,$total_amount); // credit customer
								
									}
							}
						}
						if(getAccountsStatus() && $account_settings['include_penalty']==1 && $payment_mode==2)
						{
							$customer_id=getCustomerIdFromLoanId($loan_id);
							if(strtotime($paid_date)>=strtotime($books_starting_date))
							{
								if($account_settings['mercantile']==2)
								{
									//$principal_amount=getPrincipalAmountToBeCreditedForEMIPaymentId($emi_payment_id);
									//$interest_amount=$amount-$principal_amount;
									
									debitAccountingCustomer($customer_id,$total_amount); // debit bank account
								    creditAccountingLedger($ledger_id,$total_amount); // credit customer
									
								}
								else if($account_settings['mercantile']==0)
								{
									
									
									debitAccountingCustomer($customer_id,$total_amount); // debit bank account
								    creditAccountingLedger($ledger_id,$total_amount); // credit customer
								
									}
							}
						}
						/* accounts stop */
					
	
	return "success";
	}
	else
	return "error";
	}
	
function getPenaltyById($id)
{
	$sql="SELECT loan_id, days_paid, paid_date, payment_mode, amount_per_day, total_amount, rasid_type_id, paid, rasid_no, paid_by, file_id FROM fin_loan_penalty WHERE penalty_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0];
		}
	return false;	
}

function getPenaltyDaysLeftForLoan($loan_id)
{
	$total_Penalty_days=getTotalPenaltyForLoan($loan_id);
		$total_days_paid=getTotalPenaltyPaidDaysForLoan($loan_id);
		$days_left=$total_Penalty_days-$total_days_paid;
	return $days_left;
	}	
	
function getChequeDetailsPenalty($id)
{
	$sql="SELECT penalty_cheque_id, bank_id, branch_id, cheque_no, cheque_date, ledger_id
	      FROM fin_loan_penalty_cheque
		  WHERE penalty_id=$id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		$resultArray=dbResultToArray($result);
		return $resultArray[0];
		}	  
	else
	return false;	
}
	
function getDiffernceBetweenEMIandLastEMI($emi_id,$loan_id)
{
	if(checkForNumeric($emi_id,$loan_id))
	{
		$sql="SELECT COUNT(*) FROM fin_loan_emi WHERE loan_emi_id>=$emi_id AND loan_id=$loan_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
		}
	}
function getTotalAmountForPenaltyId($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT (days_paid*amount_per_day) as amount
	      FROM fin_loan_penalty
		  WHERE penalty_id=$id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		$resultArray=dbResultToArray($result);
		return $resultArray[0][0];
		}	  
	else
	return false;	
		
		}
	
	}

function calculatePenaltyForLoanByInterest($interest_rate,$loan_id,$uptoDate=false)
{
	if($uptoDate==false)
	{
		$uptoDate=date('Y-m-d');
		}
	else
	{
		$uptoDate = str_replace('/', '-', $uptoDate);
	$uptoDate=date('Y-m-d',strtotime($uptoDate));
		}	
	
	$full_table=getFullPenaltyTableForLoanId($loan_id,$uptoDate);
	$amount_array=array();
	$duration_array=array();
	foreach($full_table as $row)
	{	
	
		$payment=null;
		$upPaid=null;
		if(isset($row['paidDetails']))
		$payment=$row['paidDetails'];
		if(isset($row['UnPaidDetails']))
		$upPaid=$row['UnPaidDetails'];
		
	   if(is_array($payment) && isset($payment[0]['days']))
	   {
	    foreach($payment as $p)
		{
			$duration_array[]=$p['days'];
			$amount_array[]=$p['amount'];
			}
	   }
	   
	    if(is_array($upPaid) && isset($upPaid['days']))
	   {
	    
			$duration_array[]=$upPaid['days'];
			$amount_array[]=$upPaid['amount'];
			
	   }
	   
	   
	   
	}
	$penalty=calculatePenaltyBYInterest($amount_array,$interest_rate,$duration_array);
	return $penalty;
}		

function calculatePenaltyBYInterest($amount_array,$interest,$duration_array,$skipDays=0)			
{
	$total=0;
	if($uptoDate==false)
	
	if(is_array($amount_array) && is_array($duration_array) && count($amount_array)>0)
	{
		for($i=0;$i<count($amount_array);$i++)
		{
			$amount=null;
			$duration_in_days=null;
			$amount=$amount_array[$i];
			$duration_in_days=$duration_array[$i];
			$peanlty=(($amount*$interest)/(100*365))*$duration_in_days;
			
			$total=$total+$peanlty;
		}
	}
	return $total;
}
function getTotalPenaltyAmountPaidForLoan($id)
{
	$totalDetails=getPenaltyDetailsForLoan($id);
	$total=0;
	foreach($totalDetails as $payment)
	{
		$payment_amount=null;
		$payment_amount=$payment['amount_per_day']*$payment['days_paid'];
		$total=$total+$payment_amount;
		}
	return $total;	
}
function getRemainBalanceForLoanReducingInterest($loan_id) // settelemant figure for reducing interest loan
{
	$full_table=getIntPrincBalanceTableForLoan($loan_id);
	$emi=getEmiForLoanId($loan_id);
	$totalPayment=getTotalPaymentForLoan($loan_id);
	$totalEMIsPaid=$totalPayment/$emi;
	$floor_paid = floor($totalEMIsPaid);      
	$fraction_unpaid = 1-($totalEMIsPaid - $floor_paid);
	$index_in_full_table=($totalEMIsPaid+$fraction_unpaid)-1;
	$fractional_emi_total_payment=$emi-($fraction_unpaid*$emi);
	if($index_in_full_table>=0)
	{
		$remaing_balance=$full_table[$index_in_full_table]['balance'];
		$principal=$full_table[$index_in_full_table]['principal'];
		$principal_paid_in_last_fraction_emi=($fractional_emi_total_payment*$principal)/$emi;
		$principal_left_in_last_fractional_emi=$principal-$principal_paid_in_last_fraction_emi;
		}
	else
	{
		$remaing_balance=getLoanAmountById($loan_id);
		return $remaing_balance;
		}	
	
	$remaing_balance=$remaing_balance+$principal_left_in_last_fractional_emi;
	return $remaing_balance;
}

function getChequeReturnDetailsForFileId($file_id)
{
	$sql="SELECT cheque_return_id,bank_id,branch_id,cheque_no,cheque_date,file_id,loan_emi_id,cheque_amount
	      FROM fin_loan_emi_payment_cheque_return
		  WHERE file_id=$file_id";
	$result=dbQuery($sql);	  
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray;
		
		}
	return "error";	
	}
	
function deleteChequeReturnById($cheque_return_id)
{
	if(checkForNumeric($cheque_return_id))
	{
	$sql="DELETE FROM fin_loan_emi_payment_cheque_return WHERE cheque_return_id=$cheque_return_id";
	$result=dbQuery($sql);	  
	return "success";	
	}
	return "error";
	}	
	
function closeFileIfBalanceZero($loan_id)
{
	
	if(checkForNumeric($loan_id))
	{
	
	$balance=getBalanceForLoan($loan_id);
	$file_id=getFileIdFromLoanId($loan_id);
	$file_status=getFileStatusforFile($file_id);
	$loan=getLoanDetailsByFileId($file_id);
	
	if($balance>=0 && $file_status!=4)
	{
		
		if($file_status==1)
		{
			$sql="UPDATE fin_file SET file_status=2 WHERE file_id=$file_id";
			dbQuery($sql);
			}
		
			
		}
	else if($balance<0 && $file_status!=4)
	{
			
	
		if($file_status==2)
		{
		
		
	
		$loan_ending_date=$loan['loan_ending_date'];
		$today=date('Y-m-d');
		
				if(strtotime($loan_ending_date)<strtotime($today))
				{
						
				
					$sql="UPDATE fin_file SET file_status=2 WHERE file_id=$file_id";
				dbQuery($sql);
					}
				else
				{
						
					$sql="UPDATE fin_file SET file_status=1 WHERE file_id=$file_id";
				dbQuery($sql);
					}	
			
			
		}
		else if($file_status==1)
		{
		
		
	
		$loan_ending_date=$loan['loan_ending_date'];
		$today=date('Y-m-d');
		
				if(strtotime($loan_ending_date)<strtotime($today))
				{
						
				
					$sql="UPDATE fin_file SET file_status=2 WHERE file_id=$file_id";
				dbQuery($sql);
					}
				else
				{
						
					$sql="UPDATE fin_file SET file_status=1 WHERE file_id=$file_id";
				dbQuery($sql);
					}	
			
			
		}
	
	}
	}

}
function getTotalPaymentsBetweenTwoDates($from,$to,$agency_id=null,$our_company_id=null)
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
	
	$sql="SELECT payment_amount FROM fin_loan_emi_payment,fin_loan_emi,fin_loan,fin_file WHERE payment_date>='$from' AND payment_date<='$to'
	      AND fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
		  AND fin_loan.loan_id=fin_loan_emi.loan_id
		  AND fin_file.file_id=fin_loan.file_id ";
		  if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql." AND agency_id=$agency_id ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."AND oc_id=$our_company_id ";
}
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$total=0;
	if(dbNumRows($result)>0)
	{
	foreach($resultArray as $re)
	{
		$total=$total+$re['payment_amount'];
		}
	}
	return $total;
}
function getLoanIdFromFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT loan_id From fin_loan WHERE file_id=$file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0][0];
		else 
		return 0;
		}
	}
function getNearestEMIDateFromToday($loan_id)
{
	$today=date('Y-m-d');
	$sql="SELECT MIN(actual_emi_date) FROM fin_loan_emi WHERE actual_emi_date>='$today' AND loan_id=$loan_id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		$resultArray=dbResultToArray($result);
		return $resultArray[0][0];
		
		}
	else return "1970-01-01";	
	
	}	
function getChequeDetailsFromEmiPaymentId($emi_payment_id)
{
	if(checkForNumeric($emi_payment_id))
	{
		$sql="SELECT payment_cheque_id,bank_id,branch_id,cheque_no,cheque_date,ledger_id,cheque_return FROM fin_loan_emi_payment_cheque
		WHERE emi_payment_id=$emi_payment_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0];
			}
		else
		return false;	
		
		}
	
	}	

function getLoanNoFromEMIIdForLoan($emi_id)
{
	$loan_id=getLoanIdFromEmiId($emi_id);
	$sql="SELECT COUNT(loan_emi_id) FROM fin_loan_emi WHERE loan_id=$loan_id AND loan_emi_id<=$emi_id GROUP BY loan_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	return $resultArray[0][0];
}

function getPaidEmiSchemeForLoan($id){
	if(checkForNumeric($id))
	{
		$max_emi_id=getEmiIdForLatestPaymentForLoan($id);
		
		$sql="SELECT emi_amount,loan_emi_id FROM fin_loan_emi WHERE loan_id=$id AND loan_emi_id<=$max_emi_id  ORDER BY actual_emi_date";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			$returnArray=array();
			$previous_emi=-1;
			$i=-1;
			foreach($resultArray as $re)
			{
					$current_emi=$re['emi_amount'];
					if($current_emi==$previous_emi)
					{
						
						$returnArray[$i]['duration']=$returnArray[$i]['duration']+1;
						}
					else
					{
						$returnArray[++$i]['emi']=$current_emi;
						$returnArray[$i]['duration']=1;
						}	
					$previous_emi=$current_emi;	
			}
		
		return $returnArray;	
		}
		else 
		return "error";
		}
	
	}	
function getunPaidEmiSchemeForLoan($id){
	if(checkForNumeric($id))
	{
		$max_emi_id=getEmiIdForLatestPaymentForLoan($id);
		
		$sql="SELECT emi_amount,loan_emi_id FROM fin_loan_emi WHERE loan_id=$id AND loan_emi_id>$max_emi_id ORDER BY actual_emi_date";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			$returnArray=array();
			$previous_emi=-1;
			$i=-1;
			foreach($resultArray as $re)
			{
					$current_emi=$re['emi_amount'];
					if($current_emi==$previous_emi)
					{
						
						$returnArray[$i]['duration']=$returnArray[$i]['duration']+1;
						}
					else
					{
						$returnArray[++$i]['emi']=$current_emi;
						$returnArray[$i]['duration']=1;
						}	
					$previous_emi=$current_emi;	
			}
		
		return $returnArray;	
		}
		else 
		return "error";
		}
	
	}	

function getLoanSchemeAgency($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT agency_emi,agency_duration FROM fin_loan_scheme_agency WHERE loan_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray;
		else
		return "error";
		}
	
	}

function getRasidTypes()
{
	$sql="SELECT rasid_type_id , rasid_type_name FROM fin_rasid_type";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;
	else
	return false;
	}	

function getRasidTypeById($id)
{
	$sql="SELECT rasid_type_name FROM fin_rasid_type WHERE rasid_type_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	return false;
	}	
	
function deleteAllPaymentsForFile($file_id)
{
	
	if(checkForNumeric($file_id))
	{
		$loan_id=getLoanIdFromFileId($file_id);
		$sql="DELETE  FROM fin_loan_emi_payment
WHERE   loan_emi_id IN (
            SELECT  loan_emi_id
            FROM    fin_loan_emi
            WHERE   loan_id = $loan_id
        );";
		
		$result=dbQuery($sql);
		}
}				
?>