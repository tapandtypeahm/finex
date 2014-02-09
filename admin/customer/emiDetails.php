<?php

if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

if(!isset($_GET['state']))
header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$_GET['id']);

$file_id=$_GET['id'];
$emi_id=$_GET['state'];
$file=getFileDetailsByFileId($file_id);
$loan_id=getLoanIdFromEmiId($emi_id);
$customer=getCustomerNameANDCoByFileId($file_id);
$reg_no=getRegNoFromFileID($file_id);
$emi=getEmiDetailsByEmiId($emi_id);
$emiDetails=$emi['loanDetails'];
$paymentDetails=$emi['paymentDetails'];
$balance=getBalanceForEmi($emi_id);
if($balance<0)
$penalty=getPenaltyDaysFroEmiId($emi_id);

$loan_emi_id_unpaid=getOldestUnPaidEmi(getLoanIdFromEmiId($emi_id));
?>
<div class="insideCoreContent adminContentWrapper wrapper">
<div class="addDetailsBtnStyling no_print"><?php  if($loan_emi_id_unpaid!=false && is_numeric($loan_emi_id_unpaid)) { ?><a class="no_print" href="payment/index.php?id=<?php echo $file_id; ?>&state=<?php echo $loan_emi_id_unpaid; ?>" style="font-size:12px; color:#d00;"><button class="btn btn-success"  >+ Add payment</button></a><?php } ?> <a href="<?php if(isset($_GET['return']) && $_GET['return']=='findEmiDetails'){ echo WEB_ROOT."admin/customer/EMI/index.php?view=details&id=".$_GET['id'];  } else  echo WEB_ROOT."admin/customer/index.php?view=details&id=".$_GET['id']; ?>"><button class="btn btn-warning">Back</button></a></div>

