<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
if(is_array($file) && $file!="error")
{
	$customer=getCustomerDetailsByFileId($file_id);
	$guarantor=getGuarantorDetailsByFileId($file_id);
	$loan=getLoanDetailsByFileId($file_id);
	$vehicle=getVehicleDetailsByFileId($file_id);
	$customer_id=$customer['customer_id'];
	if($file['file_status']==4)
	{
		$closureDetails=getPrematureClosureDetails($file_id);
		}
	if($loan!="error")
	{
		
		$emiTable=getLoanTableForLoanId($loan['loan_id']);
		$totalPayment=getTotalPaymentForLoan($loan['loan_id']);
		$totalEMIsPaid=$totalPayment/$loan['emi'];
		$balance_left=getBalanceForLoan($loan['loan_id']); 
		$total_collection =  getTotalCollectionForLoan($loan['loan_id']);
	};
	if($file!="error")
	{
	$remarks=listRemarksForFile($file_id);
	$insurance=getInsurancesForFileID($file_id);
	$insurance=$insurance[0]; //latest insurance
	}
	else
	{
		$insurance="error";
	}
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


<div class="addDetailsBtnStyling no_print"><a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button class="btn btn-warning">Go to Main File</button></a> <a href="index.php?view=search"><button class="btn btn-warning">Go to Search</button></a> <a href="index.php?view=interest_certificate&id=<?php echo $file_id; ?>"><button class="btn btn-warning">Interest Certificate</button></a></div>
<div class="detailStyling">

<h4 class="headingAlignment">General Details</h4>

<table class="insertTableStyling detailStylingTable">

<tr>
<td>Agency Name : </td>
				<td>
				
                             <?php
							 
							  $id =  $file['agency_id']; 
							 if($id!=null)
							 {
							        $agencyDetails=getAgencyById($id);
									echo $agencyDetails['agency_name'];
							 }
							 else
							 {
								 $id=$file['oc_id'];
								 echo getOurCompanyNameByID($id);
								 }
							 ?>					
                             
                </td>
                    
                    
                  
</tr>

<tr>
<td>
File Agreement No : 
</td>
<td>
 
                             <?php echo $file['file_agreement_no'] ?>					
                             

</td>
</tr>

<tr>
<td>File Number : </td>
				<td>
				
                             <?php echo $file['file_number']; ?>					
                            

                 </td>
</tr>

<tr>

<td class="firstColumnStyling">
EMI : 
</td>

<td>

                             <?php echo "Rs. ".number_format($loan['emi']); ?>					
                           
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Total Loan Amount : 
</td>

<td>

                             <?php echo "Rs. ".number_format($loan['loan_amount']); ?>					
                           
</td>
</tr>



<tr>

<td class="firstColumnStyling">
Total Payments Received : 
</td>

<td>

                             <?php  
							 		echo "Rs. ".number_format($total_collection+$balance_left);
							 ?>					
                           
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Total Payments Left : 
</td>

<td>

                             <?php  $balance_left=getBalanceForLoan($loan['loan_id']); 
							 		echo "Rs. ".number_format(-$balance_left);
							 ?>					
                           
</td>
</tr>

<td class="firstColumnStyling">
Loan Duration (In months) : 
</td>

<td>
                             <?php echo $loan['loan_duration']; ?>					
                          
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Total EMIs Left : 
</td>

<td>

                             <?php echo ($loan['loan_duration']-$totalEMIsPaid)." / ".$loan['loan_duration']; ?>					
                            </td>
</tr>


<!--<tr>

<td class="firstColumnStyling">
Bucket : 
</td>

<td>

                             <?php
							 $actualEMis=getNoOfEmiBeforeDateForLoanId($loan['loan_id'],date('Y-m-d'));
							 $bucket=$actualEMis-$totalEMIsPaid;
							if($bucket>0)  echo $bucket; else echo "0"; ?>					
                            </td>
</tr> -->


<?php if($vehicle!="error"){ ?>

 
<tr>
<td class="firstColumnStyling">
Registration Number : 
</td>

<td>
<?php  $reg_no=$vehicle['vehicle_reg_no']; $reg_no=strtoupper($reg_no); echo $reg_no;?>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Vehicle Company : 
</td>

<td>
<?php  echo getVehicleCompanyNameById($vehicle['vehicle_company_id']);?>
</td>
</tr>

<?php } ?>

<!--<tr>
<td class="firstColumnStyling">
Total Penalty uptill today : 
</td>

<td>
 
                             <?php echo getTotalPenaltyForLoan($loan['loan_id'])." Days"; ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Penalty Days Paid: 
</td>

<td>
 
                             <?php echo getTotalPenaltyPaidDaysForLoan($loan['loan_id'])." Days"; ?>					
                           
</td>
</tr> -->

</table>
</div>

<div class="detailStyling" style="min-height:300px">

<h4 class="headingAlignment">Customer's Details</h4>

<table id="insertCustomerTable" class="insertTableStyling detailStylingTable">


<tr>

<td class="firstColumnStyling">
Name : 
</td>

<td>

                             <?php echo $customer['customer_name']; ?>					
                            
</td>
</tr>

<tr>
<td >
Address : 
</td>

<td style="max-width:300px;">

                             <?php echo $customer['customer_address']; ?>					
                            
</td>
</tr>


<tr>
<td>City : </td>
				<td>

                             <?php $cid = $customer['city_id'];
							 		
							       $cityDetails = getCityByID($cid);
								   echo $cityDetails['city_name'];
							?>
                            </td>
</tr>

<tr>
<td>Area : </td>
				<td>

                             <?php $cid = $customer['area_id'];
							 		
							       $cityDetails = getAreaByID($cid);
								   echo $cityDetails['area_name'];
							?>
                            </td>
</tr>

<tr>
<td>Pincode : </td>
<td>

                             <?php if($customer['customer_pincode']!=0) echo $customer['customer_pincode']; else echo "NA"; ?>					
                          
</td>
</tr>



 <tr id="addcontactTrCustomer">
                <td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                <?php
                            $contactNumbers = $customer['contact_no'];
							
                            for($z=0;$z<count($contactNumbers);$z++)
                              {
								$c=$contactNumbers[$z];
								if($z==(count($contactNumbers)-1))
								echo $c[0];  
								else
                      			echo $c[0]." <br> ";				
                              } ?>
                </td>
            </tr>


</table>
</div>


<?php if(isset($loan) && $loan!="error") {
	
	$loan_emi_id_unpaid=getOldestUnPaidEmi($loan['loan_id']);

?>
<div class="clearfix"></div>
<h4 class="headingAlignment">Emi Details</h4>

<div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button><?php  if($loan_emi_id_unpaid!=false && is_numeric($loan_emi_id_unpaid)) { ?> <a class="no_print" href="<?php echo WEB_ROOT ?>admin/customer/payment/index.php?id=<?php echo $file_id; ?>&state=<?php echo $loan_emi_id_unpaid; ?>" style="font-size:12px; color:#d00;"><button class="btn btn-success" style="float:right;position:relative;top:10px;margin-left:10px;" >+ Add payment</button></a> <?php } ?> <?php  if($loan_emi_id_unpaid!=false && is_numeric($loan_emi_id_unpaid)) { ?>  <a class="no_print" href="<?php echo WEB_ROOT ?>admin/customer/payment/index.php?view=addMultiple&id=<?php echo $file_id; ?>&state=<?php echo $loan_emi_id_unpaid; ?>" style="font-size:12px; color:#d00;"><button class="btn btn-success" style="float:right;position:relative;top:10px;" >+ Add Multiple payment</button></a>  <?php } ?></div>
    <div class="no_print" style="width:100%;">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading date">EMI Date</th>
            <th class="heading">Amount</th>
            <th class="heading">Payment</th>
            <th class="heading">Balance</th>
            <th class="heading">Payment Date</th>
            <th class="heading">Penalty</th>
            <th class="heading">Rasid No</th>
          	<th class="heading">Company Paid Date</th>
            <th class="heading" width="20%">Remarks</th>
            <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
		
		$no=0;
		foreach($emiTable as $emi)
		{	
			$emiDetails=$emi['loanDetails'];
			$paymentDetails=$emi['paymentDetails'];
			$acBal=getBalanceForEmi($emiDetails['loan_emi_id']);
			
			$balance=0;
		 ?>
         
         <tr class="resultRow <?php if(date("Y-m-d")>=$emiDetails['actual_emi_date'] && $acBal<0) { ?> dangerRow<?php } ?> <?php if(date("Y-m-d")>=$emiDetails['actual_emi_date'] && $acBal==0) { ?> shantiRow<?php } ?>">
        	<td><?php echo ++$no; ?>
            </td>
            
            <td><?php echo date('d/m/Y',strtotime($emiDetails['actual_emi_date'])); ?>
            </td>
            
            <td><?php echo  number_format($loan['emi']); ?>
            </td>
            
             <td><?php if($paymentDetails==false) { $balance=$loan['emi'];} else{ $totalPaid=0; foreach($paymentDetails as $paymentDetail){ if($paymentDetail['payment_mode']==1)$payment_mode="CS"; else $payment_mode="CQ"; echo number_format($paymentDetail['payment_amount'])."(".$payment_mode.")<br/>"; $totalPaid=$totalPaid+$paymentDetail['payment_amount']; $balance=$loan['emi']-$totalPaid;} // echo "<hr class='inTableHr'>".number_format($totalPaid); 
			  }  ?>
            </td>
            
            <td><?php if($balance>0) echo "-".number_format($balance); else echo $balance; ?></td>
            
             <td><?php if($paymentDetails==false) echo "NA"; else{ foreach($paymentDetails as $paymentDetail){ echo date('d/m/Y',strtotime($paymentDetail['payment_date']))."<br/>";}}  ?>
            </td>
            
            <td>
            	<?php echo getPenaltyDaysFroEmiId($emiDetails['loan_emi_id'])." Days"; ?>
            </td>
            
            <td><?php if($paymentDetails==false) echo "NA"; else{ foreach($paymentDetails as $paymentDetail){ echo $paymentDetail['rasid_no']."<br/>";}}  ?>
            </td>
            
            <td><?php if( $emiDetails['company_paid_date']!=null) { echo date('d/m/Y',strtotime($emiDetails['company_paid_date'])); ?><br /><a class="no_print" href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=editCompanyPaidDate&id=<?php echo $file_id; ?>&lid=<?php echo $emiDetails['loan_emi_id']; ?>" style="font-size:12px; color:#d00;">Edit</a> <a class="no_print" onclick="return confirm('Are you sure?')" href="<?php echo WEB_ROOT ?>admin/customer/index.php?action=deleteCompanyPaidDate&id=<?php echo $file_id; ?>&lid=<?php echo $emiDetails['loan_emi_id']; ?>" style="font-size:12px; color:#d00;">Del</a><?php } ?><?php if(date("Y-m-d")>=$emiDetails['actual_emi_date'] && $emiDetails['company_paid_date']==null) { ?><a class="no_print" href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=addCompanyPaidDate&id=<?php echo $file_id; ?>&lid=<?php echo $emiDetails['loan_emi_id']; ?>" style="font-size:12px; color:#d00;">Add</a><?php } ?>
            </td>
            
             <td><?php if($paymentDetails==false) echo "NA"; else{ foreach($paymentDetails as $paymentDetail){  if($paymentDetail['remarks']=="" && ($paymentDetail['remainder_date']=="0000-00-00" || $paymentDetail['remainder_date']=="1970-01-01")){ echo "<br>"; }else if($paymentDetail['remarks']!="" && ($paymentDetail['remainder_date']=="0000-00-00" || $paymentDetail['remainder_date']=="1970-01-01")){ echo " <li> ".$paymentDetail['remarks']."</li><br/>";}else if($paymentDetail['remarks']!="" && $paymentDetail['remainder_date']!=null && $paymentDetail['remainder_date']!="1970-01-01" && $paymentDetail['remainder_date']!="0000-00-00"){  echo " <li> ".$paymentDetail['remarks']." | ".$paymentDetail['remainder_date']."</li><br/>";}else if($paymentDetail['remarks']=="" && $paymentDetail['remainder_date']!=null && $paymentDetail['remainder_date']!="1970-01-01" && $paymentDetail['remainder_date']!="0000-00-00"){  echo " <li> ".$paymentDetail['remainder_date']."</li><br/>";}}}  ?>
            </td>
            
            
            
             	<td class="no_print"> <a href="<?php echo WEB_ROOT.'admin/customer/index.php?view=EMIdetails&id='.$file_id.'&state='.$emiDetails['loan_emi_id'].'&return=findEmiDetails' ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            
            
          
  
        </tr>

         <?php }?>
         </tbody>
    </table>
	</div>    
 <table style="width:100%;" id="to_print" class="to_print adminContentTable"></table>     
<?php	
	} ?>
</div>
<div class="clearfix"></div>