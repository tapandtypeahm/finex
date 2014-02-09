<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");
$file_id=$_GET['id'];
if(!isset($_GET['lid']))
header("Location: ".WEB_ROOT."admin/customer/index.php?view=addRemainder&id=".$file_id);

$remainder_id=$_GET['lid'];
$remainder=getRemainderById($remainder_id);
?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Edit Remainder</h4>
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=editRemainder'; ?>" method="post" enctype="multipart/form-data" onsubmit="disableSubmitButton();">
<input name="lid" value="<?php echo $remainder_id; ?>" type="hidden">
<input name="file_id" value="<?php echo $file_id; ?>" type="hidden">

<table class="insertTableStyling no_print">

<tr>
<td width="220px">Remainder Date : </td>
				<td>
					<input type="text" id="raminderDate" autocomplete="off" size="12"  name="remainderDate" class="datepicker1 datepick" placeholder="Click to Select!" value="<?php if(($remainder['date']!="0000-00-00") && ($remainder['date']!="1970-01-01")) echo date('d/m/Y',strtotime($remainder['date'])); ?>" /><span class="DateError customError">Please select a date!</span>
</td>
                    
                    
                  
</tr>
<?php ?>
<tr>
<td>
Remarks<span class="requiredField">* </span> : 
</td>
<td>
<textarea type="text"  name="remarks" id="remarks_remainder"><?php echo $remainder['remarks']; ?></textarea>
</td>
</tr>

<tr>
<td></td>
<td>
<input type="submit" value="Edit" class="btn btn-warning"> <a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=addRemainder&id=<?php echo $file_id; ?>"><input type="button" class="btn btn-success" value="Back" /></a>
</td>
</tr>

</table>

</form>
</div>
<div class="clearfix"></div>