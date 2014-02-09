<?php
require_once('../lib/cg.php');
require_once('../lib/bd.php');
scan();

function getScannerStatus()
{
	$sql="SELECT in_use,TIMESTAMPDIFF(MINUTE, `time_stamp`, NOW())
		  FROM fin_scanner
		  WHERE fin_scan_id=1";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	return  $resultArray[0];	  
	}


function  scan() {
		$scanner_status=getScannerStatus();
		if($scanner_status[0]==0 || $scanner_status[1]>0)
		{

$file_name=substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,5);
			$file_name=$file_name.".jpg";
			$file_name='\\'.$file_name;
        	setScannerStatus(1);
			$string1='"C:\Program Files (x86)\GssEziSoft\CmdTwain\CmdTwain.exe"';
			$string2=' "'.'C:\Users\tnt\Documents'.$file_name.'"';
			$WshShell = new COM("WScript.Shell");
$oExec = $WshShell->Run($string1.$string2, 0, false);
			$WshShell = null;
			sleep(15);
			echo "hello";
			//exec($string1.$string2 , $output);
		
			setScannerStatus(0);
			
		}
		else
		{
			echo "Scanner already in use";
			exit;
			}
}

 
function setScannerStatus($status)
{
	$sql="UPDATE fin_scanner
			SET in_use=$status, time_stamp=NOW()";
	dbQuery($sql);		
	}

//$scanner_in_use_already = true;
	//	exec('"C:\Program Files (x86)\GssEziSoft\CmdTwain\CmdTwain.exe" "C:\Users\tnt\Documents\1.jpg"', $output);
		// $scanner_in_use_already = false;