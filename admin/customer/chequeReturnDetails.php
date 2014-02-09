<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
if(is_array($file) && $file!="error")
{
	$cheque_return_details=getChequeReturnDetailsForFileId($file_id);
	if($cheque_return_details=="error")
	$cheque_return_details=array();
}
else
{
	$_SESSION['ack']['msg']="Invalid File!";
	$_SESSION['ack']['type']=4; // 4 for error
	header("Location: ".WEB_ROOT."admin/search");
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



<a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><input type="button" value="Back" class="btn btn-warning" /></a>
<div class="clearfix"></div>
 <div class="no_print" style="width:100%;">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">Cheque Date</th>
             <th class="heading">Cheque NO</th>
             <th class="heading">Payment Amount</th>
            <th class="heading">Bank</th>
            <th class="heading btnCol no_print"></th>
           
        </tr>
    </thead>
    <tbody>
        
        <?php
		
		if($cheque_return_details!="error" && count($cheque_return_details)>0 && $cheque_return_details[0][0]!=0)
		{
			
		foreach($cheque_return_details as $cheque_return)
		{
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><?php echo date('d/m/Y',strtotime($cheque_return['cheque_date'])); ?>
            </td>
             <td><?php echo $cheque_return['cheque_no']; ?> 
			 </td>
             <td><?php echo number_format($cheque_return['cheque_amount']); ?> 
            </td>
            <td><?php $bank=getBankByID($cheque_return['bank_id']);echo $bank['bank_name']; ?> 
            </td>
            
           <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?action=deleteChequeReturn&id='.$file_id.'&lid='.$cheque_return['cheque_return_id'] ?>"><button title="Delete this entry" class="btn delBtn"><span class="view">X</span></button></a>
            </td> 
        </tr>
         <?php } } ?>
         </tbody>
    </table>
	</div>    
 <table id="to_print" class="to_print adminContentTable"></table>    
</div>
<div class="clearfix"></div>