<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Add File Details</h4>
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=add'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurCompany()">

<table class="insertTableStyling no_print">

<tr>
<td width="230px">Agency Name<span class="requiredField">* </span> : </td>
				<td>
					<select id="agency_id"  name="agency_id" onchange="getPrefixFromAgency(this.value)" >
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $agencies = listAgencies();
							$companies = listOurCompanies();
                            foreach($agencies as $super)
							
                              {
                             ?>
                             
                             <option value="ag<?php echo $super['agency_id'] ?>"><?php echo $super['agency_name'] ?></option>
                             
                             <?php } ?>
                              
                             <?php 
							 
							 $companies = listOurCompanies();
                              foreach($companies as $com)
							
                              {
                             ?>
                             
                             <option value="oc<?php echo $com['our_company_id'] ?>"><?php echo $com['our_company_name'] ?></option>
                             
                             <?php } ?>
                            </select> 
                    </td>
                    
                    
                  
</tr>

<tr>
<td>
File Agreement No<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="agreementNo" id="agreementNo" placeholder="Only Letters and Digits" autocomplete="off" onblur="checkAvailibilty(this,'agerror','ajax/agreementNo.php?id=','agency_id')"/><span id="agerror" class="availError">Agreement Number already taken!</span>
</td>
</tr>

<tr>
<td>File Number<span class="requiredField">* </span> : </td>
				<td>
				<span id="agencyPrefix"></span> <input type="text" value="/" autocomplete="off"  name="fileNumber" id="fileNumber" placeholder="Only Digits" onblur="checkAvailibilty(this,'agerror','ajax/fileNumber.php?id=','agency_id')"/><span id="agerror" class="availError">File Number already taken!</span>	
                 </td>
</tr>

