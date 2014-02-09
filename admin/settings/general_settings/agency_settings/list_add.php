<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Add a New Agency</h4>
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
<form id="addAgencyForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=add'; ?>" method="post" onsubmit="return submitOurAgency()">
<table class="insertTableStyling no_print">

<tr>
<td class="firstColumnStyling">
Agency name<span class="requiredField">* </span> : 
</td>
<td>
<input type="text" name="agencyName" id="txtName" placeholder="Only Letters and numbers!"/>
</td>
</tr>
<tr>
<td>
Prefix for the Agency<span class="requiredField">* </span> : 
</td>

<td>
<input type="text" name="agencyPrefix" id="txtPrefix" placeholder="Only Letters and numbers!"/>
</td>
</tr>
  <tr>
            <td> Heading : </td>
            <td> <input id="txtsubheading" type="text" name="sub_heading"/> </td>
            </tr>
<tr>
<td>Contact Person : </td>
<td><input type="text" name="contactPerson" id="txtPersonName" placeholder="Only letters!"/><span class="ValidationErrors contactNoError">First character should a  Letter!</span></td>
</tr>

<tr>
<td>Contact Number : </td>
<td>
<input type="text" name="contactNumber" id="txtPhone" placeholder="Only Numbers! '-' not allowed"/><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
</td>
</tr>

<tr>
<td> Contact Address : </td>
<td><textarea name="contactAddress" rows="6" cols="5" id="txtAddress"></textarea></td>
</tr>

<tr>
<td class="firstColumnStyling">
EMI Auto Paid on Date : 
</td>

<td>
<input  type="radio" name="auto_pay" class="auto_pay" id="auto_pay_no" value="0"  checked="checked"  /> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="auto_pay_no">No</label>
<input  type="radio" name="auto_pay" class="auto_pay" id="auto_pay_yes" value="1" /> <label style="display:inline-block;top:3px;position:relative;" for="auto_pay_yes">Yes</label>
</td> 
</tr> 

  <tr>
            <td> Auto Pay Date(1-30)<span class="requiredField">* </span> : </td>
            <td> <input id="txtsubheading" type="text" name="auto_pay_date" /> </td>
            </tr>

<tr>
<td></td>
<td>
<input type="submit" value="Add Agency" class="btn btn-warning">
<a href="<?php echo WEB_ROOT ?>admin/settings/"><input type="button" value="back" class="btn btn-success" /></a>
</td>
</tr>
</table>
</form>
	
    <hr class="firstTableFinishing" />

<h4 class="headingAlignment">List of Agencies</h4>
<div class="printBtnDiv no_print"><a href="<?php echo 'index.php?action=resetCounters'; ?>" onclick="return confirm('Are you sure that you want to reset Rasid No to 1?')"><input type="button" class="btn btn-danger"  value="Reset Radid No" /></a><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>
    <div class="no_print">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">Agency Name</th>
            <th class="heading">Prefix for the Agency</th>
            <th class="heading">Contact Person</th>
            <th class="heading">Contact Number</th>
            <th class="heading">Contact Address</th>
             <th class="heading">Auto Paid</th>
            <th class="heading">Rasid Counter</th>
             <th class="heading no_print btnCol"></th>
            <th class="heading no_print btnCol"></th>
            <th class="heading no_print btnCol"></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
		$agencies=listAgencies();
		$no=0;
		foreach($agencies as $agencyDetails)
		{
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><?php echo $agencyDetails['agency_name']; ?>
            </td>
            <td><?php echo $agencyDetails['agency_prefix']; ?>
            </td>
            <td><?php echo $agencyDetails['agency_contact_name']; ?>
            </td>
            <td><?php if($agencyDetails['agency_contact_no']>0) echo $agencyDetails['agency_contact_no']; else echo "NA"; ?>
            </td>
            <td><?php echo $agencyDetails['agency_address']; ?>
            </td>
            <td><?php  if($agencyDetails['auto_pay']==0) echo "NO"; else echo "YES"; ?>
            </td>
            <td><?php echo $agencyDetails['rasid_counter']; ?>
            </td>
            <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&lid='.$agencyDetails['agency_id'] ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=edit&lid='.$agencyDetails['agency_id'] ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
            </td>
            <td class="no_print"> 
            <a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&lid='.$agencyDetails['agency_id'] ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
            </td>
        </tr>
         <?php }?>
         </tbody>
    </table>
    </div>
     <table id="to_print" class="to_print adminContentTable"></table> 
</div>
<div class="clearfix"></div>
<script type="text/javascript">
 $( ".datepicker1" ).datepicker({
      changeMonth: true,
      changeYear: true,
	   dateFormat: 'dd/mm/yy'
    });

</script>