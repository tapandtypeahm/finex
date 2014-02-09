<?php
require_once "../../lib/cg.php";
require_once "../../lib/bd.php";
require_once "../../lib/loan-functions.php";
require_once "../../lib/common.php";

$loan_id=$_GET['lid'];
$amount_interest=$_GET['amount_interest'];
$penalty_mode=$_GET['mode'];
$uptoDate=$_GET['uptoDate'];
if(!validateForNull($uptoDate))
$uptoDate=date('Y-m-d');
if($penalty_mode==1)
{
	$daylen = 60*60*24;
	$totalPenaltyDys=getTotalPenaltyForLoan($loan_id,$uptoDate);
	echo $totalPenaltyDys*$amount_interest;
	}
else if($penalty_mode==2)
{
	$penalty=calculatePenaltyForLoanByInterest($amount_interest,$loan_id,$uptoDate);
	echo $penalty;
	}

?>