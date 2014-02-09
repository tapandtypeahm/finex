<?php 
if(!isset($_GET['lid']))
{
	header("Location: index.php");
	}
$agency_id=$_GET['lid'];
$agency=getAgencyById($agency_id);
?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment">Agency Details</h4>
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
<table  id="DetailsTable" class="insertTableStyling">

<tr>
<td class="firstColumnStyling">
Agency name : 
</td>
<td>
<?php echo $agency['agency_name']; ?>
</td>
</tr>
<tr>
<td>
Prefix for the Agency : 
</td>

<td>
<?php echo $agency['agency_prefix']; ?>
</td>
</tr>
<tr>
<td>
Sub Heading for the Agency : 
</td>

<td>
<?php echo $agency['sub_heading']; ?>
</td>
</tr>
<tr>
<td>Contact Person : </td>
<td><?php if($agency['agency_contact_name']!="" && $agency['agency_contact_name']!=null) echo $agency['agency_contact_name']; else echo "NA"; ?></td>
</tr>

<tr>
<td>Contact Number : </td>
<td>
<?php  if($agency['agency_contact_no']!="" && $agency['agency_contact_no']!=null && $agency['agency_contact_no']>0) echo $agency['agency_contact_no']; else echo "NA";?>
</td>
</tr>

<tr>
<td> Contact Address : </td>
<td><?php  if($agency['agency_address']!="" && $agency['agency_address']!=null) echo $agency['agency_address']; else echo "NA";?>
</td>
</tr>

<tr>
<td> Rasid Counter : </td>
<td><?php echo $agency['rasid_counter']; ?></td>
</tr>

<tr>
<td class="firstColumnStyling">
EMI Auto Paid on Date : 
</td>

<td>
<?php  if($agency['auto_pay']==1)echo "YES"; else echo "NO"; ?>
</td> 
</tr> 

<tr>
<td class="firstColumnStyling">
Auto Paid Date : 
</td>

<td>
<?php echo  $agency['auto_pay_date']; ?>
</td> 
</tr> 

<tr class="no_print">
<td></td>
<td>
<a href="<?php echo $_SERVER['PHP_SELF'].'?view=edit&lid='.$agency_id ?>"><button title="Edit this entry" class="btn editBtn"><span class="edit">E</span></button></a>
<a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&lid='.$agency_id ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
<a href="index.php"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>
</table>
</form>
</div>
<div class="clearfix"></div>