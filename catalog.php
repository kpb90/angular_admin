<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

ini_set('display_errors','On');


require_once "../admin/dbconnect.php";
require_once "translit.php";
require_once "excel_reader/reader.php";
$action = null;
@$action = $_REQUEST['action'];

ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);
set_time_limit(0); 

//var_dump($action);
//var_dump($_FILES);
$SITE_INTRASETI_ROOT = $_SERVER['DOCUMENT_ROOT'].'/';
if($action == 'upload_catalog') {
    	$name_of_uploaded_file = basename($_FILES['catalog']['name']);
	if(isset($_FILES['catalog']) && 
	   $_FILES['catalog']['tmp_name'] != '' && 
	   $_FILES['catalog']['size'] != 0 &&
	   strpos($name_of_uploaded_file, '.xls'))
	{
		$folder = $SITE_INTRASETI_ROOT . '/catalog/backup_excel/';
		$filename = $folder . 'site_data.xls';		
		copy($filename, $folder . 'site_data_' . time() . '.xls');
		$rename_result = rename($_FILES['catalog']['tmp_name'], $filename);		
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('CP1251');
		$data->read($filename);
		$ret_str = '';
		if(is_array($data->sheets[0]['cells']))
		{
			$id = '13';
			dbconnect();
                        load_products($data);   
                        $ret_str = "<font style='color: green;'>Каталог загружен.</font>";
                        echo "<div style='position: absolute; top: 500px; left: 0px; width: 100%;'><div style=' margin: 0 auto; width: 665px;'>{$ret_str}</div></div>";
		}
		else
		{
                    $ret_str = "<font style='color: red;'>Не удалось прочитать Excel файл.</font>";
        }
	}
	else		
	{
		echo "<font style='color: red;'>Файл с каталогом пустой или имеет недопустимое имя/расширение.</font>";
    }
} else if($action == 'upload_images') {
	if(isset($_FILES['images']) && $_FILES['images']['tmp_name'] != '' && $_FILES['images']['size'] != 0) {
	
		$large_folder = $_SERVER['DOCUMENT_ROOT'].'/images/catalog/large/';
		$big_folder = $_SERVER['DOCUMENT_ROOT'].'/images/catalog/big/';
		$mid_folder = $_SERVER['DOCUMENT_ROOT'].'/images/catalog/middle/';
		$small_folder = $_SERVER['DOCUMENT_ROOT'].'/images/catalog/small/';
		
		$filename = $folder . $_FILES['images']['name'];
		
		if(move_uploaded_file($_FILES['images']['tmp_name'], $filename)) {			
			$zip = zip_open($filename);
			if ($zip) {
				while ($zip_entry = zip_read($zip)) {
				    $file_name =  zip_entry_name($zip_entry);				   
				    $file_name = str_replace('+', '_', $file_name);
				    $large_file = $large_folder . $file_name;
				    $fp = fopen($large_file, "w");
				    if (zip_entry_open($zip, $zip_entry, "r")) {
				      $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				      fwrite($fp,"$buf");
				      zip_entry_close($zip_entry);				      
				      fclose($fp);
				      $small_file = $small_folder . $file_name;
				      $mid_file = $mid_folder . $file_name;	
				      $big_file = $big_folder . $file_name;			      
				      make_thumbnail($large_file, $big_file, 260, 280);
				      make_thumbnail($large_file, $mid_file, 130, 140);
				      make_thumbnail($large_file, $small_file, 28, 26);				      
				    } else {
				    	echo "zip entry" . zip_entry_name($zip_entry) . " cannot be open";
				    }
				  }
				  zip_close($zip);
				  unlink($filename);
			} else {
				echo "zip cannot be open";
			}
		} else {
			echo "rename failed";
		}	
	}
}



function load_products($data) {
	dbconnect();
        importer_query("DROP TABLE IF EXISTS `chemical_resistance`;");
         importer_query("
                CREATE TABLE IF NOT EXISTS `chemical_resistance` (	
                  `id` int(3) NOT NULL auto_increment,
                  `environment` varchar(400) NOT NULL,
                  `concentration` varchar(400) NOT NULL,
                  `version`  varchar(400) NOT NULL,	
                  `o_rings`  varchar(400) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ");

        $skip_titles = true;
        foreach($data->sheets[0]['cells'] as $v) 
        {
			if ($skip_titles) {
				$skip_titles = false;
				continue;
			}

			importer_query("INSERT INTO `chemical_resistance` ( `environment`, `concentration`, `version`, `o_rings`) VALUES
                    (
                    '" . get_prop($v[1]) . "', 	
                    '" . get_prop($v[2]) . "',
                    '" . get_prop($v[3]) . "',
                    '" . get_prop($v[4]) . "'
                    );
            ");

        }
    
		importer_query("DROP TABLE IF EXISTS `internal_structure`;");
         importer_query("
                CREATE TABLE IF NOT EXISTS `internal_structure` (	
                  `id` int(3) NOT NULL auto_increment,
                  `size` varchar(400) NOT NULL,
                  `concentration` varchar(400) NOT NULL,
                  `dry_running`  int (1) DEFAULT 0,	
                  `coupling`  varchar(400) NOT NULL,
                  `seal`  varchar(400) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ");

        $skip_titles = 0;
        foreach($data->sheets[1]['cells'] as $v) 
        {
			if ($skip_titles<2) {
				$skip_titles++;
				continue;
			}

			importer_query("INSERT INTO `internal_structure` ( `size`, `concentration`, `dry_running`, `coupling`,`seal`) VALUES
                    (
                    '" . get_prop($v[1]) . "', 	
                    '" . get_prop($v[2]) . "',
                    '" . get_prop($v[3]) . "',
                    '" . get_prop($v[4]) . "',
                    '" . get_prop($v[5]) . "'
                    );
            ");

        }
	
}


function importer_query ($q_str)
	{		
		mysql_query($q_str);

		if (mysql_errno()) 
		{ 
			echo "MySQL error ".mysql_errno().": ".mysql_error()."\n<br>When executing:<br>\n{$q_str}\n<br>"; 
		} 
	}
	
function get_prop($e)
	{
		$value = trim(str_replace(array("'", "\n"), array("\\'", "<br>\n"), $e));		
		return iconv('cp1251', 'utf-8', $value);
	}	
?>