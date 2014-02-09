<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("file-functions.php");
require_once("customer-functions.php");
require_once("vehicle-insurance-functions.php");
require_once("insurance-company-functions.php");
require_once("vehicle-functions.php");
require_once("loan-functions.php");
require_once("common.php");
require_once("bd.php");

function generalEMIReports($from=null,$to=null,$win_gt=null,$win_lt=null,$emi_gt=null,$emi_lt=null,$balance_gt=null,$balance_lt=null,$city_id=null,$area_id=null,$file_status=null,$agency_id=null,$broker_string=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
	$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
		
    
	$sql="SELECT fin_file.file_id, fin_loan.loan_id, file_number, emi, file_status, oc_id, agency_id,
		  GROUP_CONCAT(loan_emi_id) as loan_emi_array,
		  GROUP_CONCAT(actual_emi_date) as loan_emi_date_array,
		  max(actual_emi_date) as last_emi_date
	 	  FROM 
	      fin_file, fin_loan, fin_loan_emi, fin_customer
		  WHERE fin_loan.loan_id=fin_loan_emi.loan_id
		  AND file_status!=3
		  AND "; 
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}
	if(isset($from) && validateForNull($from))
	$sql=$sql."actual_emi_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."actual_emi_date<='$to'
		  AND ";
	if(isset($emi_gt) && validateForNull($emi_gt))  
	$sql=$sql."emi>=$emi_gt
		  AND ";
	if(isset($emi_lt) && validateForNull($emi_lt))  
	$sql=$sql."emi<=$emi_lt
		  AND ";	  	  
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id))  
	$sql=$sql." area_id IN ($area_id)
		  AND ";
	if(isset($broker_string) && validateForNull($broker_string))  
	$sql=$sql." broker_id IN ($broker_string)
		  AND ";	  	  	  	  
	$sql=$sql."	 fin_loan.file_id=fin_file.file_id
		  AND fin_customer.file_id=fin_file.file_id
		  AND our_company_id=$oc_id 
		  GROUP BY fin_file.file_id
		  ORDER BY fin_file.file_id";
	
	
	$result=dbQuery($sql);
	
	$resultArray=dbResultToArray($result);
	
$today=date('Y-m-d');
	$loan_emi_array_array=array();
	foreach($resultArray as $resulta)
	{
	$file_id=$resulta['file_id'];	
	$sql="SELECT 
		  GROUP_CONCAT(loan_emi_id) as loan_emi_array,
		  max(actual_emi_date) as last_emi_date
	 	  FROM 
	      fin_file, fin_loan, fin_loan_emi, fin_customer
		  WHERE fin_loan.loan_id=fin_loan_emi.loan_id
		  AND file_status!=3
		  AND "; 
		  if($our_company_id=="NULL" && is_numeric($agency_id))
			{
			$sql=$sql."agency_id=$agency_id AND ";
			}
			if($agency_id=="NULL" && is_numeric($our_company_id))
			{
			$sql=$sql."oc_id=$our_company_id AND ";
			}
			if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}
			$sql=$sql."actual_emi_date<='$today'
				  AND";
			if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
			$sql=$sql." city_id=$city_id
				  AND ";
			if(isset($area_id) && validateForNull($area_id))  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	  	  	  
			$sql=$sql."	fin_loan.file_id=fin_file.file_id
				  AND fin_customer.file_id=fin_file.file_id
				  AND our_company_id=$oc_id
				  AND fin_file.file_id=$file_id
				  GROUP BY fin_file.file_id";
	
			$result2=dbQuery($sql);
			$resultArray2=dbResultToArray($result2);
			
			if(isset($resultArray2[0]))
			$loan_emi_array_array[]=$resultArray2[0];
			else
			$loan_emi_array_array[]=array();
	}
			
		
	
	$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$bucket_details=0;
			$reportRow=$resultArray[$xy];
			$file_id=$reportRow['file_id'];
			$file_status=$reportRow['file_status'];
			$loan_id=$reportRow['loan_id'];
			$file_number=$reportRow['file_number'];
			$emi=$reportRow['emi'];
			$file_agency_id=$reportRow['agency_id'];
			$file_oc_id=$reportRow['oc_id'];
			if(isset($loan_emi_array_array[$xy]['loan_emi_array']))
			{
				
			$loan_emi_array=explode(",",$loan_emi_array_array[$xy]['loan_emi_array']);
			$emi_date=$loan_emi_array_array[$xy]['last_emi_date'];
			}
			else
			{
				$loan_emi_array=null;
				$emi_date=explode(",",$reportRow['loan_emi_date_array']);
				$emi_date=$emi_date[0];
				}
			$loan_emi_date_array=$reportRow['loan_emi_date_array'];
			
			if(isset($loan_emi_array))
			{
			$window=getBucketForLoan($loan_id);
			$bucket_details=getBucketDetailsForLoan($loan_id);
			$balance=0;
			foreach($bucket_details as $emi=>$corresponding_bucket)
			{
				$balance=$balance+($emi*$corresponding_bucket);
				}
			}
			else
			{
			$totalPayment=0;
			$totalEMIs=0;
			$totalActualPayment=$totalEMIs*$emi;
			$balance=$totalActualPayment-$totalPayment;
			$window=$balance/$emi;
			}
			if($file_status==4)
			{
				$balance=0;
				$window=0;
				}
			
			
			
			if(isset($win_gt) && validateForNull($win_gt))
			{
				$setWin_gt=$win_gt;
				}
			else
			$setWin_gt=-1;
			if(isset($win_lt) && validateForNull($win_lt))
			{
				$setWin_lt=$win_lt;
				}
			else
			$setWin_lt=-1;
			
			if(isset($balance_gt) && validateForNull($balance_gt))
			{
				$setbal_gt=$balance_gt;
				}
			else
			$setbal_gt=-1;
			if(isset($balance_lt) && validateForNull($balance_lt))
			{
				$setbal_lt=$balance_lt;
				}
			else
			$setbal_lt=-1;
			
		
			if((($setWin_gt==-1 && $setWin_lt==-1) || ( ($setWin_gt==-1 || $window>=$setWin_gt) && ($setWin_lt==-1 || $window<=$setWin_lt) )) && ( ($setbal_gt==-1 && $setbal_lt==-1) || ( ($setbal_gt==-1 || $balance>=$setbal_gt) && ($setbal_lt==-1 || $balance<=$setbal_lt) )) && validateForNull($emi_date) && $emi_date!="NA")
			{	
			
			$oldest_unpaid_emi=getOldestUnPaidEmiDate($loan_id);
			$last_unpaid_date=$oldest_unpaid_emi['actual_emi_date'];
			$returnArray[$j]['file_id']=$file_id;
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['file_status']=$file_status;
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['emi_date']=$oldest_unpaid_emi['actual_emi_date'];
			$returnArray[$j]['payment_date']=getLatestPaymentDateForLoan($loan_id);
			$returnArray[$j]['emi_date_array']=$reportRow['loan_emi_date_array'];
			$returnArray[$j]['emi']=$emi;
			$returnArray[$j]['loan_scheme']=getLoanScheme($loan_id);
			$returnArray[$j]['balance']=$balance;
			$returnArray[$j]['agency_id']=$file_agency_id;
			$returnArray[$j]['oc_id']=$file_oc_id;
			$returnArray[$j]['window']=number_format($window,1);
			$returnArray[$j]['bucket_details']=$bucket_details;
			$customer=getCustomerDetailsByFileId($file_id);
			$returnArray[$j]['customer']=$customer;
			}
			$j++;
			}
		
		uasort($returnArray,'EMIDatesComparatorForEmiReports');
		return $returnArray;	
		
		}
		

}

function generalEMIReportsPaymentDate($from=null,$to=null,$win_gt=null,$win_lt=null,$emi_gt=null,$emi_lt=null,$balance_gt=null,$balance_lt=null,$city_id=null,$area_id=null,$file_status=null,$agency_id=null,$broker_string=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
	$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
		
    
	$sql="SELECT fin_file.file_id, fin_loan.loan_id, file_number, emi, file_status, oc_id, agency_id,
		  GROUP_CONCAT(loan_emi_id) as loan_emi_array,
		  GROUP_CONCAT(actual_emi_date) as loan_emi_date_array,
		  max(actual_emi_date) as last_emi_date
	 	  FROM 
	      fin_file, fin_loan, fin_loan_emi, fin_customer
		  WHERE fin_loan.loan_id=fin_loan_emi.loan_id
		  AND file_status!=3
		  AND "; 
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}
/*	if(isset($from) && validateForNull($from))
	$sql=$sql."actual_emi_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."actual_emi_date<='$to'
		  AND "; */
	if(isset($emi_gt) && validateForNull($emi_gt))  
	$sql=$sql."emi>=$emi_gt
		  AND ";
	if(isset($emi_lt) && validateForNull($emi_lt))  
	$sql=$sql."emi<=$emi_lt
		  AND ";	  	  
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id))  
	$sql=$sql." area_id IN ($area_id)
		  AND ";
	if(isset($broker_string) && validateForNull($broker_string))  
	$sql=$sql." broker_id IN ($broker_string)
		  AND ";	  	  	  	  
	$sql=$sql."	 fin_loan.file_id=fin_file.file_id
		  AND fin_customer.file_id=fin_file.file_id
		  AND our_company_id=$oc_id 
		  GROUP BY fin_file.file_id
		  ORDER BY fin_file.file_id";
	
	
	$result=dbQuery($sql);
	
	$resultArray=dbResultToArray($result);
	
