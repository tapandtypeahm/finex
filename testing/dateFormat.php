<?php
require_once "../lib/EMI-functions.php"; 

echo (strtotime('2013-12-12')>=strtotime('2014-12-12'));
$date="31/08/2011";
$date = str_replace('/', '-', $date);
date('Y-m-d',strtotime($date));
$paid_date="2012-03-30";

 
 	$company_paid_date_minus_one_month= date("Y-m-d", strtotime("-1 month", strtotime($paid_date)));
	echo $company_paid_date_minus_one_month;
	$should_be_month_less=date('m',strtotime($paid_date))-1;

	if($should_be_month_less<1)
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
echo $company_paid_date_minus_one_month;			
 ?>