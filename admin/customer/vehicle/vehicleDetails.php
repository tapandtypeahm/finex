<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
if(is_array($file) && $file!="error")
{
	$vehicle=getVehicleDetailsByFileId($file_id);
	$proof_details=getVehicleProofByFileId($file_id);
}
else
{
	$_SESSION['ack']['msg']="Invalid File!";
	$_SESSION['ack']['type']=4; // 4 for error
	header("Location: ".WEB_ROOT."admin/search");
	
}

?>
<div class="insideCoreContent adminContentWrapper wrapper">
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
<div class="detailStyling">
<h4 class="headingAlignment"> Vehicle Details </h4>


<table id="insertGuarantorTable" class="insertTableStyling detailStylingTable">

<tr>
<td>Vehicle Company : </td>
<td><?php  $company=getVehicleCompanyById($vehicle['vehicle_company_id']); echo $company['company_name']; ?> </td>
</tr>

<tr>
<td>Vehicle Model : </td>
				<td>
					<?php echo getModelNameById($vehicle['model_id']); ?>
                            </td>
</tr>

<tr>
<td>Vehicle Dealer : </td>
				<td>
					<?php echo getDealerNameFromDealerId($vehicle['vehicle_dealer_id']); ?>
                            </td>
</tr>

<tr>
       <td>Vehicle Condition :</td>
           
           
        <td>
            <?php if($vehicle['vehicle_condition']==1) echo "NEW"; else echo "OLD"; ?>
        </td>
 </tr>
 
 <tr>
<td>Vehicle Model : </td>
				<td>
					<?php echo $vehicle['vehicle_model']; ?>
                            </td>
</tr>

<tr>
<td>Vehicle Type : </td>
				<td>
					<?php $vehicle_type = getVehicleTypeById($vehicle['vehicle_type_id']); echo $vehicle_type['vehicle_type']; ?>	
                </td>
</tr>
 
<tr>
<td class="firstColumnStyling">
Registration Number : 
</td>

<td>
<?php  $reg_no=$vehicle['vehicle_reg_no']; $reg_no=strtoupper($reg_no); echo $reg_no;?>
</td>
</tr>

<tr>

<td class="firstColumnStyling">
 Registration Date : 
</td>

<td>
<?php echo date('d/m/Y',strtotime($vehicle['vehicle_reg_date'])); ?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Engine Number : 
</td>

<td>
<?php echo $vehicle["vehicle_engine_no"]; ?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Chasis Number : 
</td>

<td>
<?php echo $vehicle["vehicle_chasis_no"]; ?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Fitness Exp Date: 
</td>

<td>
<?php echo date('d/m/Y',strtotime($vehicle["fitness_exp_date"])); ?>

</td>
</tr>

<tr>
<td class="firstColumnStyling">
Permit Exp Date : 
</td>

<td>
<?php echo date('d/m/Y',strtotime($vehicle["permit_exp_date"])); ?>

</td>
</tr>

<tr>
	<td></td>
  <td class="no_print">
            
          <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editVehicle&id='.$file_id.'&state='.$vehicle['vehicle_id'] ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
             <a href="<?php echo WEB_ROOT.'admin/customer/index.php?view=details&id='.$file_id ?>"><button title="Back" class="btn btn-success">Back</button></a>
            </td>
</tr>   

</table>

<?php
$gh=0;
if(is_array($proof_details) && count($proof_details)>0)
{
foreach($proof_details as $proof) 
{
	
	?>

<h4 class="headingAlignment">Proof <?php echo ++$gh; ?></h4> 



<table id="insertCustomerTable" class="insertTableStyling detailStylingTable">
<tr>

<td class="firstColumnStyling">
 Proof Type : 
</td>

<td>

                             <?php echo $proof['vehicle_document_type']; ?>					
                            
</td>
</tr>

<tr>
<td>
Proof No : 
</td>

<td>

                             <?php echo $proof['vehicle_document_no']; ?>					
                            
</td>
</tr>

<?php $imgArray=getVehicleProofimgByProofId($proof['vehicle_document_id']); 
if(is_array($imgArray) && count($imgArray)>0)
{
foreach($imgArray as $img)
{
	 $ext = substr(strrchr($img['vehicle_document_img_href'], "."), 1); 	
  if($ext=="jpg" || $ext=="JPG" || $ext=="png" || $ext=="PNG" || $ext=="gif" || $ext=="GIF" || $ext=="jpeg" || $ext=="JPEG")
  { 
?>

<tr>
<td>Image : </td>
				<td>

                             <a href="<?php echo WEB_ROOT."images/vehicle_proof/".$img['vehicle_document_img_href']; ?>"><img style="height:100px;" src="<?php echo WEB_ROOT."images/vehicle_proof/".$img['vehicle_document_img_href']; ?>" /></a>
                            </td>
</tr>

 

<?php
  }
  else if($ext=="pdf" || $ext=="PDF")
  {
?>
<tr>
<td>Proof Link: </td>
				<td>

                             <a style="text-decoration:underline;color:#00F;" href="<?php echo WEB_ROOT."images/vehicle_proof/".$img['vehicle_document_img_href']; ?>">Proof link</a>
                            </td>
</tr>
<?php	  
	  }
  
 } } ?>

<tr>
	<td></td>
  <td class="no_print">
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?action=delVehicleProof&id='.$file_id.'&state='.$proof['vehicle_document_id']; ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
  </td>          
</tr>        

</table>
<?php } } ?>
</div>
</div>
<div class="clearfix"></div>