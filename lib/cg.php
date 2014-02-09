<?php
ini_set('display_errors', 'On');
//ob_start("ob_gzhandler");
//error_reporting(E_ALL);
//ini_set('display_errors', True);



// start the session
/* GODADDY SESSION
session_save_path('/home/content/06/11039806/html/mysession');
ini_set('session.gc_probability', 0);
ini_set('session.gc_maxlifetime', 604800);

if(!($_SESSION)) {
    session_start();
}
*/
session_save_path('C:\Apache2\htdocs\finex\sessions');
ini_set('session.gc_probability', 0);
ini_set('session.gc_maxlifetime', 604800);
session_start();

// database connection config
$dbHost = '127.0.0.1:3306';
$dbUser = 'tapandtype';
$dbPass = 'Iamtnt12@gmail';
$dbName = 'finexfloating';

// setting up the web root and server root for
// this shopping cart application


function debug($value=''){ 
   
    $btr=debug_backtrace(); 
    $line=$btr[0]['line']; 
    $file=basename($btr[0]['file']); 
	return " ".$file." : ".$line."\r\n";
   /* print"<pre>$file:$line</pre>\n"; 
    if(is_array($value)){ 
        print"<pre>"; 
        print_r($value); 
        print"</pre>\n"; 
    }elseif(is_object($value)){ 
        $value.dump(); 
    }else{ 
        print("<p>&gt;${value}&lt;</p>"); 
    } */
} 

// MECHANISM TO LOGOUT THE USER IF USER IS INACTIVE FOR MORE THAN $inactive amount of time
$inactive = 144000; // amount of seconds of inactivity after which the user should be logged out

// check to see if $_SESSION["timeout"] is set
if (isset($_SESSION["timeout"])) {
    // calculate the session's "time to live"
    $sessionTTL = time() - $_SESSION["timeout"];
    if ($sessionTTL > $inactive) {
        session_destroy();
        header("Location:".WEB_ROOT);
		exit;
    }
}
//$_SESSION["timeout"] = time();


date_default_timezone_set('Asia/Calcutta'); 

// these are the directories where we will store all
// category and product images
define('CATEGORY_IMAGE_DIR', 'images/category/');
define('PRODUCT_IMAGE_DIR',  'images/product/');
// some size limitation for the category
// and product images

// all category image width must not 
// exceed 75 pixels
define('MAX_CATEGORY_IMAGE_WIDTH', 75);

// do we need to limit the product image width?
// setting this value to 'true' is recommended
define('LIMIT_PRODUCT_WIDTH',     true);

// maximum width for all product image
define('MAX_PRODUCT_IMAGE_WIDTH', 300);

// the width for product thumbnail
define('THUMBNAIL_WIDTH',         75);

// maximum width for all product image
define('MAX_EVENT_IMAGE_WIDTH', 1000);


// since all page will require a database access
// and the common library is also used by all
// it's logical to load these library here
require_once 'common.php';
require_once 'bd.php';
require_once 'loan-functions.php';
require_once 'EMI-functions.php';

?>