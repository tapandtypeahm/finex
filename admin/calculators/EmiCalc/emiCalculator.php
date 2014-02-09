<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">EMI Calculator</h4>
<h4 class="subheadingAlignment no_print">All Inputs Required</h4>
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=calc'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurCompany()">

<table class="insertTableStyling no_print">

<tr>
<td>
Starting Amount : 
</td>
<td>
<input type="text" name="start_amount" id="start_amount" placeholder="Only Digits"/>
</td>
</tr>

<tr>
<td>Ending Amount : </td>
				<td>
				 <input type="text"  name="end_amount" id="end_amount" placeholder="Only Digits"/>	
                 </td>
</tr>

<tr>
<td>Amount Interval : </td>
				<td>
				 <input type="text"  name="amount_interval" id="amount_interval" placeholder="Only  Digits"/>	
                 </td>
</tr>

<tr>
<td>Months Interval : </td>
				<td>
				 <input type="text"  name="month" id="month" placeholder="Only Digits"/>	
                </td>
</tr>

<tr>
<td>Rate of Interest (% annually) : </td>
				<td>
				 <input type="text"  name="roi" id="roi" placeholder="Only Digits"/>	
                </td>
</tr>


<td></td>
				<td>
				 <input type="submit" value="search" class="btn btn-warning"/>	
                </td>
</tr>


</table>

</form>
<?php if(isset($_SESSION['emiCalc']['emi_array']))
{

	$emi_array=$_SESSION['emiCalc']['emi_array'];
		$start_amount=$_SESSION['emiCalc']['start_amount'];
		$end_amount=$_SESSION['emiCalc']['end_amount'];
		$amount_interval=$_SESSION['emiCalc']['amount_interval'];
		$duration_interval=$_SESSION['emiCalc']['duration_interval'];
		$roi=$_SESSION['emiCalc']['roi'];
		
	 ?>
  
<hr class="firstTableFinishing" />

<table id="DetailsTable" cellpadding="10px" style="left:10px;">
<tr>
<td width="150px">
Start Amount : </td><td><?php echo $start_amount." Rs."; ?></td></tr>
<tr><td>    
End Amount : </td><td><?php echo $end_amount." Rs."; ?></td></tr>
<tr><td>    
Amount Interval : </td><td><?php echo $amount_interval." Rs."; ?></td></tr>
<tr><td>    
Duration Interval : </td><td><?php echo $duration_interval." Months"; ?></td></tr>
<tr><td>   
Rate of Interest : </td><td><?php echo $roi." % (Annually)"; ?></td></tr>
</table>
<div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>
	<div class="no_print">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading" style="font-weight:bold;">Amount</th>
            <?php for($i=1;$i<11;$i++) { ?>
            <th class="heading"><?php echo $i*$duration_interval; ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        
        <?php
		$j=0;
		for($start_amount;$start_amount<=$end_amount;$start_amount=$start_amount+$amount_interval)
		{
		 ?>
          <tr class="resultRow">
        	<td style="font-weight:bold;"><?php echo $start_amount; ?>
            </td>
           <?php for($i=1;$i<11;$i++)
			{ ?>
            <td><?php echo $emi_array[$j][$i]; ?>
            </td>
            <?php } ?>
        </tr>
         <?php $j++; }?>
         </tbody>
    </table>
    </div>
       <table id="to_print" style="width:100%;" class="to_print adminContentTable"></table> 
<?php } ?>       
</div>
<div class="clearfix"></div>