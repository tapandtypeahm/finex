<?php 
if(!isset($_GET['lid']))
{
	header("Location: index.php");
	}
$agency_id=$_GET['lid'];
$agency=getAgencyById($agency_id);
?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Edit Agency Details</h4>
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
<form id="addAgencyForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=edit'; ?>" method="post" onsubmit="return submitOurAgency()">
<table class="insertTableStyling no_print">

<tr>
<td class="firstColumnStyling">
Agency name<span class="requiredField">* </span> : 
</td>
<td>
<input type="hidden" name="lid" value="<?php echo $agency['agency_id']; ?>" />
<input type="text" name="agencyName" id="txtName" placeholder="Only Letters and numbers!" value="<?php echo $agency['agency_name']; ?>"/>
</td>
</tr>
<tr>
<td>
Prefix for the Agency<span class="requiredField">* </span> : 
</td>

<td>
<input type="text"  placeholder="Only Letters and numbers!" disabled="disabled" value="<?php echo $agency['agency_prefix']; ?>"/>
<input type="hidden" name="agencyPrefix"  value="<?php echo $agency['agency_prefix']; ?>"/>
</td>
</tr>
 <tr>
            <td> Subheading<span class="requiredField">* </span> : </td>
            <td> <input id="txtsubheading" type="text" name="sub_heading" value="<?php echo $agency['sub_heading']; ?>"/> </td>
            </tr>
<tr>
<td>Contact Person : </td>
<td><input type="text" name="contactPerson" id="txtPersonName" placeholder="Only letters!" value="<?php echo $agency['agency_contact_name']; ?>"/><span class="ValidationErrors contactNoError">First character should a  Letter!</span></td>
</tr>

<tr>
<td>Contact Number : </td>
<td>
<input type="text" name="contactNumber" id="txtPhone" placeholder="Only Numbers! '-' not allowed" value="<?php echo $agency['agency_contact_no']; ?>"/><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
</td>
</tr>

<tr>
<td> Contact Address : </td>
<td><textarea name="contactAddress" rows="6" cols="5" id="txtAddress"><?php echo $agency['agency_address']; ?></textarea></td>
</tr>

<!--<tr>
<td> Rasid Counter : </td>
<td><input type="text" name="rasid_counter"  id="rasid_counter" value="<?php echo $agency['rasid_counter']; ?>" /></td>
</tr> -->

<tr>
<td class="firstColumnStyling">
EMI Auto Paid on Date : 
</td>

<td>
<input  type="radio" name="auto_pay" class="auto_pay" id="auto_pay_no" value="0" <?php if($agency['auto_pay']==0) { ?> checked="checked" <?php } ?>  /> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="auto_pay_no">No</label>
<input  type="radio" name="auto_pay" class="auto_pay" id="auto_pay_yes" value="1" <?php if($agency['auto_pay']==1) { ?> checked="checked" <?php } ?> /> <label style="display:inline-block;top:3px;position:relative;" for="auto_pay_yes">Yes</label>
</td> 
</tr> 

 <tr>
            <td> Auto Pay Date(1-30)<span class="requiredField">* </span> : </td>
            <td> <input id="txtsubheading" type="text" name="auto_pay_date"  value="<?php echo $agency['auto_pay_date']; ?>"/> </td>
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