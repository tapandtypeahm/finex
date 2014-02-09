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
<form>

<table class="insertTableStyling no_print">

<tr style="display:none;">
<td width="230px;" class="firstColumnStyling">
Loan Structure<span class="requiredField">* </span> : 
</td>

<td>
<input  type="radio" name="loan_scheme" class="loan_type" id="even_loan" value="1" onselect="changeLoanStr()" onchange="changeLoanStr()" onblur="changeLoanStr()" checked="checked"/> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="even_loan">EVEN (Same EMI)</label>
<input  type="radio" name="loan_scheme" class="loan_type" id="uneven_loan" value="2" onselect="changeLoanStr()" onchange="changeLoanStr()" onblur="changeLoanStr()"/> <label style="display:inline-block;top:3px;position:relative;" for="uneven_loan">UNEVEN (Different EMI)</label>
</td> 
</tr> 

<tr>

<td class="firstColumnStyling">
Total Loan Amount : 
</td>

<td>
<input type="text" name="amount" id="amount" autocomplete="off" placeholder="only Digits" onchange="calculateEmi()" onblur="calculateEmi()"/>
</td>
</tr>


<tr>

<td class="firstColumnStyling">
Loan Duration (In months) : 
</td>

<td>
<input type="text" name="duration" id="duration" placeholder="only Digits " onchange="calculateEmi()" onblur="calculateEmi()"/> 
</td>
</tr>
<input  type="hidden" name="loan_type"  value="1" />
<tr>
<td class="firstColumnStyling">
Loan Type : 
</td>

<td>
<input  type="radio" name="loan_type" class="loan_type" id="flat_loan" value="1" onselect="calculateEmi()" onchange="calculateEmi()" onblur="calculateEmi()" checked="checked"/> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="flat_loan">Flat Interest</label>
<input  type="radio" name="loan_type" class="loan_type" id="reducing_loan" value="2" onselect="calculateEmi()" onchange="calculateEmi()" onblur="calculateEmi()"/> <label style="display:inline-block;top:3px;position:relative;" for="reducing_loan">Reducing Rate Interest</label>
</td> 
</tr> 


<tr>

<td class="firstColumnStyling">
Rate of Interest (annually in %) : 
</td>

<td>
<input type="text" name="roi" id="roi" placeholder="only Digits" onchange="calculateEmi()" onblur="calculateEmi()"/> 
</td>
</tr>



<tr>



<td></td>
				<td>
				 <input type="button" value="Calculate" class="btn btn-warning" onclick="calculateEmi()"/>	
                </td>
</tr>

<tr>

<td class="firstColumnStyling">
EMI : 
</td>

<td>
<input disabled="disabled" type="text" name="emi" id="emi"  />
</td>
</tr>



</table>

</form>

</div>
<div class="clearfix"></div>