<?php
require_once('cg.php');
require_once('bd.php');
require_once('common.php');
require_once('file-functions.php');
require_once('phpExcel/PHPExcel/IOFactory.php');
require_once('addNewCustomer-functions.php');
require_once('city-functions.php');
require_once('area-functions.php');
require_once('vehicle-functions.php');
require_once('customer-functions.php');
require_once('loan-functions.php');
$inputFileName = 'Book1.xlsx';

//  Read your Excel workbook
try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName);
} catch(Exception $e) {
    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}

//  Get worksheet dimensions
$sheet = $objPHPExcel->getSheet(0); 
$highestRow = $sheet->getHighestRow(); 
$highestColumn = $sheet->getHighestColumn();
$rowData=array();
//  Loop through each row of the worksheet in turn
for ($row = 1; $row <= $highestRow; $row++){ 
    //  Read a row of data into an array
    $rowDat = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                    NULL,
                                    TRUE,
                                    FALSE);
/*	$file_number=$rowData[0][0];
	$agreement_no=$rowData[0][1];
	if($agreement_no!="" && $file_number!="")
	{
	$file_number=stripFileNo($file_number);
	$agreement_no=substr_replace($agreement_no,"O",1,1);
	$sql="UPDATE fin_file SET file_agreement_no='$agreement_no' WHERE file_number='$file_number'";	
	dbQuery($sql);		
		
	} */
    //  Insert row data array into your database of choice here
	$rowData[]=$rowDat[0];
	
}

$customer_row=array();
	$j=-1;
	for($i=0;$i<count($rowData);$i++)
	{
		
		$row=$rowData[$i];
		if(($i%47)==0)
		{
			$j++;
			
			}
			
		$customer_row[$j][]=$row;	
		}
$no=1;

foreach($customer_row as $customer)
{
	//echo $customer[8][0];
	if($customer[8][0]=="n>.")
	{
	$excel_date="1899-12-30";
	$file_number=$customer[9][0];
	
	$file_number=intval($file_number+1);
	$agreement_no=$customer[6][2];
	
	/*if($agreement_no=="" || $agreement_no==null)
	$agreement_no=$file_number;
	
	$agreement_no=str_replace("PERSONAL","PERSONAL".$file_number,$agreement_no); */
	$file_number="M".$file_number;
	$file_id=getFileIdFromFileNo($file_number);
	$loan_id=getLoanIdFromFileId($file_id);
	
	/*for($k=9;$k<45;$k++)
	{
		$loan_emi_id=0;
		$payment=0;
		$payment_amount=0;
		$payment_days=0;
		$loan_emi_id=getOldestUnPaidEmi($loan_id);
		$payment=$customer[$k];
		$payment_amount=$payment[5];
		$payment_days=$payment[3];
		if(checkForNumeric($payment_days))
		$payment_date=date('Y-m-d', strtotime($excel_date. ' + '.$payment_days.' days'));
		else
		{
		
			$loan_emi=getEmiDetailsByEmiId($loan_emi_id);
			$payment_date=$loan_emi['loanDetails']['actual_emi_date'];
			
			}
		$remarks=$payment[6];
		
		$int = filter_var($remarks, FILTER_SANITIZE_NUMBER_INT);
		if($int==null)
		{
		$int=9999;
		$remarks=$remarks." | Rasid no missing";
		}
		$rasid_no=$int;
		
		$balance=0;
		$balance=getBalanceForLoan($loan_id);
		
		if($balance>=0)
		break;
		
		if($payment_amount>-($balance))
		{
			$payment_amount=$balance;
			}
		
		if(checkForNumeric($loan_emi_id,$payment_amount) && $payment_amount>0 && validateForNull($payment_date,$rasid_no))
		{
			echo $loan_emi_id." ".$payment_amount." ".$payment_date." ".$rasid_no." ".$remarks." ".$k."<br>";
			 usleep(200);
			insertPayment($loan_emi_id,$payment_amount,1,$payment_date,$rasid_no,$remarks,"1970-01-01",false,false,false,false,false,"");
			
			}
		} */ 
	/*$city=$customer[2][4];
	$area=$customer[2][5];
	$city_id=insertCityIfNotDuplicate($city);
	$customer_name=$customer[1][2];
	$customer_address=$customer[2][2];
	$contact_no=$customer[1][7];
	if(!is_numeric($contact_no))
	$contact_no=9999999999;
	$loan_amount=$customer[5][7];
	$loan_amount=str_replace(",","",$loan_amount);
	$loan_amount=str_replace(".","",$loan_amount);
	$loan_amount=str_replace(" ","",$loan_amount);
	$agency_amount=$customer[4][7];
	$agency_amount=str_replace(",","",$agency_amount);
	$agency_amount=str_replace(".","",$agency_amount);
	$agency_amount=str_replace(" ","",$agency_amount);
	if(!is_numeric($agency_amount))
	$agency_amount=$loan_amount;
	$duration=$customer[4][2];
	$duration_in_years=$duration/12;
	$emi=$customer[5][4];
	$total_collection=$emi*$duration;
	$interest=$total_collection-$loan_amount;
	$roi=(($interest/$loan_amount)/$duration_in_years)*100;
	$starting_date_days_from_excel_date=$customer[9][2];
	$agency_scheme=$customer[4][4];
	$agency_scheme_array=explode("*",$agency_scheme);
	$agency_emi=$agency_scheme_array[0];
	$agency_duration=$agency_scheme_array[1];
	if(!is_numeric($agency_duration))
	$agency_duration=$duration;
	if(!is_numeric($agency_emi))
	$agency_emi=$emi;
	
	$reg_no=$customer[5][2];
	$reg_no=str_replace(" ","",$reg_no);
	$reg_no=str_replace(".","",$reg_no);
	$reg_no=str_replace(",","",$reg_no);
	$reg_no=str_replace("-","",$reg_no);
	$chasis_no=$customer[6][5];
	$engine_no=$customer[7][5];
	$starting_date=date('Y-m-d', strtotime($excel_date. ' + '.$starting_date_days_from_excel_date.' days'));
	$approval_date=date("Y-m-d", strtotime($starting_date. ' - 1 month'));
	
	echo $agreement_no;
	addNewCustomer("ag2",$agreement_no,$file_number,1,$customer_name,$customer_address,$city_id,$area,0,$contact_no,array(),array(),array(),array(),null,null,null,null,null,null,null,null,null,null,$loan_amount,1,$duration,1,1,$roi,$emi,$approval_date,$starting_date,false,false,false,false,false,false,$agency_amount,array($agency_emi),array($agency_duration));

	$file_id=getFileIdFromAgreementNo($agreement_no);
    
	$customer_id=getCustomerIdByFileId($file_id);
	if(!is_null($reg_no))
	{
		
	
	if(!validateForNull($chasis_no))
	{
		
		$chasis_no="0";
	}
	if(!validateForNull($engine_no))
	{
		$engine_no="0";
	}
	
	insertVehicle(25,$reg_no,"1970-01-01",$engine_no,$chasis_no,8,"2013",1,16,35,"1970-01-01","1970-01-01",$file_id,$customer_id,array(),array(),array(),array());
	}*/
	
	}
	else
	{print_r($customer);}
}	
		

 ?>