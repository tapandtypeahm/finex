<div class="jvp"><?php if(isset($_SESSION['cCustomReport']['agency_id']) && $_SESSION['cCustomReport']['agency_id']!="") { echo getAgecnyIdOrOCidNameFromAgnecySelectInput($_SESSION['cCustomReport']['agency_id']);  } ?></div>
<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Full Custom Reports</h4>
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
<td>From Date (EMI date) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="start_date" id="start_date" placeholder="Click to select Date!" class="datepicker2" value="<?php if(isset($_SESSION['cCustomReport']['from'])) echo $_SESSION['cCustomReport']['from']; ?>" />	
                 </td>
</tr>


<tr>
<td>Up To Date (EMI date) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="end_date" id="end_date" placeholder="Click to select Date!" class="datepicker2" value="<?php if(isset($_SESSION['cCustomReport']['to'])) echo $_SESSION['cCustomReport']['to']; ?>"/>	
                 </td>
</tr>

<tr>
<td>Bucket(>=) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="win_gt" id="win_gt" placeholder="Only Digits!" value="<?php if(isset($_SESSION['cCustomReport']['win_gt'])) echo $_SESSION['cCustomReport']['win_gt']; ?>" />	
                 </td>
</tr>

<tr>
<td>Bucket(<=) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="win_lt" id="win_lt" placeholder="Only Digits!" value="<?php if(isset($_SESSION['cCustomReport']['win_lt'])) echo $_SESSION['cCustomReport']['win_lt']; ?>" />	
                 </td>
</tr>

<tr>
<td>EMI(>=) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="emi_gt" id="emi_gt" placeholder="Only Digits!" value="<?php if(isset($_SESSION['cCustomReport']['emi_gt'])) echo $_SESSION['cCustomReport']['emi_gt']; ?>" />	
                 </td>
</tr>

<tr>
<td>EMI(<=) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="emi_lt" id="emi_lt" placeholder="Only Digits!" value="<?php if(isset($_SESSION['cCustomReport']['emi_lt'])) echo $_SESSION['cCustomReport']['emi_lt']; ?>" />	
                 </td>
</tr>

<tr>
<td>Balance(>=) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="balance_gt" id="balance_gt" placeholder="Only Digits!" value="<?php if(isset($_SESSION['cCustomReport']['balance_gt'])) echo $_SESSION['cCustomReport']['balance_gt']; ?>" />	
                 </td>
</tr>

