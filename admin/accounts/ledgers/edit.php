<?php
if(!isset($_GET['lid']) && is_numeric($_GET['lid']))
exit;
$ledger_id=$_GET['lid'];
$ledger=getLedgerById($ledger_id);
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment">Add a New Ledger</h4>
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
<input type="hidden" name="lid" value="<?php echo  $ledger_id; ?>" />
<table class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Ledger Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="name" id="name" value="<?php if(isset($ledger['ledger_name'])) echo $ledger['ledger_name'] ?>" />
</td>
</tr>

<tr>
<td width="200px;">Head<span class="requiredField">* </span> : </td>
				<td>
					<select id="head" name="head_id">
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $heads = listAllHeads();
							
							$i=1;
                            foreach($heads as $head)
                              {
                             ?>
                             
                             <option value="<?php echo $head['head_id']; ?>" <?php if(isset($ledger['head_id'])){ if($head['head_id']==$ledger['head_id']) { ?> selected="selected" <?php } } ?> ><?php echo $head['head_name'] ?></option>
                             <?php } ?>
                              
                         </select>
                         
                            </td>
</tr>
<tr>
<td>
Notes : 
</td>

<td>
<textarea name="notes" cols="5" rows="4" id="notes"><?php if(isset($ledger['notes']) && $ledger['notes']!="NA") { echo $ledger['notes']; } ?></textarea>
</td>
</tr>
</table>
<h4 class="headingAlignment">Postal & Contact Details</h4>
<table class="insertTableStyling no_print">

<tr>

<td width="200px;" class="firstColumnStyling">
Postal Name : 
</td>

<td>
<input type="text" name="postal_name" id="postal_name" value="<?php if(isset($ledger['postal_name'])) echo $ledger['postal_name'] ?>"/>
</td>
</tr>

<tr>
<td>
Address : 
</td>

<td>
<textarea name="address" cols="5" rows="6" id="address"><?php if(isset($ledger['address']) && $ledger['address']!="NA") echo $ledger['address']; ?></textarea>
</td>
</tr>


<tr>
<td>City : </td>
				<td>
					<select id="city" name="city">
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $cities = listCities();
                            foreach($cities as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['city_id'] ?>" <?php if(isset($ledger['city_id'])){ if($super['city_id']==$ledger['city_id']) { ?> selected="selected" <?php } } ?>><?php echo $super['city_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>


<tr>
<td>Area : </td>
				<td>
					<input type="text" name="area" id="city_area1" placeholder="Only Letters" value="<?php if(isset($ledger['area_id'])) echo getAreaNameByID($ledger['area_id']); ?>" />
                            </td>
</tr>

<tr>
<td> Contact Number : </td>
<td> <input type="text" name="contactNo" value="<?php echo getledgerNumbersByledgerId($ledger_id); ?>"/> </tr>
</tr>
</table>
<h4 class="headingAlignment">TAX Information</h4>
<table class="insertTableStyling no_print">

<tr>

<td width="200px;" class="firstColumnStyling">PAN Number : </td>
<td> <input type="text" name="pan_no" value="<?php if(isset($ledger['pan_no']) && $ledger['pan_no']!=0) echo $ledger['pan_no']; ?>"/> </tr>
</tr>

<tr>
<td> TIN/Sales Number : </td>
<td> <input type="text" name="sales_no" value="<?php if(isset($ledger['sales_no']) && $ledger['sales_no']!=0)  echo $ledger['sales_no']; ?>"/> </tr>
</tr>

<tr>
<td> Opening Balance on <?php echo date('01/04/Y',strtotime($ledger['opening_date'])); ?> : </td>
<td> <input type="text" name="opening_balance" value="<?php echo $ledger['opening_balance']; ?>"/> <select name="opening_balance_cd" class="credit_debit"><option value="0" <?php if(isset($ledger['opening_cd']) && $ledger['opening_cd']==0) { ?> selected="selected" <?php } ?>>Debit</option><option value="1" <?php if(isset($ledger['opening_cd']) && $ledger['opening_cd']==1) { ?> selected="selected" <?php } ?>>Credit</option> </select> </tr>
</tr>

<tr>
<td></td>
<td>
<input type="submit" value="Edit" class="btn btn-warning">
<a href="<?php echo WEB_ROOT ?>admin/accounts/ledgers"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>
</table>
</form>
</div>
<div class="clearfix"></div>
<script>
 $( "#city_area1" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/city_area.php',
                { term: request.term, city_id:$('#city').val() }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#city_area1" ).val(ui.item.label);
			return false;
		}
    });
</script>	