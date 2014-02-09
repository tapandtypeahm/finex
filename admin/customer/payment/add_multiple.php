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
$emi_id=$_GET['state'];
if(isset($_GET['return']))
$return=$_GET['return'];
else
$return=0;
$balance=getBalanceForLoan(getLoanIdFromEmiId($emi_id));
$differnce=getDiffernceBetweenEMIandLastEMI($emi_id,getLoanIdFromEmiId($emi_id));
$ag_id_array=getAgnecyIdFromEmiId($emi_id);
		if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$rasid_no=getRasidnoForAgencyId($agency_id);
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_no=getRasidNoForOCID($oc_id);
			$rasid_prefix=getPrefixFromOCId($oc_id);
}	
$form_identifier=uniqid("",true).strtotime(date());
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
<form  onsubmit="disableSubmitButton();" id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=addMultiple'; ?>" method="post" enctype="multipart/form-data" >
<input name="emi_id" value="<?php echo $emi_id; ?>" id="emi_id" type="hidden"  />
<input name="file_id" value="<?php echo $file_id; ?>" id="file_id" type="hidden" />
<input type="hidden" name="form_identifier" value="<?php echo $form_identifier ?>"  />
<input value="<?php if($agency_id!=null) echo $agency_id; else echo 0; ?>" id="agency_id" type="hidden" />
<input  value="<?php if($oc_id!=null) echo $oc_id; else echo 0; ?>" id="oc_id" type="hidden" />
<input  value="<?php echo 0; ?>" id="old_rasid_no" type="hidden" />
<input name="return" value="<?php echo $return; ?>" type="hidden" />
<?php for($i=0;$i<$differnce;$i++) {?>
<h4 class="headingAlignment"> Add Payment <?php echo $i+1; ?> </h4>
<table id="insertInsuranceTable" class="insertTableStyling no_print">

<tr>
<td width="220px">Payment Amount<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="amount[<?php echo $i; ?>]" class="amount<?php echo $i; ?>" placeholder="Only Digits!" /><span class="DateError customError">Amount Should less than <?php echo -$balance; ?> Rs. !</span>
                            </td>
</tr>

<tr>
<td>Payment Mode<span class="requiredField">* </span> : </td>
				<td>
					<table>
               <tr><td><input type="radio"   name="mode[<?php echo $i; ?>]" id="cash<?php echo $i; ?>"  value="1" checked="checked" ></td><td><label for="cash<?php echo $i; ?>">Cash</label></td></tr>
            <tr><td><input type="radio"  id="mode<?php echo $i; ?>" name="mode[<?php echo $i; ?>]"  value="2" ></td><td><label for="mode<?php echo $i; ?>">Cheque</label></td>
               </tr> 
            </table>
                            </td>
</tr>
</table>
<table id="chequePaymentTable<?php echo $i; ?>" class="insertTableStyling no_print chequePaymentTable">
<tr>
<td width="220px">Bank Name<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" value="NA" name="bank_name[<?php echo $i; ?>]" id="bank<?php echo $i; ?>" class="bank" placeholder="Only Letters!" autocomplete="off" />
                            </td>
</tr>
<tr>
<td width="220px">Branch Name<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" value="NA" name="branch_name[<?php echo $i; ?>]" id="branch<?php echo $i; ?>" class="branch" placeholder="Only Letters!" autocomplete="off" />
                            </td>
</tr>
<tr>
<td width="220px">Cheque No<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" value="123456" name="cheque_no[<?php echo $i; ?>]" id="cheque_no<?php echo $i; ?>" placeholder="Only Digits!" />
                            </td>
</tr>
<tr>
<td width="220px">Cheque Date<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" value="1970-01-01" name="cheque_date[<?php echo $i; ?>]" id="cheque_date<?php echo $i; ?>" class="datepicker3" placeholder="click to select date!" /><span class="DateError customError">Please select a date!</span>
                            </td>
</tr>
</table>
<table  class="insertTableStyling no_print">
<tr>
<td width="220px">Payment Date<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="payment_date[<?php echo $i; ?>]" id="payment_date<?php echo $i; ?>" class="datepicker1" placeholder="click to select date!"/><span class="DateError customError">Please select a date!</span>
                            </td>
</tr>

<tr>
<td>Rasid No<span class="requiredField">* </span> : </td>
				<td>
					<span><?php echo $rasid_prefix; ?></span><input type="text" name="rasid_no[<?php echo $i; ?>]" id="rasid_no<?php echo $i; ?>" placeholder="Only Digits!"   /><span id="agerror" class="availError">Rasid Number already taken!</span>
                </td>
</tr>


<td class="firstColumnStyling">
Remarks : 
</td>

<td>
<textarea name="remarks[<?php echo $i; ?>]" id="remarks<?php echo $i; ?>"></textarea>
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Remainder Date : 
</td>

<td>
 <input type="text" class="datepicker2"  name="remainder_date[<?php echo $i; ?>]" id="remainder_date<?php echo $i; ?>" placeholder="Click to select date!" /><span class="DateError customError">Please select a date!</span>
</td>
</tr>


 
</table>
<?php } ?>
<table>
<tr>
<td width="250px;"></td>
<td>
<input id="disableSubmit" type="submit" value="Add Payment" class="btn btn-warning"  >
<?php if(isset($_SERVER['HTTP_REFERER'])) { ?><a href="<?php echo $_SERVER['HTTP_REFERER']; ?>"><input type="button" class="btn btn-success" value="Back"/></a><?php } ?>
</td>
</tr>

</table>

</form>
</div>
<div class="clearfix"></div>
<script>
document.noOfPayments=<?php echo $differnce; ?>;
document.balance=<?php echo -$balance; ?>;
 $( ".bank" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/bank_name.php',
                { term: request.term }, 
                response );
            },
	 select: function( event, ui ) {
			$( ".bank" ).val(ui.item.label);
			return false;
		}
    });
	 $( ".branch" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/branch_name.php',
                { term: request.term, bank_name:$('.bank').val() }, 
                response );
            },
	 select: function( event, ui ) {
			$( ".branch" ).val(ui.item.label);
			return false;
		}
    });	
</script>