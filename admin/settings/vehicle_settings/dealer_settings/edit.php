<?php
if(!isset($_GET['lid']))
{
	header("Location: index.php");
	}
$vehicleDealer=getVehicleDealerById($_GET['lid']);

$vehicleDealer_id=$_GET['lid'];	
$companiesForDealer=getVehicleCompanyIDsByDealerId($vehicleDealer_id);
$contactNo=getDealerNumbersByDealerId($vehicleDealer_id);
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Edit Vehicle Dealer</h4>
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
<form id="addAgencyForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=edit'; ?>" method="post" onsubmit="return checkCheckBox()">
<input type="hidden" name="lid" value="<?php echo $vehicleDealer['dealer_id'] ?>" />
<table class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Dealer Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="name" id="name" value="<?php echo $vehicleDealer['dealer_name'] ?>"/>
</td>
</tr>

<tr>
<td>
Address : 
</td>

<td>
<textarea name="address" cols="5" rows="6" id="address"><?php echo $vehicleDealer['dealer_address'] ?></textarea>
</td>
</tr>


<tr>
<td>City<span class="requiredField">* </span> : </td>
				<td>
					<select id="city" name="city">
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $cities = listCities();
                            foreach($cities as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['city_id'] ?>"  <?php if($vehicleDealer['city_id']==$super['city_id']) {?> selected="selected" <?php } ?>><?php echo $super['city_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>


<tr>
<td>Company<span class="requiredField">* </span> : </td>
				<td>
					
                        <?php
                            $companies = listVehicleCompanies();
                            foreach($companies as $super)
                              {
                             ?>
                             
                             <input name="company[]" type="checkbox" class="company"  value="<?php echo $super['vehicle_company_id'] ?>" id="comp<?php echo $super['vehicle_company_id']; ?>" <?php if(in_array($super['vehicle_company_id'],$companiesForDealer)){ ?> checked="checked" <?php } ?>><label style="display:inline-block; top:3px; position:relative;padding-left:5px;" for="comp<?php echo $super['vehicle_company_id']; ?>"> <?php echo $super['company_name']; ?>	</label><br />
                             <?php } ?>
                              
                         
                           
                            </td>
</tr>

<tr>
<td> Contact Number : </td>
<td> <input type="text" name="contactNo" value="<?php echo $contactNo[0][0]; ?>"/> </tr>
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