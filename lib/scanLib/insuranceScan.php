<?php
require_once('../cg.php');
require_once('../bd.php');
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

$file_name=substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz',5)),0,5);
			$file_name=$file_name.round(microtime(true)).".jpg";
			$or_file_name=$file_name;
			$file_name='\\'.$file_name;
        	setScannerStatus(1);
			$string1='"C:\Program Files (x86)\GssEziSoft\CmdTwain\CmdTwain.exe"';
			$string2=' "'.SRV_ROOT.'images\insurance_proof'.$file_name.'"';
			$file_path=SRV_ROOT.'images\insurance_proof'.$file_name;
			$WshShell2 = new COM("WScript.Shell");
$oExec = $WshShell2->Run($string1.$string2, 0, false);
			sleep(17);
			//exec($string1.$string2 , $output);
			setScannerStatus(0);
			$handle = fopen($file_path, "r");
			if($handle)
			echo "new String('".$or_file_name."')";
			else
			echo "new String('0')";
		}
		else
		{
			echo "new String('1')";
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