<?php
if(!isset($_GET['id']))
{
header("Location: index.php?view=details");
exit;
}

$file_id=$_GET['id'];
$customer=getCustomerDetailsByFileId($file_id);
$customer_id=$customer['customer_id'];
if(is_numeric($file_id))
{	
$insurances=getInsurancesForFileID($file_id);
if($insurances=="error")
{
	
	$_SESSION['ack']['msg']="Valid Insurance not found!";
	$_SESSION['ack']['type']=4; // 4 for error
	header("Location: index.php?view=search");
exit;
}
}
else if(!is_numeric($file_id))
{
	
	$insurances="error";
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

<div class="addDetailsBtnStyling no_print"><a href="index.php?id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button class="btn btn-success">+ Add Insurance</button></a> <a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button class="btn btn-warning">Go to Main File</button></a> <a href="index.php?view=search"><button class="btn btn-warning">Go to Search</button></a></div>



<?php if(is_array($insurances)) {
	foreach($insurances as $insurance)
	{
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
  <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=insuranceDetails&id='.$file_id.'&state='.$insurance['insurance_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editInsurance&id='.$file_id.'&state='.$insurance['insurance_id']; ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
            </td>
</tr>    

</table>
</div>
<?php } } ?>

</div>
<div class="clearfix"></div>