<?php
$grp_id=$_GET['lid'];
$grp=getAreaGroupByID($grp_id);

 ?>

<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Group Details</h4>

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
<table id="DetailsTable" class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Group Name : 
</td>

<td>
<?php  echo $grp['grp_name']; ?>
</td>
</tr>



<?php
$areas=$grp['areas_id']; 

 if($areas!=null)
			 {
			 $area_id_array=explode(",",$areas);
			$b=0;
			 foreach($area_id_array as $area_id)
			 {
			
?>
<tr>
<td>
<?php if($b==0) { ?> Areas : <?php } ?>
</td>
<td><?php $area=getAreaByID($area_id); echo $area['area_name']; ?></td>
</tr>
<?php 
			$b++;
			 }
}

?>
<tr>
<td></td>
<td>
<a href="<?php echo $_SERVER['PHP_SELF'].'?view=edit&lid='.$grp_id ?>" ><span class="btn editBtn delete">E</span></a>
<a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&lid='.$grp_id ?>"><span class="btn delBtn delete">X</span></a>
<a href="index.php"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>
</table>

</div>
<div class="clearfix"></div>
