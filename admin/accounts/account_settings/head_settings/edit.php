<?php
if(!isset($_GET['lid']))
{
header("Location: index.php");
exit;
}
$head_id=$_GET['lid'];
$head=getHeadById($head_id);
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Edit Head Details</h4>
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=edit'; ?>" method="post">
<table class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Head Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="hidden" name="lid"  value="<?php echo $head_id; ?>"/>
<input type="text" name="head_name" id="txtbank" value="<?php echo $head['head_name']; ?>"/> 
<input type="hidden" name="parent_id" value="0"/>
</td>
</tr>

<tr>
<td></td>
<td>
<input type="submit" value="Edit" class="btn btn-warning">
<a href="index.php"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>

<?php $branches=getSubHeadsOfHead($head_id);

if($branches!=false && count($branches)>0)
{

?>
<tr>
<td class="headingAlignment">Sub Heads</td>
<td></td>
</tr>
<?php	

foreach($branches as $branch)
{
?>
<tr>
<td>
<span  class="editSubHeadName" id="<?php echo $branch['head_id'] ?>"><?php echo $branch['head_name']; ?></span></td>
<td><span class="editSubHeadBtn btn btn-warning">Edit</span> <a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&lid='.$branch['head_id'] ?>"><span class="deleteBranchBtn btn btn-danger">Delete</span></a>
</td>
</tr>
<?php }} ?>


</table>
</form>
<hr class="firstTableFinishing" />

<h4 class="headingAlignment no_print">Add Sub Head</h4>
<form id="addBranchForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=add'; ?>" method="post">
<table class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Sub Head Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="hidden" name="parent_id"  value="<?php echo $head_id; ?>"/>
<input type="text" name="name" id="txtbranch"/> 
</td>
</tr>

<tr>
<td></td>
<td>
<input type="submit" value="Add Sub Head" class="btn btn-warning">
</td>
</tr>
</table>
</form>
</div>
<div class="clearfix"></div>
