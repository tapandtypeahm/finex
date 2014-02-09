</div><!-- end of coreContent -->
<div class="clearfix"></div>
</div><!-- end of content -->
<div id="footer" class="no_print">
	<div class="footerProduct">A Tap & Type Product</div>
    <div class="footerWeb"><a href="http://www.tapandtype.com/">www.tapandtype.com</a></div>
    <div class="footerContact">Contact : 09824143009, 09428592016</div>
</div>
</div><!-- end of mainDiv -->
	
    <script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/jquery.tables.min.js"></script>
   
      <script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/ZeroClipboard.js"></script>
        <script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/TableTools.js"></script>
	<script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/bp.min.js"></script>
    <script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/table.js"></script>
     <script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/common.js"></script>
      <script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/confirmDeletion.js"></script>
       
       <script type="text/javascript" src="<?php echo WEB_ROOT; ?>js/accountsCommon.js"></script>
       <?php if(isset($accounts) && $accounts==1)
{ 
$period=getPeriodForUser($_SESSION['adminSession']['admin_id']);
if($period=="error" || strtotime($period[0])==strtotime("1970-01-01") || strtotime($period[1])==strtotime("1970-01-01"))
{
?>
<script type="text/javascript">
$('#periodModal').modal('show');
</script>
<?php }}?>
 <?php if(isset($accounts) && $accounts==1)
{ 
$current_date=getCurrentDateForUser($_SESSION['adminSession']['admin_id']);
if($current_date=="error" || strtotime($current_date)==strtotime("1970-01-01"))
{
	
?>
<script type="text/javascript">
$('#currentDateModal').modal('show');
</script>
<?php }}?>
      <script type="text/javascript">
      function RefreshMe()
        {
			
            $.ajax({
                   type: "GET",
                   url: "<?php echo WEB_ROOT; ?>refresh.php",
                   data: "id=1",
                   success: function(response){
                              
                            }
           });
        }

    // Call it
setInterval('RefreshMe()', 3000); //every 5 secs


      </script>
  <script src="<?php echo WEB_ROOT; ?>js/loginDivAccount.js"></script>	
    <?php
	if(isset($jsArray))
	{
		foreach($jsArray as $js)
		{
	?>
    <script type="text/javascript" src="<?php echo WEB_ROOT."js/".$js; ?>"></script>
    <?php
			}
		}
	 ?>
      
 
</body>
</html>
