<?php
if(!isset($_GET['lid']) && is_numeric($_GET['lid']))
exit;
$file_id=$_GET['lid'];
$file=getFileDetailsByFileId($file_id);
$customer=getCustomerDetailsByFileId($file_id);
$current_company=getCurrentCompanyForUser($_SESSION['adminSession']['admin_id']);
		$oc_agency_id=$current_company[0];
		$company_type=$current_company[1];
if($company_type==0)
		{
			$account_settings=getAccountsSettingsForOC($oc_agency_id);
			}
		else if($company_type==1)
		{
			$account_settings=getAccountsSettingsForAgency($oc_agency_id);
			}			
?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment">Edit Customer Ledger</h4>
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
<input type="hidden" name="lid" value="<?php echo  $file_id; ?>" />
<input type="hidden" name="customer_id" value="<?php echo  $customer['customer_id']; ?>" />
<table class="insertTableStyling no_print">

<tr>
<td class="firstColumnStyling">
File Number : 
</td>

<td>
 <?php echo $file['file_number']; ?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Vehicle No : 
</td>

<td>
 <?php $reg_no=getRegNoFromFileID($file_id); if(validateForNull($reg_no)) echo $reg_no; ?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Agreement No : 
</td>

<td>
 <?php echo $file['file_agreement_no']; ?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Customer Name : 
</td>

<td>
 <?php echo $customer['customer_name']; ?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Customer Address : 
</td>

<td>
 <?php echo $customer['customer_address']; ?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Contact No : 
</td>

<td>
 <?php $contact_nos=$customer['contact_no']; foreach($contact_nos as $contact_no) echo $contact_no[0]."<br>"; ?>
</td>
</tr>


<tr>
<td> Opening Balance on <?php echo date('d/m/Y',strtotime($account_settings['ac_starting_date'])); ?> : </td>
<td> <input type="text" name="opening_balance" value="<?php echo $customer['opening_balance']; ?>"/> <select name="opening_balance_cd" class="credit_debit"><option value="0" <?php if(isset($customer['opening_cd']) && $customer['opening_cd']==0) { ?> selected="selected" <?php } ?>>Debit</option><option value="1" <?php if(isset($customer['opening_cd']) && $customer['opening_cd']==1) { ?> selected="selected" <?php } ?>>Credit</option> </select> </tr>
</tr>

<tr>
<td></td>
<td>
<input type="submit" value="Edit" class="btn btn-warning">
<a href="<?php echo WEB_ROOT ?>admin/accounts/customer_ledgers"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>
</table>
</form>
</div>
<div class="clearfix"></div>