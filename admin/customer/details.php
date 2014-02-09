<?php
if(!isset($_GET['id']))
header("Location: ".WEB_ROOT."admin/search");

$file_id=$_GET['id'];
$file=getFileDetailsByFileId($file_id);
if(is_array($file) && $file!="error")
{
	$settle_file=getSettleFileDetails($file_id);
	$customer=getCustomerDetailsByFileId($file_id);
	$guarantor=getGuarantorDetailsByFileId($file_id);
	$loan=getLoanDetailsByFileId($file_id);
	$vehicle=getVehicleDetailsByFileId($file_id);
	$cheque_return_detais=getChequeReturnDetailsForFileId($file_id);
	$customer_id=$customer['customer_id'];
	$agency_participation_details=getLoanSchemeAgency($loan['loan_id']);
	if($file['file_status']==4)
	{
		$closureDetails=getPrematureClosureDetails($file_id);
		}
	if($loan!="error")
	{
		$emiTable=getLoanTableForLoanId($loan['loan_id']);
		$totalPayment=getTotalPaymentForLoan($loan['loan_id']);
		$totalEMIsPaid=number_format(getTotalEmiPaidForLoan($loan['loan_id']),2);
		$balance_left=getBalanceForLoan($loan['loan_id']); 	
	};
	if($file!="error")
	{
		
		$seize=getVehicleSeizeDetailsByFileId($file_id);
		}
	else
	{
		$seize="error";
		}	
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
<?php
if($seize!="error")
{
?>
<div class="alert alert-error"><b>VEHICLE SEIZED <?php if($seize['sold']==1) echo " AND SOLD"; ?></b></div>
<?php	
	}
 ?>
<div class="addDetailsBtnStyling no_print"><?php if($vehicle=="error"){ ?><a href="vehicle/index.php?id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button class="btn btn-success">+ Add vehicle</button></a><?php  } ?> <a href="vehicle/insurance/index.php?id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button class="btn btn-success">+ Add Insurance</button></a> <?php if(!is_array($guarantor) && !is_numeric($guarantor['guarantor_id'])) { ?> <a href="index.php?view=addGuarantor&id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button class="btn btn-success">+ Add Guarantor</button></a> <?php } ?><?php if(isset($vehicle) && $seize=="error") { ?> <a href="<?php echo WEB_ROOT; ?>admin/customer/vehicle/seize/index.php?view=seize&id=<?php echo $file_id; ?>&state=<?php echo $vehicle['vehicle_id']; ?>"><button class="btn btn-danger">+ Seize Vehicle</button></a> <?php } ?> <?php if(($file['file_status']!=4 && $file['file_status']!=3) && (($file['file_status']==2 && $balance_left<0) || $file['file_status']==1)) {?> <a href="<?php echo WEB_ROOT ?>admin/file/index.php?view=closeFile&id=<?php echo $file_id; ?>"><button class="btn btn-danger">Close File</button></a> <?php } ?><?php if(is_numeric($file['agency_id']) && $file['agency_id']!=null && $settle_file=="error") { ?> <a href="<?php echo WEB_ROOT ?>admin/file/settle/index.php?view=add&id=<?php echo $file_id; ?>"><button class="btn btn-danger">Settle File</button></a><?php } ?><a href="<?php echo WEB_ROOT; ?>admin/customer/payment/additional_charges/index.php?view=payments&id=<?php echo $file_id; ?>"><button class="btn btn-warning">Additional Payments</button></a> <a href="<?php echo WEB_ROOT; ?>admin/search/"><button class="btn btn-warning">Go to Search</button></a></div>
<div class="addDetailsBtnStyling no_print"> <a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=addRemainder&id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button class="btn btn-success">+ Add / View Reminder</button></a> <span class="noOfRemainders"><b><?php if($remarks!=false) echo count($remarks)." Pending Reminders!"; ?></b></span> </div>
<div class="detailStyling">

<h4 class="headingAlignment">File Details</h4>

<table class="insertTableStyling detailStylingTable">

<tr>
<td>File Status : </td>
				<td>
				
                             <?php   if($file['file_status']==1) echo "OPEN"; else if($file['file_status']==2 && $balance_left<0) echo "CLOSED & UNPAID"; else if($file['file_status']==3) echo "DELETED";else if($file['file_status']==4 && (strtotime($loan['loan_ending_date'])<=strtotime($closureDetails['file_close_date']))) echo "FORCED CLOSURE";else if($file['file_status']==4 && (strtotime($loan['loan_ending_date'])>strtotime($closureDetails['file_close_date']))) echo "PREMATURE CLOSURE"; else echo "CLOSED"; if($settle_file!="error")
									echo " (Settled)"; ?>					
                            

                 </td>
</tr>



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
<td>Broker : </td>
				<td>
				
                             <?php echo getBrokerNameFromBrokerId($file['broker_id']); ?>					
                            

                 </td>
</tr>




<tr>

<td class="firstColumnStyling">
Total Collection : 
</td>

<td>

                             <?php 
							 $total_collection =  getTotalCollectionForLoan($loan['loan_id']);
							 echo  "Rs. ".number_format($total_collection); ?>					
                           
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Total Payments Received : 
</td>

<td>

                             <?php  
							 		echo "Rs. ".number_format($total_collection+$balance_left);
									if($file['file_status']==4)
									{
										$prematureClosureAmount=getPrematureClosureAmount($file_id);
										if($prematureClosureAmount>0)
											{
											echo " + ".number_format($prematureClosureAmount)."(Closure Amount)";
											}
										}
							 ?>					
                           
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Total Payments Left : 
</td>

<td>

                             <?php  
							 		if($file['file_status']==4)
									{
										echo number_format(-$balance_left-$prematureClosureAmount);
										}
									else
									{	
							 		$balance_left=getBalanceForLoan($loan['loan_id']); 
									
									closeFileIfBalanceZero($loan['loan_id']);
							 		echo "Rs. ".number_format(-$balance_left);
									}
							 ?>					
                           
</td>
</tr>
<?php

 if($agency_participation_details!="error" && is_numeric($agency_participation_details[0]['agency_emi']) && is_numeric($agency_participation_details[0]['agency_duration']))
{
 ?>
<tr>

<td class="firstColumnStyling">
Agency Loan Amount : 
</td>

<td>

                              <?php  
							 		if($agency_participation_details!="error")
									{
										echo "RS. ".number_format($loan['agency_loan_amount']);
										}
									
							 ?>		
                           
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Agency EMI X Durarion : 
</td>

<td>

                             <?php  
							 		if($agency_participation_details!="error")
									{ foreach($agency_participation_details as $agency_participation_detail)
										echo $agency_participation_detail['agency_emi']." X ".$agency_participation_detail['agency_duration']."<br>";
										}
									
							 ?>					
                           
</td>
</tr>



<tr>

<td class="firstColumnStyling">
Total Collection For Agency : 
</td>

<td>

                             <?php  
							 		if($agency_participation_details!="error")
									{
										$tot=0;
										foreach($agency_participation_details as $agency_participation_detail)
										$tot=$tot+($agency_participation_detail['agency_emi']*$agency_participation_detail['agency_duration']);
										echo "RS. ".number_format($tot);
										}
									
							 ?>					
                           
</td>
</tr>
<?php } ?>
 <tr>

<td class="firstColumnStyling">
Profit : 
</td>

<td>

                             <?php
							 
							  if($agency_participation_details!="error")
									{
										echo "RS. ".number_format($total_collection-$tot);
										}
									else
									echo "RS. ".number_format(getProfitForLoan($loan['loan_id'])); ?>					
                           
</td>
</tr> 

<tr>

	<td></td>
  <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=fileDetails&id='.$file_id ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editFile&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
              <a href="<?php echo $_SERVER['PHP_SELF'].'?action=deleteFile&id='.$file_id ?>"><button title="Delete this File" class="btn splEditBtn editBtn btn-danger">Delete file</button></a>
            </td>
</tr>            



</table>

</div>

<div class="detailStyling">

<h4 class="headingAlignment">Loan Details </h4>


<table class="insertTableStyling detailStylingTable">

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
Loan Duration (In months) : 
</td>

<td>
                             <?php echo $loan['loan_duration']; ?>					
                          
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Loan Type : 
</td>

<td>
                             <?php if($loan['loan_type']==1) echo "FLAT"; else if($loan['loan_type']==2) echo "REDUCING"; ?>					
                          
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Loan Structure : 
</td>

<td>
                             <?php if($loan['loan_scheme']==1) echo "EVEN"; else if($loan['loan_scheme']==2) echo "UNEVEN"; ?>					
                          
</td>
</tr>


<tr>

<td class="firstColumnStyling">
Flat Rate of Interest (annually in %) : 
</td>

<td>

                             <?php echo number_format($loan['roi'],1)." % "; ?>					
                            
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Reducing Rate of Interest (annually in %) : 
</td>

<td>

                             <?php echo number_format($loan['reducing_roi'],1)." % "; ?>					
                            
</td>
</tr>

<tr>

<td class="firstColumnStyling">
IRR (Internal Rate of Return) : 
</td>

<td>

                             <?php echo number_format($loan['IRR'],1); ?>					
                            
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
									  echo number_format($e['emi'])." X ".$e['duration']."<br>";
									  }
								  
								  } ?>					
                            </td>
</tr>

<tr>

<td class="firstColumnStyling">
Total EMIs Paid : 
</td>

<td>

                             <?php echo number_format($totalEMIsPaid,2)." / ".$loan['loan_duration']; ?>					
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

<tr>

<td class="firstColumnStyling">
Bucket : 
</td>

<td>

                             <?php
							 $bucket_details=getBucketDetailsForLoan($loan['loan_id']);
							 if(is_array($bucket_details))
							 {
							foreach($bucket_details as $emi_amount=>$bucket_for_corresponding_emi)
							{
								echo $emi_amount." X ".$bucket_for_corresponding_emi."<br>";
								}
							 }
							 else
							 echo number_format(0,2);
							// $actualEMis=getNoOfEmiBeforeDateForLoanId($loan['loan_id'],date('Y-m-d'));
							 //$bucket=$actualEMis-$totalEMIsPaid;
							//if($bucket>0)  echo number_format($bucket,1); else echo "0"; ?>					
                            </td>
</tr>

<tr>
<td class="firstColumnStyling">
Loan Approval Date : 
</td>

<td>
 
                             <?php
							 echo date('d/m/Y',strtotime($loan['loan_approval_date']));
				
							  ?>					
                           
</td>
</tr>


<tr>
<td class="firstColumnStyling">
Loan Starting Date : 
</td>

<td>
 
                             <?php
							 echo date('d/m/Y',strtotime($loan['loan_starting_date']));
				
							  ?>					
                           
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Loan Ending Date: 
</td>

<td>

                             <?php  echo date('d/m/Y',strtotime($loan['loan_ending_date'])); ?>					
                           
</td>
</tr>

<tr>
	<td></td>
  <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=loanDetails&id='.$file_id ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editLoan&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
            </td>
</tr>  

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

<tr>
	<td></td>
  <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=customerDetails&id='.$file_id ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editCustomer&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
            </td>
</tr>            

</table>
</div>



<?php   if(is_array($guarantor) && isset($guarantor['guarantor_id']) && is_numeric($guarantor['guarantor_id'])) { ?>
<div class="detailStyling" style="min-height:370px;">

<h4 class="headingAlignment">Guranteer's Details</h4>


<table id="insertGuarantorTable" class="insertTableStyling detailStylingTable">


<tr>

<td class="firstColumnStyling">
 Name : 
</td>

<td>
                             <?php echo $guarantor['guarantor_name']; ?>					
                             
</td>
</tr>

<tr>
<td>
Guranteer's Address : 
</td>

<td style="max-width:300px;">

                             <?php echo $guarantor['guarantor_address']; ?>					
                             </td>
</tr>


<tr>
<td>City : </td>
				<td>
   
                             <?php $gid =  $guarantor['city_id']; 
                             $gCityDetails = getCityByID($gid);
								   echo $gCityDetails['city_name'];	
?>
                            </td>
</tr>

<tr>
<td>Area : </td>
				<td>

                             <?php $cid = $guarantor['area_id'];
							 		
							       $cityDetails = getAreaByID($cid);
								   echo $cityDetails['area_name'];
							?>
                            </td>
</tr>
<td>Pincode : </td>
<td>
   
                             <?php if($guarantor['guarantor_pincode']!=0) echo $guarantor['guarantor_pincode']; else echo "NA"; ?>					
                           

</td>
</tr>



 <tr id="addcontactTrGuarantor">
                <td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                	<?php
                            $contactNos = $guarantor['contact_no'];
							
                            foreach($contactNos as $c)
                              {
                       ?>
                             
                             <?php echo $c[0]." <br> "; ?>					
                             <?php } ?>
                </td>
            </tr>
 
 <tr>
	<td></td>
  <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=guarantorDetails&id='.$file_id ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            
             <a href="<?php echo $_SERVER['PHP_SELF'].'?view=editGuarantor&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
            </td>
</tr>            
           
            
 </table>
</div>

<?php } ?>

<hr class="firstTableFinishing" />
<?php if($vehicle!="error"){ ?>
<div class="detailStyling" style="min-height:300px;">
<h4 class="headingAlignment"> Vehicle Details </h4>


<table id="insertGuarantorTable" class="insertTableStyling detailStylingTable">

<tr>
<td>Vehicle Company : </td>
<td><?php  $company=getVehicleCompanyById($vehicle['vehicle_company_id']); echo $company['company_name']; ?> </td>
</tr>

<tr>
<td>Vehicle Model : </td>
				<td>
					<?php echo getModelNameById($vehicle['model_id']); ?>
                            </td>
</tr>

<tr>
<td>Vehicle Dealer : </td>
				<td>
					<?php echo getDealerNameFromDealerId($vehicle['vehicle_dealer_id']); ?>
                            </td>
</tr>

<tr>
       <td>Vehicle Condition :</td>
           
           
        <td>
            <?php if($vehicle['vehicle_condition']==1) echo "NEW"; else echo "OLD"; ?>
        </td>
 </tr>
 
 <tr>
<td>Vehicle Model : </td>
				<td>
					<?php echo $vehicle['vehicle_model']; ?>
                            </td>
</tr>

<tr>
<td>Vehicle Type : </td>
				<td>
					<?php $vehicle_type = getVehicleTypeById($vehicle['vehicle_type_id']); echo $vehicle_type['vehicle_type']; ?>	
                </td>
</tr>
 
<tr>
<td class="firstColumnStyling">
Registration Number : 
</td>

<td>
<?php  $reg_no=$vehicle['vehicle_reg_no']; $reg_no=strtoupper($reg_no); echo $reg_no; ?>
</td>
</tr>



<tr>
	<td></td>
  <td class="no_print"> <a href="<?php echo 'vehicle/index.php?view=vehicleDetails&id='.$file_id.'&state='.$vehicle['vehicle_id'] ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            
             <a href="<?php echo 'vehicle/index.php?view=editVehicle&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
            </td>
</tr>   

</table>
</div>



<?php } 
if($seize!="error")
{
?>
<div class="detailStyling" style="min-height:300px;">
<h4 class="headingAlignment"> Vehicle Seize Details </h4>


<table id="insertGuarantorTable" class="insertTableStyling detailStylingTable">

<tr>
<td>Seize Date : </td>
<td><?php    echo date('d/m/Y',strtotime($seize['seize_date'])); ?> </td>
</tr>

<tr>
<td>Vehicle Sold : </td>
<td><?php    if($seize['sold']==0) echo "No"; else echo "Yes"; ?> </td>
</tr>

<tr>
<td>Remarks : </td>
				<td>
					<?php if($seize['remarks']!="") echo $seize['remarks']; else echo "NA"; ?>
                            </td>
</tr>

<tr>
	<td></td>
  <td class="no_print"> <a href="<?php echo 'vehicle/seize/index.php?view=details&id='.$file_id.'&state='.$vehicle['vehicle_id']."&state2=".$seize['seize_id'] ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            
             <a href="<?php echo 'vehicle/seize/index.php?view=edit&id='.$file_id.'&state='.$vehicle['vehicle_id']."&state2=".$seize['seize_id'] ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
              <a href="<?php echo 'vehicle/seize/index.php?action=delete&file_id='.$file_id.'&state='.$vehicle['vehicle_id']."&lid=".$seize['seize_id'] ?>"><button title="Delete this entry" class="btn splEditBtn delBtn"><span class="delete">X</span></button></a>
            </td>
</tr>   

</table>
</div>
<?php } if(is_array($insurance)) {?>
<div class="detailStyling">
<h4 class="headingAlignment"> Insurance Details </h4>


<table id="insertGuarantorTable" class="insertTableStyling detailStylingTable">

<tr>
<td>Insurance Company : </td>
				<td>
					<?php  $comp=getInsuranceCompanyById($insurance['insurance_company_id']); echo $comp[1]; ?>
                            </td>
</tr>

<tr>
<td>Insurance Issue Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($insurance['insurance_issue_date'])); ?>
                            </td>
</tr>

<tr>
<td>Insurance Expiry Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($insurance['insurance_expiry_date'])); ?>
                            </td>
