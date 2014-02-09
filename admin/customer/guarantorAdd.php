<?php
if(!isset($_GET['id']) || !isset($_GET['state']))
{
header("Location: ".WEB_ROOT."admin/search");
exit;
}
$file_id=$_GET['id'];
$customer_id=$_GET['state'];
if(checkForNumeric($file_id,$customer_id))
{
?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Add Guarantor Details</h4>

<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=addGuarantor'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurCompany()">
<input name="customer_id" value="<?php echo $customer_id; ?>" type="hidden">
<input name="file_id" value="<?php echo $file_id; ?>" type="hidden">
<table id="insertGuarantorTable" class="insertTableStyling no_print">


<tr>

<td width="220px" class="firstColumnStyling">
Gurantor's Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="guarantor_name" id="guarantor_name" class="person_name" placeholder="Only Letters" />
</td>
</tr>

<tr>
<td>
Address<span class="requiredField">* </span> : 
</td>

<td>
<textarea name="guarantor_address" id="guarantor_address" class="address" cols="5" rows="6"></textarea>
</td>
</tr>


<tr>
<td>City<span class="requiredField">* </span> : </td>
				<td>
					<select id="guarantor_city_id" name="guarantor_city_id" class="city" onchange="createDropDownAreaCustomer(this.value)">
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $cities = listCitiesAlpha();
                            foreach($cities as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['city_id'] ?>" ><?php echo $super['city_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
<td>Area<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="guarantor_area" class="city_area" id="city_area1" placeholder="Only Letters" />
                            </td>
</tr>

<tr>
<td>Pincode : </td>
<td><input type="text" name="guarantor_pincode" id="guarantor_pincode" class="pincode" placeholder="6 Digits!" /></td>
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
<td width="250px;">  </td>
<td><input type="button" class="btn btn-success" value="+ Add Proof" id="addGuarantorProofBtn"/></td>
</tr>     
</table>

    
</table>

<table style="margin-top:10px;margin-bottom:10px;">
<tr>
<td width="250px;"> </td>
<td><input type="submit" class="btn btn-warning" id="disableSubmit" value="Edit"/> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&id='.$file_id ?>"><input type="button" value="Back" class="btn btn-success" /></a>
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
                { term: request.term, city_id:$('#guarantor_city_id').val() }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#city_area1" ).val(ui.item.label);
			return false;
		}
    });
</script>
<?php } ?>