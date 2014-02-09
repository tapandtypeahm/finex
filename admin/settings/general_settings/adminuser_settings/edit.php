<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Edit Admin User</h4>
<?php 
if(!isset($_GET['lid']))
{
	header("Location: index.php");
	exit;
	}
$admin_id=$_GET['lid'];
$admin=getAdminUserByID($admin_id);
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=edit'; ?>" method="post">
<table class="insertTableStyling no_print">

<tr>
<td>
Email : 
</td>
<td>
<input type="hidden" name="lid"  value="<?php echo $admin['admin_id']; ?>"/> 
<?php echo $admin['admin_email']; ?>
</td>
</tr>

<tr>
<td> Name : </td> 
<td><?php echo $admin['admin_name']; ?> </td>
</tr>

<tr>
<td> User Name : </td>
<td><?php echo $admin['admin_username']; ?></td> 
</tr>



<tr>

<td>Access Rights : </td>
<td><?php $admin_rights=getAllAdminRights();
$currentRights=getAdminRightsForAdminId($admin_id);
foreach($admin_rights as $right)
{
	?>
  <input id="<?php echo $right['admin_right_id'] ?>" type="checkbox" name="right[]" value="<?php echo $right['admin_right_id'] ?>" class="insertCheckBox" <?php if($right['admin_right_id']==1) { ?> checked="checked" disabled="disabled" <?php } if(in_array($right['admin_right_id'],$currentRights)) { ?> checked="checked"  <?php } ?> /> <label class="insertCheckBoxLabel" for="<?php echo $right['admin_right_id'] ?>"><?php echo $right['admin_right'] ?></label> 
<?php    
}?></td>
</tr>

<tr>

<td>Report Rights : </td>
<td><?php $admin_rights_report=getAllAdminReportRights();
foreach($admin_rights_report as $right)
{
	?>
  <input id="<?php echo $right['admin_right_id'] ?>" type="checkbox" name="right[]" value="<?php echo $right['admin_right_id'] ?>" class="insertCheckBox" <?php if($right['admin_right_id']==1) { ?> checked="checked" disabled="disabled" <?php } if(in_array($right['admin_right_id'],$currentRights)) { ?> checked="checked"  <?php } ?>  /> <label class="insertCheckBoxLabel" for="<?php echo $right['admin_right_id'] ?>"><?php echo $right['admin_right'] ?></label> 
<?php    
}?></td>
</tr>

<tr>
<td></td>
<td><input type="submit" value="Edit" class="btn btn-warning">
<a href="index.php"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>
</table>
</form>
</div>
<div class="clearfix"></div>