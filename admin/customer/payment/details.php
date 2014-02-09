<?php if(!isset($_GET['id']) || !isset($_GET['state']))
{
if(isset($_GET['id']))
{
header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$_GET['id']);
exit;
}
else
{
header("Location: ".WEB_ROOT."admin/search");
exit;
}
}

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
$file_no=$file['file_number'];
$customer=getCustomerNameANDCoByFileId($file_id);
$reg_no=getRegNoFromFileID($file_id);
$emi_id=$_GET['state'];
$payment_id=$_GET['lid'];
$loan_id=getLoanIdFromEmiId($emi_id);
$emi=getEmiForLoanId($loan_id);
$first_loan_emi_id=getFirstEmiIdForLoan($loan_id);
	
$payment=getPaymentDetailsForEmiPaymentId($payment_id);

if($payment['payment_mode']==2)
$chequePayment=getChequePaymentDetailsFromEMiPaymentId($payment_id);
else
$chequePayment=false;

$otherRasidPayment=getAllPaymentsForRasidno($payment['rasid_no'],$loan_id,$payment_id);
if(isset($otherRasidPayment) && is_array($otherRasidPayment))
{
$totalRaisdPayment=$otherRasidPayment['total_paid'];
$otherRasidPayment=$otherRasidPayment['payment_details'];
}

$balance=getBalanceForEmi($emi_id);
$balance=$balance-getAmountForPaymentId($payment_id);
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">


<?php 
if(isset($_SESSION['ack']['msg']) && isset($_SESSION['ack']['type']))
{
	
	$msg=$_SESSION['ack']['msg'];
	$type=$_SESSION['ack']['type'];
	
	
		if($msg!=null && $msg!="" && $type>0)
		{
?>
<div class="alert no_print <?php if(isset($type) && $type>0 && $type<4) echo "alert-success"; else echo "alert-error" ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php if(isset($type)  && $type>0 && $type<4) { ?> <strong>Success!</strong> <?php } else if(isset($type) && $type>3) { ?> <strong>Warning!</strong> <?php } ?> <?php echo $msg; ?>
</div>
<?php
		
		
		}
	if(isset($type) && $type>0)
		$_SESSION['ack']['type']=0;
	if($msg!="")
		$_SESSION['ack']['msg']=="";
}

?>
<table id="rasidTable" class="detailStylingTable insertTableStyling no_print">

<tr class="no_print">
<td>Payment Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($payment['payment_date'])); ?>
                            </td>
</tr>

<tr>
<td>Rasid No : </td>
				<td>
					<?php echo $payment['rasid_no']; ?>
                            </td>
</tr>

<tr>
<td>Customer Name : </td>
				<td>
					<?php echo $customer['customer_name']; ?>
                            </td>
</tr>

<tr>
<td>Vehicle Number : </td>
				<td>
					<?php if($reg_no!="") echo $reg_no; else echo "NA"; ?>
                            </td>
</tr>

<tr>
<td width="220px">Payment Amount : </td>
				<td>
					<?php echo "Rs. ".number_format(getTotalAmountForRasidNo($payment['rasid_no'],$loan_id,$payment_id))." /- "; ?>
                    </td>
</tr>

<tr>
<td>Payment Mode : </td>
				<td>
					 <?php if($payment['payment_mode']==1) { echo "CASH"; }else echo "CHEQUE"; ?>
                            </td>
</tr>
<?php if($payment['paid_by']!="NA") {  ?>
<tr>
<td>Paid By : </td>
				<td>
					 <?php if($payment['paid_by']!="NA") { echo $payment['paid_by']; } ?>
                            </td>
</tr>
<?php } ?>

