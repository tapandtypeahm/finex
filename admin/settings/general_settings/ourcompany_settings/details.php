<?php
$company_id=$_GET['lid'];
$company=getOurCompanyByID($company_id);
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">

<h4 class="headingAlignment">Our Company Details</h4>

<?php 
if(isset($_SESSION['ack']['msg']) && isset($_SESSION['ack']['type']))
{
	
	$msg=$_SESSION['ack']['msg'];
	$type=$_SESSION['ack']['type'];
	
	
		if($msg!=null && $msg!="" && $type>0)
		{
?>
<div class="alert  <?php if(isset($type) && $type>0 && $type<4) echo "alert-success"; else echo "alert-error" ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php if(isset($type)  && $type>0 && $type<4) { ?> <strong>Success!</strong> <?php } else if(isset($type)   && $type>3) { ?> <strong>Warning!</strong> <?php } ?> <?php echo $msg; ?>
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
Company Name : 
</td>

<td>
<?php echo $company['our_company_name']; ?>
</td>
</tr>

<tr>
<td>
Address : 
</td>

<td>
<?php echo $company['our_company_address']; ?>
</td>
</tr>

<?php $contacts=getContactNoForOurCompany($company['our_company_id']);
if(!empty($contacts))
{
?>
 <tr id="addcontactTr">
				<td>
                Contact No : 
                </td>
                 <td id="addcontactTd">
<?php	
	for($i=0;$i<count($contacts);$i++)
	{
		$contact=$contacts[$i];
		
		?>
         
                
               
               <?php if($i==(count($contacts)-1)) echo $contact[1]; else echo $contact[1]." | "; ?>
               
<?php 	
	}
?>
 </td>
			</tr>
<?php 	
}?>




<tr>
<td>Pincode : </td>
<td><?php echo $company['our_company_pincode']; ?></td>
</tr>

<tr>
<td>City : </td>
				<td>
					
                             
                             <?php echo $company['city_name'] ?>
                            </td>
</tr>

<tr>
<td> Prefix : </td>
<td> <?php echo $company['our_company_prefix']; ?> </tr>
</tr>

<tr>
<td> Sub Heading : </td>
<td> <?php echo $company['sub_heading']; ?> </tr>
</tr>

<tr class="no_print">
<td></td>
<td >
<a href="<?php echo $_SERVER['PHP_SELF'].'?view=edit&lid='.$company_id ?>"><button title="Edit this entry" class="btn editBtn"><span class="edit">E</span></button></a>
<a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&lid='.$company_id ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
<a href="index.php"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>
</table>

</div>
<div class="clearfix"></div>