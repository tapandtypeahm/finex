<?php
if(!isset($_GET['id']))
{
header("Location : ".WEB_ROOT."admin/search");
exit;
}
if(!isset($_GET['state']))
{
header("Location : ".WEB_ROOT."admin/search");
exit;
}

if(!isset($_GET['state2']))
{
header("Location : ".WEB_ROOT."admin/search");
exit;
}

$file_id=$_GET['id'];
$vehicle_id=$_GET['state'];
$seize_id=$_GET['state2'];
$seize=getVehicleSeizeDetailsByVehicleId($vehicle_id);
?>

<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Seize Vehicle</h4>
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

<table class="insertTableStyling no_print">

<tr>
<td width="220px">Seize Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($seize['seize_date'])); ?>
</td>
                    
                    
                  
</tr>
<?php ?>
<tr>
<td>
Sold : 
</td>
<td>
<?php  if($seize['sold']==0) echo "No"; else echo "Yes"; ?>
</td>
</tr>
<tr>
<td>
Remarks : 
</td>
<td>
<?php echo $seize['remarks']; ?>
</td>
</tr>

<tr>
<td>
Entry Date : 
</td>
<td>
<?php echo date('d/m/Y',strtotime($seize['date_added'])); ?>
</td>
</tr>



<tr>
<td>
Last Created/Updated By : 
</td>
<td>
<?php echo getAdminUserNameByID($seize['created_by']); ?>
</td>
</tr>


<tr>
<td></td>
<td>
 <a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><input type="button" class="btn btn-success" value="Back" /></a>
</td>
</tr>

</table>

</form>
</div>
<div class="clearfix"></div>