$today=date('Y-m-d');
	$loan_emi_array_array=array();
	foreach($resultArray as $resulta)
	{
	$file_id=$resulta['file_id'];	
	$sql="SELECT 
		  GROUP_CONCAT(loan_emi_id) as loan_emi_array,
		  max(actual_emi_date) as last_emi_date
	 	  FROM 
	      fin_file, fin_loan, fin_loan_emi, fin_customer
		  WHERE fin_loan.loan_id=fin_loan_emi.loan_id
		  AND file_status!=3
		  AND "; 
		  if($our_company_id=="NULL" && is_numeric($agency_id))
			{
			$sql=$sql."agency_id=$agency_id AND ";
			}
			if($agency_id=="NULL" && is_numeric($our_company_id))
			{
			$sql=$sql."oc_id=$our_company_id AND ";
			}
			if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}
			$sql=$sql."actual_emi_date<='$today'
				  AND";
			if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
			$sql=$sql." city_id=$city_id
				  AND ";
			if(isset($area_id) && validateForNull($area_id))  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	  	  	  
			$sql=$sql."	fin_loan.file_id=fin_file.file_id
				  AND fin_customer.file_id=fin_file.file_id
				  AND our_company_id=$oc_id
				  AND fin_file.file_id=$file_id
				  GROUP BY fin_file.file_id";
	
			$result2=dbQuery($sql);
			$resultArray2=dbResultToArray($result2);
			
			if(isset($resultArray2[0]))
			$loan_emi_array_array[]=$resultArray2[0];
			else
			$loan_emi_array_array[]=array();
	}
			
		
	
	$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$bucket_details=0;
			$reportRow=$resultArray[$xy];
			$file_id=$reportRow['file_id'];
			$file_status=$reportRow['file_status'];
			$loan_id=$reportRow['loan_id'];
			$file_number=$reportRow['file_number'];
			$emi=$reportRow['emi'];
			$file_agency_id=$reportRow['agency_id'];
			$file_oc_id=$reportRow['oc_id'];
			if(isset($loan_emi_array_array[$xy]['loan_emi_array']))
			{
				
			$loan_emi_array=explode(",",$loan_emi_array_array[$xy]['loan_emi_array']);
			$emi_date=$loan_emi_array_array[$xy]['last_emi_date'];
			}
			else
			{
				$loan_emi_array=null;
				$emi_date=explode(",",$reportRow['loan_emi_date_array']);
				$emi_date=$emi_date[0];
				}
			$loan_emi_date_array=$reportRow['loan_emi_date_array'];
			
			if(isset($loan_emi_array))
			{
			$window=getBucketForLoan($loan_id);
			$bucket_details=getBucketDetailsForLoan($loan_id);
			$balance=0;
			foreach($bucket_details as $emi=>$corresponding_bucket)
			{
				$balance=$balance+($emi*$corresponding_bucket);
				}
			}
			else
			{
			$totalPayment=0;
			$totalEMIs=0;
			$totalActualPayment=$totalEMIs*$emi;
			$balance=$totalActualPayment-$totalPayment;
			$window=$balance/$emi;
			}
			if($file_status==4)
			{
				$balance=0;
				$window=0;
				}
			
			
			
			if(isset($win_gt) && validateForNull($win_gt))
			{
				$setWin_gt=$win_gt;
				}
			else
			$setWin_gt=-1;
			if(isset($win_lt) && validateForNull($win_lt))
			{
				$setWin_lt=$win_lt;
				}
			else
			$setWin_lt=-1;
			
			if(isset($balance_gt) && validateForNull($balance_gt))
			{
				$setbal_gt=$balance_gt;
				}
			else
			$setbal_gt=-1;
			if(isset($balance_lt) && validateForNull($balance_lt))
			{
				$setbal_lt=$balance_lt;
				}
			else
			$setbal_lt=-1;
			
			$latest_payment_date=getLatestPaymentDateForLoan($loan_id);
			if((($setWin_gt==-1 && $setWin_lt==-1) || ( ($setWin_gt==-1 || $window>=$setWin_gt) && ($setWin_lt==-1 || $window<=$setWin_lt) )) && ( ($setbal_gt==-1 && $setbal_lt==-1) || ( ($setbal_gt==-1 || $balance>=$setbal_gt) && ($setbal_lt==-1 || $balance<=$setbal_lt) )) && validateForNull($emi_date) && $emi_date!="NA" && strtotime($latest_payment_date)<=strtotime($from))
			{	
			
			$oldest_unpaid_emi=getOldestUnPaidEmiDate($loan_id);
			$last_unpaid_date=$oldest_unpaid_emi['actual_emi_date'];
			$returnArray[$j]['file_id']=$file_id;
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['file_status']=$file_status;
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['emi_date']=$oldest_unpaid_emi['actual_emi_date'];
			$returnArray[$j]['payment_date']=$latest_payment_date;
			$returnArray[$j]['emi_date_array']=$reportRow['loan_emi_date_array'];
			$returnArray[$j]['emi']=$emi;
			$returnArray[$j]['loan_scheme']=getLoanScheme($loan_id);
			$returnArray[$j]['balance']=$balance;
			$returnArray[$j]['agency_id']=$file_agency_id;
			$returnArray[$j]['oc_id']=$file_oc_id;
			$returnArray[$j]['window']=number_format($window,1);
			$returnArray[$j]['bucket_details']=$bucket_details;
			$customer=getCustomerDetailsByFileId($file_id);
			$returnArray[$j]['customer']=$customer;
			}
			$j++;
			}
		
		uasort($returnArray,'EMIPaymentDatesComparatorForEmiReports');
		return $returnArray;	
		
		}
		

}


function generalLoanStarterReports($from=null,$to=null,$win_gt=null,$win_lt=null,$emi_gt=null,$emi_lt=null,$balance_gt=null,$balance_lt=null,$city_id=null,$area_id=null,$file_status=null,$agency_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
	$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
		
    
	$sql="SELECT fin_file.file_id, fin_loan.loan_id, file_number, emi, file_status, oc_id, agency_id,
		  GROUP_CONCAT(loan_emi_id) as loan_emi_array,
		  GROUP_CONCAT(actual_emi_date) as loan_emi_date_array,
		  max(actual_emi_date) as last_emi_date
	 	  FROM 
	      fin_file, fin_loan, fin_loan_emi, fin_customer
		  WHERE fin_loan.loan_id=fin_loan_emi.loan_id
		  AND file_status!=3
		  AND "; 
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}
	if(isset($from) && validateForNull($from))
	$sql=$sql."actual_emi_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."actual_emi_date<='$to'
		  AND ";
	if(isset($emi_gt) && validateForNull($emi_gt))  
	$sql=$sql."emi>=$emi_gt
		  AND ";
	if(isset($emi_lt) && validateForNull($emi_lt))  
	$sql=$sql."emi<=$emi_lt
		  AND ";	  	  
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id))  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  	  	  
	$sql=$sql."	 fin_loan.file_id=fin_file.file_id
		  AND fin_customer.file_id=fin_file.file_id
		  AND our_company_id=$oc_id 
		  GROUP BY fin_file.file_id
		  ORDER BY fin_file.file_id";
	
	
	$result=dbQuery($sql);
	
	$resultArray=dbResultToArray($result);
	
$today=date('Y-m-d');
	$loan_emi_array_array=array();
	foreach($resultArray as $resulta)
	{
	$file_id=$resulta['file_id'];	
	$sql="SELECT 
		  GROUP_CONCAT(loan_emi_id) as loan_emi_array,
		  max(actual_emi_date) as last_emi_date
	 	  FROM 
	      fin_file, fin_loan, fin_loan_emi, fin_customer
		  WHERE fin_loan.loan_id=fin_loan_emi.loan_id
		  AND file_status!=3
		  AND "; 
		  if($our_company_id=="NULL" && is_numeric($agency_id))
			{
			$sql=$sql."agency_id=$agency_id AND ";
			}
			if($agency_id=="NULL" && is_numeric($our_company_id))
			{
			$sql=$sql."oc_id=$our_company_id AND ";
			}
			if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}
			$sql=$sql."actual_emi_date<='$today'
				  AND";
			if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
			$sql=$sql." city_id=$city_id
				  AND ";
			if(isset($area_id) && validateForNull($area_id))  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	  	  	  
			$sql=$sql."	fin_loan.file_id=fin_file.file_id
				  AND fin_customer.file_id=fin_file.file_id
				  AND our_company_id=$oc_id
				  AND fin_file.file_id=$file_id
				  GROUP BY fin_file.file_id";
	
			$result2=dbQuery($sql);
			$resultArray2=dbResultToArray($result2);
			
			if(isset($resultArray2[0]))
			$loan_emi_array_array[]=$resultArray2[0];
			else
			$loan_emi_array_array[]=array();
	}
			
		
	
	$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$bucket_details=0;
			$reportRow=$resultArray[$xy];
			$file_id=$reportRow['file_id'];
			$file_status=$reportRow['file_status'];
			$loan_id=$reportRow['loan_id'];
			$file_number=$reportRow['file_number'];
			$emi=$reportRow['emi'];
			$file_agency_id=$reportRow['agency_id'];
			$file_oc_id=$reportRow['oc_id'];
			if(isset($loan_emi_array_array[$xy]['loan_emi_array']))
			{
				
			$loan_emi_array=explode(",",$loan_emi_array_array[$xy]['loan_emi_array']);
			$emi_date=$loan_emi_array_array[$xy]['last_emi_date'];
			}
			else
			{
				$loan_emi_array=null;
				$emi_date=explode(",",$reportRow['loan_emi_date_array']);
				$emi_date=$emi_date[0];
				}
			$loan_emi_date_array=$reportRow['loan_emi_date_array'];
			
			if(isset($loan_emi_array))
			{
			
			$window=getBucketForLoan($loan_id);
			$bucket_details=getBucketDetailsForLoan($loan_id);
			$balance=0;
			foreach($bucket_details as $emi=>$corresponding_bucket)
			{
				$balance=$balance+($emi*$corresponding_bucket);
				}
			}
			else
			{
			$totalPayment=0;
			$totalEMIs=0;
			$totalActualPayment=$totalEMIs*$emi;
			$balance=$totalActualPayment-$totalPayment;
			$window=$balance/$emi;
			}
			if($file_status==4)
			{
				$balance=0;
				$window=0;
				}
			
			
			
			if(isset($win_gt) && validateForNull($win_gt))
			{
				$setWin_gt=$win_gt;
				}
			else
			$setWin_gt=-1;
			if(isset($win_lt) && validateForNull($win_lt))
			{
				$setWin_lt=$win_lt;
				}
			else
			$setWin_lt=-1;
			
			if(isset($balance_gt) && validateForNull($balance_gt))
			{
				$setbal_gt=$balance_gt;
				}
			else
			$setbal_gt=-1;
			if(isset($balance_lt) && validateForNull($balance_lt))
			{
				$setbal_lt=$balance_lt;
				}
			else
			$setbal_lt=-1;
			
		
			if(getTotalEmiPaidForLoan($loan_id)==0 && (($setWin_gt==-1 && $setWin_lt==-1) || ( ($setWin_gt==-1 || $window>=$setWin_gt) && ($setWin_lt==-1 || $window<=$setWin_lt) )) && ( ($setbal_gt==-1 && $setbal_lt==-1) || ( ($setbal_gt==-1 || $balance>=$setbal_gt) && ($setbal_lt==-1 || $balance<=$setbal_lt) )) && validateForNull($emi_date) && $emi_date!="NA")
			{	
			
			$oldest_unpaid_emi=getOldestUnPaidEmiDate($loan_id);
			$last_unpaid_date=$oldest_unpaid_emi['actual_emi_date'];
			$returnArray[$j]['file_id']=$file_id;
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['file_status']=$file_status;
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['emi_date']=$oldest_unpaid_emi['actual_emi_date'];
			$returnArray[$j]['payment_date']=getLatestPaymentDateForLoan($loan_id);
			$returnArray[$j]['emi_date_array']=$reportRow['loan_emi_date_array'];
			$returnArray[$j]['emi']=$emi;
			$returnArray[$j]['loan_scheme']=getLoanScheme($loan_id);
			$returnArray[$j]['balance']=$balance;
			$returnArray[$j]['agency_id']=$file_agency_id;
			$returnArray[$j]['oc_id']=$file_oc_id;
			$returnArray[$j]['window']=number_format($window,1);
			$returnArray[$j]['bucket_details']=$bucket_details;
			$customer=getCustomerDetailsByFileId($file_id);
			$returnArray[$j]['customer']=$customer;
			}
			$j++;
			}
		
		uasort($returnArray,'EMIDatesComparatorForEmiReports');
		return $returnArray;	
		
		}
		

}


