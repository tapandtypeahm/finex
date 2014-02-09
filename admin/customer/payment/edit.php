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
$payment_id=$_GET['lid'];
$payment=getPaymentDetailsForEmiPaymentId($payment_id);
$loan_id=getLoanIdFromEmiId($emi_id);
if($payment['payment_mode']==2)
$chequePayment=getChequePaymentDetailsFromEMiPaymentId($payment_id);
else
$chequePayment=false;



$totalPayment=getTotalAmountForRasidNo($payment['rasid_no'],$loan_id,$payment_id);
$balance=getBalanceForLoan(getLoanIdFromEmiPaymentId($payment_id));
$balance=$balance-$totalPayment;

$ag_id_array=getAgnecyIdFromEmiId($emi_id);
		if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_prefix=getPrefixFromOCId($oc_id);
			}
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">

<h4 class="headingAlignment"> Edit Payment </h4>
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
<input name="emi_id" value="<?php echo $emi_id; ?>" id="emi_id" type="hidden" />
<input name="file_id" value="<?php echo $file_id; ?>" id="file_id" type="hidden" />
<input name="lid" value="<?php echo $payment_id; ?>"  type="hidden" />
<input value="<?php if($agency_id!=null) echo $agency_id; else echo 0; ?>" id="agency_id" type="hidden" />
<input  value="<?php if($oc_id!=null) echo $oc_id; else echo 0; ?>" id="oc_id" type="hidden" />
<input name="return" value="<?php echo $return; ?>" type="hidden" />
<input  value="<?php echo $payment['rasid_no']; ?>" id="old_rasid_no" type="hidden" />
<table id="insertInsuranceTable" class="insertTableStyling no_print">

<tr>
<td width="220px">Payment Amount<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" placeholder="Only Digits" name="amount" id="amount" value="<?php echo $totalPayment; ?>" /><span class="DateError customError">Amount Should less than <?php echo -$balance; ?> Rs. !</span>
                            </td>
</tr>

<tr>
<td>Payment Mode<span class="requiredField">* </span> : </td>
				<td>
					<table>
               <tr><td><input type="radio" id="cash"  name="mode" onChange="checkMode(this.value);"  value="1" <?php if($payment['payment_mode']==1) { ?> checked="checked" <?php } ?>></td><td><label for="cash">Cash</label></td></tr>
            <tr><td><input type="radio" id="mode" name="mode" onChange="checkMode(this.value);"  value="2" <?php if($payment['payment_mode']==2) { ?> checked="checked" <?php } ?> ></td><td><label for="mode">Cheque</label></td>
               </tr> 
            </table>
                            </td>
</tr>

</table>
<table id="chequePaymentTable" class="insertTableStyling no_print">
<tr>
<td>Cheque Return<span class="requiredField">* </span> : </td>
				<td>
					<table>
               <tr><td><input type="radio"   name="cheque_return" id="cheque_return_no"  value="0" <?php if($chequePayment['cheque_return']==0) { ?> checked="checked" <?php } ?>></td><td><label for="cheque_return_no">No</label></td></tr>
            <tr><td><input type="radio"  name="cheque_return" id="cheque_return_yes"   value="1" <?php if($chequePayment['cheque_return']==1) { ?> checked="checked" <?php } ?> ></td><td><label for="cheque_return_yes">Yes</label></td>
               </tr> 
            </table>
                            </td>
</tr>
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

<?php

if(ACCOUNT_STATUS==1)
{
	
 ?>
 <tr>
<td>By (Account)<span class="requiredField">* </span> : </td>
				<td>
					<select id="by_ledger" name="bank_account">
                    <?php
					$agency_oc_array=getAgencyOrCompanyIdFromFileId($file_id);
					$agency_oc_type=$agency_oc_array[0];
					$agency_oc_id=$agency_oc_array[1];
					if($agency_oc_type=='oc')
					{
					$bank_cash_ledgers=listAccountingLedgersForOC($agency_oc_id);
					}
					else if($agency_oc_type=='agency')
					{
						$bank_cash_ledgers=listAccountingLedgersForAgency($agency_oc_id);
						}
					foreach($bank_cash_ledgers as $bank_cash_ledger)
					{
					?>
                    <option value="<?php echo $bank_cash_ledger['ledger_id']; ?>"><?php echo $bank_cash_ledger['ledger_name']; ?></option>			
                    <?php	
						}
					 ?>
                    </select>
                            </td>
</tr>

<?php }
else
{
 ?> 
 <input name="bank_account" value="0" type="hidden" />
<?php } ?>
</table>
<table  class="insertTableStyling no_print">

<tr>
<td width="220px">Payment Date<span class="requiredField">* </span> : </td>
				<td>
					<input class="datepicker1" placeholder="Click to select Date!"  type="text" name="payment_date" id="payment_date" value="<?php echo date('d/m/Y',strtotime($payment['payment_date'])); ?>" /><span class="DateError customError">Please select a date!</span>
                            </td>
</tr>

<tr>
<td>Rasid No<span class="requiredField">* </span> : </td>
				<td>
					<span><?php echo $rasid_prefix; ?></span><input type="text"  name="rasid_no" id="rasid_no"  value="<?php echo str_replace($rasid_prefix,"",$payment['rasid_no']); ?>" placeholder="Only Digits!" onblur="checkRasidNo();" onchange="checkRasidNo();" /><span id="agerror" class="availError">Rasid Number already taken!</span>
                </td>
</tr>

<tr>

<td class="firstColumnStyling">
Paid By : 
</td>

<td>
 <input type="text"   name="paid_by" id="paid_by"  />
</td>
</tr>

<td class="firstColumnStyling">
Remarks : 
</td>

<td>
<textarea name="remarks" id="remarks"><?php echo $payment['remarks']; ?></textarea>
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Remainder Date : 
</td>

<td>
 <input type="text" class="datepicker2"  name="remainder_date" id="remainder_date" placeholder="Click to select date!" value="<?php if($payment['remainder_date']!="0000-00-00" && $payment['remainder_date']!="1970-01-01") echo date('d/m/Y',strtotime($payment['remainder_date'])); ?>" /><span class="DateError customError">Please select a date!</span>
</td>
</tr>


 
</table>

<table>
<tr>
<td width="250px;"></td>
<td>
<input id="disableSubmit" type="submit" value="Edit Payment" class="btn btn-warning">
<a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=EMIdetails&id=<?php echo $file_id; ?>&state=<?php echo $emi_id; ?>"><input type="button" class="btn btn-success" value="Back"/></a>
</td>
</tr>

</table>

</form>
</div>
<div class="clearfix"></div>
<script>
document.balance=<?php echo -$balance; ?>;

 $( "#paid_by" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/broker_name.php',
                { term: request.term }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#paid_by" ).val(ui.item.label);
			return false;
		}
    });
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
