<?php 
if (!defined('WEB_ROOT')) {
	exit;
}
if(isset($_SESSION['adminSession']['admin_id']))
{
	
}
else
{
	header("Location:".WEB_ROOT."login.php");
}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Finex-Admin Section</title>
<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/adminMain.css" />
<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/rasid.css" />
<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/table.css" />
<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/bp.css" />
<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/TableTools_JUI.css" />
<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/TableTools.css" />



<?php
if(isset($cssArray)){
 foreach($cssArray as $css){
	?>
   <link rel="stylesheet" href="<?php echo WEB_ROOT."css/".$css; ?>" /> 
<?php
	}} ?>


<script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/tableToCSV.js"></script>

</head>

<body>
<div id="myModal" class="modal fade no_print"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" >
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Authentication Required</h4>
      </div>
      <div class="modal-body">
        <p>Please enter your Password, <?php echo  ucwords($_SESSION['adminSession']['admin_name']); ?>!</p>
        	<form action="<?php echo WEB_ROOT ?>lib/checkForDeletion.php" id="confirmDeletionForm" method="post">
        	<input type="password" id="confirmationPassword" name="p" />
            <input type="hidden" id="delLink" name="delLink" value="" />
           
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit" value="confirm" id="confirmDeletionSubmit"  class="btn btn-danger" />
         </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php if(isset($accounts) && $accounts==1)
{ 
$period=getPeriodForUser($_SESSION['adminSession']['admin_id']);
$current_date=getCurrentDateForUser($_SESSION['adminSession']['admin_id']);
$current_company=getCurrentCompanyForUser($_SESSION['adminSession']['admin_id']);
$company_type=$current_company[1];
$or_agency_id=$current_company[0];
if($company_type==0)
{
$oc_id=$or_agency_id;	
$agency_id=0;
}
else
{
$agency_id=$or_agency_id;	
$oc_id=0;
}
?>
<div id="periodModal" class="modal fade no_print"  tabindex="-1" role="dialog" aria-labelledby="periodModalLabel" aria-hidden="true">
  <div class="modal-dialog" >
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Account Period | Current Date | Company</h4>
      </div>
      <div class="modal-body">
        	<form action="<?php echo WEB_ROOT ?>lib/checkForPeriod.php" id="periodForm" method="post" name="periodForm">
            <table class="insertTableStyling">
            <tr>
            <td>From : </td>
        	<td><input type="text" id="from_period" name="from_period" value="<?php if(isset($period) && $period!="error") echo date('d/m/Y',strtotime($period[0])); ?>" /> 
            </td>
            <td> (dd/mm/yyyy) </td>
            </tr>
            <tr>
            <td>To : </td>
            <td><input type="text" id="to_period" name="to_period" value="<?php if(isset($period) && $period!="error") echo date('d/m/Y',strtotime($period[1])); ?>" /></td> <td>(dd/mm/yyyy)</td>
            </tr>
           
            <tr>
            <td >Current Date : </td>
            <td><input type="text" id="current_date" name="current_date" value="<?php if(isset($current_date) && $current_date!="error") echo date('d/m/Y',strtotime($current_date));  ?>" /></td> <td> (dd/mm/yyyy)</td>
            </tr>
            
            <tr>
<td width="230px">Agency Name<span class="requiredField">* </span> : </td>
				<td>
					<select name="agency_id" >
                       
                        <?php
                            $agencies = listAgencies();
							$companies = listOurCompanies();
                            foreach($agencies as $super)
							
                              {
                             ?>
                             
                             <option value="ag<?php echo $super['agency_id'] ?>" <?php if($agency_id>0 && $super['agency_id']==$agency_id) { ?> selected="selected" <?php } ?>><?php echo $super['agency_name'] ?></option>
                             
                             <?php } ?>
                              
                             <?php 
							 
							 $companies = listOurCompanies();
                              foreach($companies as $com)
							
                              {
                             ?>
                             
                             <option value="oc<?php echo $com['our_company_id'] ?>" <?php if($oc_id>0 && $com['our_company_id']==$oc_id) { ?> selected="selected" <?php } ?>><?php echo $com['our_company_name'] ?></option>
                             
                             <?php } ?>
                              
                         
                            </select> 
                    </td>
                    
                    
                  
</tr>

            </table>
           
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit" value="confirm" id="confirmPeriodSubmit"  class="btn btn-danger" />
         </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php } ?>
	<div id="mainDiv">
		<div id="header" class="no_print">
			<a href="<?php echo WEB_ROOT; ?>"><div id="logo">Finex</div></a>
				<div id="navigation">
            		<div id="nav">
                     <a id="userNameNav" href="#"><?php echo $_SESSION['adminSession']['admin_name']; ?> &#8711; </a> 
                        <div id="userDetailDropDown">
                        	<ul>
                        		<a href="<?php echo WEB_ROOT."lib/adminuser-functions.php?action=logout" ?>"><li>Log Out</li></a>
                                <a href="<?php echo WEB_ROOT."admin/Profile/index.php" ?>"><li>Change Password</li></a>
                            </ul>    
                        </div>
                	</div>
				</div>
                <div class="greeting"></div>
    	</div>
<div id="content">
<div id="navigationSection" class="no_print">
	  <div class="navigationLinks">   
         
         <?php
		 require_once 'navigation.php' ?>      
         </div>  <!-- End of navigationLinks Div -->
          <div class="clearfix"></div>
</div>


<div class="coreContent <?php
if(isset($_SESSION['adminSession']['admin_rights']))
{
	$admin_rights=$_SESSION['adminSession']['admin_rights'];
	}
 if(! (isset($_SESSION['adminSession']['admin_rights']) && (in_array(5,$admin_rights) || in_array(7,$admin_rights))) )
			{ ?> no_print <?php } ?>">
<div id="companyTitle"><?php if(isset($showTitle) && $showTitle==false){} else if(isset($customTitle) && $customTitle!=false) echo $customTitle; else  echo getOurCompanyNameByID($_SESSION['adminSession']['oc_id']);  ?></div>