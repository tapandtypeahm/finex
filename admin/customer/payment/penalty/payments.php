<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
if(is_array($file) && $file!="error")
{
	$loan=getLoanDetailsByFileId($file_id);
	$loan_id=$loan['loan_id'];
	$paymentDetails=getPenaltyDetailsForLoan($loan['loan_id']);
	$total_penalty=getTotalPenaltyForLoan($loan['loan_id']);
	$days_paid=getTotalPenaltyPaidDaysForLoan($loan['loan_id']);
	$days_left=$total_penalty-$days_paid;
}
else
{
	$_SESSION['ack']['msg']="Invalid File!";
	$_SESSION['ack']['type']=4; // 4 for error
	header("Location: ".WEB_ROOT."admin/search");
}

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
<div class="addDetailsBtnStyling no_print"><?php if($days_left>0){ ?><a href="index.php?id=<?php echo $file_id; ?>&state=<?php echo $loan_id; ?>"><button class="btn btn-success">+ Add Penalty Payment</button></a><?php  } ?> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=penaltyDetails&id=<?php echo $file_id; ?>"><button class="btn btn-success">Back</button></a></div>
<h4 class="headingAlignment">Penalty Payments</h4>
 <div class="no_print" style="width:100%;">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">No Of Days</th>
            <th class="heading">Amount/Day</th>
            <th class="heading">Total Amount</th>
            <th class="heading">Rasid No</th>
             <th class="heading">Payment Mode</th>
            <th class="heading">Payment Date</th>
            <th class="heading btnCol no_print"></th>
            <th class="heading btnCol no_print"></th>
             <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
	
		$no=0;
		if(isset($paymentDetails[0]) && is_array($paymentDetails[0]) && count($paymentDetails)>0 )
		{
		foreach($paymentDetails as $payment)
		{	
		
			$balance=0;
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><?php echo $payment['days_paid']; ?>
            </td>
             <td><?php  echo number_format($payment['amount_per_day']); ?> 
			 </td>
             <td><?php  echo number_format($payment['amount_per_day']*$payment['days_paid']); ?> 
            </td>
             <td><?php echo $payment['rasid_no']; ?>
            </td>
            <td><?php   if($payment['payment_mode']==1) echo "CASH"; else echo "CHEQUE"; ?> 
			 
            </td>
           
           
             <td><?php echo date('d/m/Y',strtotime($payment['paid_date']));  ?>
            </td>
            <td class="no_print"> <a href="<?php echo 'index.php?view=details&id='.$file_id.'&state='.$payment['penalty_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
             <td class="no_print"> <a href="<?php echo 'index.php?view=edit&lid='.$payment['penalty_id'].'&id='.$file_id.'&state='.$payment['penalty_id']; ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
            </td>
            <td class="no_print"> 
            <a href="<?php echo 'index.php?action=delete&lid='.$payment['penalty_id'].'&id='.$file_id.'&state='.$loan_id; ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
            </td>
            
            
          
  
        </tr>
         <?php } }?>
         </tbody>
    </table>
	</div>    
 <table id="to_print" class="to_print adminContentTable"></table>    
</div>
<div class="clearfix"></div>