function generalEMIReportsWidget($from=null,$to=null,$win=0.1,$city_id=null,$area_id=null,$file_status=null,$agency_id=null)
{

	$returnArray=array();
	$j=0;
	while($j<6)
	{
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
	$agency_id=substr($agency_id,2);
	if($type=="ag")
	{
	$agency_id=$agency_id;
	$our_company_id="NULL";
	}
	else if($type=="oc")
	{
	$our_company_id=$agency_id;
	$agency_id="NULL";	
	}
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
		
		$sql="SELECT fin_file.file_id, fin_loan.loan_id, file_number, emi,
			  GROUP_CONCAT(loan_emi_id) as loan_emi_array,
			  GROUP_CONCAT(actual_emi_date) as loan_emi_date_array,
			  actual_emi_date,
			  max(actual_emi_date) as last_emi_date
			  FROM 
			  fin_file, fin_loan, fin_loan_emi, fin_customer
			  WHERE fin_loan.loan_id=fin_loan_emi.loan_id
			  AND file_status!=3
			  AND loan_scheme=1
			  AND "; 
	if($our_company_id=="NULL" && is_numeric($agency_id))
	{
		$sql=$sql."agency_id=$agency_id AND ";
	}
	if($agency_id=="NULL" && is_numeric($our_company_id))
	{
		$sql=$sql."oc_id=$our_company_id AND ";
	}
		if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2 || $file_status==4))
		$sql=$sql."file_status =$file_status 
			  AND ";	  
		if(isset($from) && validateForNull($from))
		$sql=$sql."actual_emi_date>='$from' 
			  AND ";
		if(isset($to) && validateForNull($to))  
		$sql=$sql."actual_emi_date<='$to'
			  AND ";
		if(isset($city_id) && validateForNull($city_id))  
		$sql=$sql." city_id=$city_id
			  AND ";
		if(isset($area_id) && validateForNull($area_id))  
		$sql=$sql." area_id= $area_id
			  AND ";	  	  	  
		$sql=$sql."	 fin_loan.file_id=fin_file.file_id
			  AND fin_customer.file_id=fin_file.file_id
			  AND our_company_id=$oc_id 
			  GROUP BY fin_file.file_id
			  ORDER BY fin_file.file_id LIMIT 0,25";
		
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		$today=date('Y-m-d');
	$loan_emi_array_array=array();
	foreach($resultArray as $resulta)
	{
	$file_id=$resulta['file_id'];	
	$sql="SELECT 
		  GROUP_CONCAT(loan_emi_id) as loan_emi_array,
		  max(actual_emi_date) as last_emi_date
	 	  FROM 
	      fin_file, fin_loan, fin_loan_emi, fin_customer
		  WHERE fin_loan.loan_id=fin_loan_emi.loan_id
		  AND file_status!=3
		  AND "; 
		  if($our_company_id=="NULL" && is_numeric($agency_id))
			{
			$sql=$sql."agency_id=$agency_id AND ";
			}
			if($agency_id=="NULL" && is_numeric($our_company_id))
			{
			$sql=$sql."oc_id=$our_company_id AND ";
			}
			if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}
			$sql=$sql."actual_emi_date<='$today'
				  AND";
			if(isset($city_id) && validateForNull($city_id))  
			$sql=$sql." city_id=$city_id
				  AND ";
			if(isset($area_id) && validateForNull($area_id))  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	  	  	  
			$sql=$sql."	fin_loan.file_id=fin_file.file_id
				  AND fin_customer.file_id=fin_file.file_id
				  AND our_company_id=$oc_id
				  AND fin_file.file_id=$file_id
				  GROUP BY fin_file.file_id";
	
			$result2=dbQuery($sql);
			$resultArray2=dbResultToArray($result2);
			
			if(isset($resultArray2[0]))
			$loan_emi_array_array[]=$resultArray2[0];
			else
			$loan_emi_array_array[]=array();
	}
			
			
	
			
			if(dbNumRows($result)>0)
			{
				for($xy=0;$xy<count($resultArray);$xy++)
				{	
					$reportRow=$resultArray[$xy];
					$file_id=$reportRow['file_id'];
					$loan_id=$reportRow['loan_id'];
					$file_number=$reportRow['file_number'];
					$emi=$reportRow['emi'];
					if(isset($loan_emi_array_array[$xy]['loan_emi_array']))
					{
					$loan_emi_array=explode(",",$loan_emi_array_array[$xy]['loan_emi_array']);
					$emi_date=$loan_emi_array_array[$xy]['last_emi_date'];
					}
					else
					{
						$loan_emi_array=null;
						$emi_date=explode(",",$reportRow['loan_emi_date_array']);
						$emi_date=$emi_date[0];
						}
					$loan_emi_date_array=$reportRow['loan_emi_date_array'];
					
					if(isset($loan_emi_array))
					{
					$totalPayment=getTotalPaymentFroEmiIds($loan_emi_array);
					$totalEMIs=count($loan_emi_array);
					$totalActualPayment=$totalEMIs*$emi;
					$balance=$totalActualPayment-$totalPayment;
					$window=$balance/$emi;
					}
					else
					{
					$totalPayment=0;
					$totalEMIs=0;
					$totalActualPayment=$totalEMIs*$emi;
					$balance=$totalActualPayment-$totalPayment;
					$window=$balance/$emi;
					}
					if($file_status==4)
					{
						$balance=0;
						$window=0;
						}
					
					
					$customer=getCustomerDetailsByFileId($file_id);
					if(isset($win) && validateForNull($win))
					{
						$setWin=$win;
						}
					else
					$setWin=-1;
					if(($setWin==-1 || $window>=$setWin) && validateForNull($emi_date) && $emi_date!="NA")
					{	
					
					
						
					$returnArray[$j]['file_id']=$file_id;
					
					$oldest_unpaid_emi=getOldestUnPaidEmiDate($loan_id);
					$upcoming_emi_date=getNearestEMIDateFromToday($loan_id);
					$returnArray[$j]['file_no']=$file_number;
					$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
					$returnArray[$j]['emi_date']=$oldest_unpaid_emi['actual_emi_date'];
					$returnArray[$j]['upcoming_emi_date']=$upcoming_emi_date;
					$returnArray[$j]['emi_date_array']=$reportRow['loan_emi_date_array'];
					$returnArray[$j]['emi']=$emi;
					$returnArray[$j]['balance']=$balance;
					$returnArray[$j]['window']=number_format($window,1);
					$returnArray[$j]['customer']=$customer;
					$j++;
					}
					
					}
	}
		uasort($returnArray,'EMIDatesComparatorForEmiReports');
		return $returnArray;	
		}
	
}

	
function generalInsuranceReports($from=null,$to=null,$city_id=null,$area_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
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
	$today=date('Y-m-d');
	$oc_id=$_SESSION['adminSession']['oc_id'];

	$sql="SELECT fin_file.file_id, fin_vehicle_insurance.insurance_id, file_number, insurance_expiry_date
	 	  FROM 
	      fin_vehicle_insurance
		  INNER JOIN fin_file
          ON fin_vehicle_insurance.file_id = fin_file.file_id
		   INNER JOIN fin_customer
          ON fin_customer.file_id = fin_file.file_id
		  INNER JOIN (SELECT  max(insurance_expiry_date) as max_expiry_date,file_id FROM fin_vehicle_insurance GROUP BY file_id)s
		  ON  fin_vehicle_insurance.file_id=s.file_id
		  WHERE
		  fin_vehicle_insurance.insurance_expiry_date=s.max_expiry_date
		  AND file_status!=3
		  AND "; 
	if(isset($from) && validateForNull($from))
	$sql=$sql."insurance_expiry_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."insurance_expiry_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id) )  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	 	  	  
	$sql=$sql."	our_company_id=$oc_id 
		  GROUP BY fin_file.file_id
		  ORDER BY insurance_expiry_date";
	
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);

	$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$reportRow=$resultArray[$xy];
			
			$file_id=$reportRow['file_id'];
			$insurance_id=$reportRow['insurance_id'];
			$file_number=$reportRow['file_number'];
			$insurance_expiry_date=$reportRow['insurance_expiry_date'];
			$insurance=getInsuranceDetailsFromInsuranceId($insurance_id);
			$customer=getCustomerDetailsByFileId($file_id);
			$returnArray[$j]['file_id']=$file_id;
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['insurance_expiry_date']=date('Y-m-d',strtotime($reportRow['insurance_expiry_date']));
			$returnArray[$j]['insurance']=$insurance;
			$returnArray[$j]['customer']=$customer;
			$j++;
			}
		return $returnArray;	
		}
	return $returnArray;		
}

function generalInsuranceReportsWidget($from=null,$to=null,$city_id=null,$area_id=null)
{
	
	$today=date('Y-m-d');
	
	$to = new DateTime(date('Y-m-d'));
	$to->add(new DateInterval('P30D'));
	$to=$to->format('Y-m-d');
	$oc_id=$_SESSION['adminSession']['oc_id'];

	$sql="SELECT fin_file.file_id, fin_vehicle_insurance.insurance_id, file_number, insurance_expiry_date
	 	  FROM 
	      fin_vehicle_insurance
		  INNER JOIN fin_file
          ON fin_vehicle_insurance.file_id = fin_file.file_id
		  INNER JOIN (SELECT  max(insurance_expiry_date) as max_expiry_date,file_id FROM fin_vehicle_insurance GROUP BY file_id)s
		  ON  fin_vehicle_insurance.file_id=s.file_id
		  WHERE
		  fin_vehicle_insurance.insurance_expiry_date=s.max_expiry_date
		  AND
		  insurance_expiry_date>='$today'
		  AND file_status!=3
		  AND "; 
	if(isset($from) && validateForNull($from))
	$sql=$sql."insurance_expiry_date>='$today' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."insurance_expiry_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id))  
	$sql=$sql." city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id))  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	  	  	  
	$sql=$sql."	our_company_id=$oc_id 
		  GROUP BY fin_file.file_id
		  ORDER BY insurance_expiry_date
		  LIMIT 0,5";
	
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$reportRow=$resultArray[$xy];
			
			$file_id=$reportRow['file_id'];
			$insurance_id=$reportRow['insurance_id'];
			$file_number=$reportRow['file_number'];
			$insurance_expiry_date=$reportRow['insurance_expiry_date'];
			$insurance=getInsuranceDetailsFromInsuranceId($insurance_id);
			$customer=getCustomerDetailsByFileId($file_id);
			$returnArray[$j]['file_id']=$file_id;
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['insurance_expiry_date']=date('d/m/Y',strtotime($reportRow['insurance_expiry_date']));
			$returnArray[$j]['insurance']=$insurance;
			$returnArray[$j]['customer']=$customer;
			$j++;
			}
		return $returnArray;	
		}
		
}		

