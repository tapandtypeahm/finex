<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];

?>

<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Add a Remainder</h4>
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
<form onsubmit="disableSubmitButton();" id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=addRemainder'; ?>" method="post" enctype="multipart/form-data">
<input name="lid" value="<?php echo $file_id; ?>" type="hidden">
<table class="insertTableStyling no_print">

<tr>
<td width="220px">Remainder Date : </td>
				<td>
					<input onchange="onChangeDate(this.value,this)"  type="text" id="raminderDate" autocomplete="off" size="12"  name="remainderDate" class="datepicker1 datepick" placeholder="Click to Select!" /><span class="DateError customError">Please select a date!</span>
</td>
                    
                    
                  
</tr>
<?php ?>
<tr>
<td>
Remarks<span class="requiredField">* </span> : 
</td>
<td>
<textarea type="text"  name="remarks" id="remarks_remainder"></textarea>
</td>
</tr>

<tr>
<td></td>
<td>
<input id="disableSubmit" type="submit" value="Add" class="btn btn-warning"> <a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><input type="button" class="btn btn-success" value="Back" /></a>
</td>
</tr>

</table>

</form>
  <hr class="firstTableFinishing" />

<h4 class="headingAlignment">List of Remainders</h4>
    <div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>
   	<div class="no_print">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">Date</th>
            <th class="heading">Remarks</th>
            <th class="heading">Status</th>
            <th class="heading no_print btnCol" ></th>
            <th class="heading no_print btnCol"></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
		$dealers=listRemainderForFile($file_id);
		$no=0;
		if($dealers!=false)
		{ 
		foreach($dealers as $agencyDetails)
		{
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><?php if($agencyDetails['date']!="1970-01-01") echo date('d/m/Y',strtotime($agencyDetails['date'])); else echo 'NA'; ?>
            </td>
            <td><?php echo $agencyDetails['remarks'] ?>
            </td> 
             <td><?php  if($agencyDetails['remainder_status']==0) echo "UN-DONE"; else echo "DONE"; ?><br /><?php if($agencyDetails['remainder_status']==0) { ?><a class="no_print" href="<?php echo WEB_ROOT ?>admin/customer/index.php?action=doneRemainderGeneral&id=<?php echo $file_id; ?>&lid=<?php echo $agencyDetails['remainder_id']; ?>" style="font-size:12px; color:#d00;" onclick="return confirm('Are you sure?')">Set Done</a> <?php } else { ?> <a class="no_print" onclick="return confirm('Are you sure?')" href="<?php echo WEB_ROOT ?>admin/customer/index.php?action=unDoneRemainderGeneral&id=<?php echo $file_id; ?>&lid=<?php echo $agencyDetails['remainder_id']; ?>" style="font-size:12px; color:#d00;">Set unDone</a><?php } ?>
            </td> 
            <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editRemainder&id='.$file_id.'&lid='.$agencyDetails['remainder_id']; ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
            </td>
            <td class="no_print"> 
            <a href="<?php echo $_SERVER['PHP_SELF'].'?action=deleteRemainder&id='.$file_id.'&lid='.$agencyDetails['remainder_id']; ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
            </td>
            
          
  
        </tr>
         <?php } }?>
         </tbody>
    </table>
    </div>
     <table id="to_print" class="to_print adminContentTable"></table> 
</div>
<div class="clearfix"></div>