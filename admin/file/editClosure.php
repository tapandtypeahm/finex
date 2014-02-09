<?php
if(!isset($_GET['id']))
{
	header("Location: ".WEB_ROOT);
	exit;
	}
$file_id=$_GET['id'];	
$ag_id_array=getAgencyOrCompanyIdFromFileId($file_id);
if($ag_id_array[0]=='agency')
		{
			$agency_id=$ag_id_array[1];
			$oc_id=null;
			$rasid_no=getRasidnoForAgencyId($agency_id);
			$rasid_counter=getRasidCounterForAgencyId($agency_id);
			$rasid_prefix=getAgencyPrefixFromAgencyId($agency_id);
			}
		else if($ag_id_array[0]=='oc')
		{
			$oc_id=$ag_id_array[1];
			$agency_id=null;
			$rasid_no=getRasidNoForOCID($oc_id);
			$rasid_counter=getRasidCounterForOCId($oc_id);
			$rasid_prefix=getPrefixFromOCId($oc_id);
}		
$closure=getPrematureClosureDetails($file_id);
if($closure['mode']==2)
{
	$chequePayment=getClosureChequeDetails($closure['file_closed_id']);
	}
else
{
	$chequePayment=false;
	}	
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Premature File Closure</h4>
<?php 
if(isset($_SESSION['ack']['msg']) && isset($_SESSION['ack']['type']))
{
	
	$msg=$_SESSION['ack']['msg'];
	$type=$_SESSION['ack']['type'];
	
	
		if($msg!=null && $msg!="" && $type>0)
		{
?>
<div class="alert  <?php if(isset($type) && $type>0 && $type<4) echo "alert-success"; else echo "alert-error" ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php if(isset($type)  && $type>0 && $type<4) { ?> <strong>Success!</strong> <?php } else if(isset($type)   && $type>3) { ?> <strong>Warning!</strong> <?php } ?> <?php echo $msg; ?>
</div>
<?php
		
		
		}
	if(isset($type) && $type>0)
		$_SESSION['ack']['type']=0;
	if($msg!="")
		$_SESSION['ack']['msg']=="";
}

?>
<form onsubmit="submitClosure();" id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=editClosure'; ?>" method="post" enctype="multipart/form-data">
<input name="lid" value="<?php echo $file_id; ?>" type="hidden">
<input name="lid" value="<?php echo $file_id; ?>" type="hidden">
<input name="file_id" value="<?php echo $file_id; ?>" id="file_id" type="hidden" />
<input value="<?php if($agency_id!=null) echo $agency_id; else echo 0; ?>" id="agency_id" type="hidden" />
<input  value="<?php if($oc_id!=null) echo $oc_id; else echo 0; ?>" id="oc_id" type="hidden" />
<input  value="<?php echo $payment['rasid_no']; ?>" id="old_rasid_no" type="hidden" />
<table class="insertTableStyling no_print">

<tr>
<td width="220px">Closure Date<span class="requiredField">* </span> : </td>
				<td>
					<input onchange="onChangeDate(this.value,this)" value="<?php echo date('d/m/Y',strtotime($closure['file_close_date'])); ?>"  type="text" id="closureDate" autocomplete="off" size="12"  name="close_date" class="datepicker1 datepick" placeholder="Click to Select!" /><span class="DateError customError">Please select a date!</span>
</td>
                    
                    
                  
</tr>
<tr>
<td>
Amount<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" value="<?php echo $closure['amount_paid'] ?>" name="amount" id="amount"/>
<input type="hidden"  name="file_id" value="<?php echo $file_id; ?>"/>
</td>
</tr>

<tr>
<td>Payment Mode<span class="requiredField">* </span> : </td>
				<td>
					<table>
               <tr><td><input type="radio" onChange="checkMode(this.value);"  name="mode" id="cash"  value="1" <?php if($closure['mode']==1)  {?> checked="checked"<?php } ?>></td><td><label for="cash">Cash</label></td></tr>
            <tr><td><input type="radio" onChange="checkMode(this.value);" id="mode" name="mode"  value="2" <?php if($closure['mode']==2)  {?> checked="checked"<?php } ?> ></td><td><label for="mode">Cheque</label></td>
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
<td width="220px">Rasid No : </td>
				<td>
					<span><?php echo $rasid_prefix; ?></span><input type="text" name="rasid_no" id="rasid_no" placeholder="Only Digits!" value="<?php  echo str_replace($rasid_prefix,"",$closure['rasid_no']); ?>" onchange="checkRasidNo();" /><span id="agerror" class="availError">Rasid Number already taken!</span>
                </td>
</tr>

<tr>
<td>
Remarks : 
</td>
<td>
<textarea   name="remarks"  id="remarks_remainder"><?php echo $closure['remarks'] ?></textarea>
</td>
</tr>

<tr>
<td></td>
<td>
<input id="disableSubmit" type="submit" value="Edit" class="btn btn-warning"> <a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><input type="button" class="btn btn-success" value="Back" /></a>
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