function expiredInsuranceReports($from=null,$to=null,$city_id=null,$area_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
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
	$today=date('Y-m-d');
	$oc_id=$_SESSION['adminSession']['oc_id'];

	$sql="SELECT fin_file.file_id, fin_vehicle_insurance.insurance_id, file_number, 
		  max(insurance_expiry_date) as insurance_expiry_date
	 	  FROM 
	      fin_file, fin_customer, fin_vehicle_insurance
		  WHERE insurance_expiry_date<='$today'
		  AND file_status!=3
		  AND "; 
	if(isset($from) && validateForNull($from))
	$sql=$sql."insurance_expiry_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."insurance_expiry_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id))  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	  	  	  
	$sql=$sql."	
		  fin_vehicle_insurance.file_id=fin_file.file_id
		  AND fin_customer.file_id=fin_file.file_id
		  AND our_company_id=$oc_id 
		  GROUP BY fin_file.file_id
		  ORDER BY insurance_expiry_date";
	
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	
	
	
	
	$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$reportRow=$resultArray[$xy];
			
			$file_id=$reportRow['file_id'];
			$insurance_id=$reportRow['insurance_id'];
			$file_number=$reportRow['file_number'];
			$insurance_expiry_date=$reportRow['insurance_expiry_date'];
			$insurance=getInsuranceDetailsFromInsuranceId($insurance_id);
			$customer=getCustomerDetailsByFileId($file_id);
			
			if($insurance_id==getLatestInsuranceIdForFileID($file_id))
			{
			$returnArray[$j]['file_id']=$file_id;
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['insurance_expiry_date']=date('Y-m-d',strtotime($reportRow['insurance_expiry_date']));
			$returnArray[$j]['insurance']=$insurance;
			$returnArray[$j]['customer']=$customer;
			$j++;
			}
			}
		return $returnArray;	
		}
	return $returnArray;	
}		

function expiredInsuranceReportsWidget($from=null,$to=null,$city_id=null,$area_id=null)
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
	$today=date('Y-m-d');
	$oc_id=$_SESSION['adminSession']['oc_id'];

	$sql="SELECT fin_file.file_id, fin_vehicle_insurance.insurance_id, file_number, insurance_expiry_date
	 	  FROM 
	      fin_vehicle_insurance
		  INNER JOIN fin_file
          ON fin_vehicle_insurance.file_id = fin_file.file_id
		  INNER JOIN (SELECT  max(insurance_expiry_date) as max_expiry_date,file_id FROM fin_vehicle_insurance GROUP BY file_id)s
		  ON  fin_vehicle_insurance.file_id=s.file_id
		  WHERE
		  fin_vehicle_insurance.insurance_expiry_date=s.max_expiry_date
		  AND
		  file_status!=3
		  AND 
		  insurance_expiry_date<='$today'
		  AND our_company_id=$oc_id 
		  GROUP BY fin_file.file_id
		  ORDER BY insurance_expiry_date
		  LIMIT 0,5";
	
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	
	
	
	$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			
			$reportRow=$resultArray[$xy];
			
			$file_id=$reportRow['file_id'];
			$insurance_id=$reportRow['insurance_id'];
			$file_number=$reportRow['file_number'];
			$insurance_expiry_date=$reportRow['insurance_expiry_date'];
			$insurance=getInsuranceDetailsFromInsuranceId($insurance_id);
			$customer=getCustomerDetailsByFileId($file_id);
			
			
			$returnArray[$xy]['file_id']=$file_id;
			$returnArray[$xy]['file_no']=$file_number;
			$returnArray[$xy]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$xy]['insurance_expiry_date']=date('d/m/Y',strtotime($reportRow['insurance_expiry_date']));
			$returnArray[$xy]['insurance']=$insurance;
			$returnArray[$xy]['customer']=$customer;
			
			}
		return $returnArray;	
		}
return $returnArray;		
}			
function generalRasidReports($from=null,$to=null,$city_id=null,$area_id=null,$agency_id=null,$payment_mode=null){
	
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
	
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
	$today=date('Y-m-d');
	$oc_id=$_SESSION['adminSession']['oc_id'];
	
	$sql="SELECT emi_payment_id, file_number, file_agreement_no, fin_file.agency_id,fin_file.oc_id, SUM(payment_amount) as payment_amt,fin_loan_emi_payment.paid_by, payment_mode, payment_date, rasid_no, GROUP_CONCAT(fin_loan_emi.loan_emi_id) as loan_emi_array, fin_loan_emi.loan_id, fin_file.file_id
	      FROM fin_loan_emi_payment, fin_loan_emi, fin_loan, fin_file, fin_customer
		  WHERE ";
	if($our_company_id=="NULL" && is_numeric($agency_id))
	{
	$sql=$sql."agency_id=$agency_id AND ";
	}
	if($agency_id=="NULL" && is_numeric($our_company_id))
	{
	$sql=$sql."oc_id=$our_company_id AND ";
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."payment_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."payment_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id) )  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  
	if(isset($payment_mode) && validateForNull($payment_mode) && checkForNumeric($payment_mode))  
	$sql=$sql." payment_mode= $payment_mode
		  AND ";
		  	  	  
	$sql=$sql."	 
		  file_status!=3
	 	  AND fin_loan.file_id=fin_file.file_id		
		  AND fin_customer.file_id=fin_file.file_id
		  AND fin_loan_emi_payment.loan_emi_id=fin_loan_emi.loan_emi_id
		  AND fin_loan_emi.loan_id=fin_loan.loan_id
		  AND our_company_id=$oc_id 
		  GROUP BY rasid_no,loan_id,fin_loan_emi_payment.date_added,fin_customer.customer_id";
	
	$sql=$sql." UNION SELECT penalty_id, file_number, file_agreement_no, fin_file.agency_id,fin_file.oc_id, total_amount as payment_amt,fin_loan_penalty.paid_by, payment_mode, paid_date, rasid_no, 0 as loan_emi_array, fin_loan_penalty.loan_id, fin_file.file_id
	      FROM fin_loan_penalty, fin_loan, fin_file, fin_customer
		  WHERE ";
	if($our_company_id=="NULL" && is_numeric($agency_id))
	{
	$sql=$sql."agency_id=$agency_id AND ";
	}
	if($agency_id=="NULL" && is_numeric($our_company_id))
	{
	$sql=$sql."oc_id=$our_company_id AND ";
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."paid_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."paid_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id) )  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  
	if(isset($payment_mode) && validateForNull($payment_mode) && checkForNumeric($payment_mode))  
	$sql=$sql." payment_mode= $payment_mode
		  AND ";
		  	  	  
	$sql=$sql."	 
		  file_status!=3
	 	  AND fin_loan.file_id=fin_file.file_id		
		  AND fin_customer.file_id=fin_file.file_id
		  AND fin_loan_penalty.loan_id=fin_loan.loan_id
		  AND our_company_id=$oc_id ";	  	
		  
	$sql=$sql." UNION SELECT file_closed_id, file_number, file_agreement_no, fin_file.agency_id,fin_file.oc_id, amount_paid as payment_amt ,-1 as paid_by, mode, file_close_date, rasid_no, -1  as loan_emi_array,  fin_file_closed.loan_id, fin_file.file_id
	      FROM fin_file_closed, fin_loan, fin_file, fin_customer
		  WHERE ";
	if($our_company_id=="NULL" && is_numeric($agency_id))
	{
	$sql=$sql."agency_id=$agency_id AND ";
	}
	if($agency_id=="NULL" && is_numeric($our_company_id))
	{
	$sql=$sql."oc_id=$our_company_id AND ";
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."file_close_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."file_close_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id) )  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  
	if(isset($payment_mode) && validateForNull($payment_mode) && checkForNumeric($payment_mode))  
	$sql=$sql." mode= $payment_mode
		  AND ";
		  	  	  
	$sql=$sql."	 
		  file_status!=3
	 	  AND fin_loan.file_id=fin_file.file_id		
		  AND fin_customer.file_id=fin_file.file_id
		  AND fin_file_closed.loan_id=fin_loan.loan_id
		  AND our_company_id=$oc_id ";	  		    
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	return $resultArray;	  
	
}

