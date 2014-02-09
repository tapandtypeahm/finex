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
<h4 class="headingAlignment no_print">Vehicle Dealer Details</h4>
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


<table id="DetailsTable" class="insertTableStyling">

<tr>

<td class="firstColumnStyling">
Dealer Name : 
</td>

<td>
<?php echo $vehicleDealer['dealer_name'] ?>
</td>
</tr>

<tr>
<td>
Address : 
</td>

<td>
<?php echo $vehicleDealer['dealer_address'] ?>
</td>
</tr>


<tr>
<td>City : </td>
				<td>
					
                        <?php
                            $cities = listCities();
                            foreach($cities as $super)
                              {
                             if($vehicleDealer['city_id']==$super['city_id']) { echo $super['city_name']; } 
                              } ?>
                              
                         
                            
                            </td>
</tr>


<tr>
<td>Company : </td>
				<td>
					
                        <?php
                            $companies = listVehicleCompanies();
                            foreach($companies as $super)
                              {
                              if(in_array($super['vehicle_company_id'],$companiesForDealer)){  echo $super['company_name']; } 
                              } ?>
                              
                         
                           
                            </td>
</tr>

<tr>
<td> Contact Number : </td>
<td> <?php echo $contactNo[0][0]; ?> </tr>
</tr>

<tr class="no_print">
<td></td>
<td>
<a href="<?php echo $_SERVER['PHP_SELF'].'?view=edit&lid='.$vehicleDealer_id ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
<a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&lid='.$vehicleDealer_id ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
<a href="index.php"><input type="button" value="back" class="btn btn-success" /></a></td>
</tr>

</table>


</div>
<div class="clearfix"></div>