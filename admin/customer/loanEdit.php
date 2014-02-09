<?php

if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
$loan=getLoanDetailsByFileId($file_id);
$loan_id=$loan['loan_id'];
$payment=getTotalPaymentForLoan($loan_id);

$paid_scheme=getPaidEmiSchemeForLoan($loan_id);
$agency_participation_details=getLoanSchemeAgency($loan['loan_id']);
if($paid_scheme!="error")
$un_paid_scheme=getunPaidEmiSchemeForLoan($loan_id);

$agency_comapny_array=getAgencyOrCompanyIdFromFileId($file_id);
$agency_type=$agency_comapny_array[0];
?>
<?php if($agency_type=="agency") { ?>
<script type="text/javascript">
document.agency_type=2;
</script>
<?php } ?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Edit Loan Details</h4>
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

<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=editLoan'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurCompany()">
<input type="hidden" name="file_id" value="<?php echo $file_id; ?>" />
<input type="hidden" name="lid" value="<?php echo $loan_id; ?>" />
<table class="insertTableStyling no_print">


<tr>

<td class="firstColumnStyling">
Total Loan Amount<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="amount" id="amount" autocomplete="off" placeholder="only Digits" onchange="calculateEmi()" onblur="calculateEmi()" value="<?php echo $loan['loan_amount'] ?>"/>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Loan Amount Given In<span class="requiredField">* </span> : 
</td>

<td>
<input  type="radio" name="loan_amount_type" class="loan_amount_type" id="loan_amount_type_cash" value="1" onchange="generateChequeDetails()" <?php   if($loan['loan_amount_type']==1) { ?> checked="checked" <?php } ?>  /> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="loan_amount_type_cash">Cash</label>
<input  type="radio" name="loan_amount_type" class="loan_amount_type" id="loan_amount_type_cheque" value="2" onchange="generateChequeDetails()" <?php   if($loan['loan_amount_type']==2) { ?> checked="checked" <?php } ?>/> <label style="display:inline-block;top:3px;position:relative;" for="loan_amount_type_cheque">Cheque</label>
</td> 
</tr> 


<!--<input  type="hidden" name="loan_type"  value="1" /> -->

 <tr>

<td class="firstColumnStyling">
Loan Type<span class="requiredField">* </span> : 
</td>

<td>
<input  type="radio" name="loan_type" id="flat_loan" value="1" onselect="calculateEmi()" onchange="calculateEmi()" onblur="calculateEmi()" <?php if($loan['loan_type']==1) { ?> checked="checked" <?php } ?>/> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="flat_loan">Flat Interest</label>
<input  type="radio" name="loan_type" id="reducing_loan" value="2" onselect="calculateEmi()" onchange="calculateEmi()" onblur="calculateEmi()" <?php if($loan['loan_type']==2) { ?> checked="checked" <?php } ?> /> <label style="display:inline-block;top:3px;position:relative;" for="reducing_loan">Reducing Rate Interest</label>
</td>
</tr>


<tr>

<td class="firstColumnStyling">
Rate of Interest (annually in %)<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="roi" id="roi" placeholder="only Digits" onchange="calculateEmi()" onblur="calculateEmi()" value="<?php echo $loan['roi'] ?>"/> 
</td>
</tr>

<tr>
<td width="230px;" class="firstColumnStyling">
Loan Structure<span class="requiredField">* </span> : 
</td>

<td>
<input  type="radio" name="loan_scheme" class="loan_type" id="even_loan" value="1" onselect="changeLoanStr()" onchange="changeLoanStr()" onblur="changeLoanStr()" <?php if($loan['loan_scheme']==1) { ?> checked="checked" <?php } ?>/> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="even_loan">EVEN (Same EMI)</label>
<input  type="radio" name="loan_scheme" class="loan_type" id="uneven_loan" value="2" onselect="changeLoanStr()" onchange="changeLoanStr()" onblur="changeLoanStr()" <?php if($loan['loan_scheme']==2) { ?> checked="checked" <?php } ?>/> <label style="display:inline-block;top:3px;position:relative;" for="uneven_loan">UNEVEN (Different EMI)</label>
</td> 
</tr> 

