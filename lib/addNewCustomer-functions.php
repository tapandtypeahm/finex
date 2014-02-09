<?php
require_once('cg.php');
require_once('bd.php');
require_once('common.php');
require_once('file-functions.php');
require_once('customer-functions.php');
require_once('guarantor-functions.php');
require_once('loan-functions.php');
require_once('EMI-functions.php');
require_once('vehicle-functions.php');
require_once('city-functions.php');
require_once('area-functions.php');

// $oc_id and file_id 

function addNewCustomer($agency_id, $agreement_no, $file_number, $broker_id, $customer_name, $customer_address, $customer_city_id, $customer_area_id, $customer_pincode,$customer_contact_no,$customer_human_proof_type_id,$customer_proofno,$customer_proofImg,$customer_proofImgScan,$guarantor_name,$guarantor_address,$guarantor_city_id,$guarantor_area_id,$guarantor_pincode,$guarantor_contact_no,$guarantor_human_proof_type_id,$guarantor_proofno,$guarantor_proofImg,$guarantor_proofImgScan,$amount,$loan_amount_type,$duration,$loan_type,$loan_scheme,$roi,$emi,$loan_approval_date,$loan_starting_date,$bank_name,$branch_name,$cheque_amount,$cheque_date,$cheque_no,$axin_no,$ledger_id,$agency_loan_amount=0,$agency_emi=0,$agency_duration=0,$duration_unit=1)
{
	
	if(!isset($customer_pincode) || !validateForNull($customer_pincode))
	{
		$customer_pincode=0;
		}
	if(!isset($guarantor_pincode) || !validateForNull($guarantor_pincode))
	{
		$guarantor_pincode=0;
		}	
	
	if(validateForNull($agreement_no,$file_number) && $agency_id!=-1 && checkForNumeric($customer_city_id,$amount,$roi,$loan_amount_type,$broker_id) && $customer_city_id>0   && validateForNull($customer_name,$customer_address,$loan_approval_date,$loan_starting_date,$customer_area_id))
	{
	
		$file_id=insertFile($agency_id,$agreement_no,$file_number,$_SESSION['adminSession']['oc_id'],$broker_id);
		
		if(checkForNumeric($file_id,$customer_city_id) && $file_id>0 && $customer_city_id>0  && validateForNull($customer_name,$customer_address))
		{
				
			
		
			$customer_id=insertCustomer($customer_name,$customer_address,$customer_city_id,$customer_area_id,$customer_pincode,$file_id,$customer_contact_no,$customer_human_proof_type_id,$customer_proofno,$customer_proofImg,$customer_proofImgScan);
			
				
			
			if(checkForNumeric($customer_id) && validateForNull($guarantor_name,$guarantor_address) && checkForNumeric($guarantor_city_id))
			{
				$guarantor_id=insertGuarantor($guarantor_name,$guarantor_address,$guarantor_city_id,$guarantor_area_id,$guarantor_pincode,$file_id,$customer_id,$guarantor_contact_no,$guarantor_human_proof_type_id,$guarantor_proofno,$guarantor_proofImg,$guarantor_proofImgScan);
			}
			
			
			
			$loan_id=insertLoan($amount,$loan_amount_type,$duration,$loan_type,$loan_scheme,$roi,$emi,$loan_approval_date,$loan_starting_date,$file_id,$customer_id,$agency_loan_amount,$agency_emi,$agency_duration,$duration_unit);
		
			
			
			if(checkForNumeric($loan_id) && $loan_amount_type==2)
			{
				
				insertLoanCheque($loan_id,$bank_name,$branch_name,$cheque_amount,$cheque_date,$cheque_no,$axin_no,$ledger_id);
				return $file_id;
				}
			else if($loan_amount_type==1 && checkForNumeric($loan_id))
			{
				return $file_id;	
				}	
			$sql="DELETE FROM fin_file WHERE file_id=$file_id";
			dbQuery($sql);	
			return "error";	
			
		}
	}
	else
	{
		return "error";
	}	
	
	
}

