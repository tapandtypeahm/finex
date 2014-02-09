<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment">List of Customer Ledgers</h4>
    <div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>
   	<div class="no_print">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">File No</th>
            <th class="heading">Reg No</th>
            <th class="heading">Name</th>
            <th class="heading">Address</th>
            <th class="heading">Contact No</th>
            <th class="heading">Opening Balance</th> 
            <th class="heading no_print btnCol" ></th>
            <th class="heading no_print btnCol" ></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
		$current_company=getCurrentCompanyForUser($_SESSION['adminSession']['admin_id']);
		$oc_agency_id=$current_company[0];
		$company_type=$current_company[1];
		if($company_type==0)
		{
			$dealers=listFilesForOurCompany($oc_agency_id);
			}
		else if($company_type==1)
		{
			$dealers=listFilesForAgency($oc_agency_id);
			}	
		$no=0;
		foreach($dealers as $agencyDetails)
		{
			$reg_no=getRegNoFromFileID($agencyDetails['file_id']);
			$contact_nos=$agencyDetails['contact_no'];
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><span  class="editLocationName" id="<?php echo $agencyDetails['file_number'] ?>"><?php echo $agencyDetails['file_number']; ?></span>
            </td>
             <td><span  class="editLocationName"><?php if(validateForNull($reg_no)) echo $reg_no; else echo "NA";   ?></span>
            </td> 
            <td><span  class="editLocationName" ><?php echo $agencyDetails['customer_name']; ?></span>
            </td>
            
           <td><span  class="editLocationName" ><?php  echo $agencyDetails['customer_address'];    ?></span>
            </td>
            <td><span  class="editLocationName" ><?php if($contact_nos!=false && is_array($contact_nos)) { foreach($contact_nos as $contact_no) echo $contact_no[0]."<br>"; }   ?></span>
            </td>
             <td><span  class="editLocationName"><?php echo $agencyDetails['opening_balance']." "; if($agencyDetails['opening_cd']==0) echo "Dr"; else echo "Cr"; ?></span>
            </td>
             
            
            <td class="no_print"> <a href="<?php echo WEB_ROOT.'admin/customer/index.php?view=details&id='.$agencyDetails['file_id'] ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=edit&lid='.$agencyDetails['file_id'] ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
            </td>
           
  
        </tr>
         <?php }?>
         </tbody>
    </table>
    </div>
     <table id="to_print" class="to_print adminContentTable"></table> 
</div>
<div class="clearfix"></div>
<script>
 $( "#city_area1" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/city_area.php',
                { term: request.term, city_id:$('#city').val() }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#city_area1" ).val(ui.item.label);
			return false;
		}
    });
</script>	