<tr <?php if(defined('MULTIPLE_DURATION') && ( MULTIPLE_DURATION!=1 || $payment>0)) { ?> style="display:none;" <?php } ?>>

<td width="230px;" class="firstColumnStyling">
Duration Unit<span class="requiredField">* </span> : 
</td>

<td>
<select id="duration_unit" name="duration_unit" onchange="changeDurationStr(this.value)" onblur="changeDurationStr(this.value)" >
	<option value="1" <?php if($loan['duration_unit']==1) { ?>selected="selected" <?php } ?>>1</option>
    <option value="3"  <?php if($loan['duration_unit']==3) { ?>selected="selected" <?php } ?> >3</option>
    <option value="6"  <?php if($loan['duration_unit']==6) { ?>selected="selected" <?php } ?> >6</option>
    <option value="12"  <?php if($loan['duration_unit']==12) { ?>selected="selected" <?php } ?> >12</option>
</select>
</td>
</tr>
</table>

<table id="even_loan_table" class="insertTableStyling no_print">

<tr>

<td width="230px;" class="firstColumnStyling">
Loan Duration (In months)<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="duration" id="duration" placeholder="only Digits " onchange="calculateEmi()" onblur="calculateEmi()" value="<?php if($loan['loan_scheme']==1) echo $loan['loan_duration'] ?>" /> 
</td>
</tr>
<tr>