<?php  if($chequePayment!=false) { ?>

<tr>
<td width="220px">Bank Name : </td>
				<td>
					<?php if($chequePayment!=false) { echo getBankNameByID($chequePayment['bank_id']); } ?>
                            </td>
</tr>
<tr>
<td width="220px">Branch Name : </td>
				<td>
					<?php if($chequePayment!=false) { echo getBranchhById($chequePayment['branch_id']); } ?>
                            </td>
</tr>
<tr>
<td width="220px">Cheque No : </td>
				<td>
					<?php if($chequePayment!=false) { echo $chequePayment['cheque_no']; } ?>
                            </td>
</tr>
<tr>
<td width="220px">Cheque Date : </td>
				<td>
					<?php if($chequePayment!=false) { echo date('d/m/Y',strtotime($chequePayment['cheque_date'])); } ?>
                            </td>
</tr>

<tr>
<td width="220px">Cheque Return : </td>
				<td>
					<?php if($chequePayment!=false) { if($chequePayment['cheque_return']==1) echo "Yes"; else echo "No"; } ?>
                            </td>
</tr>

<?php } ?>




 
</table>

<table class="no_print">
<tr>
<td width="250px;"></td>
<td>
 <a href="<?php echo 'index.php?view=edit&lid='.$payment_id.'&id='.$file_id.'&state='.$emi_id; ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
 <a onclick="window.print();"><button title="Print this entry" class="btn btn-primary">Print Rasid</button></a>
            
<a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=EMIdetails&id=<?php echo $file_id; ?>&state=<?php echo $emi_id; ?>"><button class="btn btn-warning" >Back</button></a>
</td>
</tr>

</table>
<div class="rasidCover">

<div class="leftDiv">

<DIV class="ganesh">
|| શ્રી ગણેશાય નમઃ ||
</DIV>
<div class="realisation">
Subject to Realisation of Cheque / Draft
</div>

</div>

<?php $contactNos=getContactNoForOurCompany($_SESSION['adminSession']['oc_id']); ?>
<div class="contactNos">
<?php 
if(is_array($contactNos) && count($contactNos)>0)
{
foreach($contactNos as $no) { ?>
<div class="leneline">
<img src="<?php echo WEB_ROOT; ?>/images/fon.png" class="fonClass" /> <?php echo $no['our_company_contact_no']; ?>
</div>
<?php }} ?>
</div>




<div style="clear:both"></div>

<div class="headingInRed">
<?php if(is_numeric($file['agency_id']))  {echo  getAgencyHeadingById($file['agency_id']);} else  echo getOurCompanyNameByID($file['oc_id']); ?>

</div>



<div class="subHeading">
<?php if(is_numeric($file['agency_id']))  {?>JVP of <?php echo getAgencyNameFromFileId($file_id); ?> <?php } ?>
</div>

<div class="address">
<?php echo getOurCompanyAddressByID($_SESSION['adminSession']['oc_id']); ?>
</div>

<div class="borderBottom"></div>
<div class="container">
<div class="dateDiv">
<b> <i> Date :</b> </i> <?php echo date("d/m/Y",strtotime($payment['payment_date'])); ?>
</div>
<div style="clear:both"></div>

<div class="rasid">
<b> <i> Receipt No :</b> </i> <?php  $rasid_no=$payment['rasid_no']; preg_match('#[0-9]+$#', $rasid_no, $match);
$end_number=$match[0];
if(is_numeric($end_number) && validateForNull($end_number))
{
$pos = strrpos($rasid_no, $end_number);

    if($pos !== false)
    {
        $start_string = substr_replace($rasid_no, "", $pos, strlen($end_number));
    }
}
echo $start_string." / ".$end_number."     ";
 ?>  
 
 <b> <i> File No :</b> </i> <?php   echo $file_no;
 ?>  
</div>

<div class="rasid">
<b> <i> Received From Shri/M/s. :</b>  </i> <?php echo $customer['customer_name']; ?>
</div>

