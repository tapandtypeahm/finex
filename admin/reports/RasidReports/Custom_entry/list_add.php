<div class="jvp"><?php if(isset($_SESSION['cRasidReportEntry']['agency_id']) && $_SESSION['cRasidReportEntry']['agency_id']!="") { echo getAgecnyIdOrOCidNameFromAgnecySelectInput($_SESSION['cRasidReportEntry']['agency_id']);  } ?></div>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Custom Rasid Reports</h4>
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=generateReportEntry'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurCompany()">

<table class="insertTableStyling no_print">

<tr>
<td>From Date (Entry Date) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="start_date" id="start_date" placeholder="Click to select Date!" class="datepicker2" value="<?php if(isset($_SESSION['cRasidReportEntry']['from'])) echo $_SESSION['cRasidReportEntry']['from']; ?>" />	
                 </td>
</tr>


<tr>
<td>Up To Date (Entry Date) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="end_date" id="end_date" placeholder="Click to select Date!" class="datepicker2" value="<?php if(isset($_SESSION['cRasidReportEntry']['to'])) echo $_SESSION['cRasidReportEntry']['to']; ?>"/>	
                 </td>
</tr>



<tr>
<td>City : </td>
				<td>
					<select id="customer_city_id" name="city_id" class="city"   onchange="createDropDownAreaReports(this.value)">
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $cities = listCitiesAlpha();
                            foreach($cities as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['city_id'] ?>" <?php if(isset($_SESSION['cRasidReportEntry']['city_id'])){ if( $super['city_id'] == $_SESSION['cRasidReportEntry']['city_id'] ) { ?> selected="selected" <?php }} ?>><?php echo $super['city_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
<td>Area : </td>
				<td>
					<select name="area[]" class="city_area selectpicker" multiple="multiple"  id="city_area1" >
                    	 <option value="-1" >--Please Select--</option>
                          <?php
						  if(isset($_SESSION['cRasidReportEntry']['city_id'])){
                            $areas = listAreasFromCityIdWithGroups($_SESSION['cRasidReportEntry']['city_id']);
                            foreach($areas as $area)
                              {
                             ?>
                             
                             <option value="<?php echo $area['area_id'] ?>" <?php if(isset($_SESSION['cRasidReportEntry']['area_id_array'])){ if(in_array($area['area_id'],$_SESSION['cRasidReportEntry']['area_id_array'])) { ?> selected="selected" <?php }} ?>><?php echo $area['area_name'] ?></option					>
                             <?php } 
						  }
							 ?>
                    </select>
                            </td>
</tr>

<tr>
<td width="220px">Agency Name : </td>
				<td>
					<select id="agency_id" name="agency_id">
                        <option value="-1" >--Please Select--</option>
                        <?php
                            $agencies = listAgencies();
							$companies = listOurCompanies();
                            foreach($agencies as $super)
							
                              {
                             ?>
                             
                             <option value="ag<?php echo $super['agency_id'] ?>" <?php if(isset($_SESSION['cRasidReportEntry']['agency_id'])){ if( "ag".$super['agency_id'] == $_SESSION['cRasidReportEntry']['agency_id'] ) { ?> selected="selected" <?php }} ?>><?php echo $super['agency_name'] ?></option>
                             
                             <?php } ?>
                              
                             <?php 
							 
							 $companies = listOurCompanies();
                              foreach($companies as $com)
							
                              {
                             ?>
                             
                             <option value="oc<?php echo $com['our_company_id'] ?>" <?php if(isset($_SESSION['cRasidReportEntry']['agency_id'])){ if( "oc".$com['our_company_id'] == $_SESSION['cRasidReportEntry']['agency_id'] ) { ?> selected="selected" <?php }} ?> ><?php echo $com['our_company_name'] ?></option>
                             
                             <?php } ?>
                              
                         
                            </select> 
                    </td>
                    
                    
                  
</tr>
<tr>
<td>Payment Mode</td>
<td>
	<input  type="radio" name="payment_mode" id="open"value="1" <?php if(isset($_SESSION['cRasidReportEntry']['payment_mode'])){ if(  $_SESSION['cRasidReportEntry']['payment_mode']==1 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="open">Cash</label>
		<input  type="radio" name="payment_mode" id="closed" value="2" <?php if(isset($_SESSION['cRasidReportEntry']['payment_mode'])){ if( $_SESSION['cRasidReportEntry']['payment_mode']==2 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block;top:3px;position:relative;" for="closed">Cheque</label>
	<input  type="radio" name="payment_mode" id="both"  <?php if(!isset($_SESSION['cRasidReportEntry']['payment_mode']) || ($_SESSION['cRasidReportEntry']['payment_mode']!=1 && $_SESSION['cRasidReportEntry']['payment_mode']!=2)){  ?> checked="checked" <?php } ?> /> <label style="display:inline-block;top:3px;position:relative;" for="both">Both</label>
</td>
</tr>

<tr>

<td></td>
				<td>
				 <input type="submit" value="Generate" class="btn btn-warning"/>	
                </td>
</tr>


</table>

</form>

  
<hr class="firstTableFinishing" />

	<div class="no_print">
     <?php if(isset($_SESSION['cRasidReportEntry']['emi_array']))
{
	
	$emi_array=$_SESSION['cRasidReportEntry']['emi_array'];
		
		
	 ?>
     <div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>
    <div class="showColumns">
    	Print Columns : <input class="showCB" type="checkbox" id="1" checked="checked" /><label class="showLabel" for="1">No</label> 
        <input class="showCB" type="checkbox" id="2" checked="checked"  /><label class="showLabel" for="2">Rasid No</label> 
        <input class="showCB" type="checkbox" id="3" checked="checked"  /><label class="showLabel" for="3">File No</label> 
         <input class="showCB" type="checkbox" id="4" checked="checked"  /><label class="showLabel" for="4">Agreement No</label> 
        <input class="showCB" type="checkbox" id="5" checked="checked"  /><label class="showLabel" for="5">Reg No</label> 
        <input class="showCB" type="checkbox" id="6" checked="checked"   /><label class="showLabel" for="6">Payment Mode</label> 
        <input class="showCB" type="checkbox" id="7" checked="checked"  /><label class="showLabel" for="7">Payment Date</label> 
         <input class="showCB" type="checkbox" id="8" checked="checked"  /><label class="showLabel" for="8">Payment Amount</label> 
        <input class="showCB" type="checkbox" id="9" checked="checked"  /><label class="showLabel" for="9">Name</label> 
         <input class="showCB" type="checkbox" id="10" checked="checked"  /><label class="showLabel" for="10">Paid By</label> 
          <input class="showCB" type="checkbox" id="11" checked="checked"  /><label class="showLabel" for="11">EMI No</label> 
           <input class="showCB" type="checkbox" id="11" checked="checked"  /><label class="showLabel" for="11">Entry Date</label> 
    </div>    
    <table id="adminContentReport" class="adminContentTable no_print">
    <thead>
    	<tr>
        <th class="heading no_print">Print</th>
        	<th class="heading">No</th>
            <th class="heading file">Rasid No</th>
            <th class="heading file">File No</th>
             <th class="heading file">Agreement No</th>
            <th class="heading">Reg No</th>
             <th class="heading">Payment Mode</th>
            <th class="heading date">Payment Date</th>
            <th class="heading">Payment Amount</th>
            <th class="heading">Name</th>
             <th class="heading">Paid by</th>
               <th class="heading">EMI No</th>
               <th class="heading date">Entry Date</th>
            <th class="heading no_print btnCol"></th>
           
        </tr>
    </thead>
    <tbody>
       
        <?php
		$total=0;
		$total_agencies=getTotalNoOfAgencies();
		foreach($emi_array as $emi)
		{
			$customer=getCustomerDetailsByFileId($emi['file_id']);
			$total=$total+$emi['payment_amt'];
		 ?>
         <tr class="resultRow">
         <td class="no_print"><input type="checkbox" class="selectTR" name="selectTR"  /></td>
        	<td><?php echo ++$i; ?></td>
            <td><span style="display:none"><?php 
			if(is_numeric($emi['agency_id'])) {
				$prefix=$emi['agency_id'];}
			else if(is_numeric($emi['oc_id']))
			{$prefix=$total_agencies+$emi['oc_id'];}
			echo $prefix.".".preg_replace('/[^0-9]+/', '', $emi['rasid_no']); ?></span> <?php  echo  $emi['rasid_no']; ?>
            </td>
            <td><span style="display:none"><?php 
			if(is_numeric($emi['agency_id'])) {
				$prefix=$emi['agency_id'];}
			else if(is_numeric($emi['oc_id']))
			{$prefix=$total_agencies+$emi['oc_id'];}
			echo $prefix.".".preg_replace('/[^0-9]+/', '', $emi['file_number']); ?></span> <?php  echo  $emi['file_number']; ?>
            </td>
            <td><?php echo $emi['file_agreement_no']; ?></td>
              <td><?php  $reg_no= getRegNoFromFileID($emi['file_id']); if($reg_no) echo $reg_no; else echo "NA"; ?>
            </td>
             <td><?php $chequeDetails=getChequeDetailsFromEmiPaymentId($emi['emi_payment_id']); $mode=$emi['payment_mode']; if($mode==1) echo "CASH"; else { echo "CHEQUE (".$chequeDetails['cheque_no']." )"; } ?>
            </td>
            <td><?php echo date('d/m/Y',strtotime($emi['payment_date'])); ?>
            </td>
            <td><?php   echo $emi['payment_amt'] ?>
            </td>
           
             <td><?php   echo $customer['customer_name']; ?></td>
                <td><?php if($emi['paid_by']!=-1) echo $emi['paid_by']; ?></td> 
              <td><?php   
			  if($emi['loan_emi_array']==0)
			  echo "OD";
			  else if($emi['loan_emi_array']==-1)
			  echo "CLS";
			  else
			  {
			  $loan_emi_array=explode(",",$emi['loan_emi_array']);
			  sort($loan_emi_array);
			 $first_emi_id=getFirstEmiIdForLoan($emi['loan_id']);
						foreach($loan_emi_array as $loan_emi_id)
						{
							echo getLoanNoFromEMIIdForLoan($loan_emi_id)." , ";
							}
			  }
			 	?></td>
                 
             <td><?php echo date('d/m/Y H:i:s',strtotime($emi['date_added'])); ?></td>   
             <td class="no_print"> <a href="<?php echo WEB_ROOT.'admin/customer/index.php?view=details&id='.$emi['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
           
            
          
  
        </tr>
         <?php } }?>
         </tbody>
    </table>
    </div>
     <table class="reportFiltersTable">
    <tr>
    	<td><?php if(isset($_SESSION['cRasidReportEntry']['report_time'])){ echo  $_SESSION['cRasidReportEntry']['report_time'];} ?></td>
    	<td> From : <?php if(isset($_SESSION['cRasidReportEntry']['from']) && $_SESSION['cRasidReportEntry']['from']!="") echo $_SESSION['cRasidReportEntry']['from']; else echo "NA"; ?></td>
        <td> To : <?php if(isset($_SESSION['cRasidReportEntry']['to']) && $_SESSION['cRasidReportEntry']['to']!="") echo $_SESSION['cRasidReportEntry']['to']; else echo "NA"; ?></td>
        <td> City : <?php if(isset($_SESSION['cRasidReportEntry']['city_id']) && $_SESSION['cRasidReportEntry']['city_id']!="") {$city=getCityByID($_SESSION['cRasidReportEntry']['city_id']); echo $city['city_name']; } else echo "NA"; ?></td>
        <td> Agency : <?php if(isset($_SESSION['cRasidReportEntry']['agency_id']) && $_SESSION['cRasidReportEntry']['agency_id']!="") { echo getAgecnyIdOrOCidNameFromAgnecySelectInput($_SESSION['cRasidReportEntry']['agency_id']);  } else echo "NA"; ?></td>
        <td> Payment Mode : <?php if(isset($_SESSION['cRasidReportEntry']['payment_mode']) && $_SESSION['cRasidReportEntry']['payment_mode']!="") { if($_SESSION['cRasidReportEntry']['payment_mode']==1) echo "CASH";else if($_SESSION['cRasidReportEntry']['payment_mode']==2) echo "CHEQUE";  } else echo "BOTH"; ?></td>
    </tr>
    </table> 
   <table id="to_print" style="width:100%;" class="to_print adminContentTable"></table> 
   <span class="Total">Total Amount : <?php if(isset($total)) echo number_format($total); ?></span>
<?php  ?>      
</div>
<div class="clearfix"></div>
<script>
 $( "#city_area1" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/city_area.php',
                { term: request.term, city_id:$('#customer_city_id').val() }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#city_area1" ).val(ui.item.label);
			return false;
		}
    });

</script>