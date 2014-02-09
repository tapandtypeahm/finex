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
		$totalPayment=getTotalPaymentForLoan($loan['loan_id']);
		$balance_left=getBalanceForLoan($loan['loan_id']); 
		$total_collection = getTotalCollectionForLoan($loan['loan_id']);
		$paid_emis=getTotalEmiPaidForLoan($loan['loan_id']);
		
	    $duration=$loan['loan_duration'];
		$emi_without_interest=$loan['loan_amount']/$duration;
		$total_interet=$total_collection-$loan['loan_amount'];
		$interest=$total_interet/$duration;
		
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


<div class="addDetailsBtnStyling no_print"><a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>&state=<?php echo $customer_id; ?>"><button class="btn btn-warning">Go to Main File</button></a> <a href="index.php?view=search"><button class="btn btn-warning">Go to Search</button></a></div>
<div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button>    </div> 
<div class="interest_certificate_container">
<div class="prati">પ્રતિ ,</div>
<div class="saheb_shri">સાહેબ શ્રી   ,</div>
             <div class="main_para"> &nbsp; &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;    સવિનય઼ સાથે જાણવાનું  કે અમારી company <?php
							 
							  $id =  $file['agency_id']; 
							 if($id!=null)
							 {
							        $agencyDetails=getAgencyById($id);
							?>
                            <span class="special_text">
                            <?php		
									echo $agencyDetails['agency_name'];
							?>
                           </span> ની franchisee ધરાવે  છે,તે
                            <?php		
									
							 }
							 else
							 {
							 ?>
                              <span class="special_text">
                             <?php	 
								 $id=$file['oc_id'];
								 echo getOurCompanyNameByID($id);
							?>
                            </span>
                            <?php	 
								 }
							 ?>	માં શ્રી <span class="special_text"><?php echo $customer['customer_name']; ?></span> ની <span class="special_text"><?php $vehicle_company_id=$vehicle['vehicle_company_id']; echo getVehicleCompanyNameById($vehicle_company_id); ?></span>
રીક્ષા  ની <span class="special_text"><?php echo number_format($loan['loan_amount']); ?></span>/- રૂપિયાની લોંન  <span class="special_text"><?php echo date('d/m/Y',strtotime($loan['loan_approval_date'])); ?></span> ના રોજ મંજુર થયેલ છે, તેમાં તેઓ શ્રી એ  <?php if($loan['loan_scheme']==1) { ?><span class="special_text"><?php echo number_format($loan['emi']); ?>/- </span> ના <span class="special_text"><?php echo $loan['loan_duration']; ?></span><?php } else if($loan['loan_scheme']==2) { $loan_scheme=getLoanScheme($loan['loan_id']); $i=0; foreach($loan_scheme as $scheme) { ?> <span class="special_text"><?php echo number_format($scheme['emi']); ?>/- </span> ના <span class="special_text"><?php echo $scheme['duration']; ?></span> <?php if($i<(count($loan_scheme)-1)) { ?> અને <?php } $i++; } } ?> હપ્તા ભરવાનું નક્કી કરેલ છે . તેમાં તેઓ શ્રી એ 
આજ દિન સુધી કુલ  <span class="special_text"><?php echo number_format($totalPayment); ?></span>/- રૂપિયા જમા કરાવેલ છે તેમાં આજ દિન સુધી મૂડી માં <span class="special_text"><?php echo number_format($totalPayment-($interest*$paid_emis)); ?></span>/- રૂપિયા જમા થયેલ છે, અને વ્યાજ માં  <span class="special_text"><?php echo number_format($interest*$paid_emis); ?></span>/-   રૂપિયા જમા થયેલ છે. આજ દિન સુધી તેઓ શ્રી એ રેગુલર હપ્તા ભરેલ છે .  તેઓ શ્રી એ આજ દિન સુધી ગાડી ન  <span class="special_text"><?php if($vehicle!=false && is_array($vehicle) && isset($vehicle['vehicle_reg_no'])) echo $vehicle['vehicle_reg_no']; ?></span> પેટે કુલ <span class="special_text"><?php echo number_format($totalPayment); ?></span>/- રૂપિયા જમા કરાવેલ છે ,                                                                                                                                                                                                                                                                  
 </div>
<ul class="overview">  
<li>નામ : <?php echo  $customer['customer_name'] ?></li>
<li>ગાડી ન : <?php if($vehicle!=false && is_array($vehicle) && isset($vehicle['vehicle_reg_no'])) echo $vehicle['vehicle_reg_no']; ?> </li>
<li>હપ્તા જમા : <?php echo number_format($paid_emis,2); ?></li>
<li>મૂડી માં : <?php echo  number_format($totalPayment-($interest*$paid_emis)); ?>/-</li>
<li>વ્યાજ માં : <?php echo number_format($paid_emis*$interest); ?>/-   જમા આવેલ છે ,</li>
<li>કુલ : <?php echo number_format($totalPayment); ?>/- જમા આવેલ છે. </li>
 </ul>         

                                                                          <div class="lee">   લી  .</div>
                                                                       <div class="visvasu"> આપનોવિશ્વાસુ </div>                                                                            


</div>
</div>
<div class="clearfix"></div>