<td class="firstColumnStyling">
EMI<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="emi" id="emi" placeholder="only Digits" value="<?php if($loan['loan_scheme']==1) echo $loan['emi'] ?>" onblur="calculateROI()" onchange="calculateROI()"/>
</td>
</tr>
</table>
<table id="uneven_loan_table" class="insertTableStyling no_print">
<?php
if($paid_scheme!="error")
{
	for($i=0;$i<count($paid_scheme);$i++)
{
?>

<tr>
<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" disabled="disabled" value="<?php  echo $paid_scheme[$i]['emi']; ?>" class="emi" placeholder="only Digits" onblur="calculateROI()" onchange="calculateROI()"/> X <input type="text" disabled="disabled" class="duration" value="<?php  echo $paid_scheme[$i]['duration']; ?>" placeholder="only Digits " onchange="calculateROI()" onblur="calculateROI()"/<span class="ValidationErrors contactNoError">
</td>
</tr>

<?php }

if($un_paid_scheme!="error")
{
	for($i=0;$i<count($un_paid_scheme);$i++)
{
?>

<tr>
<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="emi_uneven[]" value="<?php  echo $un_paid_scheme[$i]['emi']; ?>" class="emi" placeholder="only Digits" onblur="calculateROI()" onchange="calculateROI()"/> X <input type="text" name="duration_uneven[]" class="duration" value="<?php  echo $un_paid_scheme[$i]['duration']; ?>" placeholder="only Digits " onchange="calculateROI()" onblur="calculateROI()"/><?php if($i==0) { ?> <span class="addContactSpan"><input type="button" title="add more" value="+" class="btn btn-success addEMIDurationBtn"/></span> <?php } else { ?><span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteEMIDurationBtn" onclick="deleteEMIDurationTr(this)"/></span> <?php } ?><span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
</td>
</tr>

<?php }
	
	}
	
	}
else if($loan['loan_scheme']==2)
{
	
	$loan_structure=getLoanScheme($loan_id);
	

if(count($loan_structure)>0)	
{ 
for($i=0;$i<count($loan_structure);$i++)
{
?>

<tr>
<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="emi_uneven[]" value="<?php  echo $loan_structure[$i]['emi']; ?>" class="emi" placeholder="only Digits" onblur="calculateROI()" onchange="calculateROI()"/> X <input type="text" name="duration_uneven[]" class="duration" value="<?php  echo $loan_structure[$i]['duration']; ?>" placeholder="only Digits " onchange="calculateROI()" onblur="calculateROI()"/><?php if($i==0) { ?> <span class="addContactSpan"><input type="button" title="add more" value="+" class="btn btn-success addEMIDurationBtn"/></span> <?php } else { ?><span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteEMIDurationBtn" onclick="deleteEMIDurationTr(this)"/></span> <?php } ?><span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
</td>
</tr>

<?php } } }
else
{ ?>
<tr>
<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="emi_uneven[]"  class="emi" placeholder="only Digits" onblur="calculateROI()" onchange="calculateROI()"/> X <input type="text" name="duration_uneven[]" class="duration"  placeholder="only Digits " onchange="calculateROI()" onblur="calculateROI()"/><span class="addContactSpan"><input type="button" title="add more" value="+" class="btn btn-success addEMIDurationBtn"/></span> <span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
</td>
</tr>
<?php } ?>

<!-- for regenreation purpose Please donot delete -->
            
            <tr id="EMIDurationTR">

<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
            
            <td id="addEMIDurationTd">
           <input type="text" name="emi_uneven[]" class="emi" placeholder="only Digits" onblur="calculateROI()" onchange="calculateROI()"/> X <input type="text" name="duration_uneven[]" class="duration" placeholder="only Digits " onchange="calculateROI()" onblur="calculateROI()"/>  <span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteEMIDurationBtn" onclick="deleteEMIDurationTr(this)"/></span><span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
                </td>
            </tr>
            
<!-- end for regenreat

ion purpose -->
</table>
<table id="loan_date_table" class="insertTableStyling no_print">
<tr>

<td width="230px" class="firstColumnStyling">
Loan Approval Date<span class="requiredField">* </span> : 
</td>

<td>
<input onchange="onChangeDate(this.value,this)" type="text" autocomplete="off" size="12"  name="approvalDate" class="datepicker1 datepick" placeholder="Click to Select!" value="<?php echo date('d/m/Y',strtotime($loan['loan_approval_date'])) ?>" /><span class="DateError customError">Please select a date!</span>
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Loan Starting Date<span class="requiredField">* </span> : 
</td>

<td>
<input onchange="onChangeDate(this.value,this)" type="text" size="12" autocomplete="off"  name="startingDate" class="datepicker2 datepick" placeholder="Click to Select!" value="<?php echo date('d/m/Y',strtotime($loan['loan_starting_date'])) ?>" /><span class="customError DateError">Please select a date!</span>
</td>
</tr>

</table>
<?php if($agency_type=="agency")
{ ?>
<div id="agencyParticipationDetails" >
<hr class="firstTableFinishing" />
<h4 class="headingAlignment"> Agnecy Participation Details </h4>

<table id="agency_participation_table" class="insertTableStyling no_print">

<tr>
<td width="230px">Agency Loan Amount<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="agency_amount" id="agency_amount" placeholder="only Digits" autocomplete="off" value="<?php echo $loan['agency_loan_amount']; ?>"/>
                            </td>
</tr>

</table>


<table id="uneven_loan_table_agency" class="insertTableStyling no_print">
<?php
if($agency_participation_details!="error")
{
 for($j=0;$j<count($agency_participation_details);$j++)
{ 
$agency_participation_detail=$agency_participation_details[$j];
if($j==0)
{
 ?>
<tr>
<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="agency_emi[]" class="emi_agency" placeholder="only Digits" value="<?php echo $agency_participation_detail['agency_emi']; ?>" /> X <input type="text" name="agency_duration[]" class="duration_agency" placeholder="only Digits " value="<?php echo $agency_participation_detail['agency_duration']; ?>"/> <span class="addContactSpan"><input type="button" title="add more" value="+" class="btn btn-success addEMIDurationBtnAgency"/></span><span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
</td>
</tr>
<?php
}else
{
?>
<tr>
<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="agency_emi[]" class="emi_agency" placeholder="only Digits" value="<?php echo $agency_participation_detail['agency_emi']; ?>" /> X <input type="text" name="agency_duration[]" class="duration_agency" placeholder="only Digits " value="<?php echo $agency_participation_detail['agency_duration']; ?>"/> <span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteEMIDurationBtnAgency" onclick="deleteEMIDurationAgencyTr(this)"/></span><span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
</td>
</tr>
<?php	
	}
 } }
else if($agency_participation_details=="error" && $agency_type="agency")
{
?>
<tr>
<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="agency_emi[]" class="emi_agency" placeholder="only Digits" /> X <input type="text" name="agency_duration[]" class="duration_agency" placeholder="only Digits "/> <span class="addContactSpan"><input type="button" title="add more" value="+" class="btn btn-success addEMIDurationBtnAgency"/></span><span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
</td>
</tr>
<?php	
	
	}

 ?>
<!-- for regenreation purpose Please donot delete -->
            
            <tr id="EMIDurationAgencyTR">

<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
            
            <td id="addEMIDurationTd">
           <input type="text" name="agency_emi[]" class="emi_agency" placeholder="only Digits" /> X <input type="text" name="agency_duration[]" class="duration_agency" placeholder="only Digits " />  <span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteEMIDurationBtnAgency" onclick="deleteEMIDurationAgencyTr(this)"/></span><span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
                </td>
            </tr>
            
<!-- end for regenreat

ion purpose -->
</table>
</div>
<?php } ?>
<div id="loanChequeDetails" <?php if($loan['loan_amount_type']==1){ ?>style="display:none;" <?php } ?>>
<hr class="firstTableFinishing" />
<h4 class="headingAlignment"> Loan Cheque Details </h4>

<?php

if($loan['loan_amount_type']==2)
{
	  $loan_cheque=getLoanChequeByLoanId($loan['loan_id']);
	
	 }
else
{
	$loan_cheque=false;
	}	 
 ?>

<table id="" class="insertTableStyling no_print">

<tr>
<td width="220px">Bank Name<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="bank_name" id="bank_name" placeholder="only Letters" autocomplete="off" value="<?php if($loan_cheque!=false && isset($loan_cheque['bank_id'])) echo getBankNameByID($loan_cheque['bank_id']); ?>" />
                            </td>
</tr>

<tr>
<td>Branch Name<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="branch_name" id="branch_name" placeholder="only Letters" autocomplete="off" value="<?php if($loan_cheque!=false && isset($loan_cheque['branch_id'])) echo getBranchhById($loan_cheque['branch_id']); ?>"/>
                            </td>
</tr>

<td class="firstColumnStyling">
Cheque Amount<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="cheque_amount" id="cheque_amount" placeholder="only Digits" value="<?php if($loan_cheque!=false && isset($loan_cheque['loan_cheque_amount'])) echo $loan_cheque['loan_cheque_amount']; ?>"/>
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Cheque Date<span class="requiredField">* </span> : 
</td>

<td>
 <input type="text" size="12" value="<?php if($loan_cheque!=false && isset($loan_cheque['loan_cheque_date'])) echo date('d/m/Y',strtotime($loan_cheque['loan_cheque_date'])); ?>" onchange="onChangeDate(this.value,this)" autocomplete="off" id="chequeDate"  name="cheque_date" class="datepicker3 datepick" placeholder="Click to Select!" /><span class="ValidationErrors contactNoError">Please select a date!</span>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Cheque Number<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="cheque_no" value="<?php if($loan_cheque!=false && isset($loan_cheque['loan_cheque_no'])) echo $loan_cheque['loan_cheque_no']; ?>" autocomplete="off" id="cheque_no" placeholder="only Digits" onblur="checkAvailibilty(this,'agerror','ajax/chequeNo.php?id=','')"/><span id="agerror" class="availError">File Number already taken!</span>	
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Axin Number : 
</td>

<td>
<input type="text" name="axin_no" value="<?php if($loan_cheque!=false && isset($loan_cheque['loan_cheque_axin_no']) && $loan_cheque['loan_cheque_axin_no']!="NA") echo $loan_cheque['loan_cheque_axin_no']; ?>" id="axin_no" placeholder="only Digits"/>
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
					$bank_cash_ledgers=listAccountingLedgers();
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
</div>

<table>
<tr>
<td width="260px;"></td>
<td>
<input type="submit" id="disableSubmit" value="Edit" class="btn btn-warning">
<a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&id='.$file_id ?>"><input type="button" value="Back" class="btn btn-success" /></a>
</td>
</tr>
</table>

</div>

<div class="clearfix"></div>
<script>
 $( "#bank_name" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/bank_name.php',
                { term: request.term }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#bank_name" ).val(ui.item.label);
			return false;
		}
    });
	 $( "#branch_name" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/branch_name.php',
                { term: request.term, bank_name:$('#bank_name').val() }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#branch_name" ).val(ui.item.label);
			return false;
		}
    });	
</script>