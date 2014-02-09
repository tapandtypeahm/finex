<?php
if(!isset($_GET['id']))
{
	header("Location: ".WEB_ROOT);
	exit;
	}
$file_id=$_GET['id'];	
$file=getFileDetailsByFileId($file_id);
$file_no=$file['file_number'];
$closure=getPrematureClosureDetails($file_id);
$customer=getCustomerNameANDCoByFileId($file_id);
if($closure['mode']==2)
{
	$chequePayment=getClosureChequeDetails($closure['file_closed_id']);
	}
else
{
	$chequePayment=false;
	}	
 ?>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Premature File Closure</h4>
<?php 
if(isset($_SESSION['ack']['msg']) && isset($_SESSION['ack']['type']))
{
	
	$msg=$_SESSION['ack']['msg'];
	$type=$_SESSION['ack']['type'];
	
	
		if($msg!=null && $msg!="" && $type>0)
		{
?>
<div class="alert  <?php if(isset($type) && $type>0 && $type<4) echo "alert-success"; else echo "alert-error" ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php if(isset($type)  && $type>0 && $type<4) { ?> <strong>Success!</strong> <?php } else if(isset($type)   && $type>3) { ?> <strong>Warning!</strong> <?php } ?> <?php echo $msg; ?>
</div>
<?php
		
		
		}
	if(isset($type) && $type>0)
		$_SESSION['ack']['type']=0;
	if($msg!="")
		$_SESSION['ack']['msg']=="";
}

?>

<table class="insertTableStyling detailStylingTable no_print">

<tr>
<td width="220px">Closure Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($closure['file_close_date'])); ?>
</td>
                    
                    
                  
</tr>
<tr>
<td>
Amount : 
</td>
<td>
<?php echo "Rs. ".number_format($closure['amount_paid']); ?>
</td>
</tr>

</tr>
<tr>
<td>
Mode : 
</td>
<td>
<?php if($closure['mode']==1) echo "CASH"; else echo "CHEQUE"; ?>
</td>
</tr>

<?php  if($chequePayment!=false) { ?>

<tr>
<td width="220px">Bank Name : </td>
				<td>
					<?php if($chequePayment!=false) { echo getBankNameByID($chequePayment['bank_id']); } ?>
                            </td>
</tr>
<tr>
<td width="220px">Branch Name : </td>
				<td>
					<?php if($chequePayment!=false) { echo getBranchhById($chequePayment['branch_id']); } ?>
                            </td>
</tr>
<tr>
<td width="220px">Cheque No : </td>
				<td>
					<?php if($chequePayment!=false) { echo $chequePayment['cheque_no']; } ?>
                            </td>
</tr>
<tr>
<td width="220px">Cheque Date : </td>
				<td>
					<?php if($chequePayment!=false) { echo $chequePayment['cheque_date']; } ?>
                            </td>
</tr>

<?php } ?>

</tr>
<tr>
<td>
Rasid No : 
</td>
<td>
<?php echo $closure['rasid_no']; ?>
</td>
</tr>


<tr>
<td>
Remarks : 
</td>
<td>
<?php if(validateForNull($closure['remarks'])) echo $closure['remarks']; else echo "NA"; ?>
</td>
</tr>

<tr>
    <td class="firstColumnStyling">
    Closed By: 
    </td>
    
    <td>
     
                                 <?php echo getAdminUserNameByID($closure['closed_by']); ?>					
                               
    </td>
</tr>

<tr>
<td width="220px">Entry Date : </td>
				<td>
					<?php echo date('d/m/Y',strtotime($closure['date_closed'])); ?>
</td>
                    
                    
                  
</tr>

<tr>

	<td></td>
  <td class="no_print"> 
             <a href="<?php echo WEB_ROOT.'admin/file/index.php?view=editClosure&id='.$file_id ?>"><button title="Edit this entry" class="btn splEditBtn editBtn"><span class="delete">E</span></button></a>
              <a href="<?php echo WEB_ROOT.'admin/file/index.php?action=deleteClosure&id='.$file_id ?>"><button title="Delete this File" class="btn splEditBtn editBtn btn-danger">Delete Closure</button></a>
           <a href="<?php echo WEB_ROOT; ?>admin/customer/index.php?view=details&id=<?php echo $file_id; ?>"><input type="button" class="btn btn-success" value="Back" /></a>
            </td>
</tr>       



</table>

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
<?php }} ?>
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
<b> <i> Date :</b> </i> <?php echo date("d/m/Y",strtotime($closure['file_close_date'])); ?>
</div>
<div style="clear:both"></div>

<div class="rasid">
<b> <i> Receipt No :</b> </i> <?php  $rasid_no=$closure['rasid_no']; preg_match('#[0-9]+$#', $rasid_no, $match);
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
<b> <i> the sum of Rupees :</b>  </i> <?php echo numberToWord($closure['amount_paid'])." Only"; ?>
</div>
<?php if($chequePayment!=false) { 
?>
<div class="rasid">
<b> <i> Cheque/Draft No. :</b>  </i><?php echo $chequePayment['cheque_no']; ?>  of <?php echo getBankNameByID($chequePayment['bank_id']); ?> For FINAL SETTLEMENT
</div>
<?php
 }
 else
 {
  ?>
<div class="rasid">
<b> <i> via CASH Payment For FINAL SETTLEMENT  </i> </b>
</div>  
<?php } ?>

<div class="rasid smallerfont">
ખાસ નોંધ : વીમો, ટેક્ષ, પરમીટ તથા પાસીંગ ની જવાબદારી લોન લેનાર પાર્ટીની છે. 
</div>

<div class="lowerLeftDiv">


        <div class="rectangle">
            <div class="Rs">
            Rs. 
            </div>
            
            <div class="amount">
            <?php echo number_format($closure['amount_paid'])." /- "; ?>
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
 

<div class="clearfix"></div>