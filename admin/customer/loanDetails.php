<?php

if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
$loan=getLoanDetailsByFileId($file_id);
$full_table=getIntPrincBalanceTableForLoan($loan['loan_id']);
?>
<div class="insideCoreContent adminContentWrapper wrapper">

<h4 class="headingAlignment">Loan Details </h4>

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

<div class="detailStyling">

<table class="insertTableStyling detailStylingTable">


<tr>

<td class="firstColumnStyling">
Total Loan Amount : 
</td>

<td>

                             <?php echo $loan['loan_amount']; ?>					
                           
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Loan Duration (In months) : 
</td>

<td>
                             <?php echo $loan['loan_duration']; ?>					
                          
</td>
</tr>

<tr>

<tr>

<td class="firstColumnStyling">
Loan Type : 
</td>

<td>
                             <?php if($loan['loan_type']==1) echo "FLAT"; else if($loan['loan_type']==2) echo "REDUCING"; ?>					
                          
</td>
</tr>


<td class="firstColumnStyling">
Rate of Interest (annually in %) : 
</td>

<td>

                             <?php echo $loan['roi']; ?>					
                            
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Reducing Rate of Interest (annually in %) : 
</td>

<td>

                             <?php echo $loan['reducing_roi']; ?>					
                            
</td>
</tr>

<tr>

<td class="firstColumnStyling">
IRR (Internal Rate of Return) : 
</td>

<td>

                             <?php echo $loan['IRR']; ?>					
                            
</td>
</tr>

<tr>

<td class="firstColumnStyling">
EMI : 
</td>

<td>

                             <?php
							 $emi=getEmiForLoanId($loan['loan_id']); // amount if even loan or loan structure if loan is uneven
							 if($loan['loan_scheme']==1)
							  echo "Rs. ".number_format($loan['emi']);
							  else
							  {
								  foreach($emi as $e)
								  {
									  echo $e['emi']." X ".$e['duration']."<br>";
									  }
								  
								  } ?>					
                            </td>
</tr>

<tr>
<td class="firstColumnStyling">
Loan Approval Date : 
</td>

<td>
 
                             <?php echo date('d/m/Y',strtotime($loan['loan_approval_date'])); ?>					
                           
</td>
</tr>


<tr>
<td class="firstColumnStyling">
Loan Starting Date : 
</td>

<td>
 
                             <?php echo date('d/m/Y',strtotime($loan['loan_starting_date'])); ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Loan Ending Date : 
</td>

<td>
 
                             <?php echo date('d/m/Y',strtotime($loan['loan_ending_date'])); ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Loan Amount Given In : 
</td>

<td>
 
                             <?php  if($loan['loan_amount_type']==1) echo "CASH"; else echo "CHEQUE"; ?>					
                           
</td>
</tr>

<?php

if($loan['loan_amount_type']==2)
{
  $loan_cheque=getLoanChequeByLoanId($loan['loan_id']);
 ?>

<tr>
<td class="firstColumnStyling">
Bank Name : 
</td>

<td>
 
                             <?php echo getBankNameByID($loan_cheque['bank_id']); ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Branch Name : 
</td>

<td>
 
                              <?php echo getBranchhById($loan_cheque['branch_id']); ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Cheque Amount : 
</td>

<td>
 
                              <?php echo $loan_cheque['loan_cheque_amount']; ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Cheque Date : 
</td>

<td>
 
                               <?php echo date('d/m/Y',strtotime($loan_cheque['loan_cheque_date'])); ?>							
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Cheque No : 
</td>

<td>
 
                               <?php echo $loan_cheque['loan_cheque_no']; ?>							
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Axin No : 
</td>

<td>
 
                               <?php echo $loan_cheque['loan_cheque_axin_no']; ?>							
                           
</td>
</tr>



<?php 

}
 ?>

<tr>
	<td></td>
  <td class="no_print">
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editLoan&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
            <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&id='.$file_id ?>"><input type="button" value="Back" class="btn btn-success" /></a>
</td>
</tr>    

</table>
</div>

<div class="no_print" style="width:100%;">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">Actual Date</th>
             <th class="heading">EMI</th>
             <th class="heading">Interest</th>
            <th class="heading">Principal</th>
            <th class="heading">Balance</th>
           
        </tr>
    </thead>
    <tbody>
        
        <?php
	
		$no=0;
		if($full_table!=false)
		{
		foreach($full_table as $row)
		{	
	
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><?php echo date('d/m/Y',strtotime($row['actual_emi_date'])); ?>
            </td>
            <td><?php  echo number_format($row['emi']); ?> 
            </td>
             <td><?php echo number_format($row['interest']); ?> 
			 </td>
              <td><?php echo number_format($row['principal']); ?> 
			 </td>
            <td><?php  if($no==count($full_table)) echo 0;else  echo number_format($row['balance']); ?> 
			 </td>
            
        </tr>
         <?php  }}?>
         </tbody>
    </table>
	</div>    
 <table id="to_print" class="to_print adminContentTable"></table>    

</div>

<div class="clearfix"></div>