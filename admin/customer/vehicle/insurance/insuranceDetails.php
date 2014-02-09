<?php
if(!isset($_GET['id']))
{
header("Location: index.php?view=details");
exit;
}

$file_id=$_GET['id'];
$insurance_id=$_GET['state'];
	
$insurance=getInsuranceDetailsFromInsuranceId($insurance_id);

if($insurance=="error")
{
	
	$_SESSION['ack']['msg']="Valid Insurance not found!";
	$_SESSION['ack']['type']=4; // 4 for error
	header("Location: index.php?view=search");
exit;
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
<div class="alert no_print  <?php if(isset($type) && $type>0 && $type<4) echo "alert-success"; else echo "alert-error" ?>">
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


<?php 
		$str_yr=strtotime($insurance['insurance_issue_date']);
		$end_yr=strtotime($insurance['insurance_expiry_date']);
		$str_yr=date('Y',$str_yr);
		$end_yr=date('Y',$end_yr);
	?>
<div class="detailStyling">
<h4 class="headingAlignment"> Insurance Details (<?php echo $str_yr." - ".$end_yr; ?>)</h4>


<table id="insertGuarantorTable" class="insertTableStyling detailStylingTable">

<tr>
<td>Insurance Company : </td>
				<td>
					<?php  $comp=getInsuranceCompanyById($insurance['insurance_company_id']); echo $comp[1]; ?>
                            </td>
</tr>

<tr>
<td>Insurance Issue Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($insurance['insurance_issue_date'])); ?>
                            </td>
</tr>

<tr>
<td>Insurance Expiry Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($insurance['insurance_expiry_date'])); ?>
                            </td>
</tr>


<td class="firstColumnStyling">
Isurance Declared Value (IDV) : 
</td>

<td>
<?php echo $insurance['idv']; ?>
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Premium : 
</td>

<td>
 <?php echo $insurance['insurance_premium']; ?>
</td>
</tr>

<tr>
	<td></td>
  <td class="no_print"> 
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editInsurance&id='.$file_id.'&state='.$insurance['insurance_id']; ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
            <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&id='.$file_id; ?>"><button  class="btn btn-success">Back</button></a>
            </td>
</tr> 

</table>  

<?php $imgArray=$insurance['insurance_image']; 

if(is_array($imgArray) && count($imgArray)>0)
{
?>
<h4 class="headingAlignment">Proof Images</h4> 
<table id="insertCustomerTable" class="insertTableStyling detailStylingTable">
<?php 
foreach($imgArray as $img)
{
	 $ext = substr(strrchr($img['insurance_img_href'], "."), 1); 	
  if($ext=="jpg" || $ext=="JPG" || $ext=="png" || $ext=="PNG" || $ext=="gif" || $ext=="GIF" || $ext=="jpeg" || $ext=="JPEG")
  { 
?>

<tr>
<td>Image : </td>
				<td>

                             <a href="<?php echo WEB_ROOT."images/insurance_proof/".$img['insurance_img_href']; ?>"><img style="height:100px;" src="<?php echo WEB_ROOT."images/insurance_proof/".$img['insurance_img_href']; ?>" /></a>
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

                             <a style="text-decoration:underline;color:#00F;" href="<?php echo WEB_ROOT."images/insurance_proof/".$img['insurance_img_href']; ?>">Proof link</a>
                            </td>
</tr>
<?php	  
	  }
  ?>


<tr>
	<td></td>
  <td class="no_print">
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?action=delInsuranceImg&id='.$file_id.'&state='.$img['insurance_img_id']; ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
  </td>          
</tr>        


<?php }
?>
</table>
<?php
 } ?> 


</div>
</div>
<div class="clearfix"></div>