function updateRasidNo()
{
	$sql="SELECT emi_payment_id,rasid_no,loan_emi_id FROM fin_loan_emi_payment";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	foreach($resultArray as $re)
	{
		$payment_id=$re[0];
		$rasid_no=$re[1];
		if(is_numeric($rasid_no))
		{
		$emi_id=$re[2];
		$ag_id_array=getAgnecyIdFromEmiId($emi_id);
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
		$sql="UPDATE fin_loan_emi_payment SET rasid_no='$rasid_no' WHERE emi_payment_id=$payment_id";
		dbQuery($sql);
		}
	}
	}
function updateRasidIdentifier()
{
	$sql="SELECT GROUP_CONCAT(emi_payment_id) FROM fin_loan_emi_payment GROUP BY rasid_no,date_added ORDER BY emi_payment_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	
	foreach($resultArray as $re)
	{
		$payment_id_array=explode(",",$re[0]);
		
		if(count($payment_id_array)>1)
		{
			sort($payment_id_array);
			$parent_payment_id=$payment_id_array[0];
			
				$sql="UPDATE fin_loan_emi_payment SET rasid_identifier=0 WHERE emi_payment_id=$parent_payment_id";
				dbQuery($sql);
			for($i=1;$i<count($payment_id_array);$i++)
			{
				$payment_id=$payment_id_array[$i];
				$sql="UPDATE fin_loan_emi_payment SET rasid_identifier=$parent_payment_id WHERE emi_payment_id=$payment_id";
				dbQuery($sql);
				
				}
			
			}
		}
	}	

function updateLoanEndingDateForAllLoans()
{
	$sql="SELECT loan_id,loan_starting_date,loan_duration From fin_loan";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	foreach($resultArray as $loan)
	{
		$loan_id=$loan['loan_id'];
		$loan_starting_date=$loan['loan_starting_date'];
		$duration=$loan['loan_duration'];
		
		$actual_ending_date=getEndingDateForLoan($loan_starting_date,$duration);
		$sql="UPDATE fin_loan SET loan_ending_date='$actual_ending_date' WHERE loan_id=$loan_id";
		dbQuery($sql);
		}
}

function updateVehicleNo()
{
	$sql="SELECT vehicle_id,vehicle_reg_no From fin_vehicle";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	foreach($resultArray as $loan)
	{
		$vehicle_id=$loan['vehicle_id'];
		$reg_no=$loan['vehicle_reg_no'];
		$new_reg_no=stripVehicleno($reg_no);
		
		$sql="UPDATE fin_vehicle SET vehicle_reg_no='$new_reg_no' WHERE vehicle_id=$vehicle_id";
		dbQuery($sql);
		}
	}	

function updatefinLoanEMIAmount()
{
	$sql="SELECT loan_id,emi from fin_loan";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		foreach($resultArray as $re)
		{
			$emi=$re[1];
			$loan_id=$re[0];
			$sql="UPDATE fin_loan_emi SET emi_amount=$emi WHERE loan_id=$loan_id";
			dbQuery($sql);
			}
		
		}
	}
	
function updateCityAreaCustomer($admin_id,$id)
{
	$sql="SELECT customer_id,area_id,city_id FROM fin_customer WHERE created_by=$admin_id";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	
	foreach($resultArray as $re)
	{
		$customer_id=0;
		$area_id=0;
		$city_id=0;
		$customer_id=$re['customer_id'];
		$area_id=$re['area_id'];
		$city_id=$re['city_id'];
		
		if($city_id>0 && $customer_id>0 && $area_id>0)
		{
		$city=0;	
		$city=getCityByID($city_id);
			if($city!=0)
			{
				$city_name=$city['city_name'];
				$new_area_id=insertArea($city_name,$id);
				$sql="UPDATE  fin_customer SET city_id=$id, area_id=$new_area_id WHERE customer_id=$customer_id";
				$result=dbQuery($sql);
				}
		}
		}
	
}		
?>