</tr>

<tr>

    <td class="firstColumnStyling">
    Isurance Declared Value (IDV) : 
    </td>
    
    <td>
    <?php echo "Rs. ".number_format($insurance['idv']); ?>
    </td>
</tr>

<tr>

    <td class="firstColumnStyling">
    Premium : 
    </td>
    
    <td>
     <?php echo "Rs. ".number_format($insurance['insurance_premium']); ?>
    </td>
</tr>

<tr>
	<td></td>
  <td class="no_print"> <a href="<?php echo WEB_ROOT.'admin/customer/vehicle/insurance/?view=details&id='.$file_id ?>"><button title="View this entry" class="btn viewBtn"><span class="view">View All</span></button></a>
   </td>        
</tr>  


</table>
</div>
<?php } ?>
<div class="detailStyling">

<h4 class="headingAlignment">Penalty Details </h4>


<table class="insertTableStyling detailStylingTable">

<tr>
    <td class="firstColumnStyling">
    Total Penalty uptill today : 
    </td>
    
    <td>
     
                                 <?php echo number_format(getTotalPenaltyForLoan($loan['loan_id']))." Days"; ?>					
                               
    </td>
</tr>

<tr>
    <td class="firstColumnStyling">
    Penalty Days Paid: 
    </td>
    
    <td>
     
                                 <?php echo number_format(getTotalPenaltyPaidDaysForLoan($loan['loan_id']))." Days"; ?>					
                               
    </td>