function generalRasidReportsEntry($from=null,$to=null,$city_id=null,$area_id=null,$agency_id=null,$payment_mode=null){
	
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
	
	if(isset($from) && validateForNull($from))
	{
	$from = str_replace('/', '-', $from);
		$from=date('Y-m-d',strtotime($from));
		$from=$from." 00:00:00";
	}
if(isset($to) && validateForNull($to))
	{
	$to = str_replace('/', '-', $to);
		$to=date('Y-m-d',strtotime($to));
		$to=$to." 23:59:59";
	}	
	$today=date('Y-m-d');
	$oc_id=$_SESSION['adminSession']['oc_id'];
	
	$sql="SELECT emi_payment_id, file_number, file_agreement_no, fin_file.agency_id,fin_file.oc_id, SUM(payment_amount) as payment_amt,fin_loan_emi_payment.paid_by, payment_mode, payment_date, rasid_no, GROUP_CONCAT(fin_loan_emi.loan_emi_id) as loan_emi_array, fin_loan_emi.loan_id, fin_file.file_id, fin_loan_emi_payment.date_added as date_added
	      FROM fin_loan_emi_payment, fin_loan_emi, fin_loan, fin_file, fin_customer
		  WHERE ";
	if($our_company_id=="NULL" && is_numeric($agency_id))
	{
	$sql=$sql."agency_id=$agency_id AND ";
	}
	if($agency_id=="NULL" && is_numeric($our_company_id))
	{
	$sql=$sql."oc_id=$our_company_id AND ";
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."fin_loan_emi_payment.date_added>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."fin_loan_emi_payment.date_added<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id) )  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  
	if(isset($payment_mode) && validateForNull($payment_mode) && checkForNumeric($payment_mode))  
	$sql=$sql." payment_mode= $payment_mode
		  AND ";
		  	  	  
	$sql=$sql."	 
		  file_status!=3
	 	  AND fin_loan.file_id=fin_file.file_id		
		  AND fin_customer.file_id=fin_file.file_id
		  AND fin_loan_emi_payment.loan_emi_id=fin_loan_emi.loan_emi_id
		  AND fin_loan_emi.loan_id=fin_loan.loan_id
		  AND our_company_id=$oc_id 
		  GROUP BY rasid_no,loan_id,fin_loan_emi_payment.date_added,fin_customer.customer_id";
	
	$sql=$sql." UNION SELECT penalty_id, file_number, file_agreement_no, fin_file.agency_id,fin_file.oc_id, (days_paid*amount_per_day) as payment_amt,fin_loan_penalty.paid_by, payment_mode, paid_date, rasid_no, 0 as loan_emi_array, fin_loan_penalty.loan_id, fin_file.file_id, fin_loan_penalty.date_added as date_added
	      FROM fin_loan_penalty, fin_loan, fin_file, fin_customer
		  WHERE ";
	if($our_company_id=="NULL" && is_numeric($agency_id))
	{
	$sql=$sql."agency_id=$agency_id AND ";
	}
	if($agency_id=="NULL" && is_numeric($our_company_id))
	{
	$sql=$sql."oc_id=$our_company_id AND ";
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."fin_loan_penalty.date_added>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."fin_loan_penalty.date_added<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id) )  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  
	if(isset($payment_mode) && validateForNull($payment_mode) && checkForNumeric($payment_mode))  
	$sql=$sql." payment_mode= $payment_mode
		  AND ";
		  	  	  
	$sql=$sql."	 
		  file_status!=3
	 	  AND fin_loan.file_id=fin_file.file_id		
		  AND fin_customer.file_id=fin_file.file_id
		  AND fin_loan_penalty.loan_id=fin_loan.loan_id
		  AND our_company_id=$oc_id ";	  	
		  
	$sql=$sql." UNION SELECT file_closed_id, file_number, file_agreement_no, fin_file.agency_id,fin_file.oc_id, amount_paid as payment_amt ,-1 as paid_by, mode, file_close_date, rasid_no, -1  as loan_emi_array,  fin_file_closed.loan_id, fin_file.file_id, fin_file_closed.date_closed as date_added
	      FROM fin_file_closed, fin_loan, fin_file, fin_customer
		  WHERE ";
	if($our_company_id=="NULL" && is_numeric($agency_id))
	{
	$sql=$sql."agency_id=$agency_id AND ";
	}
	if($agency_id=="NULL" && is_numeric($our_company_id))
	{
	$sql=$sql."oc_id=$our_company_id AND ";
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."date_closed>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."date_closed<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id) )  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  
	if(isset($payment_mode) && validateForNull($payment_mode) && checkForNumeric($payment_mode))  
	$sql=$sql." mode= $payment_mode
		  AND ";
		  	  	  
	$sql=$sql."	 
		  file_status!=3
	 	  AND fin_loan.file_id=fin_file.file_id		
		  AND fin_customer.file_id=fin_file.file_id
		  AND fin_file_closed.loan_id=fin_loan.loan_id
		  AND our_company_id=$oc_id ";
	
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	return $resultArray;	  
	
}	


function generalRemianderReports($from=null,$to=null,$remainder_status=0,$city_id=null,$area_id=null,$file_status=null,$agency_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
	
$sql="SELECT remainder_id as id, date as date, remarks, fin_file.file_id, file_number, IF(1 = 1, 'general', ' ') AS remainder_type, remainder_status
      FROM fin_file_remainder
	  INNER JOIN fin_file
	  ON fin_file.file_id=fin_file_remainder.file_id
	  INNER JOIN fin_customer
	  ON fin_customer.file_id=fin_file.file_id
	  WHERE 1 AND file_status!=3
		  AND 
	  ";
if(is_numeric($remainder_status))
{
	$sql=$sql."remainder_status=$remainder_status AND ";
}	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}  
	if(isset($from) && validateForNull($from))
	$sql=$sql."date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql."city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id) )  
	$sql=$sql." area_id IN ($area_id)
		  AND ";  	  	  
	$sql=$sql."	 our_company_id=$oc_id";	 

$sql.=" UNION 
		SELECT emi_payment_id as id, remainder_date as date, remarks, fin_file.file_id, file_number, IF(1 = 1, 'payment', ' ') AS remainder_type, remainder_status
		FROM fin_loan_emi_payment
		INNER JOIN fin_loan_emi
		ON fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
		INNER JOIN fin_loan
		ON fin_loan_emi.loan_id=fin_loan.loan_id
		INNER JOIN fin_file
		ON fin_loan.file_id=fin_file.file_id
		INNER JOIN fin_customer
		ON fin_customer.file_id=fin_file.file_id
		WHERE (remarks!='' OR (remainder_date!='0000-00-00' AND remainder_date!='1970-01-01')) AND ";	
if(is_numeric($remainder_status))
{
	$sql=$sql."remainder_status=$remainder_status AND ";
}	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	$sql=$sql."file_status =$file_status 
		  AND ";	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."remainder_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."remainder_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id))  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  	  	  
	$sql=$sql."	file_status!=3
		  AND  our_company_id=$oc_id 
	GROUP BY rasid_no,fin_loan_emi_payment.date_added
		  ORDER BY date";
	if(strtotime($to)==strtotime(date('Y-m-d')))
	{
		$sql.=" desc";
		}	  	 
		  			  
$result=dbQuery($sql);

$resultArray=dbResultToArray($result);

$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$reportRow=$resultArray[$xy];
			$file_id=$reportRow['file_id'];
			$file_number=$reportRow['file_number'];
			
			
			
			
			$customer=getCustomerDetailsByFileId($file_id);
			
			$returnArray[$j]['id']=$reportRow['id'];	
			$returnArray[$j]['file_id']=$file_id;
			$returnArray[$j]['type']=$reportRow['remainder_type'];
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['date']=$reportRow['date'];
			$returnArray[$j]['remarks']=$reportRow['remarks'];
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['customer']=$customer;
			$j++;
			}
		return $returnArray;	
		
		}
}	

function generalRemianderReportsWidget($from=null,$to=null,$remainder_status=0,$city_id=null,$area_id=null,$file_status=null,$agency_id=null)
{
	
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
	
$sql="SELECT remainder_id as id, date as date, remarks, fin_file.file_id, file_number, IF(1 = 1, 'general', ' ') AS remainder_type, remainder_status
      FROM fin_file_remainder
	  INNER JOIN fin_file
	  ON fin_file.file_id=fin_file_remainder.file_id
	  WHERE 1 AND file_status!=3
		  AND 
	  ";
if(is_numeric($remainder_status))
{
	$sql=$sql."remainder_status=$remainder_status AND ";
}	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	} 
	if(isset($from) && validateForNull($from))
	$sql=$sql."date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id))  
	$sql=$sql." city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id))  
			$sql=$sql." area_id IN ($area_id)
				  AND ";		  	  	  
	$sql=$sql."	 our_company_id=$oc_id";	 

/*$sql.=" UNION 
		SELECT emi_payment_id as id, remainder_date as date, remarks, fin_file.file_id, file_number, IF(1 = 1, 'payment', ' ') AS remainder_type, remainder_status
		FROM fin_loan_emi_payment
		INNER JOIN fin_loan_emi
		ON fin_loan_emi.loan_emi_id=fin_loan_emi_payment.loan_emi_id
		INNER JOIN fin_loan
		ON fin_loan_emi.loan_id=fin_loan.loan_id
		INNER JOIN fin_file
		ON fin_loan.file_id=fin_file.file_id
		WHERE (remarks!='' OR (remainder_date!='0000-00-00' AND remainder_date!='1970-01-01')) AND ";	
if(is_numeric($remainder_status))
{
	$sql=$sql."remainder_status=$remainder_status AND ";
}	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	$sql=$sql."file_status =$file_status 
		  AND ";	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."remainder_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."remainder_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id))  
	$sql=$sql." area_id= $area_id
		  AND GROUP BY rasid_no,fin_loan_emi_payment.date_added"; */	  	  	  
	$sql=$sql." AND	file_status!=3
		  AND  our_company_id=$oc_id 
		  ORDER BY date"; 
	if(strtotime($to)==strtotime(date('Y-m-d')))
	{
		$sql.=" desc ";
		}	 	  
	$sql.=" LIMIT 0,5";
			  
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);

$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$reportRow=$resultArray[$xy];
			$file_id=$reportRow['file_id'];
			$file_number=$reportRow['file_number'];
			$customer=getCustomerDetailsByFileId($file_id);
			$returnArray[$j]['id']=$reportRow['id'];	
			$returnArray[$j]['type']=$reportRow['remainder_type'];	
			$returnArray[$j]['file_id']=$file_id;
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['date']=$reportRow['date'];
			$returnArray[$j]['remarks']=$reportRow['remarks'];
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['customer']=$customer;
			$j++;
			}	
		return $returnArray;	
		
		}
}	

function EMIDatesComparatorForEmiReports($a,$b){
	$aEMIDate=$a['emi_date'];
	$bEMIDate=$b['emi_date'];
	$aEMIDate = str_replace('/', '-', $aEMIDate);
	$aEMIDate=date('Y-m-d',strtotime($aEMIDate));
	$bEMIDate = str_replace('/', '-', $bEMIDate);
	$bEMIDate=date('Y-m-d',strtotime($bEMIDate));
if (strtotime($aEMIDate) < strtotime($bEMIDate)) return -1;
if (strtotime($aEMIDate) > strtotime($bEMIDate)) return 1;
return 0;
}

function EMIPaymentDatesComparatorForEmiReports($a,$b){
	$aEMIDate=$a['payment_date'];
	$bEMIDate=$b['payment_date'];
	$aEMIDate = str_replace('/', '-', $aEMIDate);
	$aEMIDate=date('Y-m-d',strtotime($aEMIDate));
	$bEMIDate = str_replace('/', '-', $bEMIDate);
	$bEMIDate=date('Y-m-d',strtotime($bEMIDate));
if (strtotime($aEMIDate) < strtotime($bEMIDate)) return -1;
if (strtotime($aEMIDate) > strtotime($bEMIDate)) return 1;
return 0;
}

function EMIDatesComparatorForEmiReportsUpcomingDate($a,$b){
	$aEMIDate=$a['upcoming_emi_date'];
	$bEMIDate=$b['upcoming_emi_date'];
	$aEMIDate = str_replace('/', '-', $aEMIDate);
	$aEMIDate=date('Y-m-d',strtotime($aEMIDate));
	$bEMIDate = str_replace('/', '-', $bEMIDate);
	$bEMIDate=date('Y-m-d',strtotime($bEMIDate));
if (strtotime($aEMIDate) < strtotime($bEMIDate)) return -1;
if (strtotime($aEMIDate) > strtotime($bEMIDate)) return 1;
return 0;
}

