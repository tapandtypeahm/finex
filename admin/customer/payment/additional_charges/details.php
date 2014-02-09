<?php

if(!isset($_GET['id']))
{
header("Location: ".WEB_ROOT."admin/search");
exit;
}
$file_id=$_GET['id'];
$penalty_id=$_GET['state'];


	$loan=getLoanDetailsByFileId($file_id);
	$loan_id=$loan['loan_id'];
	$penalty=getPenaltyById($penalty_id);
	$file=getFileDetailsByFileId($file_id);
	$file_no=$file['file_number'];
	$customer=getCustomerNameANDCoByFileId($file_id);
	$reg_no=getRegNoFromFileID($file_id);
	$rasid_type_name=getRasidTypeById($penalty['rasid_type_id']);
	if($penalty['payment_mode']==2)
	{
		$cheque_details=getChequeDetailsPenalty($penalty_id);
		}
	else
		{
			$cheque_details=0;
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

<div class="detailStyling no_print">


<h4 class="headingAlignment">Additional Payment Details</h4>


<table class="insertTableStyling detailStylingTable">



<tr>
<td class="firstColumnStyling">
Rasid Type : 
</td>

<td>
 
                             <?php  echo $rasid_type_name; ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Amount : 
</td>

<td>
 
                             <?php  echo $penalty['total_amount']." Rs"; ?>					
                           
</td>
</tr>


<tr>
<td class="firstColumnStyling">
Paid : 
</td>

<td>
 
                             <?php  if($penalty['paid']==1) echo "Yes"; else echo "No"; ?>					
                           
</td>
</tr>



<tr>
<td class="firstColumnStyling">
Penalty Payment Date: 
</td>

<td>
 
                             <?php  $myDate=strtotime($penalty['paid_date']); echo date('d/m/Y',$myDate); ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Payment Mode: 
</td>

<td>
 
                             <?php  if($penalty['payment_mode']==1) echo "CASH"; else echo "CHEQUE"; ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Rasid No: 
</td>

<td>
 
                             <?php  echo $penalty['rasid_no'];?>					
                           
</td>
</tr>
<tr>
<td class="firstColumnStyling">
Paid By: 
</td>

<td>
 
                             <?php  echo $penalty['paid_by'];?>					
                           
</td>
</tr>

<?php
if($cheque_details!=0)
{
 ?>
 <tr>
<td class="firstColumnStyling">
Bank name: 
</td>

<td>
 
                             <?php  echo getBankNameByID($cheque_details['bank_id']); ?>					
                           
</td>
</tr>

 <tr>
<td class="firstColumnStyling">
Branch name: 
</td>

<td>
 
                             <?php  echo getBranchhById($cheque_details['branch_id']); ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Cheque No: 
</td>

<td>
 
                             <?php  echo $cheque_details['cheque_no']; ?>					
                           
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Cheque Date: 
</td>

<td>
 
                             <?php  $myDate=strtotime($cheque_details['cheque_date']); echo date('d/m/Y',$myDate); ?>					
                           
</td>
</tr>
<?php } ?>


</table>

<table class="no_print">
<tr>
<td width="185px;"></td>
<td> <a href="<?php echo 'index.php?view=edit&id='.$file_id.'&state='.$penalty_id; ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
            
<a href="<?php echo WEB_ROOT; ?>admin/customer/payment/additional_charges/index.php?view=payments&id=<?php echo $file_id; ?>&state=<?php echo $loan_id; ?>"><button class="btn btn-success" >Back</button></a>
</td>
</tr>

</table>

</div>
<div class="rasidCover">

<div class="leftDiv">

<DIV class="ganesh">
|| શ્રી ગણેશાય નમઃ ||
</DIV>
<div class="realisation">
Subject to Realisation of Cheque / Draft
</div>

</div>

<?php $contactNos=getContactNoForOurCompany($_SESSION['adminSession']['oc_id']); ?>
<div class="contactNos">
<?php 
if(is_array($contactNos) && count($contactNos)>0)
{
foreach($contactNos as $no) { ?>
<div class="leneline">
<img src="<?php echo WEB_ROOT; ?>/images/fon.png" class="fonClass" /> <?php echo $no['our_company_contact_no']; ?>
</div>
<?php }}  ?>
</div>




<div style="clear:both"></div>

<div class="headingInRed">
<?php if(is_numeric($file['agency_id']))  {echo  getAgencyHeadingById($file['agency_id']);} else  echo getOurCompanyNameByID($file['oc_id']); ?>
</div>



<div class="subHeading">
<?php if(is_numeric($file['agency_id']))  {?>JVP of <?php echo getAgencyNameFromFileId($file_id); ?> <?php } ?>
</div>

<div class="address">
<?php echo getOurCompanyAddressByID($_SESSION['adminSession']['oc_id']); ?>
</div>

<div class="borderBottom"></div>
<div class="container">
<div class="dateDiv">
<b> <i> Date :</b> </i> <?php echo date("d/m/Y",strtotime($penalty['paid_date'])); ?>
</div>
<div style="clear:both"></div>

<div class="rasid">
<b> <i> Receipt No :</b> </i> <?php  $rasid_no=$penalty['rasid_no']; preg_match('#[0-9]+$#', $rasid_no, $match);
$end_number=$match[0];
if(is_numeric($end_number) && validateForNull($end_number))
{
$pos = strrpos($rasid_no, $end_number);

    if($pos !== false)
    {
        $start_string = substr_replace($rasid_no, "", $pos, strlen($end_number));
    }
}
echo $start_string." / ".$end_number."     ";
 ?>  
 
 <b> <i> File No :</b> </i> <?php   echo $file_no;
 ?>  
</div>

<div class="rasid">
<b> <i> Received From Shri/M/s. :</b>  </i> <?php echo $customer['customer_name']; ?>
</div>

<div class="rasid">
<b> <i> the sum of Rupees :</b>  </i> <?php echo numberToWord($penalty['total_amount'])." Only"; ?>
</div>
<?php if($chequePayment!=false) { 
?>
<div class="rasid">
<b> <i> Cheque/Draft No. :</b>  </i><?php echo $cheque_details['cheque_no']; ?>  of <?php echo getBankNameByID($cheque_details['bank_id']); ?>  <?php if($penalty['paid_by']!="NA" && $penalty['paid_by']!="") echo "By ".$penalty['paid_by']; ?>
</div>
<?php
 }
 else
 {
  ?>
<div class="rasid">
<b> <i> via CASH Payment</b>  </i>  <?php if($penalty['paid_by']!="NA" && $penalty['paid_by']!="")  echo "By ".$penalty['paid_by']; ?>
</div>  
<?php } ?>

<div class="rasid">
<b> <i>  For <?php echo $rasid_type_name; ?> </i></b>
</div>


<div class="rasid">
<b> <i> For Vehicle No : </i>  </b><?php echo $reg_no; ?>
</div>  

<div class="rasid smallerfont">
ખાસ નોંધ : વીમો, ટેક્ષ, પરમીટ તથા પાસીંગ ની જવાબદારી લોન લેનાર પાર્ટીની છે. 
</div>

<div class="lowerLeftDiv">


        <div class="rectangle">
            <div class="Rs">
            Rs. 
            </div>
            
            <div class="amount">
            <?php echo number_format($penalty['total_amount'])." /- "; ?>
            </div>
            
            <div style="clear:both"></div>
        </div>
      
        <div class="juridiction">
        Subject To Ahmedabad Juridiction
        </div>
        
         <div class="partySign">
        <b> Sign. of Party : </b> 
        <div class="signSpace"></div>
        </div>

</div>

<div class="lowerRightdiv">

      <div class="aboveSign">
      For, <?php echo getOurCompanyNameByID($_SESSION['adminSession']['oc_id']); ?>
      </div>

     <div class="square">
     </div>
     
     <div class="belowSign">
      Proprietor / Manager
      </div>
     
</div>
</div>
<div style="clear:both"></div>
   
</div> 
</div>
<div class="clearfix"></div>
<?php
if(isset($_GET['print_rasid']) && $_GET['print_rasid']=='yes')
{
 ?>
<script type="text/javascript">
window.print();
</script> 
 <?php } ?>