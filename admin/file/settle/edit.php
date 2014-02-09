<?php if(!isset($_GET['id']))
{
header("Location: ".WEB_ROOT."admin/search");
exit;
}

$file_id=$_GET['id'];
$settle_file=getSettleFileDetails($file_id);
if($settle_file['payment_mode']==2)
$chequePayment=getSettleChequeBySettleId($settle_file['settle_id']);
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">

<h4 class="headingAlignment"> Edit Settlement </h4>
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
<form onsubmit="return submitPayment();" id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=edit'; ?>" method="post" enctype="multipart/form-data">
<input name="file_id" value="<?php echo $file_id; ?>" id="file_id" type="hidden" />
<input name="lid" value="<?php echo $settle_file['settle_id']; ?>"  type="hidden" />

<table id="insertInsuranceTable" class="insertTableStyling no_print">

<tr>
<td width="220px">Payment Amount<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" placeholder="Only Digits" name="amount" id="amount" value="<?php echo $settle_file['settle_amount']; ?>" />
                            </td>
</tr>

<tr>
<td>Payment Mode<span class="requiredField">* </span> : </td>
				<td>
					<table>
               <tr><td><input type="radio" id="cash"  name="mode" onChange="checkMode(this.value);"  value="1" <?php if($settle_file['payment_mode']==1) { ?> checked="checked" <?php } ?>></td><td><label for="cash">Cash</label></td></tr>
            <tr><td><input type="radio" id="mode" name="mode" onChange="checkMode(this.value);"  value="2" <?php if($settle_file['payment_mode']==2) { ?> checked="checked" <?php } ?> ></td><td><label for="mode">Cheque</label></td>
               </tr> 
            </table>
                            </td>
</tr>

</table>
<table id="chequePaymentTable" class="insertTableStyling no_print">

<tr>
<td width="220px">Bank Name<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="bank_name" id="bank" placeholder="Only Letters!" value="<?php if($chequePayment!=false) { echo getBankNameByID($chequePayment['bank_id']); } ?>" />
                            </td>
</tr>
<tr>
<td width="220px">Branch Name<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="branch_name" id="branch" placeholder="Only Letters!" value="<?php if($chequePayment!=false) { echo getBranchhById($chequePayment['branch_id']); } ?>" />
                            </td>
</tr>
<tr>
<td width="220px">Cheque No<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="cheque_no" id="cheque_no" placeholder="Only Digits!" value="<?php if($chequePayment!=false) { echo $chequePayment['cheque_no']; } ?>" />
                            </td>
</tr>
<tr>
<td width="220px">Cheque Date<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="cheque_date" id="cheque_date" class="datepicker3" placeholder="click to select date!" value="<?php if($chequePayment!=false) { echo date('d/m/Y',strtotime($chequePayment['cheque_date'])); } ?>" /><span class="DateError customError">Please select a date!</span>
                            </td>
</tr>
</table>
<table  class="insertTableStyling no_print">

<tr>
<td width="220px">Settle Date<span class="requiredField">* </span> : </td>
				<td>
					<input class="datepicker1" placeholder="Click to select Date!"  type="text" name="settle_date" id="payment_date" value="<?php echo date('d/m/Y',strtotime($settle_file['settle_date'])); ?>" /><span class="DateError customError">Please select a date!</span>
                            </td>
</tr>

<tr>
<td>Receipt No<span class="requiredField">* </span> : </td>
				<td>
					<input type="text"  name="rasid_no" id="rasid_no"  value="<?php echo $settle_file['receipt_no']; ?>" placeholder="Only Digits!" />
                </td>
</tr>



<td class="firstColumnStyling">
Remarks : 
</td>

<td>
<textarea name="remarks" id="remarks"><?php echo $settle_file['remarks']; ?></textarea>
</td>
</tr>
 
</table>

<table>
<tr>
<td width="250px;"></td>
<td>
<input id="disableSubmit" type="submit" value="Edit Payment" class="btn btn-warning">
<a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><input type="button" class="btn btn-success" value="Back"/></a>
</td>
</tr>

</table>

</form>
</div>
<div class="clearfix"></div>
<script>

 $( "#bank" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/bank_name.php',
                { term: request.term }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#bank" ).val(ui.item.label);
			return false;
		}
    });
	 $( "#branch" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/branch_name.php',
                { term: request.term, bank_name:$('#bank').val() }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#branch" ).val(ui.item.label);
			return false;
		}
    });	
</script>