</tr>

<tr>
        <td></td>
      <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=penaltyDetails&id='.$file_id ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
       </td>        
</tr>  


</table>
</div>

<?php
    if($file['file_status']==4)
	{
		
?>
<div class="detailStyling">

<h4 class="headingAlignment">Premature Closure Details </h4>


<table class="insertTableStyling detailStylingTable">


<tr>
    <td class="firstColumnStyling">
    Closed Date: 
    </td>
    
    <td>
     
                                 <?php echo date("d/m/Y",strtotime($closureDetails['file_close_date'])); ?>					
                               
    </td>
</tr>

<tr>
    <td class="firstColumnStyling">
    Amount Paid: 
    </td>
    
    <td>
     
                                 <?php echo "Rs. ".number_format($closureDetails['amount_paid']); ?>					
                               
    </td>
</tr>

<tr>
    <td class="firstColumnStyling">
    Remarks: 
    </td>
    
    <td>
     
                                 <?php if($closureDetails['remarks']!="") echo $closureDetails['remarks']; else echo "NA"; ?>			
                               
    </td>
</tr>

<tr>
    <td class="firstColumnStyling">
    Closed By: 
    </td>
    
    <td>
     
                                 <?php echo getAdminUserNameByID($closureDetails['closed_by']); ?>					
                               
    </td>
</tr>


 
 

<tr>

	<td></td>
  <td class="no_print"> <a href="<?php echo WEB_ROOT.'admin/file/index.php?view=closureDetails&id='.$file_id ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            
             <a href="<?php echo WEB_ROOT.'admin/file/index.php?view=editClosure&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
              <a href="<?php echo WEB_ROOT.'admin/file/index.php?action=deleteClosure&id='.$file_id ?>"><button title="Delete this File" class="btn splEditBtn editBtn btn-danger">Delete Closure</button></a>
            </td>
</tr>       




</table>
</div>
<?php		
		}
