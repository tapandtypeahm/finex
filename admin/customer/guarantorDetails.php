<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
if(is_array($file) && $file!="error")
{
	$guarantor=getGuarantorDetailsByFileId($file_id);
	$proof_details=getGuarantorProofByFileId($file_id);
	
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
<div class="detailStyling">

<h4 class="headingAlignment">Guarantor's Details</h4>


<table id="insertCustomerTable" class="insertTableStyling detailStylingTable">


<tr>

<td class="firstColumnStyling" style="width:110px">
Name : 
</td>

<td>

                             <?php echo $guarantor['guarantor_name']; ?>					
                            
</td>
</tr>

<tr>
<td>
Address : 
</td>

<td>

                             <?php echo $guarantor['guarantor_address']; ?>					
                            
</td>
</tr>


<tr>
<td>City : </td>
				<td>

                             <?php $cid = $guarantor['city_id'];
							 		
							       $cityDetails = getCityByID($cid);
								   echo $cityDetails['city_name'];
							?>
                            </td>
</tr>

<tr>
<td>Area : </td>
				<td>

                             <?php $cid = $guarantor['area_id'];
							 		
							       $cityDetails = getAreaByID($cid);
								   echo $cityDetails['area_name'];
							?>
                            </td>
</tr>

<tr>
<td>Pincode : </td>
<td>

                             <?php if($guarantor['guarantor_pincode']!=0) echo $guarantor['guarantor_pincode']; else echo "NA"; ?>					
                          
</td>
</tr>



 <tr id="addcontactTrCustomer">
                <td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                <?php
                            $contactNumbers = $guarantor['contact_no'];
							
                           for($z=0;$z<count($contactNumbers);$z++)
                              {
								$c=$contactNumbers[$z];
								if($z==(count($contactNumbers)-1))
								echo $c[0];  
								else
                      			echo $c[0]." | ";				
                              } ?>
                </td>
            </tr>

<tr>
	<td></td>
  <td class="no_print">
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editGuarantor&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&id='.$file_id ?>"><button title="Back" class="btn btn-warning">Back</button></a>
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

                             <?php echo $proof['proof_type']; ?>					
                            
</td>
</tr>

<tr>
<td>
Proof No : 
</td>

<td>

                             <?php echo $proof['guarantor_proof_no']; ?>					
                            
</td>
</tr>

<?php $imgArray=getGuarantorProofimgByProofId($proof['guarantor_proof_id']); 
if(is_array($imgArray) && count($imgArray)>0)
{
foreach($imgArray as $img)
{
	 $ext = substr(strrchr($img['guarantor_proof_img_href'], "."), 1); 	
  if($ext=="jpg" || $ext=="JPG" || $ext=="png" || $ext=="PNG" || $ext=="gif" || $ext=="GIF" || $ext=="jpeg" || $ext=="JPEG")
  { 
?>

<tr>
<td>Image : </td>
				<td>

                             <a href="<?php echo WEB_ROOT."images/guarantor_proof/".$img['guarantor_proof_img_href']; ?>"><img style="height:100px;" src="<?php echo WEB_ROOT."images/guarantor_proof/".$img['guarantor_proof_img_href']; ?>" /></a>
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

                             <a style="text-decoration:underline;color:#00F;" href="<?php echo WEB_ROOT."images/guarantor_proof/".$img['guarantor_proof_img_href']; ?>">Proof link</a>
                            </td>
</tr>
<?php	  
	  }
  
 } }?>
<tr>
	<td></td>
  <td class="no_print">
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?action=delGuarantorProof&id='.$file_id.'&state='.$proof['guarantor_proof_id']; ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
  </td>          
</tr>        

</table>
<?php } } ?>


</div>

</div>
<div class="clearfix"></div>