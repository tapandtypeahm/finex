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
<form id="addAgencyForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=add'; ?>" method="post" onsubmit="return checkCheckBox()">
<table class="insertTableStyling no_print">

<tr>

<td class="firstColumnStyling">
Ledger Name<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="name" id="name" />
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
                             
                             <option value="<?php echo $head['head_id'] ?>" ><?php echo $head['head_name'] ?></option>
                             <?php } ?>
                              
                         </select>
                         
                            </td>
</tr>
<tr>
<td>
Notes : 
</td>

<td>
<textarea name="notes" cols="5" rows="4" id="notes"></textarea>
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
<input type="text" name="postal_name" id="postal_name"/>
</td>
</tr>

<tr>
<td>
Address : 
</td>

<td>
<textarea name="address" cols="5" rows="6" id="address"></textarea>
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
                             
                             <option value="<?php echo $super['city_id'] ?>"><?php echo $super['city_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>


<tr>
<td>Area : </td>
				<td>
					<input type="text" name="area" id="city_area1" placeholder="Only Letters" />
                            </td>
</tr>

<tr>
<td> Contact Number : </td>
<td> <input type="text" name="contactNo"/> </tr>
</tr>
</table>
<h4 class="headingAlignment">TAX Information</h4>
<table class="insertTableStyling no_print">

<tr>

<td width="200px;" class="firstColumnStyling">PAN Number : </td>
<td> <input type="text" name="pan_no"/> </tr>
</tr>

<tr>
<td> TIN/Sales Number : </td>
<td> <input type="text" name="sales_no"/> </tr>
</tr>

<tr>
<td> Opening Balance on <?php echo date('01/04/Y',strtotime(getCurrentDateForUser($admin_id=$_SESSION['adminSession']['admin_id']))); ?> : </td>
<td> <input type="text" name="opening_balance"/> <select name="opening_balance_cd" class="credit_debit"><option value="0">Debit</option><option value="1">Credit</option> </select> </tr>
</tr>

<tr>
<td></td>
<td>
<input type="submit" value="Add Ledger" class="btn btn-warning">
<a href="<?php echo WEB_ROOT ?>admin/accounts/"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>
</table>
</form>
	
    <hr class="firstTableFinishing" />

<h4 class="headingAlignment">List of Ledgers</h4>
    <div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>
   	<div class="no_print">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">Ledger Name</th>
            <th class="heading">Head</th>
            <th class="heading">City</th>
            <th class="heading">Contact Number</th>
            <th class="heading">Opening Balance</th> 
            <th class="heading">Opening Balance Date</th> 
            <th class="heading no_print btnCol" ></th>
            <th class="heading no_print btnCol" ></th>
            <th class="heading no_print btnCol"></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
		$dealers=listLedgers();
		$no=0;
		foreach($dealers as $agencyDetails)
		{
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><span  class="editLocationName" id="<?php echo $agencyDetails['ledger_id'] ?>"><?php echo $agencyDetails['ledger_name']; ?></span>
            </td>
             <td><span  class="editLocationName" id="<?php echo $agencyDetails['ledger_id'] ?>"><?php $head=getHeadById($agencyDetails['head_id']); echo $head['head_name'] ?></span>
            </td> 
            <td><span  class="editLocationName" id="<?php echo $agencyDetails['ledger_id'] ?>"><?php   $city=getCityByID($agencyDetails['city_id']); echo $city['city_name']; ?></span>
            </td>
            
           <td><span  class="editLocationName" id="<?php echo $agencyDetails['ledger_id'] ?>"><?php   $contact_nos=getledgerNumbersByledgerId($agencyDetails['ledger_id']); if(is_array($contact_nos)){ foreach($contact_nos as $contact_no) { echo $contact_no[0]." <br>"; }}  ?></span>
            </td>
             <td><span  class="editLocationName" id="<?php echo $agencyDetails['ledger_id'] ?>"><?php echo $agencyDetails['opening_balance']." "; if($agencyDetails['opening_cd']==0) echo "Dr"; else echo "Cr"; ?></span>
            </td>
              <td><span  class="editLocationName" id="<?php echo $agencyDetails['ledger_id'] ?>"><?php echo date('d/m/Y',strtotime($agencyDetails['opening_date'])); ?></span>
            </td>
            
            <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&lid='.$agencyDetails['ledger_id'] ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=edit&lid='.$agencyDetails['ledger_id'] ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
            </td>
            <td class="no_print"> 
            <a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&lid='.$agencyDetails['ledger_id'] ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
            </td>
            
          
  
        </tr>
         <?php }?>
         </tbody>
    </table>
    </div>
     <table id="to_print" class="to_print adminContentTable"></table> 
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