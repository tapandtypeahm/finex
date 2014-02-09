<?php
if(!isset($_GET['id']))
{
header("Location: ".WEB_ROOT."admin/search");
exit;
}
$file_id=$_GET['id'];

if(!isset($_GET['lid']))
{
header("Location: ".WEB_ROOT."admin/customer/index.php?view=addRemainder&id=".$file_id);
exit;
}
$emi_id=$_GET['lid'];
?>
<div class="insideCoreContent adminContentWrapper wrapper">
<div class="addDetailsBtnStyling no_print"> </div>
<h4 class="headingAlignment no_print">Add Company Paid Date</h4>
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=addCompanyPaidDate'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurCompany()">
<input name="file_id" value="<?php echo $file_id; ?>" type="hidden">
<input name="lid" value="<?php echo $emi_id; ?>" type="hidden">
<?php if(isset($_GET['return']) && $_GET['return']=='emiDetails') { ?>
<input name="return" value="emiDetails" type="hidden">
<?php } ?>
<table class="insertTableStyling no_print">

<tr>
<td width="220px">Company Paid on Date<span class="requiredField">* </span> : </td>
				<td>
					<input onchange="onChangeDate(this.value,this)" type="text" id="companyPaidDate" autocomplete="off" size="12"  name="remainderDate" class="datepicker1 datepick" placeholder="Click to Select!" /><span class="DateError customError">Please select a date!</span>
</td>
                    
                    
                 
<tr>
<td></td>
<td>
<input type="submit" value="Add" class="btn btn-warning">
<a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><input type="button" value="Back" class="btn btn-success" /></a>
</td>
</tr>
</table>
</form>
</div>
<div class="clearfix"></div>