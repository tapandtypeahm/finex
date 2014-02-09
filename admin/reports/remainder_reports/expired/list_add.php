<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Expired Remainder Reports</h4>
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

<input  type="hidden"  name="end_date"  value="<?php echo date('d/m/Y'); ?>"/>	
<tr>
	<td>Remainder Status:</td>
    <td>
    	<input  type="radio" name="remainder_status" id="done" value="1" <?php if(isset($_SESSION['eRemainderReport']['remainder_status'])){ if(  $_SESSION['eRemainderReport']['remainder_status']==1 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="done">Done</label>
		<input  type="radio" name="remainder_status" id="undone" value="0" <?php if(isset($_SESSION['eRemainderReport']['remainder_status'])){ if( $_SESSION['eRemainderReport']['remainder_status']==0 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block;top:3px;position:relative;" for="undone">Undone</label>
    	<input  type="radio" name="remainder_status" id="done_undone"  <?php if(!isset($_SESSION['eRemainderReport']['remainder_status']) || ($_SESSION['eRemainderReport']['remainder_status']!=1 && $_SESSION['eRemainderReport']['file_status']!=0)){  ?> checked="checked" <?php } ?> /> <label style="display:inline-block;top:3px;position:relative;" for="done_undone">Both</label>
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
                             
                             <option value="<?php echo $super['city_id'] ?>" <?php if(isset($_SESSION['eRemainderReport']['city_id'])){ if( $super['city_id'] == $_SESSION['eRemainderReport']['city_id'] ) { ?> selected="selected" <?php }} ?>><?php echo $super['city_name'] ?></option					>
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
						  if(isset($_SESSION['eRemainderReport']['city_id'])){
                            $areas = listAreasFromCityIdWithGroups($_SESSION['eRemainderReport']['city_id']);
                            foreach($areas as $area)
                              {
                             ?>
                             
                             <option value="<?php echo $area['area_id'] ?>" <?php if(isset($_SESSION['eRemainderReport']['area_id_array'])){ if(in_array($area['area_id'],$_SESSION['eRemainderReport']['area_id_array'])) { ?> selected="selected" <?php }} ?>><?php echo $area['area_name'] ?></option					>
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
                             
                             <option value="ag<?php echo $super['agency_id'] ?>" <?php if(isset($_SESSION['eRemainderReport']['agency_id'])){ if( "ag".$super['agency_id'] == $_SESSION['eRemainderReport']['agency_id'] ) { ?> selected="selected" <?php }} ?>><?php echo $super['agency_name'] ?></option>
                             
                             <?php } ?>
                              
                             <?php 
							 
							 $companies = listOurCompanies();
                              foreach($companies as $com)
							
                              {
                             ?>
                             
                             <option value="oc<?php echo $com['our_company_id'] ?>" <?php if(isset($_SESSION['eRemainderReport']['agency_id'])){ if( "oc".$com['our_company_id'] == $_SESSION['eRemainderReport']['agency_id'] ) { ?> selected="selected" <?php }} ?> ><?php echo $com['our_company_name'] ?></option>
                             
                             <?php } ?>
                              
                         
                            </select> 
                    </td>
                    
                    
                  
</tr>

<tr>
	<td>File Status:</td>
    <td>
    	<input  type="radio" name="file_status" id="open" value="1" <?php if(isset($_SESSION['eRemainderReport']['file_status'])){ if(  $_SESSION['eRemainderReport']['file_status']==1 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="open">Open</label>
		<input  type="radio" name="file_status" id="closed" value="2" <?php if(isset($_SESSION['eRemainderReport']['file_status'])){ if( $_SESSION['eRemainderReport']['file_status']==2 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block;top:3px;position:relative;" for="closed">Closed</label>
    	<input  type="radio" name="file_status" id="both"  <?php if(!isset($_SESSION['eRemainderReport']['file_status']) || ($_SESSION['eRemainderReport']['file_status']!=1 && $_SESSION['eRemainderReport']['file_status']!=2)){  ?> checked="checked" <?php } ?> /> <label style="display:inline-block;top:3px;position:relative;" for="both">Both</label>
    </td>
</tr>

<td></td>
				<td>
				 <input type="submit" value="Generate" class="btn btn-warning"/>	
                </td>
</tr>


</table>

</form>

  
<hr class="firstTableFinishing" />
 

	<div class="no_print">
 <?php if(isset($_SESSION['eRemainderReport']['remainder_array']))
{
	
	$emi_array=$_SESSION['eRemainderReport']['remainder_array'];
		
		
	 ?>    
<div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>     
    <div class="showColumns">
    	Print Columns : <input class="showCB" type="checkbox" id="1" checked="checked" /><label class="showLabel" for="1">No</label> 
         <input class="showCB" type="checkbox" id="2" checked="checked"  /><label class="showLabel" for="2">File No</label>
        <input class="showCB" type="checkbox" id="3" checked="checked"  /><label class="showLabel" for="3">Type</label> 
        
        <input class="showCB" type="checkbox" id="4" checked="checked"  /><label class="showLabel" for="4">Reg No</label> 
        <input class="showCB" type="checkbox" id="5" checked="checked"   /><label class="showLabel" for="5">Remainder Date</label> 
        <input class="showCB" type="checkbox" id="6" checked="checked"  /><label class="showLabel" for="6">Remarks</label> 
         <input class="showCB" type="checkbox" id="7" checked="checked"  /><label class="showLabel" for="7">Name</label> 
        <input class="showCB" type="checkbox" id="8" checked="checked"  /><label class="showLabel" for="8">Address</label> 
         <input class="showCB" type="checkbox" id="9" checked="checked"  /><label class="showLabel" for="9">Contact</label> 
          
    </div>
    <table id="adminContentReport" class="adminContentTable no_print">
    <thead>
    	<tr>
        <th class="heading no_print">Print</th>
        	<th class="heading">No</th>
              <th class="heading file">File No</th>
              <th class="heading">Type</th>
          
            <th class="heading">Reg No</th>
            <th class="heading date">Remainder Date</th>
            <th class="heading">Remarks</th>
            <th class="heading">Name</th>
            <th width="10%" class="heading">Address</th>
            <th class="heading">Contact No</th>
            <th class="heading no_print btnCol"></th>
           
        </tr>
    </thead>
    <tbody>
      
        <?php
		
		foreach($emi_array as $emi)
		{
		 ?>
         <tr class="resultRow">
         	<td class="no_print"><input type="checkbox" class="selectTR" name="selectTR" onchange="selectTR(this)" /></td>
        	<td><?php echo ++$i; ?></td>
             <td><span style="display:none"><?php $infoArray=getAgencyOrCompanyIdFromFileId($emi['file_id']); 
			if($infoArray[0]=='agency') {
				$prefix=$infoArray[1];}
			else if($infoArray[0]=='oc')
			{$prefix=getTotalNoOfAgencies()+$infoArray[1]; }
			
			echo $prefix.".".preg_replace('/[^0-9]+/', '', $emi['file_no']); ?></span> <?php  echo  $emi['file_no']; ?>
            </td>
             <td><?php echo ucwords($emi['type']); ?>
            </td>
           
              <td><?php if($emi['reg_no']!=null && $emi['reg_no']!="") echo $emi['reg_no']; else echo "NA"; ?>
            </td>
            <td><?php if($emi['date']!="1970-01-01" && $emi['date']!='0000-00-00') echo date('d/m/Y',strtotime($emi['date'])); else echo "NA"; ?>
            </td>
            <td><?php echo $emi['remarks']; ?>
            </td>
            <td><?php   echo $emi['customer']['customer_name']; ?></td>
             <td><?php   echo $emi['customer']['customer_address']; ?></td>
             <td><?php   $contactArray = $emi['customer']['contact_no']; 
			 			
			 			for($j=0;$j<count($contactArray);$j++)
						{
							$contact=$contactArray[$j];
							if($j==(count($contactArray)-1))
							{
								echo $contact[0];
								}
							else
							echo $contact[0]." <br> ";	
							}	
							
			 	?></td>
             <td class="no_print"><?php if($emi['type']=='general') { ?> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=addRemainder&id=<?php echo $emi['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a><?php } ?>
                					<?php if($emi['type']=='payment') { ?> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=EMIdetails&id=<?php echo $emi['file_id']; ?>&state=<?php  echo getEMIIDFromPaymentId($emi['id']); ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a><?php } ?>
            </td>
           
            
          
  
        </tr>
         <?php } }?>
         </tbody>
    </table>
    </div>
   <table id="to_print" style="width:100%;" class="to_print adminContentTable"></table> 
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