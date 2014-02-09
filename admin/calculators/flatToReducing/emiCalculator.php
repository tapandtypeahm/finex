<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print">Flat to Reducing Rate Calculator</h4>
<h4 class="subheadingAlignment no_print">All Inputs Required</h4>
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
<form action="<?php echo "index.php?action=calculate"; ?>" method="post">

<table class="insertTableStyling no_print">

<tr style="display:none;">
<td width="230px;" class="firstColumnStyling">
Loan Structure<span class="requiredField">* </span> : 
</td>

<td>
<input  type="radio" name="loan_scheme" class="loan_type" id="even_loan" value="1" onselect="changeLoanStr()" onchange="changeLoanStr()" onblur="changeLoanStr()" checked="checked"/> <label style="display:inline-block; top:3px;position:relative;margin-right:10px;" for="even_loan">EVEN (Same EMI)</label>
<input  type="radio" name="loan_scheme" class="loan_type" id="uneven_loan" value="2" onselect="changeLoanStr()" onchange="changeLoanStr()" onblur="changeLoanStr()"/> <label style="display:inline-block;top:3px;position:relative;" for="uneven_loan">UNEVEN (Different EMI)</label>
</td> 
</tr> 
<tr>

<td class="firstColumnStyling">
Total Loan Amount : 
</td>

<td>
<input type="text" name="amount" id="amount" autocomplete="off" placeholder="only Digits"  value="<?php if(isset($_SESSION['femiCalc']['amount'])) echo $_SESSION['femiCalc']['amount'] ?>"/>
</td>
</tr>


<tr>

<td class="firstColumnStyling">
Loan Duration (In months) : 
</td>

<td>
<input type="text" name="duration" id="duration" placeholder="only Digits " value="<?php if(isset($_SESSION['femiCalc']['duration'])) echo $_SESSION['femiCalc']['duration'] ?>"/> 
</td>
</tr>
<input  type="hidden" name="loan_type"  value="1" />

<tr>

<td class="firstColumnStyling">
Flat Rate of Interest (annually in %) : 
</td>

<td>
<input type="text" name="roi" id="roi" placeholder="only Digits" value="<?php if(isset($_SESSION['femiCalc']['roi'])) echo $_SESSION['femiCalc']['roi'] ?>"/> 
</td>
</tr>



<tr>



<td></td>
				<td>
				 <input type="submit" value="Calculate" class="btn btn-warning" />	
                </td>
</tr>

<tr>

<td class="firstColumnStyling">
EMI : 
</td>

<td>
<input disabled="disabled" type="text" name="emi" value="<?php if(isset($_SESSION['femiCalc']['emi'])) echo $_SESSION['femiCalc']['emi'] ?>"   />
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Reducing Rate of Interest : 
</td>

<td>
<input disabled="disabled" type="text" name="reducing_roi"  value="<?php if(isset($_SESSION['femiCalc']['reducing_roi'])) echo $_SESSION['femiCalc']['reducing_roi'] ?>" />
</td>
</tr>


<tr>

<td class="firstColumnStyling">
IRR : 
</td>

<td>
<input disabled="disabled" type="text" name="IRR" value="<?php if(isset($_SESSION['femiCalc']['irr'])) echo $_SESSION['femiCalc']['irr'] ?>"  />
</td>
</tr>



</table>

</form>
 <?php
		if(isset($_SESSION['femiCalc']['princ_interest_table']) && is_array($_SESSION['femiCalc']['princ_interest_table']))
		$full_table=$_SESSION['femiCalc']['princ_interest_table'];
		$no=0;
		if(isset($full_table) && $full_table!=false)
		{
?>			
<div class="no_print" style="width:100%;">
    <table id="adminContentTable" class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">No</th>
             <th class="heading">EMI</th>
             <th class="heading">Interest</th>
            <th class="heading">Principal</th>
            <th class="heading">Balance</th>
           
        </tr>
    </thead>
    <tbody>
        
       <?php
		foreach($full_table as $row)
		{	
	
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$no; ?>
            </td>
          
            <td><?php  echo number_format($row['emi']); ?> 
            </td>
             <td><?php echo number_format($row['interest']); ?> 
			 </td>
              <td><?php echo number_format($row['principal']); ?> 
			 </td>
            <td><?php  if($no==count($full_table)) echo 0;else  echo number_format($row['balance']); ?> 
			 </td>
            
        </tr>
         <?php  }?>
         </tbody>
    </table>
	</div>    
 <table id="to_print" class="to_print adminContentTable"></table>    
<?php } ?>
</div>
<div class="clearfix"></div>