function generalFileReports($from=null,$to=null,$city_id=null,$area_id=null,$file_status=null,$agency_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
	
$sql="SELECT fin_file.file_id , file_number, fin_file.date_added, fin_file.created_by, admin_username,city_id
      FROM fin_file
	  INNER JOIN fin_customer
	  ON fin_customer.file_id=fin_file.file_id
	  INNER JOIN fin_admin
	  ON fin_file.created_by=admin_id
	  WHERE 1 AND file_status!=3
		  AND 
	  ";	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}  
	if(isset($from) && validateForNull($from))
	$sql=$sql."fin_file.date_added>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."fin_file.date_added<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql."city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id) )  
			$sql=$sql." area_id IN ($area_id)
				  AND ";		  	  	  
	$sql=$sql."	 our_company_id=$oc_id
	ORDER BY fin_file.date_added DESC";	 


 
		  			  
$result=dbQuery($sql);

$resultArray=dbResultToArray($result);


$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$reportRow=$resultArray[$xy];
			$file_id=$reportRow['file_id'];
			$file_number=$reportRow['file_number'];
			
			
			
			
			$customer=getCustomerDetailsByFileId($file_id);
			$city=getCityByID($reportRow['city_id']);
			$returnArray[$j]['date_added']=$reportRow['date_added'];
			$returnArray[$j]['city']=$city['city_name'];
			$returnArray[$j]['username']=$reportRow['admin_username'];
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['file_id']=$reportRow['file_id'];
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['customer']=$customer;
			$j++;
			}
		return $returnArray;	
		
		}
}	

function generalLoanReports($from=null,$to=null,$city_id=null,$area_id=null,$file_status=null,$agency_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();	
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
	
$sql="SELECT fin_file.file_id , fin_loan.loan_id, file_number, loan_amount,loan_scheme, roi, emi, loan_approval_date,loan_starting_date,loan_duration
      FROM fin_file
	  INNER JOIN fin_customer
	  ON fin_customer.file_id=fin_file.file_id
	  INNER JOIN fin_loan
	  ON fin_file.file_id=fin_loan.file_id
	  WHERE 1 AND file_status!=3
		  AND 
	  ";	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."loan_starting_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."loan_starting_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql."city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id) )  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	  	  	  
	$sql=$sql."	 our_company_id=$oc_id
	ORDER BY fin_file.date_added DESC";	 

 
		  			  
$result=dbQuery($sql);

$resultArray=dbResultToArray($result);

$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$reportRow=$resultArray[$xy];
			$file_id=$reportRow['file_id'];
			$file_number=$reportRow['file_number'];
			
			
			
			
			$customer=getCustomerDetailsByFileId($file_id);
			
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['file_id']=$reportRow['file_id'];
			$returnArray[$j]['loan_amount']=$reportRow['loan_amount'];
			$returnArray[$j]['loan_duration']=$reportRow['loan_duration'];
			$returnArray[$j]['loan_scheme']=$reportRow['loan_scheme'];
			$returnArray[$j]['scheme']=getLoanScheme($reportRow['loan_id']);
			$returnArray[$j]['emi']=$reportRow['emi'];
			$returnArray[$j]['roi']=$reportRow['roi'];
			$returnArray[$j]['loan_starting_date']=$reportRow['loan_starting_date'];
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['customer']=$customer;
			$j++;
			}
		return $returnArray;	
		
		}
}
function generalChequeReports($from=null,$to=null,$city_id=null,$area_id=null,$file_status=null,$agency_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
	
$sql="SELECT fin_file.file_id , file_number, fin_loan_emi_payment.emi_payment_id as id,fin_loan_emi_payment_cheque.bank_id,fin_loan_emi_payment_cheque.branch_id,fin_loan_emi_payment_cheque.cheque_date,fin_loan_emi_payment_cheque.cheque_no, IF(1=1, 'Payment', '') as type
      FROM fin_loan_emi_payment_cheque
	  INNER JOIN fin_loan_emi_payment
	  ON fin_loan_emi_payment.emi_payment_id=fin_loan_emi_payment_cheque.emi_payment_id
	  INNER JOIN fin_loan_emi
	  ON fin_loan_emi_payment.loan_emi_id=fin_loan_emi.loan_emi_id
	  INNER JOIN fin_loan
	  ON fin_loan_emi.loan_id=fin_loan.loan_id
	  INNER JOIN fin_file
	  ON fin_loan.file_id=fin_file.file_id
	  INNER JOIN fin_customer
	  ON fin_customer.file_id=fin_file.file_id
	  WHERE 1 AND file_status!=3
		  AND 
	  ";	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}  
	if(isset($from) && validateForNull($from))
	$sql=$sql."cheque_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."cheque_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql."city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id) )  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	 	  	  
	$sql=$sql."	 our_company_id=$oc_id";	 

 $sql=$sql." UNION
      SELECT fin_file.file_id , file_number, fin_loan_penalty.penalty_id as id, fin_loan_penalty_cheque.bank_id, fin_loan_penalty_cheque.branch_id, fin_loan_penalty_cheque.cheque_date, fin_loan_penalty_cheque.cheque_no, IF(1=1, 'Penalty', '') as type
      FROM fin_loan_penalty_cheque
	  INNER JOIN fin_loan_penalty
	  ON fin_loan_penalty.penalty_id=fin_loan_penalty_cheque.penalty_id
	  INNER JOIN fin_loan
	  ON fin_loan_penalty.loan_id=fin_loan.loan_id
	  INNER JOIN fin_file
	  ON fin_loan.file_id=fin_file.file_id
	  INNER JOIN fin_customer
	  ON fin_customer.file_id=fin_file.file_id
	  WHERE 1 AND file_status!=3
		  AND 
	  ";	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	$sql=$sql."file_status =$file_status 
		  AND ";	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."cheque_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."cheque_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql."city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id))  
			$sql=$sql." area_id IN ($area_id)
				  AND ";	 	  	  
	$sql=$sql."	 our_company_id=$oc_id
	";	
	
 $sql=$sql." UNION
      SELECT fin_file.file_id , file_number, fin_loan_cheque.loan_id as id, fin_loan_cheque.bank_id, fin_loan_cheque.branch_id, fin_loan_cheque.loan_cheque_date as cheque_date, fin_loan_cheque.loan_cheque_no as cheque_no, IF(1=1, 'Loan', '') as type
      FROM fin_loan_cheque
	  INNER JOIN fin_loan
	  ON fin_loan_cheque.loan_id=fin_loan.loan_id
	  INNER JOIN fin_file
	  ON fin_loan.file_id=fin_file.file_id
	  INNER JOIN fin_customer
	  ON fin_customer.file_id=fin_file.file_id
	  WHERE 1 AND file_status!=3
		  AND 
	  ";	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."cheque_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."cheque_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql."city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id))  
			$sql=$sql." area_id IN ($area_id)
				  AND ";		  	  	  
	$sql=$sql."	 our_company_id=$oc_id
	ORDER BY cheque_date";		
		  			  
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);

$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$reportRow=$resultArray[$xy];
			$file_id=$reportRow['file_id'];
			$file_number=$reportRow['file_number'];
			
			
			
			
			$customer=getCustomerDetailsByFileId($file_id);
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['file_id']=$reportRow['file_id'];
			$returnArray[$j]['id']=$reportRow['id'];
			if($reportRow['type']=="Payment")
			{
			$returnArray[$j]['type']=$reportRow['type'];
			$returnArray[$j]['amount']=getTotalAmountForPaymentId($reportRow['id']);
			}
			else if($reportRow['type']=="Penalty")
			{
			$returnArray[$j]['type']=$reportRow['type'];
			$returnArray[$j]['amount']=getTotalAmountForPenaltyId($reportRow['id']);
			}
			else if($reportRow['type']=="Loan")
			{
			$returnArray[$j]['type']=$reportRow['type'];
			$returnArray[$j]['amount']=getLoanAmountById($reportRow['id']);
			}
			$returnArray[$j]['cheque_date']=$reportRow['cheque_date'];
			$returnArray[$j]['cheque_no']=$reportRow['cheque_no'];
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['customer']=$customer;
			$j++;
			}
		return $returnArray;	
		
		}
}
function generalAccountReports($from=null,$to=null,$city_id=null,$area_id=null,$file_status=null,$agency_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
	
$sql="SELECT fin_file.file_id , file_number, fin_loan.loan_id
	  FROM fin_file
	  INNER JOIN fin_loan
	  ON fin_file.file_id=fin_loan.file_id
	  INNER JOIN fin_customer
	  ON fin_customer.file_id=fin_file.file_id
	  WHERE 1 AND file_status!=3
		  AND 
	  ";	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}	  
/*	if(isset($from) && validateForNull($from))
	$sql=$sql."actual_emi_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."actual_emi_date<='$to'
		  AND "; */
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql."city_id=$city_id
		  AND ";
		if(isset($area_id) && validateForNull($area_id) )  
			$sql=$sql." area_id IN ($area_id)
				  AND ";		  	  	  
	$sql=$sql."	 our_company_id=$oc_id";		
	
/*$sql="SELECT fin_file.file_id , file_number, fin_loan.loan_id
	  FROM fin_file
	  INNER JOIN fin_loan
	  ON fin_file.file_id=fin_loan.file_id
	  INNER JOIN fin_loan_emi
	  ON fin_loan.loan_id=fin_loan_emi.loan_id
	  INNER JOIN fin_customer
	  ON fin_customer.file_id=fin_file.file_id
	  WHERE 1 AND
	  ";	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	$sql=$sql."file_status =$file_status 
		  AND ";	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."actual_emi_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."actual_emi_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id))  
	$sql=$sql."city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id))  
	$sql=$sql." area_id= $area_id
		  AND ";	  	  	  
	$sql=$sql."	 our_company_id=$oc_id";	 

 $sql=$sql." UNION
      SELECT fin_file.file_id , file_number, fin_loan.loan_id
	  FROM fin_loan_emi_payment
	  INNER JOIN fin_loan_emi
	  ON fin_loan_emi_payment.loan_emi_id=fin_loan_emi.loan_emi_id
	  INNER JOIN fin_loan
	  ON fin_loan_emi.loan_id=fin_loan.loan_id
	  INNER JOIN fin_file
	  ON fin_loan.file_id=fin_file.file_id
	  INNER JOIN fin_customer
	  ON fin_customer.file_id=fin_file.file_id
	  WHERE 1 AND
	  ";	  	
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	$sql=$sql."file_status =$file_status 
		  AND ";	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."payment_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."payment_date<='$to'
		  AND ";
	if(isset($from) && validateForNull($from))
	$sql=$sql."(actual_emi_date<'$from' 
		   ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."OR actual_emi_date>'$to' )
		  AND ";
	else
	$sql=$sql." ) AND";	  	  
	if(isset($city_id) && validateForNull($city_id))  
	$sql=$sql."city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id))  
	$sql=$sql." area_id= $area_id
		  AND ";	  	  	  
	$sql=$sql."	 our_company_id=$oc_id
	ORDER BY file_id";
*/	
		  			  
$result=dbQuery($sql);

$resultArray=dbResultToArray($result);

$returnArray=array();
	$j=0;
	if(dbNumRows($result)>0)
	{
		for($xy=0;$xy<count($resultArray);$xy++)
		{
			$reportRow=$resultArray[$xy];
			$file_id=$reportRow['file_id'];
			$loan_id=$reportRow['loan_id'];
			$file_number=$reportRow['file_number'];
			$customer=getCustomerDetailsByFileId($file_id);
			$opening_balance=getOpeningBalanceForLaonAtDate($loan_id,$from);
			$idealPayment=getIdealPaymentsForLoanIdBetweenDates($loan_id,$from,$to);
			$payments_made=getPaymentsWithinDates($loan_id,$from,$to);
			$closing_balance=$opening_balance-($idealPayment-$payments_made);
			$returnArray[$j]['file_no']=$file_number;
			$returnArray[$j]['file_id']=$reportRow['file_id'];
			$returnArray[$j]['loan_id']=$reportRow['loan_id'];
			$returnArray[$j]['opening']=$opening_balance;
			$returnArray[$j]['ideal']=$idealPayment;
			$returnArray[$j]['payments_made']=$payments_made;
			$returnArray[$j]['closing']=$closing_balance;
			$returnArray[$j]['reg_no']=getRegNoFromFileID($file_id);
			$returnArray[$j]['customer']=$customer;
			$j++;
			}
			
		return $returnArray;	
		
		}
}