<tr>
<td>Balance(<=) : </td>
				<td>
				 <input autocomplete="off" type="text"  name="balance_lt" id="balance_lt" placeholder="Only Digits!" value="<?php if(isset($_SESSION['cCustomReport']['balance_lt'])) echo $_SESSION['cCustomReport']['balance_lt']; ?>" />	
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
                             
                             <option value="<?php echo $super['city_id'] ?>" <?php if(isset($_SESSION['cCustomReport']['city_id'])){ if( $super['city_id'] == $_SESSION['cCustomReport']['city_id'] ) { ?> selected="selected" <?php }} ?>><?php echo $super['city_name'] ?></option					>
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
						  if(isset($_SESSION['cCustomReport']['city_id'])){
                            $areas = listAreasFromCityIdWithGroups($_SESSION['cCustomReport']['city_id']);
                            foreach($areas as $area)
                              {
                             ?>
                             
                             <option value="<?php echo $area['area_id'] ?>" <?php if(isset($_SESSION['cCustomReport']['area_id_array'])){ if(in_array($area['area_id'],$_SESSION['cCustomReport']['area_id_array'])) { ?> selected="selected" <?php }} ?>><?php echo $area['area_name'] ?></option					>
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
                             
                             <option value="ag<?php echo $super['agency_id'] ?>" <?php if(isset($_SESSION['cCustomReport']['agency_id'])){ if( "ag".$super['agency_id'] == $_SESSION['cCustomReport']['agency_id'] ) { ?> selected="selected" <?php }} ?>><?php echo $super['agency_name'] ?></option>
                             
                             <?php } ?>
                              
                             <?php 
							 
							 $companies = listOurCompanies();
                              foreach($companies as $com)
							
                              {
                             ?>
                             
                             <option value="oc<?php echo $com['our_company_id'] ?>" <?php if(isset($_SESSION['cCustomReport']['agency_id'])){ if( "oc".$com['our_company_id'] == $_SESSION['cCustomReport']['agency_id'] ) { ?> selected="selected" <?php }} ?> ><?php echo $com['our_company_name'] ?></option>
                             
                             <?php } ?>
                              
                         
                            </select> 
                    </td>
                    
                    
                  
</tr>

<tr>
<td>Broker Name : </td>
				<td>
					<select name="broker[]" class="broker selectpicker" multiple="multiple"  id="broker" >
                    	 <option value="-1" disabled="disabled">--Please Select--</option>
                          <?php
						  $brokers=listBrokers();
						  
                          
                            foreach($brokers as $broker)
                              {
                             ?>
                             <option value="<?php echo $broker['broker_id'] ?>" <?php if(isset($_SESSION['cCustomReport']['broker_id_array'])){ if(in_array($broker['broker_id'],$_SESSION['cCustomReport']['broker_id_array'])) { ?> selected="selected" <?php }} ?>><?php echo $broker['broker_name'] ?></option					>
                             <?php } 
						  
							 ?>
                    </select>
                            </td>
</tr>

<tr>
	<td>File Status :</td>
    <td>
    	<input  type="radio" name="file_status" id="open" value="1" <?php if(isset($_SESSION['cCustomReport']['file_status'])){ if(  $_SESSION['cCustomReport']['file_status']==1 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="open">Open</label>
		<input  type="radio" name="file_status" id="closed" value="2" <?php if(isset($_SESSION['cCustomReport']['file_status'])){ if( $_SESSION['cCustomReport']['file_status']==2 ) { ?> checked="checked" <?php }} ?> /> <label style="display:inline-block;top:3px;position:relative;" for="closed">Closed</label>
    	<input  type="radio" name="file_status" id="both"  <?php if(!isset($_SESSION['cCustomReport']['file_status']) || ($_SESSION['cCustomReport']['file_status']!=1 && $_SESSION['cCustomReport']['file_status']!=2)){  ?> checked="checked" <?php } ?> /> <label style="display:inline-block;top:3px;position:relative;" for="both">Both</label>
    </td>
</tr>

<tr>
	<td>File Fields :</td>
    <td>
    <!--	<input class="showFields" name="fields[]" value="1" type="checkbox" id="1" checked="checked" /><label class="showLabel" for="1">No</label> -->
        <input class="showFields" name="fields[]" value="3" type="checkbox" id="3" <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(3,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="3">File Status</label> 
    <!--    <input class="showFields" name="fields[]" value="2" type="checkbox" id="2"  <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(2,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?>  /><label class="showLabel" for="2">File No</label> -->
        <input class="showFields" name="fields[]" value="4" type="checkbox" id="4"  <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(4,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?>  /><label class="showLabel" for="4">Agreement No</label> 
        <input class="showFields" name="fields[]" value="5" type="checkbox" id="5"   <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(5,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?>  /><label class="showLabel" for="5">Broker</label><br />
     </td>
</tr>
<tr>
<td>Loan Fields :</td>
<td>        
        <input class="showFields" name="fields[]" value="6" type="checkbox" id="6"  <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(6,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?>  /><label class="showLabel" for="6">Last EMI Date</label> 
         <input class="showFields" name="fields[]" value="7" type="checkbox" id="7"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(7,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="7">Loan Amount</label>
         <input class="showFields" name="fields[]" value="56" type="checkbox" id="56"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(7,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="56">Agency Loan Amount</label>
         <input class="showFields" name="fields[]" value="59" type="checkbox" id="59"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(7,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="59">Our Participation</label>
         <input class="showFields" name="fields[]" value="8" type="checkbox" id="8"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(8,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="8">Total Collection</label>
         <input class="showFields" name="fields[]" value="9" type="checkbox" id="9"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(9,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="9">Total Payments Receieved</label>
         <input class="showFields" name="fields[]" value="10" type="checkbox" id="10"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(10,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="10">Total Payments Left</label>
         <input class="showFields" name="fields[]" value="54" type="checkbox" id="54"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(9,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="54">Total EMIs Receieved</label>
         <input class="showFields" name="fields[]" value="55" type="checkbox" id="55"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(9,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="55">Total EMIs Left</label>
         <input class="showFields" name="fields[]" value="11" type="checkbox" id="11"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(11,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="11">Interest Type</label>
         <input class="showFields" name="fields[]" value="12" type="checkbox" id="12"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(12,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="12">Flat ROI</label>
         <input class="showFields" name="fields[]" value="13" type="checkbox" id="13"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(13,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="13">Reducing ROI</label> 
         <input class="showFields" name="fields[]" value="14" type="checkbox" id="14"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(14,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="14">IRR</label> 
         <input class="showFields" name="fields[]" value="15" type="checkbox" id="15"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(15,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="15">Loan Approval Date</label> 
         <input class="showFields" name="fields[]" value="16" type="checkbox" id="16"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(16,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="16">Loan starting Date</label> 
         <input class="showFields" name="fields[]" value="17" type="checkbox" id="17"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(17,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="17">Loan Ending Date</label> 
         <input class="showFields" name="fields[]" value="18" type="checkbox" id="18"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(18,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="18">Loan Mode</label>
         <input class="showFields" name="fields[]" value="19" type="checkbox" id="19"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(19,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="19">Duration</label>
        <input class="showFields" name="fields[]" value="20" type="checkbox" id="20"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(20,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="20">EMI</label> 
               <input class="showFields" name="fields[]" value="57" type="checkbox" id="57"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(19,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="57">Agency Duration</label>
        <input class="showFields" name="fields[]" value="58" type="checkbox" id="58"     <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(20,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="58">Agency EMI</label> 
        <input class="showFields" name="fields[]" value="21" type="checkbox" id="21"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(21,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="21">Bucket</label> 
         <input class="showFields" name="fields[]" value="22" type="checkbox" id="22"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(22,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="22">Balance</label> 
</td>
</tr>
<tr>
<td>Customer Fields :</td>  
<td>       
        <input class="showFields" name="fields[]" value="23" type="checkbox" id="23"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(23,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="23">Name</label> 
         <input class="showFields" name="fields[]" value="24" type="checkbox" id="24"   <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(24,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="24">Address</label>
         <input class="showFields" name="fields[]" value="25" type="checkbox" id="25"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(25,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="25">Pincode</label>
         <input class="showFields" name="fields[]" value="26" type="checkbox" id="26"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(26,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="26">Area</label>
         <input class="showFields" name="fields[]" value="27" type="checkbox" id="27"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(27,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="27">City</label> 
          <input class="showFields" name="fields[]" value="28" type="checkbox" id="28"   <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(28,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="28">Contact No</label> <br />
</td></tr>
<tr>
<td>Guarantor Fields</td> 
<td>          
           <input class="showFields" name="fields[]" value="29" type="checkbox" id="29"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(29,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="29">Guarantor Name</label> 
         <input class="showFields" name="fields[]" value="30" type="checkbox" id="30"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(30,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="30">Guarantor Address</label>
        <input class="showFields" name="fields[]" value="31" type="checkbox" id="31"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(31,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="31">Guarantor Pincode</label>
         <input class="showFields" name="fields[]" value="32" type="checkbox" id="32"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(32,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="32">Guarantor Area</label> 
         <input class="showFields" name="fields[]" value="33" type="checkbox" id="33"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(33,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="33">Guarantor City</label>
          <input class="showFields" name="fields[]" value="34" type="checkbox" id="34"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(34,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="34">Guarantor Contact No</label> <br />
 </td>
 </tr>
 <tr>
 <td>Vehicle Fields :</td>    
 <td>     
           <input class="showFields" name="fields[]" value="35" type="checkbox" id="35" <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(35,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="35">Reg No</label> 
        <input class="showFields" name="fields[]" value="36" type="checkbox" id="36"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(36,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="36">Engine No</label> 
        <input class="showFields" name="fields[]" value="37" type="checkbox" id="37"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(37,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="37">Chasis No</label> 
         <input class="showFields" name="fields[]" value="38" type="checkbox" id="38"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(38,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="38">Vehicle Company</label> 
          <input class="showFields" name="fields[]" value="39" type="checkbox" id="39"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(39,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="39">Vehicle Dealer</label> 
           <input class="showFields" name="fields[]" value="40" type="checkbox" id="40"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(40,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="40">Vehicle Model</label> 
            <input class="showFields" name="fields[]" value="41" type="checkbox" id="41"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(41,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="41">Vehicle Type</label>
             <input class="showFields" name="fields[]" value="42" type="checkbox" id="42"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(42,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="42">Vehicle Condition</label>
              <input class="showFields" name="fields[]" value="43" type="checkbox" id="43"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(43,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="43">Vehicle Year</label>
               <input class="showFields" name="fields[]" value="44" type="checkbox" id="44"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(44,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="44">Reg Date</label> 
                <input class="showFields" name="fields[]" value="45" type="checkbox" id="45"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(45,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="45">Permit Exp Date</label> 
                 <input class="showFields" name="fields[]" value="46" type="checkbox" id="46"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(46,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="46">Fitness Exp Date</label> <br />
 </td>
 </tr>
 <tr>
 <td>Insurance Fields :</td>
 <td>               
                 <input class="showFields" name="fields[]" value="47" type="checkbox" id="47"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(47,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="47">Insurance Issue Date</label> 
                 <input class="showFields" name="fields[]" value="48" type="checkbox" id="48"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(48,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="48">Insurance Exp Date</label>
                 <input class="showFields" name="fields[]" value="49" type="checkbox" id="49"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(49,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="49">IDV</label> 
                 <input class="showFields" name="fields[]" value="50" type="checkbox" id="50"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(50,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="50">Premium</label>
                 <input class="showFields" name="fields[]" value="51" type="checkbox" id="51"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(51,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="51">Insurance Company</label>      
         
    </td>
</tr>
 <tr>
 <td>Creation Fields :</td>
 <td>               
                 <input class="showFields" name="fields[]" value="52" type="checkbox" id="52"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(52,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="52">Created By</label> 
                 <input class="showFields" name="fields[]" value="53" type="checkbox" id="53"    <?php if(isset($_SESSION['cCustomReport']['fields'])){  if(in_array(53,$_SESSION['cCustomReport']['fields'])) { ?>  checked="checked" <?php }} ?> /><label class="showLabel" for="53">Entry Date</label>
                
         
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
 <?php if(isset($_SESSION['cCustomReport']['emi_array']))
{
	
	$emi_array=$_SESSION['cCustomReport']['emi_array'];
	$file_fields_array=array(2,3,4,5,52,53);
	$loan_fields_array=range(6,22);	
	$customer_fields_array=range(23,28);		
	$guarantor_fields_array=range(29,34);
	$vehicle_fields_array=range(35,46);	
	$insurance_fields_array=range(46,51);
		
	 ?>    
<div class="printBtnDiv no_print"><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>     
   <?php $fields=$_SESSION['cCustomReport']['fields'];  ?>
    <table id="adminContentReport" class="adminContentTable no_print">
    <thead>
    	<tr>
        <th class="heading no_print">Print</th>
        	<th class="heading">No</th>
            <th class="heading file">File No</th>
          	<?php foreach($fields as $field)
			{
			 ?>
             <th class="heading">
             <?php
			 
			 switch($field)
			 {
				 case 3: echo "File Status";
				 		break;
				case 4: echo "Agreement No";
				 		break;	
				case 5: echo "Broker";
				 		break;	
				case 6: echo "Last EMI Date";
				 		break;	
				case 7: echo "Loan Amount";
				 		break;
				case 8: echo "Total Collection";
				 		break;	
				case 9: echo "Total Payments Recevied";
				 		break;	
				case 10: echo "Total Payments Left";
				 		break;	
				case 11: echo "Interest Type";
				 		break;
				case 12: echo "Flat ROI";
				 		break;	
				case 13: echo "Reducing ROI";
				 		break;	
				case 14: echo "IRR";
				 		break;	
				case 15: echo "Loan Approval Date";
				 		break;	
				case 16: echo "Laon Starting Date";
				 		break;	
				case 17: echo "Loan Ending Date";
				 		break;	
				case 18: echo "Loan Mode";
				 		break;	
				case 19: echo "Duration";
				 		break;	
				case 20: echo "EMI";
				 		break;	
						
				case 21: echo "Bucket";
				 		break;	
				case 22: echo "Balance";
				 		break;	
				case 23: echo "Name";
				 		break;	
						
				case 24: echo "Address";
				 		break;	
				case 25: echo "Pincode";
				 		break;	
				case 26: echo "Area";
				 		break;	
				case 27: echo "City";
				 		break;	
				case 28: echo "Contact No";
				 		break;	
				case 29: echo "Guarantor Name";
				 		break;	
				case 30: echo "Guarantor Address";
				 		break;
				case 31: echo "Guarantor Pincode";
				 		break;	
				case 32: echo "Guarantor Area";
				 		break;	
				case 33: echo "Guarantor City";
				 		break;	
				case 34: echo "Guarantor Contact No";
				 		break;	
				case 35: echo "Reg No";
				 		break;	
				case 36: echo "Engine No";
				 		break;	
				case 37: echo "Chasis No";
				 		break;	
				case 38: echo "Vehicle Company";
				 		break;	
				case 39: echo "Vehicle Dealer";
				 		break;	
						
				case 40: echo "Vehicle Model";
				 		break;	
				case 41: echo "Vehicle Type";
				 		break;	
				case 42: echo "Vehicle Condition";
				 		break;	
				case 43: echo "Vehicle Year";
				 		break;	
				case 44: echo "Reg Date";
				 		break;	
				case 45: echo "Permit Exp Date";
				 		break;	
				case 46: echo "Fitness Exp Date";
				 		break;	
				case 47: echo "Insurance Issue Date";
				 		break;	
				case 48: echo "Insurance Exp Date";
				 		break;	
				case 49: echo "IDV";
				 		break;	
				case 50: echo "Premium";
				 		break;	
				case 51: echo "Insurance Company";
				 		break;
				case 52: echo "Created By";
				 		break;	
				case 53: echo "Entry Date";
				 		break;
				case 54: echo "EMIs Received";
				 		break;	
				case 55: echo "EMIs Left";
				 		break;			
						
				case 56: echo "Agency Loan";
						break;
				case 57: echo "Agency Loan Duration";
						break;
				case 58: echo "Agecny Loan EMI";
						break;	
				case 59: echo "Our Participation";
						break;								
				default: break;																			
				 }
			  ?>
             </th>
             <?php } ?>
            <th class="heading no_print btnCol"></th>
           
        </tr>
    </thead>
    <tbody>
      
        <?php
		$total_agencies=getTotalNoOfAgencies();
		foreach($emi_array as $emi)
		{
		$file=getFullFileDetailsByFileId($emi['file_id']);	
		$guarantor=getGuarantorDetailsByFileId($emi['file_id']);
		$vehicle=getVehicleDetailsByFileId($emi['file_id']);
		$loan=getLoanDetailsByFileId($emi['file_id']);
		$insurance=getLatestInsuranceDetailsForFileID($emi['file_id']);	
		 ?>
         <tr class="resultRow">
         	<td class="no_print"><input type="checkbox" class="selectTR" name="selectTR"  /></td>
        	<td><?php echo ++$i; ?></td>
             <td><span style="display:none"><?php $infoArray=getAgencyOrCompanyIdFromFileId($emi['file_id']); 
			if($infoArray[0]=='agency') {
				$prefix=$infoArray[1];}
			else if($infoArray[0]=='oc')
			{$prefix=$total_agencies+$infoArray[1]; }
			
			echo $prefix.".".preg_replace('/[^0-9]+/', '', $file['file_number']); ?></span> <?php  echo  $file['file_number']; ?>
            </td>
            <?php foreach($fields as $field)
			{
			 ?>
            <td>
             <?php
			 
			 switch($field)
			 {
				 case 3: if($file['file_status']==1) echo "OPEN"; else if($file['file_status']==2) echo "CLOSED"; else if($file['file_status']==3) echo "DELETED";else if($file['file_status']==4) echo "PRECLOSED";
				 		break;
				case 4: if(isset($file['file_agreement_no'])) echo $file['file_agreement_no'];
				 		break;	
				case 5: if(isset($file['broker_id'])) echo getBrokerNameFromBrokerId($file['broker_id']);
				 		break;	
				case 6: echo "<span style='display:none;'>".$emi['emi_date']."</span>".date('d/m/Y',strtotime($emi['emi_date'])); 
				 		break;	
				case 7: if(isset($loan['loan_amount'])) echo $loan['loan_amount'];
				 		break;
				case 8: if(isset($loan['emi']) && isset($loan['loan_duration'])) echo number_format($loan['emi']*$loan['loan_duration'],0);
				 		break;	
				case 9:  $totalPayment=getTotalPaymentForLoan($loan['loan_id']);
				
				echo $totalPayment;
				 		break;	
				case 10: echo -$balance_left=getBalanceForLoan($loan['loan_id']); 
				 		break;	
				case 11:  if($loan['loan_type']==1) echo "FLAT"; else echo "REDUCING";
				 		break;
				case 12: echo $loan['roi'];
				 		break;	
				case 13: echo $loan['reducing_roi'];
				 		break;	
				case 14: echo $loan['IRR'];
				 		break;	
				case 15: echo "<span style='display:none;'>".$loan['loan_approval_date']."</span>".date('d/m/Y',strtotime($loan['loan_approval_date']));
				 		break;	
				case 16: echo "<span style='display:none;'>".$loan['loan_starting_date']."</span>".date('d/m/Y',strtotime($loan['loan_starting_date']));
				 		break;	
				case 17: echo "<span style='display:none;'>".$loan['loan_ending_date']."</span>".date('d/m/Y',strtotime($loan['loan_ending_date']));
				 		break;	
				case 18:  if($loan['loan_amount_type']==1) echo "CASH"; else echo "CHEQUE";
				 		break;	
				case 19: echo $loan['loan_duration'];
				 		break;	
				case 20: echo $loan['emi'];
				 		break;	
						
				case 21:    echo $emi['window'];
				 		break;	
				case 22: echo "-".$emi['balance'];
				 		break;	
				case 23: echo $emi['customer']['customer_name'];
				 		break;	
						
				case 24: echo $emi['customer']['customer_address'];
				 		break;	
				case 25: if($emi['customer']['customer_pincode']!=0)echo $emi['customer']['customer_pincode'];else echo "NA";
				 		break;	
				case 26: $area=getAreaByID($emi['customer']['area_id']);
							echo $area['area_name'];
				 		break;	
				case 27:  $city=getCityByID($emi['customer']['city_id']);
							echo $city['city_name'];
				 		break;	
				case 28:$contactArray=null;
						 $contactArray = $emi['customer']['contact_no']; 
						 if(is_array($contactArray))
						 {
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
						 }
				 		break;	
				case 29: if($guarantor!="error") echo $guarantor['guarantor_name']; else echo "NA";
				 		break;	
				case 30: if($guarantor!="error")echo $guarantor['guarantor_address']; else echo "NA";
				 		break;
				case 31: if($guarantor!="error" && $guarantor['guarantor_pincode']!=0)echo $guarantor['guarantor_pincode'];else echo "NA";
				 		break;	
				case 32: if($guarantor!="error") {$area=getAreaByID($guarantor['area_id']);
							echo $area['area_name'];
				 			} else echo "NA";
				 		break;	
				case 33: if($guarantor!="error"){$city=getCityByID($guarantor['city_id']);
							echo $city['city_name'];} else echo "NA";
				 		break;	
				case 34: $contactArray=null;
						if($guarantor!="error")
						{
						 $contactArray = $guarantor['contact_no']; 
						 if(is_array($contactArray))
						 {
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
						 }
						}
				 		break;	
				case 35: if($emi['reg_no']!=null && $emi['reg_no']!="") echo $emi['reg_no']; else echo "NA";
				 		break;	
				case 36: if($vehicle!='error') echo $vehicle['vehicle_engine_no']; else echo "NA";
				 		break;	
				case 37: if($vehicle!='error')echo $vehicle['vehicle_chasis_no']; else echo "NA";
				 		break;	
				case 38: if($vehicle!='error') echo getVehicleCompanyNameById($vehicle['vehicle_company_id']); else echo "NA";
				 		break;	
				case 39: if($vehicle!='error') echo getDealerNameFromDealerId($vehicle['vehicle_dealer_id']); else echo "NA";;
				 		break;	
				case 40: if($vehicle!='error') echo getModelNameById($vehicle['model_id']); else echo "NA";;
				 		break;	
				case 41: if($vehicle!='error') echo getVehicleTypeNameById($vehicle['vehicle_type_id']); else echo "NA";;
				 		break;	
				case 42: if($vehicle!='error'){ if($vehicle['vehicle_condition']==1) echo "NEW"; else echo "OLD"; } else echo "NA";
				 		break;	
				case 43: if($vehicle!='error') echo $vehicle['vehicle_model']; else echo "NA";;
				 		break;	
				case 44: if($vehicle!='error')  echo "<span style='display:none;'>".$vehicle['vehicle_reg_date']."</span>".date('d/m/Y',strtotime($vehicle['vehicle_reg_date'])); else echo "NA";;
				 		break;	
				case 45: if($vehicle!='error') echo "<span style='display:none;'>".$vehicle['permit_exp_date']."</span>".date('d/m/Y',strtotime($vehicle['permit_exp_date'])); else echo "NA";;
				 		break;	
				case 46: if($vehicle!='error') echo "<span style='display:none;'>".$vehicle['fitness_exp_date']."</span>".date('d/m/Y',strtotime($vehicle['fitness_exp_date'])); else echo "NA";;
				 		break;	
				case 47: if(is_array($insurance)) echo "<span style='display:none;'>".$insurance['insurance_issue_date']."</span>".date('d/m/Y',strtotime($insurance['insurance_issue_date'])); else echo "NA";
				 		break;	
				case 48: if(is_array($insurance)) echo "<span style='display:none;'>".$insurance['insurance_expiry_date']."</span>".date('d/m/Y',strtotime($insurance['insurance_expiry_date'])); else echo "NA";
				 		break;	
				case 49: if(is_array($insurance)) echo $insurance['idv']; else echo "NA";
				 		break;	
				case 50: if(is_array($insurance)) echo $insurance['insurance_premium']; else echo "NA";
				 		break;	
				case 51: if(is_array($insurance)) echo getInsuranceCompanyNameById($insurance['insurance_company_id']); else echo "NA";
				 		break;
				case 52: if(isset($file['created_by'])) echo $file['admin_username'];
				 		break;	
				case 53: if(isset($file['date_added'])) echo "<span style='display:none'>".$file['date_added']."</span>".date('d/m/Y',strtotime($file['date_added']));
				 		break;	
				case 54: echo getTotalEmiPaidForLoan($loan['loan_id']);
						break;
				case 55: echo number_format(intval($loan['loan_duration'])-getTotalEmiPaidForLoan($loan['loan_id']),2);
						break;
				case 56: echo $loan['agency_loan_amount'];
						break;		
				case 57:  $parti=getParticipationDetailsForLoanId($loan['loan_id']); echo $parti['agency_duration'];
						break;	
				case 58:  $parti=getParticipationDetailsForLoanId($loan['loan_id']); echo $parti['agency_emi'];
						break;		
				case 59: echo number_format(intval($loan['loan_amount'])-intval($loan['agency_loan_amount']),2);
						break;										
				default: break;																			
				 }
			  ?>
            </td>
            <?php } ?>
           
             <td class="no_print"> <a href="<?php echo WEB_ROOT.'admin/customer/index.php?view=details&id='.$emi['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
           
            
          
  
        </tr>
         <?php } }?>
         </tbody>
    </table>
    </div>
      <table class="reportFiltersTable">
    <tr>
    	<td> From : <?php if(isset($_SESSION['cCustomReport']['from']) && $_SESSION['cCustomReport']['from']!="") echo $_SESSION['cCustomReport']['from']; else echo "NA"; ?></td>
        <td> To : <?php if(isset($_SESSION['cCustomReport']['to']) && $_SESSION['cCustomReport']['to']!="") echo $_SESSION['cCustomReport']['to']; else echo "NA"; ?></td>
    	<td> Bucket(>=) : <?php if(isset($_SESSION['cCustomReport']['win_gt']) && $_SESSION['cCustomReport']['win_gt']!="") echo $_SESSION['cCustomReport']['win_gt']; else echo "NA"; ?></td>
        <td> Bucket(<=) : <?php if(isset($_SESSION['cCustomReport']['win_lt']) && $_SESSION['cCustomReport']['win_lt']!="") echo $_SESSION['cCustomReport']['win_lt']; else echo "NA"; ?></td>
        <td> EMI(>=) : <?php if(isset($_SESSION['cCustomReport']['emi_gt']) && $_SESSION['cCustomReport']['emi_gt']!="") echo $_SESSION['cCustomReport']['emi_gt']; else echo "NA"; ?></td>
        <td> EMI(<=) : <?php if(isset($_SESSION['cCustomReport']['emi_lt']) && $_SESSION['cCustomReport']['emi_lt']!="") echo $_SESSION['cCustomReport']['emi_lt']; else echo "NA"; ?></td>
        <td> Balance(>=) : <?php if(isset($_SESSION['cCustomReport']['balance_gt']) && $_SESSION['cCustomReport']['balance_gt']!="") echo $_SESSION['cCustomReport']['balance_gt']; else echo "NA"; ?></td>
        <td> balance(<=) : <?php if(isset($_SESSION['cCustomReport']['balance_lt']) && $_SESSION['cCustomReport']['balance_lt']!="") echo $_SESSION['cCustomReport']['balance_lt']; else echo "NA"; ?></td>
        <td> City : <?php if(isset($_SESSION['cCustomReport']['city_id']) && $_SESSION['cCustomReport']['city_id']!="") {$city=getCityByID($_SESSION['cCustomReport']['city_id']); echo $city['city_name']; } else echo "NA"; ?></td>
       <td> Agency : <?php if(isset($_SESSION['cCustomReport']['agency_id']) && $_SESSION['cCustomReport']['agency_id']!="") { echo getAgecnyIdOrOCidNameFromAgnecySelectInput($_SESSION['cCustomReport']['agency_id']);  } else echo "NA"; ?></td>
        <td> File Status : <?php if(isset($_SESSION['cCustomReport']['file_status']) && $_SESSION['cCustomReport']['file_status']!="") { if($_SESSION['cCustomReport']['file_status']==1) echo "OPEN";else if($_SESSION['cCustomReport']['file_status']==2) echo "CLOSED";  } else echo "BOTH"; ?></td>
    </tr>
    </table> 
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