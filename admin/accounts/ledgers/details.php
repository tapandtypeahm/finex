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
<table class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Ledger Name  : 
</td>

<td>
<?php if(isset($ledger['ledger_name'])) echo $ledger['ledger_name'] ?>
</td>
</tr>

<tr>
<td width="200px;">Head  : </td>
				<td>
                      
                           
                             
      <?php if(isset($ledger['head_id'])){  $head=getHeadById($ledger['head_id']); echo $head['head_name']; } ?> 
                            
                              
                        
                         
                            </td>
</tr>
<tr>
<td>
Notes : 
</td>

<td>
<?php if(isset($ledger['notes'])) { echo $ledger['notes']; } ?>
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
<?php if(isset($ledger['postal_name'])) echo $ledger['postal_name'] ?>
</td>
</tr>

<tr>
<td>
Address : 
</td>

<td>
<?php if(isset($ledger['address']) ) echo $ledger['address']; ?>
</td>
</tr>


<tr>
<td>City : </td>
				<td>
					 <?php if(isset($ledger['city_id'])){ $city=getCityByID($ledger['city_id']); echo $city['city_name']; } ?> 
                            
                            </td>
</tr>


<tr>
<td>Area : </td>
				<td>
					<?php if(isset($ledger['area_id'])) echo getAreaNameByID($ledger['area_id']); ?>
                            </td>
</tr>

<tr>
<td> Contact Number : </td>
<td> <?php echo getledgerNumbersByledgerId($ledger_id); ?> </tr>
</tr>
</table>
<h4 class="headingAlignment">TAX Information</h4>
<table class="insertTableStyling no_print">

<tr>

<td width="200px;" class="firstColumnStyling">PAN Number : </td>
<td> <?php if(isset($ledger['pan_no']) && $ledger['pan_no']!=0) echo $ledger['pan_no']; ?> </tr>
</tr>

<tr>
<td> TIN/Sales Number : </td>
<td> <?php if(isset($ledger['sales_no']) && $ledger['sales_no']!=0)  echo $ledger['sales_no']; ?> </tr>
</tr>

<tr>
<td> Opening Balance on <?php echo date('01/04/Y',strtotime($ledger['opening_date'])); ?> : </td>
<td> <?php echo number_format($ledger['opening_balance']); ?> <?php if(isset($ledger['opening_cd']) && $ledger['opening_cd']==0) { echo "Dr";  }  else echo "Cr"; ?>
 </tr>
</tr>

<tr>
<td></td>
<td>
<a href="<?php echo WEB_ROOT ?>admin/accounts/ledgers/index.php?view=edit&lid=<?php echo $ledger_id; ?>"><input type="submit" value="Edit" class="btn btn-warning" /></a>
<a href="<?php echo WEB_ROOT ?>admin/accounts/ledgers/"><input type="button" value="back" class="btn btn-success" /></a>
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