<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
if(is_array($file) && $file!="error")
{
	
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

<h4 class="headingAlignment">File Details</h4>

<table class="insertTableStyling detailStylingTable">

<tr>
<td>Agency Name : </td>
				<td>
				
                             <?php
							 
							  $id =  $file['agency_id']; 
							 if($id!=null)
							 {
							        $agencyDetails=getAgencyById($id);
									echo $agencyDetails['agency_name'];
							 }
							 else
							 {
								 $id=$file['oc_id'];
								 echo getOurCompanyNameByID($id);
								 }
							 ?>					
                             
                </td>
                    
                    
                  
</tr>

<tr>
<td>
File Agreement No : 
</td>
<td>
 
                             <?php echo $file['file_agreement_no'] ?>					
                             

</td>
</tr>

<tr>
<td>File Number : </td>
				<td>
				
                             <?php echo $file['file_number']; ?>					
                            

                 </td>
</tr>

<tr>
<td>Broker : </td>
				<td>
				
                             <?php echo getBrokerNameFromBrokerId($file['broker_id']); ?>					
                            

                 </td>
</tr>

<tr>
<td>File Status : </td>
				<td>
				
                             <?php  if($file['file_status']==1) echo "OPEN"; else echo "CLOSED"; ?>					
                            

                 </td>
</tr>

<tr>
<td>File Belongs To : </td>
				<td>
				
                             <?php echo getOurCompanyNameByID($file['our_company_id']); ?>					
                            

                 </td>
</tr>

<tr>
<td>Created By : </td>
				<td>
				
                             <?php  echo getAdminUserNameByID($file['created_by']); ?>					
                            

                 </td>
</tr>

<td>Creation Date : </td>
				<td>
				
                             <?php  $myDate=strtotime($file['date_added']); echo date('d/m/Y h:i:s',$myDate);  ?>					
                            

                 </td>
</tr>


<tr>
	<td></td>
  <td class="no_print">
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editFile&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&id='.$file_id ?>"><input type="button" value="Back" class="btn btn-success" /></a>
</td>
</tr>            



</table>

</div>


</div>
<div class="clearfix"></div>