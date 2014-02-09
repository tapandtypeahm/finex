<div class="jvp"><?php if(isset($_SESSION['cRasidReport']['agency_id']) && $_SESSION['cRasidReport']['agency_id']!="") { echo getAgecnyIdOrOCidNameFromAgnecySelectInput($_SESSION['cRasidReport']['agency_id']);  } ?></div>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">STFC Collection Reports</h4>
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=generateReport'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurCompany()">

<table class="insertTableStyling no_print">

<tr>
<td>From Date (Payment Date) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="start_date" id="start_date" placeholder="Click to select Date!" class="datepicker2" value="<?php if(isset($_SESSION['cRasidReport']['from'])) echo $_SESSION['cRasidReport']['from']; ?>" />	
                 </td>
</tr>


<tr>
<td>Up To Date (Payment Date) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="end_date" id="end_date" placeholder="Click to select Date!" class="datepicker2" value="<?php if(isset($_SESSION['cRasidReport']['to'])) echo $_SESSION['cRasidReport']['to']; ?>"/>	
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
                             
                             <option value="<?php echo $super['city_id'] ?>" <?php if(isset($_SESSION['cRasidReport']['city_id'])){ if( $super['city_id'] == $_SESSION['cRasidReport']['city_id'] ) { ?> selected="selected" <?php }} ?>><?php echo $super['city_name'] ?></option					>
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
						  if(isset($_SESSION['cRasidReport']['city_id'])){
                            $areas = listAreasFromCityIdWithGroups($_SESSION['cRasidReport']['city_id']);
                            foreach($areas as $area)
                              {
                             ?>
                             
                             <option value="<?php echo $area['area_id'] ?>" <?php if(isset($_SESSION['cRasidReport']['area_id_array'])){ if(in_array($area['area_id'],$_SESSION['cRasidReport']['area_id_array'])) { ?> selected="selected" <?php }} ?>><?php echo $area['area_name'] ?></option					>
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
                             
                             <option value="ag<?php echo $super['agency_id'] ?>" <?php if(isset($_SESSION['cRasidReport']['agency_id'])){ if( "ag".$super['agency_id'] == $_SESSION['cRasidReport']['agency_id'] ) { ?> selected="selected" <?php }} ?>><?php echo $super['agency_name'] ?></option>
                             
                             <?php } ?>
                              
                             <?php 
							 
							 $companies = listOurCompanies();
                              foreach($companies as $com)
							
                              {
                             ?>
                             
                             <option value="oc<?php echo $com['our_company_id'] ?>" <?php if(isset($_SESSION['cRasidReport']['agency_id'])){ if( "oc".$com['our_company_id'] == $_SESSION['cRasidReport']['agency_id'] ) { ?> selected="selected" <?php }} ?> ><?php echo $com['our_company_name'] ?></option>
                             
                             <?php } ?>
                              
                         
                            </select> 
                    </td>
                    
                    
                  
</tr>
<tr>
<td>Payment Mode</td>
<td>
	<input  type="radio" name="payment_mode" id="open"value="1" <?php if(isset($_SESSION['cRasidReport']['payment_mode'])){ if(  $_SESSION['cRasidReport']['payment_mode']==1 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="open">Cash</label>
		<input  type="radio" name="payment_mode" id="closed" value="2" <?php if(isset($_SESSION['cRasidReport']['payment_mode'])){ if( $_SESSION['cRasidReport']['payment_mode']==2 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block;top:3px;position:relative;" for="closed">Cheque</label>
	<input  type="radio" name="payment_mode" id="both"  <?php if(!isset($_SESSION['cRasidReport']['payment_mode']) || ($_SESSION['cRasidReport']['payment_mode']!=1 && $_SESSION['cRasidReport']['payment_mode']!=2)){  ?> checked="checked" <?php } ?> /> <label style="display:inline-block;top:3px;position:relative;" for="both">Both</label>
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
     <?php if(isset($_SESSION['cRasidReport']['emi_array']))
	{
	
	$emi_array=$_SESSION['cRasidReport']['emi_array'];
		
		
	 ?>
     <div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>
     <div class="showColumns">
    	Print Columns : <input class="showCB" type="checkbox" id="1" checked="checked" /><label class="showLabel" for="1">No</label> 
         <input class="showCB" type="checkbox" id="2" checked="checked"  /><label class="showLabel" for="2">Agreement No</label> 
        <input class="showCB" type="checkbox" id="3" checked="checked"  /><label class="showLabel" for="3">Reg No</label> 
        <input class="showCB" type="checkbox" id="4" checked="checked"  /><label class="showLabel" for="4">Rasid No</label> 
         <input class="showCB" type="checkbox" id="5" checked="checked"  /><label class="showLabel" for="5">Payment Amount</label> 
        <input class="showCB" type="checkbox" id="6" checked="checked"  /><label class="showLabel" for="6">Name</label> 
       
    </div> 
    <div id="deleteSelectedDiv"><button id="deleteSelected" class="btn viewBtn">delete selected rows</button></div>   
    <table id="adminContentReport" class="adminContentTable no_print">
    <thead>
    	<tr>
        <th class="heading no_print"><input type="checkbox" id="selectAllTR" name="selectAllTR"  /></th>
        	<th class="heading">No</th>
             <th class="heading file">Agreement No</th>
            <th class="heading">Reg No</th>
            <th class="heading date">Rasid No</th>
            <th class="heading">Payment Amount</th>
            <th class="heading">Name</th>
             <th class="heading">Settled</th>
            <th class="heading no_print btnCol"></th>
           
        </tr>
    </thead>
    <tbody>
       
        <?php
		$total=0;
		$total_agencies=getTotalNoOfAgencies();
		foreach($emi_array as $emi)
		{
			$settled="No";
			$customer=getCustomerDetailsByFileId($emi['file_id']);
			$total=$total+$emi['payment_amt'];
			$settle_file=getSettleFileDetails($emi['file_id']);
			if($settle_file!="error" && $settle_file['settle_id']>0)
			{
				$settled="Yes";
				}
			else
			$settled="No";	
		 ?>
         <tr class="resultRow <?php if($settled=="Yes") echo "dangerRow"; ?>">
         <td class="no_print"><input type="checkbox" class="selectTR" name="selectTR"  /></td>
        	<td><?php echo ++$i; ?></td>
            <td><?php echo $emi['file_agreement_no']; ?></td>
              <td><?php  $reg_no= getRegNoFromFileID($emi['file_id']); if($reg_no) echo $reg_no; else echo "NA"; ?>
            </td>
            
            <td><?php echo $emi['rasid_no']; ?>
            </td>
            <td class="payment_amount"><?php   echo $emi['payment_amt'] ?>
            </td>
            <td><?php   echo $customer['customer_name']; ?></td>
            <td><?php echo $settled; ?></td>
             <td class="no_print"> <a href="<?php echo WEB_ROOT.'admin/customer/index.php?view=details&id='.$emi['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
           
            
          
  
        </tr>
         <?php } }?>
         </tbody>
    </table>
    </div>
    
   <table id="to_print" style="width:100%;" class="to_print adminContentTable"></table> 
   <span class="Total">Total Amount : <span id="total_amount">0</span></span>
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