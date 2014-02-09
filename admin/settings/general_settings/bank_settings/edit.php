<?php
if(!isset($_GET['lid']))
{
header("Location: index.php");
exit;
}
$bank_id=$_GET['lid'];
$bank=getBankByID($bank_id);
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Edit Bank Details</h4>
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
Bank Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="hidden" name="lid"  value="<?php echo $bank['bank_id']; ?>"/>
<input type="text" name="bank_name" id="txtbank" value="<?php echo $bank['bank_name']; ?>"/> 
</td>
</tr>

<tr>
<td></td>
<td>
<input type="submit" value="Edit" class="btn btn-warning">
<a href="index.php"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>

<?php $branches=$bank['branch_array'];

if(count($branches)>0)
{

?>
<tr>
<td class="headingAlignment">Branches</td>
<td></td>
</tr>
<?php	

foreach($branches as $branch)
{
?>
<tr>
<td>
<span  class="editBranchName" id="<?php echo $branch['branch_id'] ?>"><?php echo $branch['branch_name']; ?></span></td>
<td><span class="editBranchBtn btn btn-warning">Edit</span> <a href="<?php echo $_SERVER['PHP_SELF'].'?action=deleteBranch&lid='.$branch['branch_id']."&bid=".$bank_id; ?>"><span class="deleteBranchBtn btn btn-danger">Delete</span></a>
</td>
</tr>
<?php }} ?>


</table>
</form>
<hr class="firstTableFinishing" />

<h4 class="headingAlignment no_print">Add Branch</h4>
<form id="addBranchForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=addBranch'; ?>" method="post">
<table class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Branch Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="hidden" name="lid"  value="<?php echo $bank['bank_id']; ?>"/>
<input type="text" name="branch_name" id="txtbranch"/> 
</td>
</tr>

<tr>
<td></td>
<td>
<input type="submit" value="Add Branch" class="btn btn-warning">
</td>
</tr>
</table>
</form>
</div>
<div class="clearfix"></div>
