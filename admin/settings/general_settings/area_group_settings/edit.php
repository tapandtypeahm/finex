<?php
if(!isset($_GET['lid']))
{
header("Location: index.php");
exit;
}
$grp_id=$_GET['lid'];
$grp=getAreaGroupByID($grp_id);
$areas=$grp['areas_id'];
			 if($areas!=null)
			 {
			 $area_id_array=explode(",",$areas);
			 }
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
Group Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="hidden" name="lid"  value="<?php echo $grp_id; ?>"/>
<input type="text" name="grp_name" id="txtbank" value="<?php echo $grp['grp_name']; ?>"/> 
</td>
</tr>

<tr>
<td>Area : </td>
				<td>
					<select name="area_array[]" class="city_area selectpicker" multiple="multiple"  id="city_area1" >
                    	 <option value="-1" >--Please Select--</option>
                          <?php
						  $cities=listCities();
						  foreach($cities as $city)
						 {
							?>
                            <optgroup  label="<?php echo $city['city_name'] ?>">
                            <?php 
                            $areas = listAreasFromCityId($city['city_id']);
                            foreach($areas as $area)
                              {
                             ?>
                             
                             <option value="<?php echo $area['area_id'] ?>" <?php if(in_array($area['area_id'],$area_id_array)) { ?> selected="selected" <?php } ?> ><?php echo $area['area_name'] ?></option					>
                             <?php } 
							 ?>
                            </optgroup> 
                             <?php
						  }
							 ?>
                    </select>
                            </td>
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