<tr>
<td>Broker<span class="requiredField">* </span> : </td>
				<td>
					<select id="broker_id" name="broker_id" class="broker" >
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $brokers = listBrokers();
                            foreach($brokers as $broker)
                              {
                             ?>
                             
                             <option value="<?php echo $broker['broker_id'] ?>"><?php echo $broker['broker_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>


</table>

<hr class="firstTableFinishing" />

<h4 class="headingAlignment">Add Customer's Details</h4>


<table id="insertCustomerTable" class="insertTableStyling no_print">


<tr>

<td width="230px" class="firstColumnStyling">
Customer's Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="customer_name" id="customer_name" class="person_name" placeholder="Only Letters"/>
</td>
</tr>

<tr>
<td>
Address<span class="requiredField">* </span> : 
</td>

<td>
<textarea name="customer_address" id="customer_address" class="address" cols="5" rows="6"></textarea>
</td>
</tr>


<tr>
<td>City<span class="requiredField">* </span> : </td>
				<td>
					<select id="customer_city_id" name="customer_city_id" class="city" onchange="createDropDownAreaCustomer(this.value)">
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $cities = listCitiesAlpha();
                            foreach($cities as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['city_id'] ?>"><?php echo $super['city_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
<td>Area<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="customer_area" class="city_area" id="city_area1" placeholder="Only Letters" />
                            </td>
</tr>

<tr>
<td>Pincode : </td>
<td><input type="text" name="customer_pincode" id="customer_pincode" class="pincode" placeholder="6 Digits!"/></td>
</tr>



 <tr id="addcontactTrCustomer">
                <td>
                Contact No<span class="requiredField">* </span> : 
                </td>
                
                <td id="addcontactTd">
                <input type="text" class="contact" id="customerContact" name="customerContact[]" placeholder="more than 6 Digits!" /> <span class="addContactSpan"><input type="button" title="add more contact no" value="+" class="btn btn-success addContactbtnCustomer"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
            </tr>

<!-- for regenreation purpose Please donot delete -->
            
            <tr id="addcontactTrGeneratedCustomer">
            <td>
            Contact No : 
            </td>
            
            <td id="addcontactTd">
            <input type="text" class="contact" name="customerContact[]" onblur="checkContactNo(this.value,this)" placeholder="more than 6 Digits!" />  <span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteContactbtn" onclick="deleteContactTr(this)"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
            </td>
            </tr>
       
       
<!-- end for regenreation purpose -->

<!-- for regenreation purpose Please donot delete -->

<tr id="customerProofTypeTr">
<td>Proof type : </td>
				<td id="customerProofTypeTd">
					<select id="proof" name="customerProofId[]" class="customerProofId" onblur="checkProofId(this.value,this)">
                        <option value="-1" >--Please Select Proof Type--</option>
                        <?php
                            $types = listProofTypes();
                            foreach($types as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['human_proof_type_id'] ?>"><?php echo $super['proof_type'] ?></option>
                             <?php } ?>
                              
                         
                            </select><span class="ValidationErrors contactNoError">Please select a Proof Type!</span> 
                            </td>
</tr>




<tr id="customerProofNoTr">
<td> Proof Number : </td>
<td id="customerProofNoTd"><input type="text" name="customerProofNo[]" autocomplete="off" class="customerProofNo" placeholder="Only Letters and Digits!" onblur="checkProofNo(this.value,this)"/><span class="ValidationErrors contactNoError">Please enter Proof No (only numbers and letters)!</span></td>
</tr>


<tr id="customerProofImgTr">
<td>
Proof Image : 
<br />(.jpg,.jpeg,.png,.gif,.pdf)
</td>
<td>
<input type="file" name="" class="customerFile"  /><br /> - OR - <br /><input type="button" name="scanProof" class="btn scanBtn" value="scan" /><input type="button" value="+" class="btn btn-primary addscanbtnCustomer"/>
</td>
</tr> 

<!-- end of used for regeneration -->
</table>

<table style="margin-top:10px;margin-bottom:10px;">
<tr>
<td width="260px;">  </td>
<td><input type="button" class="btn btn-success" value="+ Add Proof" id="addCustomerProofBtn"/></td>
</tr>     
</table>

<hr class="firstTableFinishing" />

<h4 class="headingAlignment">Add Guranteer's Details (All Details OR No Details)</h4>


<table id="insertGuarantorTable" class="insertTableStyling no_print">


<tr>

<td width="230px" class="firstColumnStyling">
Guranteer's Name : 
</td>

<td>
<input type="text" id="guarantor_name" name="guarantor_name"  placeholder="Only Letters!"/>
</td>
</tr>

<tr>
<td>
Guranteer's Address : 
</td>

<td>
<textarea id="guarantor_address" name="guarantor_address"  cols="5" rows="6"></textarea>
</td>
</tr>



<tr>
<td>City : </td>
				<td>
					<select id="guarantor_city_id" name="guarantor_city_id"  onchange="createDropDownAreaGuarantor(this.value)">
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $cities = listCitiesAlpha();
                            foreach($cities as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['city_id'] ?>"><?php echo $super['city_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
<td>Area : </td>
				<td>
					<input type="text" name="guarantor_area" id="city_area2" placeholder="Only Letters" />
                            </td>
</tr>

<tr>
<td>Pincode : </td>
<td><input type="text" name="guarantor_pincode" id="guarantor_pincode" class="pincode" placeholder="6 Digits!"/></td>
</tr>



 <tr id="addcontactTrGuarantor">
                <td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                <input type="text" class="contact" name="guarantorContact[]" id="guarantorContact" placeholder="more than 6 Digits!"  /> <span class="addContactSpan"><input type="button" title="add more contact no" value="+" class="btn btn-success addContactbtnGuarantor"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
            </tr>
            
            	<tr>
<td>  </td>
<td></td>
</tr>     

<!-- for regenreation purpose Please donot delete -->
            
            <tr id="addcontactTrGeneratedGuarantor">
            <td>
            Contact No : 
            </td>
            
            <td id="addcontactTd">
            <input type="text" class="contact" name="guarantorContact[]" onblur="checkContactNo(this.value,this)" placeholder="more than 6 digits!" />  <span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteContactbtn" onclick="deleteContactTr(this)"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
            </td>
            </tr>
            
<!-- end for regenreation purpose -->


<!-- for regenreation purpose Please donot delete -->

<tr id="guarantorProofTypeTr">
<td>Proof type : </td>
				<td id="guarantorProofTypeTd">
					<select id="proof" name="guarantorProofId[]" class="guarantorProofId" onblur="checkProofId(this.value,this)">
                        <option value="-1" >--Please Select Proof Type--</option>
                        <?php
                            $types = listProofTypes();
                            foreach($types as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['human_proof_type_id'] ?>"><?php echo $super['proof_type'] ?></option>
                             <?php } ?>
                              
                         
                            </select><span class="ValidationErrors contactNoError">Please select a Proof Type!</span>
                            </td>
</tr>




<tr id="guarantorProofNoTr">
<td> Proof Number : </td>
<td id="guarantorProofNoTd"><input type="text" name="guarantorProofNo[]" autocomplete="off" class="guarantorProofNo" placeholder="Only Letters and Digits!" onblur="checkProofNoGuarantor(this.value,this)" /><span class="ValidationErrors contactNoError">Please enter Proof No (only numbers and letters)!</span></td>
</tr>


<tr id="guarantorProofImgTr">
<td>
Proof Image : 
<br />(.jpg,.jpeg,.png,.gif,.pdf)
</td>
<td>
<input type="file" name="" onchange="" /><br /> - OR - <br /><input type="button" name="scanProof" class="btn scanBtn" value="scan" /><input type="button" value="+" class="btn btn-primary addscanbtnGuarantor"/>
</td>
</tr> 

<!-- end of used for regeneration -->

</table>

<table style="margin-top:0px;margin-bottom:10px;">
<tr>
<td width="260px;">  </td>
<td><input type="button" class="btn btn-success" value="+ Add Proof" id="addGuarantorProofBtn"/></td>
</tr>     
</table>


<hr class="firstTableFinishing" />

<h4 class="headingAlignment"> Loan Details </h4>


<table class="insertTableStyling no_print">


<tr>

<td class="firstColumnStyling">
Total Loan Amount<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="amount" id="amount" autocomplete="off" placeholder="only Digits" onchange="calculateEmi()" onblur="calculateEmi()"/>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Loan Amount Given In<span class="requiredField">* </span> : 
</td>

<td>
<input  type="radio" name="loan_amount_type" class="loan_amount_type" id="loan_amount_type_cash" value="1" onchange="generateChequeDetails()"  /> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="loan_amount_type_cash">Cash</label>
<input  type="radio" name="loan_amount_type" class="loan_amount_type" id="loan_amount_type_cheque" value="2" onchange="generateChequeDetails()" checked="checked"/> <label style="display:inline-block;top:3px;position:relative;" for="loan_amount_type_cheque">Cheque</label>
</td> 
</tr> 


<!--<input  type="hidden" name="loan_type"  value="1" /> -->
<tr>
<td class="firstColumnStyling">
Loan Type<span class="requiredField">* </span> : 
</td>

<td>
<input  type="radio" name="loan_type" class="loan_type" id="flat_loan" value="1" onselect="calculateEmi()" onchange="calculateEmi()" onblur="calculateEmi()" checked="checked"/> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="flat_loan">Flat Interest</label>
<input  type="radio" name="loan_type" class="loan_type" id="reducing_loan" value="2" onselect="calculateEmi()" onchange="calculateEmi()" onblur="calculateEmi()"/> <label style="display:inline-block;top:3px;position:relative;" for="reducing_loan">Reducing Rate Interest</label>
</td> 
</tr> 


<tr>

<td class="firstColumnStyling">
Rate of Interest (annually in %)<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="roi" id="roi" placeholder="only Digits" onchange="calculateEmi()" onblur="calculateEmi()"/> 
</td>
</tr>

<tr>
<td width="230px;" class="firstColumnStyling">
Loan Structure<span class="requiredField">* </span> : 
</td>

<td>
<input  type="radio" name="loan_scheme" class="loan_type" id="even_loan" value="1" onselect="changeLoanStr()" onchange="changeLoanStr()" onblur="changeLoanStr()" checked="checked"/> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="even_loan">EVEN (Same EMI)</label>
<input  type="radio" name="loan_scheme" class="loan_type" id="uneven_loan" value="2" onselect="changeLoanStr()" onchange="changeLoanStr()" onblur="changeLoanStr()"/> <label style="display:inline-block;top:3px;position:relative;" for="uneven_loan">UNEVEN (Different EMI)</label>
</td> 
</tr> 
<tr <?php if(defined('MULTIPLE_DURATION') && MULTIPLE_DURATION!=1) { ?> style="display:none;" <?php } ?>>

<td width="230px;" class="firstColumnStyling">
Duration Unit<span class="requiredField">* </span> : 
</td>

<td>
<select id="duration_unit" name="duration_unit" onchange="changeDurationStr(this.value)" onblur="changeDurationStr(this.value)" >
	<option value="1" selected="selected">1</option>
    <option value="3" >3</option>
    <option value="6" >6</option>
    <option value="12" >12</option>
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
<input type="text" name="duration" id="duration" placeholder="only Digits " onchange="calculateEmi()" onblur="calculateEmi()"/> 
</td>
</tr>

<tr>

<td class="firstColumnStyling">
EMI<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="emi" id="emi" placeholder="only Digits" onblur="calculateROI()" onchange="calculateROI()"/>
</td>
</tr>


</table>

<table id="uneven_loan_table" class="insertTableStyling no_print">
<tr>
<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="emi_uneven[]" class="emi" placeholder="only Digits" onblur="calculateROI()" onchange="calculateROI()"/> X <input type="text" name="duration_uneven[]" class="duration" placeholder="only Digits " onchange="calculateROI()" onblur="calculateROI()"/> <span class="addContactSpan"><input type="button" title="add more" value="+" class="btn btn-success addEMIDurationBtn"/></span><span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
</td>
</tr>

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
<input onchange="onChangeDate(this.value,this)" type="text" id="approvalDate" autocomplete="off" size="12"  name="approvalDate" class="datepicker1 datepick" placeholder="Click to Select!" /><span class="DateError customError">Please select a date!</span>
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Loan Starting Date<span class="requiredField">* </span> : 
</td>

<td>
<input onchange="onChangeDate(this.value,this)" type="text" id="startingDate" size="12" autocomplete="off"  name="startingDate" class="datepicker2 datepick" placeholder="Click to Select!" /><span class="customError DateError">Please select a date!</span>
</td>
</tr>

</table>
<div id="agencyParticipationDetails" style="display:none">
<hr class="firstTableFinishing" />
<h4 class="headingAlignment"> Agnecy Participation Details </h4>

<table id="agency_participation_table" class="insertTableStyling no_print">

<tr>
<td width="230px">Agency Loan Amount<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="agency_amount" id="agency_amount" placeholder="only Digits" autocomplete="off" />
                            </td>
</tr>

</table>


<table id="uneven_loan_table_agency" class="insertTableStyling no_print">
<tr>
<td width="230px;" class="firstColumnStyling">
EMI X Duration (In months)<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="agency_emi[]" class="emi_agency" placeholder="only Digits" /> X <input type="text" name="agency_duration[]" class="duration_agency" placeholder="only Digits "/> <span class="addContactSpan"><input type="button" title="add more" value="+" class="btn btn-success addEMIDurationBtnAgency"/></span><span class="ValidationErrors contactNoError">Please enter EMI X Duration (only numbers)</span>
</td>
</tr>

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

<div id="loanChequeDetails">
<hr class="firstTableFinishing" />
<h4 class="headingAlignment"> Loan Cheque Details </h4>

<table id="" class="insertTableStyling no_print">

<tr>
<td width="230px">Bank Name<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="bank_name" id="bank_name" placeholder="only Letters" autocomplete="off" />
                            </td>
</tr>

<tr>
<td>Branch Name<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="branch_name" id="branch_name" placeholder="only Letters" autocomplete="off"/>
                            </td>
</tr>

<td class="firstColumnStyling">
Cheque Amount<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="cheque_amount" id="cheque_amount" placeholder="only Digits"/>
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Cheque Date<span class="requiredField">* </span> : 
</td>

<td>
 <input type="text" size="12" onchange="onChangeDate(this.value,this)" autocomplete="off" id="chequeDate"  name="cheque_date" class="datepicker3 datepick" placeholder="Click to Select!" /><span class="ValidationErrors contactNoError">Please select a date!</span>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Cheque Number<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="cheque_no" autocomplete="off" id="cheque_no" placeholder="only Digits" onblur="checkAvailibilty(this,'agerror','ajax/chequeNo.php?id=','')"/><span id="agerror" class="availError">File Number already taken!</span>	
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Axin Number : 
</td>

<td>
<input type="text" name="axin_no" id="axin_no" placeholder="only Digits"/>
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
<td width="260px"></td>
<td>
<input type="submit" value="Add Customer" id="disableSubmit" class="btn btn-warning">
</td>
</tr>
</table>
</form>

</div>
<div class="clearfix"></div>
<script>
 $( "#city_area1" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/city_area.php',
                { term: request.term, city_id:$('#customer_city_id').val() }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#city_area1" ).val(ui.item.label);
			return false;
		}
    });
 $( "#city_area2" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/city_area.php',
                { term: request.term, city_id:$('#guarantor_city_id').val() }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#city_area2" ).val(ui.item.label);
			return false;
		}
    });
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