if($settle_file!="error")
{
 ?>
 
<div class="detailStyling">

<h4 class="headingAlignment">Settlement Details </h4>
 
<table id="rasidTable" class="detailStylingTable insertTableStyling">

<tr>
<td>Payment Amount : </td>
				<td>
					<?php echo "Rs. ".number_format($settle_file['settle_amount']); ?>
                            </td>
</tr>
<tr>
<td>Payment Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($settle_file['settle_date'])); ?>
                            </td>
</tr>

<tr>
<td>Rasid No : </td>
				<td>
					<?php echo $settle_file['receipt_no']; ?>
                            </td>
</tr>

<tr>
<td>Payment Mode : </td>
				<td>
					 <?php if($settle_file['payment_mode']==1) { echo "CASH"; }else echo "CHEQUE"; ?>
                            </td>
</tr>

<tr>
<td>NOC Received Date : </td>
				<td>
					<?php  $noc_date=date('d/m/Y',strtotime($settle_file['noc_received_date'])); if($noc_date!="01/01/1970") echo $noc_date; ?>
                            </td>
</tr>

<tr>
<td>Remarks : </td>
				<td>
					<?php echo $settle_file['remarks']; ?>
                            </td>
</tr>

<tr>

	<td></td>
  <td class="no_print"> <a href="<?php echo WEB_ROOT.'admin/file/settle/index.php?view=details&id='.$file_id ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            
             <a href="<?php echo WEB_ROOT.'admin/file/settle/index.php?view=edit&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
              <a href="<?php echo WEB_ROOT.'admin/file/settle/index.php?action=delete&lid='.$file_id ?>"><button title="Delete this File" class="btn splEditBtn editBtn btn-danger">Delete Settlement</button></a>
            </td>
