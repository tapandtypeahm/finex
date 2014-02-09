<?php
if(!isset($_GET['id']))
{
header("Location: ".WEB_ROOT."admin/search");
exit;
}

if(!isset($_GET['access']) && $_GET['access']="approved")
{
header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$_GET['id']);
exit;
}
$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
if(is_array($file) && $file!="error")
{
	$guarantor=getGuarantorDetailsByFileId($file_id);
	$guarantor_id=$guarantor['guarantor_id'];
	
}
else
{
	$_SESSION['ack']['msg']="Invalid File!";
	$_SESSION['ack']['type']=4; // 4 for error
	header("Location: ".WEB_ROOT."admin/search");
	
}
?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Edit Guarantor Details</h4>

<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=editGuarantor'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurCompany()">
<input name="lid" value="<?php echo $guarantor_id ?>" type="hidden">
<input name="file_id" value="<?php echo $file_id ?>" type="hidden">
<table id="insertGuarantorTable" class="insertTableStyling no_print">


<tr>

<td width="220px" class="firstColumnStyling">
Gurantor's Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="guarantor_name" id="guarantor_name" class="person_name" placeholder="Only Letters" value="<?php echo $guarantor['guarantor_name']; ?>"/>
</td>
</tr>

<tr>
<td>
Address<span class="requiredField">* </span> : 
</td>

<td>
<textarea name="guarantor_address" id="guarantor_address" class="address" cols="5" rows="6"><?php $address=str_replace(array('<pre>','</pre>'),"",$guarantor['guarantor_address']);  echo $address;?></textarea>
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
                             
                             <option value="<?php echo $super['city_id'] ?>" <?php if($super['city_id']==$guarantor['city_id']) { ?> selected <?php } ?>><?php echo $super['city_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
<td>Area<span class="requiredField">* </span> : </td>
				<td>
					<input type="text" name="guarantor_area" class="city_area" id="city_area1" placeholder="Only Letters" value="<?php $area=getAreaByID($guarantor['area_id']); echo $area[2]; ?>"/>
                            </td>
</tr>

<tr>
<td>Pincode : </td>
<td><input type="text" name="guarantor_pincode" id="guarantor_pincode" class="pincode" placeholder="6 Digits!" value="<?php if($guarantor['guarantor_pincode']!=0) echo $guarantor['guarantor_pincode']; ?>"/></td>
</tr>


<?php  $contactNumbers = $guarantor['contact_no'];
$lj=0;
foreach($contactNumbers as $contact)
{
 ?>
  <tr>
            <td>
            Contact No<?php if($lj==0) { ?><span class="requiredField">* </span> <?php } ?> : 
            </td>
            
            <td id="addcontactTd">
            <input type="text" class="contact" <?php if($lj==0) { ?> id="guarantorContact" <?php } ?> name="guarantorContact[]" <?php if($lj!=0) { ?> onblur="checkContactNo(this.value,this)" <?php } ?> placeholder="more than 6 Digits!" value="<?php echo $contact[0]; ?>" /><span></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
            </td>
            </tr>
<?php
$lj++;
 } ?> 


 <tr id="addcontactTrCustomer">
                <td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                <input type="text" class="contact" <?php if($lj<1) { ?> id="guarantorContact" <?php } ?> name="guarantorContact[]" placeholder="more than 6 Digits!" <?php if($lj!=0) { ?> onblur="checkContactNo(this.value,this)" <?php } ?> /> <span class="addContactSpan"><input type="button" title="add more contact no" value="+" class="btn btn-success addContactbtnCustomer"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
            </tr>

<!-- for regenreation purpose Please donot delete -->
            
            <tr id="addcontactTrGeneratedCustomer">
            <td>
            Contact No : 
            </td>
            
            <td id="addcontactTd">
            <input type="text" class="contact" name="guarantorContact[]" onblur="checkContactNo(this.value,this)" placeholder="more than 6 Digits!" />  <span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteContactbtn" onclick="deleteContactTr(this)"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
            </td>
            </tr>
       
       
<!-- end for regenreation purpose -->

<!-- for regenreation purpose Please donot delete -->

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
<td><input type="submit" id="disableSubmit" class="btn btn-warning" value="Edit"/> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&id='.$file_id ?>"><input type="button" value="Back" class="btn btn-success" /></a>
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