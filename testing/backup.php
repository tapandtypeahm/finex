<?php require_once('../lib/cg.php');
require_once('../lib/bd.php');
backup_tables();
function backup_tables($tables='*'){
	$return="SET FOREIGN_KEY_CHECKS = 0; ";
	
	if($tables=='*')
	{
	$tables=array();
	$result=dbQuery('SHOW TABLES');
	$tables=dbResultToArray($result);
	
	
	}
	else
	{
		$tables=is_array($tables)?$tables:explode(',',$tables);
	}
	foreach($tables as $tab)
	{   
		$table=$tab[0];
		$result=dbQuery('SELECT * FROM '.$table);
		$num_fields=mysql_num_fields($result);
		//$return1.='DROP TABLE IF EXISTS '.$table.';';
		$row2=mysql_fetch_row(dbQuery('SHOW CREATE TABLE '.$table));
		//$return1="\n\n".$row2[1].";\n\n";
		
		for($i=0;$i<$num_fields;$i++)
		{
			while($row=mysql_fetch_row($result))
			{
				$return.='INSERT INTO '.$table.' VALUES(';
				for($j=0;$j<$num_fields;$j++)
				{
					
					$row[$j]=addslashes($row[$j]);
					if($table=="fin_file" && $row[$j]=="")
					$row[$j]=NULL;
					$row[$j]=str_replace("\n", '\n', $row[$j]);
					
					if(isset($row[$j]))
					{
						if(($table=="fin_file" || $table=="fin_loan_emi") && $row[$j]=="")
						{	
						$return.="NULL";
						}
						else
						$return.='"'.$row[$j].'"';
					}
					else if($table=="fin_file")
					{
						$return.=NULL;
						}
					else
					{
							$return.='""';
					}
					if($j<($num_fields-1))
					{
							$return.=',';
					}
					
				}
				$return.=");\n";
				
			}
		}
	$return.="\n\n\n";
	
	}
	$handle=fopen(SRV_ROOT.'backups/dbbackup'.time().(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);}?> 