<?php if(!isset($_GET['id']))
{
header("Location: ".WEB_ROOT."admin/search");
exit;
}

$file_id=$_GET['id'];
$settle_file=getSettleFileDetails($file_id);
if($settle_file['payment_mode']==2)
$chequePayment=getSettleChequeBySettleId($settle_file['settle_id']);
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
<h4 class="headingAlignment"> Settlement Details </h4>
<table id="rasidTable" class="detailStylingTable insertTableStyling">

<tr>
<td>Payment Amount : </td>
				<td>
					<?php echo "Rs. ".number_format($settle_file['settle_amount']); ?>
                            </td>
</tr>
<tr>
<td>Payment Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($settle_file['settle_date'])); ?>
                            </td>
</tr>

<tr>
<td>Rasid No : </td>
				<td>
					<?php echo $settle_file['receipt_no']; ?>
                            </td>
</tr>

<tr>
<td>Payment Mode : </td>
				<td>
					 <?php if($settle_file['payment_mode']==1) { echo "CASH"; }else echo "CHEQUE"; ?>
                            </td>
</tr>

<?php  if($chequePayment!=false) { ?>

<tr>
<td width="220px">Bank Name : </td>
				<td>
					<?php if($chequePayment!=false) { echo getBankNameByID($chequePayment['bank_id']); } ?>
                            </td>
</tr>
<tr>
<td width="220px">Branch Name : </td>
				<td>
					<?php if($chequePayment!=false) { echo getBranchhById($chequePayment['branch_id']); } ?>
                            </td>
</tr>
<tr>
<td width="220px">Cheque No : </td>
				<td>
					<?php if($chequePayment!=false) { echo $chequePayment['cheque_no']; } ?>
                            </td>
</tr>
<tr>
<td width="220px">Cheque Date : </td>
				<td>
					<?php if($chequePayment!=false) { echo date('d/m/Y',strtotime($chequePayment['cheque_date'])); } ?>
                            </td>
</tr>




<?php } ?>


<tr>
<td>NOC Received Date : </td>
				<td>
					<?php  $noc_date=date('d/m/Y',strtotime($settle_file['noc_received_date'])); if($noc_date!="01/01/1970") echo $noc_date; ?>
                            </td>
</tr>

<tr>
<td>Remarks : </td>
				<td>
					<?php echo $settle_file['remarks']; ?>
                            </td>
</tr>
 
</table>

<table class="no_print">
<tr>
<td width="250px;"></td>
<td>
 <a href="<?php echo 'index.php?view=edit&lid='.$settle_file['settle_id'].'&id='.$file_id; ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
           
<a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><button class="btn btn-warning" >Back</button></a>
</td>
</tr>

</table>

</div>
<div class="clearfix"></div>
<?php
if(isset($_GET['print_rasid']) && $_GET['print_rasid']=='yes')
{
 ?>
<script type="text/javascript">
window.print();
</script> 
 <?php } ?>