</tr>  
</table> 
</div>
<?php  } ?>




 


</div>

<?php if($remarks!=false && is_array($remarks) && count($remarks)>0)
{ ?>
<div class="detailStyling">

<h4 class="headingAlignment">Remarks Details </h4>


<table class="insertTableStyling detailStylingTable">

<?php foreach($remarks as $remark)
{
?> 
<tr>
    <td class="firstColumnStyling">
   <?php  if($remark['date']=='1970-01-01' || $remark['date']=='0000-00-00')  {?> Remark : <?php } else {  echo date('d/m/Y',strtotime($remark['date'])); } ?> 
    </td>
    
    <td>
     
                                 <?php echo $remark['remarks']; ?>					
                               
    </td>
</tr>

<?php
}
 ?>
 
 
 
 

<tr>
	<td></td>
  <td class="no_print"> <a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=addRemainder&id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">View All</span></button></a>
   </td>        
</tr> 




</table>
</div>
<?php } ?>

<?php if($cheque_return_detais!="error" && is_array($cheque_return_detais) && count($cheque_return_detais)>0)
{ ?>
<div class="detailStyling">

<h4 class="headingAlignment">Cheque Return Details </h4>


<table class="insertTableStyling detailStylingTable">


<tr>
    <td class="firstColumnStyling">
    Total Cheque Return: 
    </td>
    
    <td>
     
                                 <?php echo count($cheque_return_detais); ?>					
                               
    </td>
</tr>


 
 
 
 

<tr>
	<td></td>
  <td class="no_print"> <a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=chequeReturnDetails&id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">View All</span></button></a>
   </td>        
</tr> 




</table>
</div>
<?php } ?>
<?php if(isset($loan) && $loan!="error") {
	
	$loan_emi_id_unpaid=getOldestUnPaidEmi($loan['loan_id']);

?>
<div class="clearfix"></div>
<h4 class="headingAlignment">Emi Details</h4>

<div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button><?php  if($loan_emi_id_unpaid!=false && is_numeric($loan_emi_id_unpaid) && ($file['file_status']==1 || $file['file_status']==2)) { ?> <a class="no_print" href="payment/index.php?id=<?php echo $file_id; ?>&state=<?php echo $loan_emi_id_unpaid; ?>" style="font-size:12px; color:#d00;"><button class="btn btn-success" style="float:right;position:relative;top:10px;margin-left:10px;" >+ Add payment</button></a> <?php } ?> <!--<?php  if($loan_emi_id_unpaid!=false && is_numeric($loan_emi_id_unpaid)) { ?>  <a class="no_print" href="payment/index.php?view=addMultiple&id=<?php echo $file_id; ?>&state=<?php echo $loan_emi_id_unpaid; ?>" style="font-size:12px; color:#d00;"><button class="btn btn-success" style="float:right;position:relative;top:10px;" >+ Add Multiple payment</button></a>  <?php } ?> --></div>
    <div class="no_print" style="width:100%;">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading date">EMI Date</th>
            <th class="heading">Amount</th>
            <th class="heading">Payment</th>
            <th class="heading">Balance</th>
            <th class="heading date">Payment Date</th>
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
            
            <td><?php echo  number_format($emiDetails['emi_amount']); ?>
            </td>
            
            <td><?php if($paymentDetails==false) { $balance=$emiDetails['emi_amount'];} else{ $totalPaid=0; foreach($paymentDetails as $paymentDetail){ if($paymentDetail['payment_mode']==1)$payment_mode="CS"; else $payment_mode="CQ"; echo number_format($paymentDetail['payment_amount'])."(".$payment_mode.")<br/>"; $totalPaid=$totalPaid+$paymentDetail['payment_amount']; $balance=$emiDetails['emi_amount']-$totalPaid;} // echo "<hr class='inTableHr'>".number_format($totalPaid); 
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
            
            
             	<td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=EMIdetails&id='.$file_id.'&state='.$emiDetails['loan_emi_id'] ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
        </tr>

         <?php }?>
         </tbody>
    </table>
    
	</div>  
 <table style="width:100%;" id="to_print" class="to_print adminContentTable"></table>     
<?php	
	} ?>
    

<div class="clearfix"></div>