<div class="rasid">
<b> <i> the sum of Rupees :</b>  </i> <?php echo numberToWord(getTotalAmountForRasidNo($payment['rasid_no'],$loan_id,$payment_id))." Only"; ?>
</div>
<?php if($chequePayment!=false) { 
?>
<div class="rasid">
<b> <i> Cheque/Draft No. :</b>  </i><?php echo $chequePayment['cheque_no']; ?>  of <?php echo getBankNameByID($chequePayment['bank_id']); ?>  <?php if($payment['paid_by']!="NA" && $payment['paid_by']!="") echo "By ".$payment['paid_by']; ?>
</div>
<?php
 }
 else
 {
  ?>
<div class="rasid">
<b> <i> via CASH Payment</b>  </i>  <?php if($payment['paid_by']!="NA" && $payment['paid_by']!="") echo "By ".$payment['paid_by']; ?>
</div>  
<?php } ?>
<?php 
$installmentArray=array();
for($l=0;$l<count($otherRasidPayment);$l++) { 

$rasidPay=$otherRasidPayment[$l];	
if($l==0)
{
	 if($rasidPay['payment_amount']==$rasidPay['emi_amount'])
	 {
		 $installmentArray['Full'][]=getLoanNoFromEMIIdForLoan($rasidPay['loan_emi_id']);
		 }
	  else
	  {	 
	     $installmentArray['Part'][]=getLoanNoFromEMIIdForLoan($rasidPay['loan_emi_id']);
		 $part_payment=$rasidPay['payment_amount'];
	 }	
}
else
{
	if($rasidPay['payment_amount']==$rasidPay['emi_amount'])
	 {
		 $installmentArray['Full'][]=getLoanNoFromEMIIdForLoan($rasidPay['loan_emi_id']);
		 }
	  else
	  {	 
	     $installmentArray['Balance'][]=getLoanNoFromEMIIdForLoan($rasidPay['loan_emi_id']);
		 $balance_payment=$rasidPay['payment_amount'];
	 }	
	
	
	}
	
	
	}
$o=0;
foreach($installmentArray as $installment_type => $ins_nos)
{

?>
<div class="rasid">
<b> <i> <?php if($o==0) { ?> For <?php }else { ?> AND <?php } ?><?php echo $installment_type;  ?> payment  <?php if($installment_type=="Full" && !is_array($emi))  echo "( Rs. ".$emi." )"; else if($installment_type=="Part")  echo "( Rs. ".$part_payment." )"; else if($installment_type=="Balance")  echo "( Rs. ".$balance_payment." )";   ?>  For Installment No. :</b>  </i> <?php echo implode(",",$ins_nos); ?> 
</div>

<?php	
$o++;	
} ?>
<div class="rasid">
<b> <i> For Vehicle No : </i>  </b><?php echo $reg_no; ?>
</div>  

<div class="rasid smallerfont">
ખાસ નોંધ : વીમો, ટેક્ષ, પરમીટ તથા પાસીંગ ની જવાબદારી લોન લેનાર પાર્ટીની છે. 
</div>

<div class="lowerLeftDiv">


        <div class="rectangle">
            <div class="Rs">
            Rs. 
            </div>
            
            <div class="amount">
            <?php echo number_format(getTotalAmountForRasidNo($payment['rasid_no'],$loan_id,$payment_id))." /- "; ?>
            </div>
            
            <div style="clear:both"></div>
        </div>
      
        <div class="juridiction">
        Subject To Ahmedabad Juridiction
        </div>
        
         <div class="partySign">
        <b> Sign. of Party : </b> 
        <div class="signSpace"></div>
        </div>

</div>

<div class="lowerRightdiv">

      <div class="aboveSign">
      For, <?php echo getOurCompanyNameByID($_SESSION['adminSession']['oc_id']); ?>
      </div>

     <div class="square">
     </div>
     
     <div class="belowSign">
      Proprietor / Manager
      </div>
     
</div>
</div>
<div style="clear:both"></div>
</div>

</div>
<div class="clearfix"></div>
<?php
if(isset($_GET['print_rasid']) && $_GET['print_rasid']=='yes')
{
 ?>
<script type="text/javascript">
window.print();
</script> 
 <?php } ?>