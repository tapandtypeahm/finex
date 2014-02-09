<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Search EMI Details</h4>
<h4 class="subheadingAlignment no_print">Minimum one Input Required</h4>
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
<form id="addLocForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=search'; ?>" method="post" enctype="multipart/form-data" onsubmit="return submitOurCompany()">

<table class="insertTableStyling no_print">

<tr>
<td>
File Agreement No : 
</td>
<td>
<input type="text" name="agreementNo" id="agreementNo" placeholder="Only letters and numbers"/>
</td>
</tr>

<tr>
<td>File Number : </td>
				<td>
				 <input type="text"  name="fileNumber" id="fileNumber" placeholder="Only Letters and numbers"/>	
                 </td>
</tr>

<tr>
<td>Vehicle Number : </td>
				<td>
				 <input type="text"  name="reg_no" id="reg_no" placeholder="Only Letters and numbers"/>	
                 </td>
</tr>

<tr>
<td>Customer's Mobile Number : </td>
				<td>
				 <input type="text"  name="mobile_no" id="mobile_no" placeholder="Only numbers"/>	
                </td>
</tr>

<tr>
<td>Customer's Name : </td>
				<td>
				 <input type="text"  name="name" id="name" placeholder="Only Letters"/>	
                </td>
</tr>

<tr>
<td></td>
				<td>
				 <input type="submit" value="search" class="btn btn-warning"/>	
                </td>
</tr>


</table>

</form>
<?php if(isset($_SESSION['searchEMI']['file_id_array']) && count($_SESSION['searchEMI']['file_id_array'])>0)
{
	$file_id_array=$_SESSION['searchEMI']['file_id_array'];
	 ?>
<hr class="firstTableFinishing" />
<?php if(isset($_SESSION['searchEMI']['parameter']) && isset($_SESSION['searchEMI']['value'])) { ?>
<h4 class="headingAlignment">Search Results For <?php  echo $_SESSION['searchEMI']['parameter']; ?><?php if(count($file_id_array)==1) echo " LIKE "; else echo " : "; ?>"<?php  echo $_SESSION['searchEMI']['value']; ?>" !</h4>
<?php } ?>
<h4 class="subheadingAlignment no_print">Please Select One From below results!</h4>
	<div class="no_print">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">Agreement No</th>
             <th class="heading">File No</th>
              <th class="heading">Customer Name</th>
               <th class="heading">Address</th>
               <th class="heading">Contact</th>
               <th class="heading">Reg No</th>
             <th class="heading no_print btnCol" ></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
		$no=0;
		foreach($file_id_array as $file_id)
		{
			if(is_array($file_id))
			{	
			$allDetails=getFileSearchResultDetailsFromFileId($file_id['file_id']);
		    
			$file=$allDetails['file_array'];
			$customer=$allDetails['customer_array'];
			$reg_no=$allDetails['reg_no'];
			
		 ?>
          <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
            <td><?php echo $file['file_agreement_no']; ?>
            </td>
            <td><?php echo $file['file_number']; ?>
            </td>
            <td><?php echo $customer['customer_name']; ?>
            </td>
             <td><?php echo $customer['customer_address']; ?>
            </td>
             <td><?php $contactNos=$customer['contact_no']; $len=count($contactNos); for($i=0;$i<$len;$i++){
				 $contact=$contactNos[$i];
				 if($i!=($len-1)) echo $contact[0]." | "; else echo $contact[0];} ?>
            </td>
            <td><?php echo $reg_no; ?>
            </td>
             <td class="no_print"> <a href="<?php echo WEB_ROOT.'admin/customer/EMI/index.php?view=details&id='.$file_id['file_id'] ?>"><button title="Select" class="btn btn-warning">select</button></a>
            </td>
        </tr>
         <?php }}?>
         </tbody>
    </table>
    </div>
       <table id="to_print" class="to_print adminContentTable"></table> 
<?php } ?>       
</div>
<div class="clearfix"></div>
<script>
 
 $( "#agreementNo" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/agreement_no.php',
                { term: request.term }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#agreementNo" ).val(ui.item.label);
			return false;
		}
    });

 $( "#fileNumber" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/file_no.php',
                { term: request.term }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#fileNumber" ).val(ui.item.label);
			return false;
		}
    });	

$( "#reg_no" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/reg_no.php',
                { term: request.term }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#reg_no" ).val(ui.item.label);
			return false;
		}
    });	

$( "#mobile_no" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/mobile_no.php',
                { term: request.term }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#mobile_no" ).val(ui.item.label);
			return false;
		}
    });	
	
$( "#name" ).autocomplete({
      minLength: 1,
    source:  function(request, response) {
                $.getJSON ('<?php echo WEB_ROOT; ?>json/customer_name.php',
                { term: request.term }, 
                response );
            },
	 select: function( event, ui ) {
			$( "#name" ).val(ui.item.label);
			return false;
		}
    });				
	
</script>