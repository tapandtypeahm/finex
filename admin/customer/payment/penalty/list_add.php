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
$loan_id=$_GET['state'];

$days_left=getPenaltyDaysLeftForLoan($loan_id);
$paid_by=getPaidByForFileID($file_id);
$ag_id_array=getAgnecyIdFromLoanId($loan_id);
if(is_numeric($ag_id_array[0]))
		{
			$agency_id=$ag_id_array[0];
			$oc_id=null;
			$rasid_no=getRasidnoForAgencyId($agency_id);
			$rasid_counter=getRasidCounterForAgencyId($agency_id);
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_no=getRasidNoForOCID($oc_id);
			$rasid_counter=getRasidCounterForOCId($oc_id);
			$rasid_prefix=getPrefixFromOCId($oc_id);
			}	
while(checkForDuplicateRasidNo($rasid_prefix.$rasid_counter,$loan_id))
{
	if(is_numeric($ag_id_array[0]))
		{
			
			$agency_id=$ag_id_array[0];
			incrementRasidCounterForAgency($agency_id);
			$oc_id=null;
			$rasid_no=getRasidnoForAgencyId($agency_id);
			$rasid_counter=getRasidCounterForAgencyId($agency_id);
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			}
		else if(is_numeric($ag_id_array[1]))
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			incrementRasidNoForOCID($oc_id);
			$rasid_no=getRasidNoForOCID($oc_id);
			$rasid_counter=getRasidCounterForOCId($oc_id);
			$rasid_prefix=getPrefixFromOCId($oc_id);
}	
	
	}			
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">

<h4 class="headingAlignment"> Add Penalty to Customer </h4>
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
<form onsubmit="return submitPayment();" id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=add'; ?>" method="post" enctype="multipart/form-data" >
<input name="loan_id" value="<?php echo $loan_id; ?>" type="hidden" />
<input name="file_id" value="<?php echo $file_id; ?>" type="hidden" />
<input name="auto_rasid_no" value="<?php echo $rasid_counter; ?>" id="auto_rasid_no" type="hidden"  />
<table id="insertInsuranceTable" class="insertTableStyling no_print">

<tr>
<td width="220px">Penalty For Days<span class="requiredField">* </span>  : </td>
				<td>
					<input value="<?php echo $days_left; ?>" type="text" name="days_paid" id="days" placeholder="Only Digits!" /><span class="DateError customError">Days Should less than <?php echo $days_left+1; ?> Days. !</span>
                            </td>
</tr>

<tr>
<td>Amount/Day<span class="requiredField">* </span>  : </td>
				<td>
					<input type="text" name="amount" id="amount" placeholder="Only Digits!" autocomplete="off" />
                            </td>
</tr>

<tr>
<td>Rasid No<span class="requiredField">* </span> : </td>
				<td>
					<span><?php echo $rasid_prefix; ?></span><input type="text" name="rasid_no" id="rasid_no_penalty" placeholder="Only Digits!" value="<?php echo $rasid_counter; ?>"  /><span id="agerror" class="availError">Rasid Number already taken!</span>
                </td>
</tr>

<tr>

<td class="firstColumnStyling">
Paid By : 
</td>

<td>
 <input type="text"   name="paid_by" id="paid_by" value="<?php echo $paid_by ?>"  />
</td>
</tr>


<tr>
<td>Payment Mode<span class="requiredField">* </span>  : </td>
				<td>
					<table>
               <tr><td><input type="radio" onChange="checkMode(this.value);"  name="mode" id="cash"  value="1" checked="checked"></td><td><label for="cash">Cash</label></td></tr>
            <tr><td><input type="radio" onChange="checkMode(this.value);" id="mode" name="mode"  value="2" ></td><td><label for="mode">Cheque</label></td>
               </tr> 
            </table>
                            </td>
</tr>
</table>
<table id="chequePaymentTable" class="insertTableStyling no_print">
<tr>
<td width="220px">Bank Name<span class="requiredField">* </span>  : </td>
				<td>
					<input type="text" name="bank_name" id="bank" placeholder="Only Letters!" autocomplete="off" />
                            </td>
</tr>
<tr>
<td width="220px">Branch Name<span class="requiredField">* </span>  : </td>
				<td>
					<input type="text" name="branch_name" id="branch" placeholder="Only Letters!" autocomplete="off" />
                            </td>
</tr>
<tr>
<td width="220px">Cheque No<span class="requiredField">* </span>  : </td>
				<td>
					<input type="text" name="cheque_no" id="cheque_no" placeholder="Only Digits!" />
                            </td>
</tr>
<tr>
<td width="220px">Cheque Date<span class="requiredField">* </span>  : </td>
				<td>
					<input type="text" name="cheque_date" id="cheque_date" class="datepicker3" placeholder="click to select date!" /><span class="DateError customError">Please select a date!</span>
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
<td width="220px">Payment Date<span class="requiredField">* </span>  : </td>
				<td>
					<input type="text" name="payment_date" id="payment_date" class="datepicker1" placeholder="click to select date!"/><span class="DateError customError">Please select a date!</span>
                            </td>
</tr>


 
</table>

<table>
<tr>
<td width="250px;"></td>
<td>
<input id="disableSubmit" type="submit" value="Add Penalty" class="btn btn-warning">
<a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=penaltyDetails&id=<?php echo $file_id; ?>"><input type="button" value="Back" class="btn btn-success" /></a>
</td>
</tr>

</table>

</form>
</div>
<div class="clearfix"></div>
<script>
document.balance=<?php echo 10000000; ?>;
document.days_left=<?php echo $days_left; ?>;
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