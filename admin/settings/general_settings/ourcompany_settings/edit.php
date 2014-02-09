<?php
if(!isset($_GET['lid']))
{
	header("Location: index.php");
	}
$company_id=$_GET['lid'];
$company=getOurCompanyByID($company_id);
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Edit Our Company Details</h4>
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
<form id="addOurCompanyForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=edit'; ?>" method="post" onsubmit="return submitOurCompany()">
<table id="insertTable" class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Company Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="hidden" name="id" value="<?php echo $company['our_company_id']; ?>" />
<input type="text" name="name" id="txtname" value="<?php echo $company['our_company_name']; ?>"/>
</td>
</tr>

<tr>
<td>
Address<span class="requiredField">* </span> : 
</td>

<td>
<textarea name="address" id="txtaddress" cols="5" rows="6"><?php echo $company['our_company_address']; ?></textarea>
</td>
</tr>

<?php $contacts=getContactNoForOurCompany($company['our_company_id']);
if(!empty($contacts))
{
	for($i=0;$i<count($contacts);$i++)
	{
		$contact=$contacts[$i];
		
		?>
          <tr id="addcontactTr">
				<td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                <input type="text" class="contact" name="contact[]" value="<?php echo $contact[1]; ?>" /> <span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteContactbtn" onclick="deleteContactTr(this)"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
                </td>
			</tr>
<?php 	
	}
}?>

<tr id="addcontactTr">
<td>
Contact No : 
</td>

<td id="addcontactTd">
<input type="text" name="contact[]" class="contact" /> <span class="addContactSpan"><input type="button" title="add more contact no" value="+" class="btn btn-success addContactbtn"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
</td>
</tr>

<!-- for regenreation purpose Please donot delete -->

<tr id="addcontactTrGenerated">
<td>
Contact No : 
</td>

<td id="addcontactTd">
<input type="text" name="contact[]" class="contact"  />  <span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteContactbtn" onclick="deleteContactTr(this)"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
</td>
</tr>

<!-- end for regenreation purpose -->


<tr>
<td>Pincode<span class="requiredField">* </span> : </td>
<td><input type="text" id="txtpincode" name="pincode" value="<?php echo $company['our_company_pincode']; ?>"/></td>
</tr>

<tr>
<td>City<span class="requiredField">* </span> : </td>
				<td>
					<select id="txtcity" name="city">
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $cities = listCities();
                            foreach($cities as $super)
                              {
                             ?>
                             
                             <option  value="<?php echo $super['city_id'] ?>" <?php if($super['city_id']==$company['city_id']) { ?> selected <?php } ?>><?php echo $super['city_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
<td> Prefix<span class="requiredField">* </span> : </td>
<td> <input type="text" disabled="disabled" value="<?php echo $company['our_company_prefix']; ?>"/> <input type="hidden" name="prefix" value="<?php echo $company['our_company_prefix']; ?>"/></td>
</tr>

  <tr>
            <td> Subheading<span class="requiredField">* </span> : </td>
            <td> <input id="txtsubheading" type="text" name="sub_heading" value="<?php echo $company['sub_heading']; ?>"/> </td>
            </tr>
            

<tr>
<td></td>
<td>
<input type="submit" value="Edit" class="btn btn-warning">
<a href="index.php"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>
</table>
</form>  
</div>
<div class="clearfix"></div>
<script type="text/javascript">
 $( ".datepicker1" ).datepicker({
      changeMonth: true,
      changeYear: true,
	   dateFormat: 'dd/mm/yy'
    });

</script>