function getAllChequeReturnReports()
{
	
	$sql="SELECT payment_cheque_id,cheque_no,bank_id,cheque_return,cheque_date,fin_loan_emi_payment.emi_payment_id,rasid_no,payment_date,fin_loan_emi_payment.loan_emi_id
	      FROM fin_loan_emi_payment_cheque,fin_loan_emi_payment
		  WHERE cheque_return=1
		  AND fin_loan_emi_payment_cheque.emi_payment_id=fin_loan_emi_payment.emi_payment_id";
	 
	$result=dbQuery($sql);	
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		$result2Array2=array();
		$return_array=array();
		foreach($resultArray as $re)
		{
			$emi_payment_id=$re['emi_payment_id'];
			$payment_cheque_id=$re['payment_cheque_id'];
			$cheque_no=$re['cheque_no'];
			$bank_id=$re['bank_id'];
			$loan_id=getLoanIdFromEmiPaymentId($emi_payment_id);
			$sql="SELECT fin_file.file_id,file_number,customer_name,customer_address,customer_contact_no as contact_no
			FROM fin_file,fin_customer,fin_loan,fin_customer_contact_no
			WHERE fin_file.file_id=fin_customer.file_id
			AND fin_customer.customer_id=fin_customer_contact_no.customer_id
			AND fin_file.file_id=fin_loan.file_id
			AND fin_loan.loan_id=$loan_id";
			$result2=dbQuery($sql);
			$result2Array=dbResultToArray($result2);
			$result2Array2[]=$result2Array[0];
			}
			for($i=0;$i<count($resultArray);$i++)
			{
				$cheque_details=null;
				$file_customer_details=null;
				$cheque_details=$resultArray[$i];
				$file_customer_details=$result2Array2[$i];
				$return_array[$i]['cheque_details']=$cheque_details;
				$return_array[$i]['file_customer_details']=$file_customer_details;
			}
		return $return_array;	
		}  
return array();		
}

function generalcollectionReport($from=null,$to=null,$agency_id=null,$city_id=null,$area_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
	$agency_id=substr($agency_id,2);
	if($type=="ag")
	{
	$agency_id=$agency_id;
	$our_company_id="NULL";
	}
	else if($type=="oc")
	{
	$our_company_id=$agency_id;
	$agency_id="NULL";	
	}
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
	
	$sql="SELECT fin_file.file_id, fin_loan.loan_id, file_number, emi, file_status, oc_id, agency_id, loan_scheme,
		  GROUP_CONCAT(loan_emi_id) as loan_emi_array,
		  COUNT(loan_emi_id) as no_of_emis,
		  COUNT(loan_emi_id)*emi as actual_collection
	 	  FROM 
	      fin_file, fin_loan, fin_loan_emi, fin_customer
		  WHERE fin_loan.loan_id=fin_loan_emi.loan_id
		  AND file_status!=3
		  AND "; 
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."actual_emi_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."actual_emi_date<='$to'
		  AND ";  	  
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id))  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  	  	  
	$sql=$sql."	 fin_loan.file_id=fin_file.file_id
		  AND fin_customer.file_id=fin_file.file_id
		  AND our_company_id=$oc_id 
		  GROUP BY fin_file.file_id
		  ORDER BY fin_file.file_id";
	$total_net_collection=0;
	$total_actual_collection=0;
	
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	foreach($resultArray as $re)
	{
		
		$loan_emi_array=$re['loan_emi_array'];
			$actual_collection=0;
		if($re['loan_scheme']==2)
		{
			
			$loan_emi_array_array=explode(",",$loan_emi_array);
			
			if(is_array($loan_emi_array_array))
			{
				
				foreach($loan_emi_array_array as $loan_emi_id)
				{
					
					$e=getEmiForLoanEmiId($loan_emi_id);
					
					$actual_collection=$actual_collection+$e;
					}
				
				}
			}
		else
		{	
		$actual_collection=$re['actual_collection'];
		}
		$total_actual_collection=$total_actual_collection+$re['actual_collection'];
		$sql="SELECT GROUP_CONCAT(emi_payment_id) as payment_id_array,SUM(payment_amount) as total_payment
		      FROM fin_loan_emi_payment
			  WHERE loan_emi_id in ($loan_emi_array)";
		if(isset($from) && validateForNull($from))
	$sql=$sql." AND payment_date>='$from' 
		   ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql." AND payment_date<='$to'
		 ";
		 $result2=dbQuery($sql);
		 $result2Array=dbResultToArray($result2);
		 $actual_payment=$result2Array[0]['total_payment'];
		 
		 $total_net_collection=$total_net_collection+$actual_payment;
		}
		$total_overall_collection=getTotalPaymentsBetweenTwoDates($from,$to,$agency_id,$our_company_id);
		return array($total_actual_collection,$total_net_collection,$total_overall_collection,count($resultArray));
}

function generalCompanyPaidReports($agency_id,$from=null,$to=null)
{
		
	$returnArray=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
	$agency_id=substr($agency_id,2);
	if($type=="ag")
	{
	$agency_id=$agency_id;
	$our_company_id="NULL";
	}
	else if($type=="oc")
	{
	$our_company_id=$agency_id;
	$agency_id="NULL";	
	}
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
	$fileAndLoanIDs=getFileIdsAndLoanIdsForAgencyId($agency_id);
	
	if(count($fileAndLoanIDs)>0)
			{
				$o=0;
			foreach($fileAndLoanIDs as $fileAndLoanID)
			{
				$file_id=$fileAndLoanID['file_id'];
				$loan_id=$fileAndLoanID['loan_id'];
				
				$unpaid_emi_ids=getUnPaidEmisUptillDateForLoanForCompanyPaidReports($loan_id,$to);
				$loan=getLoanById($loan_id);
				if($unpaid_emi_ids!=false && isset($unpaid_emi_ids[0]['loan_emi_id']))
				{
					$loan_scheme=$loan['loan_scheme'];
					$emi=0;
					if($loan_scheme==1)
					{
					$emi=getEmiForLoanId($loan_id);
					$bucket=count($unpaid_emi_ids);
					$balance=$emi*$bucket;
					}
					if($loan_scheme==2)
					{
						$balance=0;
						$bucket=0;
						$bucket_details=getBucketDetailsForEMIIDs($unpaid_emi_ids);
						if(isset($bucket_details) && $bucket_details!=0)
						{
							foreach($bucket_details as $e=>$corr_bucket)
							{
								$balance=$balance+($e*$corr_bucket);
								$bucket=$bucket+$corr_bucket;
								}
							}
					}
					$customer=getCustomerDetailsByFileId($file_id);
					$file=getFileDetailsByFileId($file_id);
					$reg_no=getRegNoFromFileID($file_id);
					$file_number=getFileNumberByFileId($file_id);
					$returnArray[$o]['file']=$file;
					$returnArray[$o]['loan_scheme']=$loan_scheme;
					$returnArray[$o]['scheme']=getLoanScheme($loan_id);
					$returnArray[$o]['bucket_details']=$bucket_details;
					$returnArray[$o]['file_id']=$file_id;
					$returnArray[$o]['file_no']=$file_number;
					$returnArray[$o]['reg_no']=$reg_no;
					$returnArray[$o]['customer']=$customer;
					$returnArray[$o]['emi']=$emi;
					$returnArray[$o]['bucket']=$bucket;
					$returnArray[$o]['balance']=$balance;
					$o++;
				}
				
			}
			}
	
	return $returnArray;
	}
	
function CapitalAndInterestReports($agency_id)
{

	if(validateForNull($agency_id))
	{
		$type=substr($agency_id,0,2);
		$agency_id=substr($agency_id,2);
		
		if($type=="ag")
		{
		$agency_id=$agency_id;
		$our_company_id="NULL";
		$fileAndLoanIDs=getFileIdsAndLoanIdsForAgencyId($agency_id);
		}
		else if($type=="oc")
		{
	
		$our_company_id=$agency_id;
		$agency_id="NULL";	
		$fileAndLoanIDs=getFileIdsAndLoanIdsForOCId($our_company_id);
		}
		
		if(count($fileAndLoanIDs)>0)
			{
				$o=0;
				$total_capital_left=0;
				$tatal_interest_left=0;
				$live_files=0;
			foreach($fileAndLoanIDs as $fileAndLoanID)
			{
				$file_id=$fileAndLoanID['file_id'];
				$loan_id=$fileAndLoanID['loan_id'];
				$file_status=getFileStatusforFile($file_id);
				$loan=null;
				$loan=getLoanById($loan_id);
				$emi=$loan['emi'];
				$loan_amount=$loan['loan_amount'];
				$total_collection=getTotalCollectionForLoan($loan_id);
				$duration=$loan['loan_duration'];
				$emi_without_interest=$loan_amount/$duration;
				$total_interet=$total_collection-$loan_amount;
				$interest=$total_interet/$duration;
				$balance=getBalanceForLoan($loan_id);
				
				if($balance<0 && ($file_status==1 || $file_status==2))
				{
					$live_files++;
					
				
				$paid_emis=getTotalEmiPaidForLoan($loan_id);
				
				$no_of_unpaid_emis=$duration-$paid_emis;
				$unpaid_interest=$interest*$no_of_unpaid_emis;
				$unpaid_capital=-($balance+$unpaid_interest);	
				
					
					$customer=getCustomerDetailsByFileId($file_id);
					$file=getFileDetailsByFileId($file_id);
					$reg_no=getRegNoFromFileID($file_id);
					$file_number=getFileNumberByFileId($file_id);
					$returnArray[$o]['file']=$file;
					$returnArray[$o]['file_id']=$file_id;
					$returnArray[$o]['file_no']=$file_number;
					$returnArray[$o]['reg_no']=$reg_no;
					$returnArray[$o]['customer']=$customer;
					$o++;
			 		$total_capital_left=$total_capital_left+$unpaid_capital;
					$tatal_interest_left=$tatal_interest_left+$unpaid_interest;
				}
			}
		}
	
	return array($live_files,$total_capital_left,$tatal_interest_left);
		
	}
	
}

