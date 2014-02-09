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
$customer_id=$_GET['state'];
if(checkForNumeric(getVehicleIdByFileId($file_id)))
{
	header("Location: ".WEB_ROOT."admin/customer/index.php?view=details&id=".$_GET['id']);
exit;
}
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">

<h4 class="headingAlignment">Add Vehicle Details </h4>
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
<form id="addLocForm" onsubmit="return submitOurVehicle();" action="<?php echo $_SERVER['PHP_SELF'].'?action=add'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurVehicle()">

<input name="customer_id" value="<?php echo $customer_id; ?>" type="hidden" />
<input name="file_id" value="<?php echo $file_id; ?>" type="hidden" />
<table id="insertVehicleTable" class="insertTableStyling no_print">

<tr>
<td width="220px">Vehicle Company<span class="requiredField">* </span> : </td>
				<td>
					<select id="vehicle_company" name="vehicle_company_id" onchange="createDropDownModelCompany(this.value)">
                        <option value="-1" >--Please Select Company--</option>
                        <?php
                            $companies = listVehicleCompanies();
                            foreach($companies as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['vehicle_company_id'] ?>"><?php echo $super['company_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
<td>Vehicle Model<span class="requiredField">* </span> : </td>
				<td>
					<select id="vehicle_model" name="model_id">
                        <option value="-1" >--Please Select Model--</option>
                     
                            </select> 
                            </td>
</tr>

<tr>
<td>Vehicle Dealer<span class="requiredField">* </span> : </td>
				<td>
					<select id="dealer" name="vehicle_dealer_id">
                        <option value="-1" >--Please Select Dealer--</option>
                       
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
       <td>Vehicle Condition<span class="requiredField">* </span> :</td>
           
           
        <td>
              <table>
               <tr><td><input type="radio"   name="condition"  value="1" checked="checked"></td><td>New</td></tr>
            <tr><td><input type="radio"  name="condition"  value="0" ></td><td>Used</td>
               </tr> 
            </table>
        </td>
 </tr>
 
 <tr>
<td>Vehicle Model Year<span class="requiredField">* </span> : </td>
				<td>
					<select id="model" name="model_year">
                        <option value="-1" >--Please Select Model Year--</option>
                       <?php
					   for($i=date('Y');$i>=1990;$i--)
					   {
						 ?>
                          <option value="<?php echo $i; ?>" ><?php echo $i; ?></option>
                         <?php  
						   }
					    ?>
                     </select> 
                            </td>
</tr>

<tr>
<td>Vehicle Type<span class="requiredField">* </span> : </td>
				<td>
					<select id="type" name="vehicle_type_id">
                        <option value="-1" >--Please Select Type--</option>
                        <?php
                            $types = listVehicleTypes();
                            foreach($types as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['vehicle_type_id'] ?>"><?php echo $super['vehicle_type'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>
 

<td class="firstColumnStyling">
Registration Number<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="vehicle_reg_no" id="vehicle_reg_no" placeholder="Only Letters and Digits!" onblur="checkAvailibilty(this,'agerror','ajax/regNo.php','')"/><span id="agerror" class="availError">Registration Number already taken!</span>	

</td>
</tr>

<tr>

<td class="firstColumnStyling">
Registration Date<span class="requiredField">* </span> : 
</td>

<td>
 <input type="text" size="12"  placeholder="Click to select Date!"  name="vehicle_reg_date" class="datepicker1 date" onchange="onChangeDate(this.value,this)"/><span class="ValidationErrors contactNoError">Please select a date!</span>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Engine Number<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" id="vehicle_engine_no" name="vehicle_engine_no"  placeholder="Only Digits!" onblur="checkAvailibilty(this,'agerror','ajax/engineNo.php','')"/><span id="agerror" class="availError">Engine Number already taken!</span>	

</td>
</tr>

<tr>
<td class="firstColumnStyling">
Chasis Number<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" id="vehicle_chasis_no" name="vehicle_chasis_no" placeholder="Only Digits!" onblur="checkAvailibilty(this,'agerror','ajax/chasisNo.php','')"/><span id="agerror" class="availError">Chasis Number already taken!</span>	

</td>
</tr>

<tr>
<td class="firstColumnStyling">
Fitness Exp Date<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" placeholder="Click to select Date!" name="fitness_exp_date" class="datepicker2 date" onchange="onChangeDate(this.value,this)"/><span class="ValidationErrors contactNoError">Please select a date!</span>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Permit Exp Date<span class="requiredField">* </span> : 
</td>

<td>
<input type="text"  placeholder="Click to select Date!" name="permit_exp_date" class="datepicker3 date" onchange="onChangeDate(this.value,this)"/><span class="ValidationErrors contactNoError">Please select a date!</span>
</td>
</tr>


<!-- for regenreation purpose Please donot delete -->

<tr id="vehicleProofTypeTr">
<td>Proof type : </td>
				<td id="vehicleProofTypeTd">
					<select id="proof" name="vehicleProofId[]" class="vehicleProofId" onblur="checkProofId(this.value,this)">
                        <option value="-1" >--Please Select Proof Type--</option>
                        <?php
                            $types = listVehicleProofTypes();
                            foreach($types as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['vehicle_document_type_id'] ?>"><?php echo $super['vehicle_document_type'] ?></option>
                             <?php } ?>
                              
                         
                            </select> <span class="ValidationErrors contactNoError">Please select a Proof Type!</span> 
                            </td>
</tr>




<tr id="vehicleProofNoTr">
<td> Proof Number : </td>
<td id="vehicleProofNoTd"><input type="text" class="vehicleProofNo" name="vehicleProofNo[]" placeholder="Only Letters and Digits!" onblur="checkProofNo(this.value,this)" /><span class="ValidationErrors contactNoError">Please enter Proof No (only numbers and letters)! OR Choose Proof Image!</span></td>
</tr>


<tr id="vehicleProofImgTr">
<td>
Proof Image : 
</td>
<td>
<input type="file" name="" class="customerFile"  /><br /> - OR - <br /><input type="button" name="scanProof" class="btn scanBtn" value="scan" /><input type="button" value="+" class="btn btn-primary addscanbtnGuarantor"/>
</td>
</tr> 

<!-- end of used for regeneration -->
</table>

<table style="margin-top:0px;margin-bottom:10px;">
<tr>
<td width="250px;">  </td>
<td><input type="button" class="btn btn-success" value="+ Add Proof" id="addVehicleProofBtn"/></td>
</tr>     
</table>

<table>
<tr>
<td width="250px;"></td>
<td>
<input id="disableSubmit" type="submit" value="Add Vehicle Details"  class="btn btn-warning">
<a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>

</table>

</form>

</div>
<div class="clearfix"></div>