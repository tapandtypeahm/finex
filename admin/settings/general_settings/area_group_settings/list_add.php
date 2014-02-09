<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Add a Area Group</h4>
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=add'; ?>" method="post">
<table class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Area Group Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="group_name" id="txtbank"/> 
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
                             
                             <option value="<?php echo $area['area_id'] ?>" ><?php echo $area['area_name'] ?></option					>
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
<input type="submit" value="Add Group" class="btn btn-warning">
<a href="<?php echo WEB_ROOT ?>admin/settings/"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>

</table>
</form>

<hr class="firstTableFinishing" />

<h4 class="headingAlignment">List of Groups</h4>
<div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>
	<div class="no_print">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">Group Name</th>
            <th class="heading">Areas</th>
           <th class="heading no_print btnCol"></th>
            <th class="heading no_print btnCol"></th>
            <th class="heading no_print btnCol"></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
		$banks=listAreaGroupsWithRest();
		$no=0;
		foreach($banks as $bank)
		{
			
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><?php echo $bank['grp_name']; ?></span>
            </td>
             <td><?php $areas=$bank['areas_id'];
			 if($areas!=null)
			 {
			 $area_id_array=explode(",",$areas);
			
			 if(is_array($area_id_array) && count($area_id_array)>0)
			 {
				 
				 foreach($area_id_array as $area_id)
				 {
					 $ar=getAreaByID($area_id);
					 echo $ar['area_name']." <br> ";
					 }
				 }
			 else
			 echo 0;
			 }
			 else
			 echo 0;
			  ?>
            </td>
             <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&lid='.$bank['grp_id'] ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=edit&lid='.$bank['grp_id'] ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
            </td>
            <td class="no_print"> 
            <a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&lid='.$bank['grp_id'] ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
            </td>
            
          
  
        </tr>
         <?php }?>
         </tbody>
    </table>
     </div>
   <table id="to_print" class="to_print adminContentTable"></table>  
</div>
<div class="clearfix"></div>