function LoanEndingAndStartingDateReport($from=null,$to=null,$agency_id=null,$city_id=null,$area_id=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	
	$returnArray=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
	$agency_id=substr($agency_id,2);
	if($type=="ag")
	{
	$agency_id=$agency_id;
	$our_company_id="NULL";
	}
	else if($type=="oc")
	{
	$our_company_id=$agency_id;
	$agency_id="NULL";	
	}
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
	$sql="SELECT COUNT(fin_file.file_id)
	 	  FROM 
	      fin_file, fin_loan, fin_customer
		  WHERE fin_loan.file_id=fin_file.file_id
		  AND file_status!=3
		  AND "; 
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."loan_approval_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."loan_approval_date<='$to'
		  AND ";  	  
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id))  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  	  	  
	$sql=$sql."	 fin_customer.file_id=fin_file.file_id
		  AND our_company_id=$oc_id 
		  GROUP BY fin_file.our_company_id";

$result1=dbQuery($sql);		  	
$result1Array=dbResultToArray($result1);
$starting=$result1Array[0][0];	

$sql="SELECT COUNT(fin_file.file_id)
	 	  FROM 
	      fin_file, fin_loan, fin_customer
		  WHERE fin_loan.file_id=fin_file.file_id
		  AND file_status!=3
		  AND "; 
if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."loan_ending_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."loan_ending_date<='$to'
		  AND ";  	  
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql." city_id=$city_id
		  AND ";
	if(isset($area_id) && validateForNull($area_id) )  
	$sql=$sql." area_id IN ($area_id)
		  AND ";	  	  	  
	$sql=$sql."	 fin_customer.file_id=fin_file.file_id
		  AND our_company_id=$oc_id 
		  GROUP BY fin_file.our_company_id";

$result2=dbQuery($sql);		  	
$result2Array=dbResultToArray($result2);
$ending=$result2Array[0][0];	
return array($starting,$ending);
	}	

function getVehicleSeizingReports($from=null,$to=null,$city_id=null,$area_id=null,$file_status=null,$agency_id=null,$sold=null)
{
	if(validateForNull($area_id))
	$area_id_array=explode(",",$area_id);
	else
	$area_id_array=array();
	$oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	$our_company_id=NULL;
	$type=substr($agency_id,0,2);
$agency_id=substr($agency_id,2);
if($type=="ag")
{
$agency_id=$agency_id;
$our_company_id="NULL";
}
else if($type=="oc")
{
$our_company_id=$agency_id;
$agency_id="NULL";	
}
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
	$sql="SELECT seize_id, seize_date, sold, remarks, fin_vehicle_seize.date_added, fin_vehicle_seize.date_modified, fin_vehicle_seize.created_by, fin_vehicle_seize.last_updated_by, fin_vehicle_seize.file_id, fin_vehicle_seize.vehicle_id 
	      FROM fin_vehicle_seize,fin_file,fin_customer
		  WHERE file_status!=3
		  AND fin_vehicle_seize.file_id=fin_customer.file_id
		  AND fin_vehicle_seize.file_id=fin_file.file_id
		  AND
		  ";
	if($our_company_id=="NULL" && is_numeric($agency_id))
{
	$sql=$sql."agency_id=$agency_id AND ";
}
if($agency_id=="NULL" && is_numeric($our_company_id))
{
	$sql=$sql."oc_id=$our_company_id AND ";
}
	if(isset($file_status) && is_numeric($file_status) && ($file_status==1 || $file_status==2))
	{
	if($file_status==1)	
	$sql=$sql."file_status =$file_status 
		  AND ";
	else if($file_status==2)
	$sql=$sql." (file_status=2 OR file_status=4) 
		  AND ";	  	  
	}	  
	if(isset($from) && validateForNull($from))
	$sql=$sql."seize_date>='$from' 
		  AND ";
	if(isset($to) && validateForNull($to))  
	$sql=$sql."seize_date<='$to'
		  AND ";
	if(isset($city_id) && validateForNull($city_id) && !in_array('0',$area_id_array))  
	$sql=$sql."city_id=$city_id
		  AND ";
	if(isset($sold) && checkForNumeric($sold))  
	$sql=$sql."sold=$sold
		  AND ";	  
	if(isset($area_id) && validateForNull($area_id) )  
	$sql=$sql." area_id IN ($area_id)
		  AND ";  	  	  
	$sql=$sql."	 our_company_id=$oc_id
	ORDER BY seize_date";		  
	 
	$result=dbQuery($sql);	
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		$result2Array2=array();
		$return_array=array();
		
		foreach($resultArray as $re)
		{
			$file_id=$re['file_id'];
			$sql="SELECT fin_file.file_id,file_number,customer_name,customer_address,customer_contact_no as contact_no
			FROM fin_file,fin_customer,fin_customer_contact_no
			WHERE fin_file.file_id=fin_customer.file_id
			AND fin_customer.customer_id=fin_customer_contact_no.customer_id
			AND fin_file.file_id=$file_id";
			
			$result2=dbQuery($sql);
			$result2Array=dbResultToArray($result2);
			$result2Array2[]=$result2Array[0];
		}
			for($i=0;$i<count($resultArray);$i++)
			{
				$cheque_details=null;
				$file_customer_details=null;
				$vehicle_seize_details=$resultArray[$i];
				$file_customer_details=$result2Array2[$i];
				$return_array[$i]['seize_details']=$vehicle_seize_details;
				$return_array[$i]['file_customer_details']=$file_customer_details;
				$return_array[$i]['file_customer_details']['vehicle_reg_no'] = getRegNoFromFileID($result2Array2[$i]['file_id']);
			}
		return $return_array;	
		}  
return array();		
}

function interestbetweenDateReports($from=null,$to=null,$agency_id=null)
{
    $oc_id=$_SESSION['adminSession']['oc_id'];
	$original_agency_id=$agency_id;
	
if(isset($from) && validateForNull($from))
{
	$from = str_replace('/', '-', $from);
		$from=date('Y-m-d',strtotime($from));
	}
else
{
	$from="1970-01-01";
	}	
if(isset($to) && validateForNull($to))
{
	$to = str_replace('/', '-', $to);
		$to=date('Y-m-d',strtotime($to));
	}
else
{
	$to=date('Y-m-d');
	}
if(validateForNull($agency_id))
	{
		$type=substr($agency_id,0,2);
		$agency_id=substr($agency_id,2);
		
		if($type=="ag")
		{
		$agency_id=$agency_id;
		$our_company_id="NULL";
		$fileAndLoanIDs=getFileIdsAndLoanIdsForAgencyId($agency_id);
		}
		else if($type=="oc")
		{
	
		$our_company_id=$agency_id;
		$agency_id="NULL";	
		$fileAndLoanIDs=getFileIdsAndLoanIdsForOCId($our_company_id);
		}
		$returnArray=array();
		if(count($fileAndLoanIDs)>0)
		{
			$i=0;
			foreach($fileAndLoanIDs as $fileAndLoanID)
			{
				$file_id=0;
				$loan_id=0;
				$total_emis_paid=0;
				$file_id=$fileAndLoanID['file_id'];
				$loan_id=$fileAndLoanID['loan_id'];
				
					if($loan_id!=0)
					{
						
						$all_payments=getAllPaymentsWithinDates($loan_id,$from,$to);
						if(is_array($all_payments) && count($all_payments)>0 && $all_payments!=0)
						{
							foreach($all_payments as $payment)
							{
								$loan_emi_id=0;
								$payment_amount=0;
								$loan_emi_id=$payment['loan_emi_id'];
								$payment_amount=$payment['amount'];
								
								$emi_amount=getEmiForLoanEmiId($loan_emi_id);
								
							    
								$emi_paid=0;
								$emi_paid=$payment_amount/$emi_amount;
								
								$total_emis_paid=$total_emis_paid+$emi_paid;
							}
							
							$loan_amount=0;
							$collection=0;
							$loan=getLoanById($loan_id);
							$loan_amount=$loan['loan_amount'];
							$loan_duration=0;
							$loan_duration=$loan['loan_duration'];
							$collection=getTotalCollectionForLoan($loan_id);
							$total_interest=0;
							$total_interest=$collection-$loan_amount;
							$interest_per_emi=0;
							$interest_per_emi=$total_interest/$loan_duration;
							$interest_for_loan=0;
							$interest_for_loan=$total_emis_paid*$interest_per_emi;
							$returnArray[$i]['file_id']=$file_id;
							$returnArray[$i]['file_no']=getFileNumberByFileId($file_id);
							$returnArray[$i]['customer']=getCustomerDetailsByFileId($file_id);
							$returnArray[$i]['reg_no']=getRegNoFromFileID($file_id);
							$returnArray[$i]['loan_id']=$loan_id;
							$returnArray[$i]['interest_per_emi']=$interest_per_emi;
							$returnArray[$i]['emi_paid']=$total_emis_paid;
							$returnArray[$i]['interest']=$interest_for_loan;
							
							$i++;				
							}
						else
						{
							$loan_amount=0;
							$collection=0;
							$loan=getLoanById($loan_id);
							$loan_amount=$loan['loan_amount'];
							$loan_duration=0;
							$loan_duration=$loan['loan_duration'];
							$collection=getTotalCollectionForLoan($loan_id);
							$total_interest=0;
							$total_interest=$collection-$loan_amount;
							$interest_per_emi=0;
							$interest_per_emi=$total_interest/$loan_duration;
							$returnArray[$i]['file_id']=$file_id;
							$returnArray[$i]['file_no']=getFileNumberByFileId($file_id);
							$returnArray[$i]['customer']=getCustomerDetailsByFileId($file_id);
							$returnArray[$i]['reg_no']=getRegNoFromFileID($file_id);
							$returnArray[$i]['loan_id']=$loan_id;
							$returnArray[$i]['interest_per_emi']=$interest_per_emi;
							$returnArray[$i]['emi_paid']=0;
							$returnArray[$i]['interest']=0;
							$i++;
							}	
					}
				}
			}
	}
	
	return $returnArray;
	}
?>