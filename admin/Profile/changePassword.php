<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Change Your Password</h4>
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=change'; ?>" method="post">
<input type="hidden" name="id" value="<?php echo $_SESSION['adminSession']['admin_id'];  ?>" />
<table class="insertTableStyling no_print">

<tr>
<td>Current Password<span class="requiredField">* </span> : </td>
<td> <input type="password" name="oldPassword" id="txtOldPassword"/> </td>
</tr>

<tr>
<td>New Password<span class="requiredField">* </span> : </td>
<td> <input type="password" name="newPassword" id="txtPassword"/> </td>
</tr>

<tr>
<td>Confirm New Password<span class="requiredField">* </span> : </td>
<td> <input type="password" id="txtConfirmPassword"/> </td>
</tr>



<tr>
<td></td>
<td><input type="submit" value="change" class="btn btn-warning"></td>
</tr>
</table>
</form>

 
</div>
<div class="clearfix"></div>