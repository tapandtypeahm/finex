<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
if(is_array($file) && $file!="error")
{
	if(isset($_GET['uptoDate']))
	{
	$uptoDate=$_GET['uptoDate'];
	}
	else
	$uptoDate=date('Y-m-d');
	$loan=getLoanDetailsByFileId($file_id);
	$loan_id=$loan['loan_id'];
	$paymentDetails=getPenaltyDetailsForLoan($loan['loan_id']);
	$total_penalty=getTotalPenaltyForLoan($loan['loan_id']);
	$days_paid=getTotalPenaltyPaidDaysForLoan($loan['loan_id']);
	$amount_paid=getTotalPenaltyAmountPaidForLoan($loan['loan_id']);
	$days_left=$total_penalty-$days_paid;
	$full_table=getFullPenaltyTableForLoanId($loan_id);
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


<div class="addDetailsBtnStyling no_print"><?php if($days_left>0){ ?> <a href="<?php  echo  WEB_ROOT; ?>admin/customer/payment/penalty/index.php?id=<?php echo $file_id; ?>&state=<?php echo $loan_id; ?>"><button class="btn btn-success">+ Add Penalty</button></a><?php  } ?> <a href="<?php echo WEB_ROOT ?>admin/customer/payment/penalty/index.php?view=payments&id=<?php echo $file_id; ?>"><button class="btn btn-success">View Penalty Payments</button></a> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><button class="btn btn-success">Back</button></a></div>
<div class="detailStyling">

<h4 class="headingAlignment">Penalty Details </h4>

<table class="insertTableStyling detailStylingTable">

<tr>
<td class="firstColumnStyling">
Total Penalty uptill today : 
</td>

<td>
 
                             <?php  echo $total_penalty." Days"; ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Penalty Days Paid : 
</td>

<td>
 
                             <?php  echo $days_paid." Days"; ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Total Penalty Amount Paid : 
</td>

<td>
 
                             <?php  echo "Rs. ".number_format($amount_paid,2); ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Avg. Penalty Per Day: 
</td>

<td>
 
                             <?php  echo "Rs. ".number_format($amount_paid/$days_paid,2); ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Penalty Days Left : 
</td>

<td>
 
                             <?php echo ($total_penalty-$days_paid)." Days"; ?>					
                           
</td>
</tr>


</table>
</div>
<h4 class="headingAlignment no_print">Calculate Penalty</h4>
<input name="loan_id" value="<?php echo $loan_id; ?>" id="loan_id" type="hidden">
<table class="insertTableStyling no_print">

<tr>
<td width="220px">Up to Date : </td>
				<td>
					<input onchange="onChangeDate(this.value,this)"  type="text" id="raminderDate" autocomplete="off" size="12"  name="remainderDate" class="datepicker1 datepick" placeholder="Click to Select!" /><span class="DateError customError">Please select a date!</span>
</td>
                    
                    
                  
</tr>

<tr>
<td width="220px">Penalty Mode<span class="requiredField">* </span> : </td>
				<td>
					<Select name="penalty_mode" id="penalty_mode" >
                    	<option value="1">Amount/Day</option>
                        <option value="2">Flat Rate of Interest</option>
                    </Select>
</td>
                    
                    
                  
</tr>

<tr>
<td>
(Amount/Day) OR Interest<span class="requiredField">* </span> : 
</td>
<td>
<input type="text"  name="amount_interest" id="amount_interest" />
</td>
</tr>

<tr>
<td></td>
<td>
<input type="submit" value="Calculate" class="btn btn-warning" onclick="calculatePenalty()"> 
</td>
</tr>

<tr>
<td>
Total Penalty : 
</td>
<td>
<span id="total_penalty"></span>
</td>
</tr>

<tr>
<td>
Penalty Paid : 
</td>
<td>
<span id="penalty_paid"><?php echo number_format($amount_paid,2) ?></span>
</td>
</tr>

<tr>
<td>
Penalty Left : 
</td>
<td>
<span id="penalty_left"></span>
</td>
</tr>

</table>

<div class="clearfix"></div>
 <div class="no_print" style="width:100%;">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">Actual Date</th>
            <th class="heading">Payment Date</th>
             <th class="heading">EMI</th>
             <th class="heading">Payment Amount</th>
            <th class="heading">Penalty on Amount</th>
            <th class="heading">Penalty Days</th>
           
        </tr>
    </thead>
    <tbody>
        
        <?php
	
		$no=0;
		if($full_table!=false)
		{
		foreach($full_table as $row)
		{	
	
		$payment=null;
		if(isset($row['paidDetails']))
		$payment=$row['paidDetails'];
		if(isset($row['UnPaidDetails']))
		$upPaid=$row['UnPaidDetails'];
		
		
		$valid=0;
		
		if(isset($payment) && is_array($payment) && count($payment)>0)
		{
			foreach($payment as $p) { if($p['amount']!=0) {$valid=1;} $emi=$p['emi']; $actualDate=$p['actual_date']; }
		}
		 if(isset($upPaid['amount']) && $upPaid['amount']!=0) {$valid=1; $emi=$upPaid['emi']; $actualDate=$upPaid['actual_date']; } 
	
		if($valid==1)
		{
			$balance=0;
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><?php echo date('d/m/Y',strtotime($actualDate)); ?>
            </td>
             <td><?php if(isset($payment) && is_array($payment) && count($payment)>0)
		{  foreach($payment as $p) { if($p['amount']!=0) echo date('d/m/Y',strtotime($p['payment_date']))."<br>"; }} if(isset($upPaid['amount']) && $upPaid['amount']!=0) echo "-"; ?> 
			 </td>
             <td><?php  echo number_format($emi); ?> 
            </td>
            <td><?php  if(isset($payment) && is_array($payment) && count($payment)>0)
		{ foreach($payment as $p) { if($p['amount']!=0) echo number_format($p['payment_amount'])."<br>";  }} if(isset($upPaid['amount']) && $upPaid['amount']!=0) echo "-"; ?> 
			 
            </td>
             <td><?php if(isset($payment) && is_array($payment) && count($payment)>0)
		{ foreach($payment as $p) { if($p['amount']!=0) echo number_format($p['amount'])."<br>";  }  } if(isset($upPaid['amount']) && $upPaid['amount']!=0) echo "BALANCE ( ".number_format(-$upPaid['amount'])." )<br>";   ?>
            </td>
            <td><?php if(isset($payment) && is_array($payment) && count($payment)>0)
		{ foreach($payment as $p) { if($p['amount']!=0) echo $p['days']."<br>";  } } if(isset($upPaid['days']) && $upPaid['days']!=0) echo number_format($upPaid['days'])."<br>"; ?>
            </td>
            
        </tr>
         <?php } } }?>
         </tbody>
    </table>
	</div>    
 <table id="to_print" class="to_print adminContentTable"></table>    
</div>
<div class="clearfix"></div>