<h4 class="headingAlignment">EMI Details</h4>
<?php 
if(isset($_SESSION['ack']['msg']) && isset($_SESSION['ack']['type']))
{
	
	$msg=$_SESSION['ack']['msg'];
	$type=$_SESSION['ack']['type'];
	
	
		if($msg!=null && $msg!="" && $type>0)
		{
?>
<div class="alert no_print  <?php if(isset($type) && $type>0 && $type<4) echo "alert-success"; else echo "alert-error" ?>">
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

<table class="insertTableStyling detailStylingTable">

<tr>
<td>File Number : </td>
				<td>
				
                             <?php echo $file['file_number']; ?>					
                            

                 </td>
</tr>

<tr>

<td class="firstColumnStyling">
Customer's Name : 
</td>

<td>

                             <?php echo $customer['customer_name']; ?>					
                            
</td>
</tr>

<tr id="addcontactTrCustomer">
                <td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                <?php
                            $contactNumbers = $customer['contact_no'];
							
                            foreach($contactNumbers as $c)
                              {
                       ?>
                             
                             <?php echo $c[0]." | "; ?>					
                             <?php } ?>
                </td>
            </tr>

<tr>
<td class="firstColumnStyling">
Registration Number : 
</td>

<td>
<?php  $reg_no=strtoupper($reg_no); echo $reg_no;?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Actual Emi Date : 
</td>

<td>
<?php  echo date('d/m/Y',strtotime($emiDetails['actual_emi_date']));?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Emi Amount : 
</td>

<td>
<?php  echo " Rs. ".getEmiForLoanEmiId($emi_id);?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Total Payments Received : 
</td>

<td>
<?php $tot=getTotalPaymetnsForEmi($emi_id); if($tot>0)  echo " Rs. ".$tot; else echo " Rs. 0";?>
</td>
</tr>
      
<tr>
<td class="firstColumnStyling">
Balance : 
</td>

<td>
<?php  echo " Rs. ".$balance;?>
</td>
</tr>
<?php if(date("Y-m-d")>=$emiDetails['actual_emi_date']) { ?>
<tr>
<td class="firstColumnStyling">Company Paid Date : </td>
<td><?php if( $emiDetails['company_paid_date']!=null) { echo date('d/m/Y',strtotime($emiDetails['company_paid_date'])); ?><br /><a class="no_print" href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=editCompanyPaidDate&return=emiDetails&id=<?php echo $file_id; ?>&lid=<?php echo $emiDetails['loan_emi_id']; ?>" style="font-size:12px; color:#d00;">Edit</a> <a class="no_print" onclick="return confirm('Are you sure?')" href="<?php echo WEB_ROOT ?>admin/customer/index.php?action=deleteCompanyPaidDate&return=emiDetails&id=<?php echo $file_id; ?>&lid=<?php echo $emiDetails['loan_emi_id']; ?>" style="font-size:12px; color:#d00;">Del</a><?php } ?><?php if(date("Y-m-d")>=$emiDetails['actual_emi_date'] && $emiDetails['company_paid_date']==null) { ?><a class="no_print" href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=addCompanyPaidDate&return=emiDetails&id=<?php echo $file_id; ?>&lid=<?php echo $emiDetails['loan_emi_id']; ?>" style="font-size:12px; color:#d00;">Add</a><?php } ?>
</td>
</tr>
<?php } ?>
<?php if(isset($penalty) && $penalty>0) { ?>
<tr>
<td class="firstColumnStyling">
Penalty Days : 
</td>

<td>
<?php  echo $penalty." Days";?>
</td>
</tr>
   
 <?php } ?>                 



</table>


 <div class="no_print" style="width:100%;">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading" >Payment</th>
            <th class="heading">Mode</th>
            <th class="heading">Payment Date</th>
            <th class="heading">Balance</th>
            <th class="heading">Penalty Days</th>
            <th class="heading">Rasid No</th>
            <th class="heading" width="8%">Remarks</th>
            <th class="heading" width="9%">Remainder Date</th>
             <th class="heading" width="9%">Status</th>
              <th class="heading btnCol no_print"></th>
            <th class="heading btnCol no_print"></th>
            <th class="heading btnCol no_print"></th>
             <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
	
		$no=0;
		if(isset($paymentDetails) && is_array($paymentDetails) && count($paymentDetails)>0)
		{
		foreach($paymentDetails as $payment)
		{	
			$otherRasidPayment=getPaymentsForRasidno($payment['rasid_no'],$loan_id,$payment['emi_payment_id']);
			
			if(isset($otherRasidPayment) && is_array($otherRasidPayment))
			{
			$totalRaisdPayment=$otherRasidPayment['total_paid'];
			$otherRasidPayment=$otherRasidPayment['payment_details'];
			}
			$balance=0;
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            
             <td><?php  echo number_format($payment['payment_amount']); if($otherRasidPayment!=false){ foreach($otherRasidPayment as $otherPayment) { echo "<br>+".number_format($otherPayment['payment_amount'])."(".date('d/m/Y',strtotime($otherPayment['actual_emi_date'])).")"; } echo "<br> Total Rs: ".number_format($totalRaisdPayment+$payment['payment_amount']); } ?> 
			 
            </td>
            <td><?php  if($payment['payment_mode']==1) echo "CASH"; else echo "CHEQUE"; ?> 
			 
            </td>
           
           
             <td><?php echo date('d/m/Y',strtotime($payment['payment_date']));  ?>
            </td>
              <td><?php $paymentPenalty=getPenaltyDaysForPaymentId($payment['emi_payment_id']); echo $paymentPenalty['amount'];  ?>
            </td>
              <td><?php echo $paymentPenalty['days'];  ?>
            </td>
            <td><?php echo  $payment['rasid_no'];  ?>
            </td>
             <td><?php if($payment['remarks']==""){ echo "NA";}else if($payment['remarks']!=""){ echo $payment['remarks'];}  ?>
            </td>
            <td><?php if($payment['remainder_date']=="1970-01-01" || $payment['remainder_date']=="0000-00-00"){echo "NA";}else if($payment['remainder_date']!=null){ echo date('d/m/Y',strtotime($payment['remainder_date']));}  ?>
            </td>
            
             <td><?php if($payment['remarks']!="" || ($payment['remainder_date']!="1970-01-01" && $payment['remainder_date']!="0000-00-00")) { ?><?php  if($payment['remainder_status']==0) echo "UN-DONE"; else echo "DONE"; ?><br /><?php if($payment['remainder_status']==0) { ?><a class="no_print" href="<?php echo WEB_ROOT ?>admin/customer/index.php?action=doneRemainderPayment&id=<?php echo $file_id; ?>&lid=<?php echo $payment['emi_payment_id']; ?>&state=<?php echo $emi_id ?>" style="font-size:12px; color:#d00;" onclick="return confirm('Are you sure?')">Set Done</a> <?php } else { ?> <a class="no_print" onclick="return confirm('Are you sure?')" href="<?php echo WEB_ROOT ?>admin/customer/index.php?action=unDoneRemainderPayment&id=<?php echo $file_id; ?>&lid=<?php echo $payment['emi_payment_id']; ?>&state=<?php echo $emi_id ?>" style="font-size:12px; color:#d00;">Set unDone</a><?php } ?>
             <?php } else {echo "NA";} ?>
            </td> 
           <td class="no_print"> <a href="<?php echo 'payment/index.php?view=details&print_rasid=yes&lid='.$payment['emi_payment_id'].'&id='.$file_id.'&state='.$emi_id; ?>"><button title="Print Rasid" class="btn viewBtn"><span class="view">P</span></button></a>
            </td>
            <td class="no_print"> <a href="<?php echo 'payment/index.php?view=details&lid='.$payment['emi_payment_id'].'&id='.$file_id.'&state='.$emi_id; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
             <td class="no_print"> <a href="<?php echo 'payment/index.php?view=edit&lid='.$payment['emi_payment_id'].'&id='.$file_id.'&state='.$emi_id; ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
            </td>
            <td class="no_print"> 
            <a href="<?php echo 'payment/index.php?action=delete&lid='.$payment['emi_payment_id'].'&id='.$file_id.'&state='.$emi_id; ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
            </td>
            
            
          
  
        </tr>
         <?php } }?>
         </tbody>
    </table>
	</div>    
 <table  id="to_print" class="to_print adminContentTable"></table>    
</div>
<div class="clearfix"></div>