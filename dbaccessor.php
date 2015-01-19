<?php
include_once "check_user.php";
include_once "translit.php";
class DBAccessor {
	
	protected $resource = '';
	
	function __construct() {
		$this->resource = dbconnect();	
		mysql_query( "SET NAMES 'cp1251' COLLATE 'cp1251_general_ci'");
	}			
		
	function __destruct() {	
		return true;
	}	
		
	function getAssocList($query, $convert = true) {
		if($convert) {
			$query = iconv("UTF-8", "windows-1251", $query);
		}
		$cursor = mysql_query( $query);		
		if($cursor) {			
			$array = array();
			while ($row = mysql_fetch_assoc($cursor)) {			
				if($convert) {
					$array[] = $this->convertRow($row);		
				} else {
					$array[] = $row;		
				}			
			}			
			mysql_free_result($cursor);
			return $array;
		} else {			
			$errorMsg = mysql_error();
			$this->addToLog("mysql error: $errorMsg");
			return false;
		}										
	}
	

	function getDataList($query, $convert = true) {
		if($convert) {
			$query = iconv("UTF-8", "windows-1251", $query);
		}
		$cursor = mysql_query( $query);	
		if($cursor) {			
			$array = array();
			while ($row = mysql_fetch_row($cursor)) {			
				if($convert) {
					$array[] = $this->convertRow($row);		
				} else {
					$array[] = $row;		
				}			
			}			
			mysql_free_result($cursor);
			return $array;
		} else {			
			$errorMsg = mysql_error();
			//addToLog("mysql error: $errorMsg");
			return false;
		}	
	}	
		
	function getAssoc($query, $convert = true) {
		if($convert) {
			$query = iconv("UTF-8", "windows-1251", $query);
		}
		$res = $this->getAssocList($query, $convert);		
		if(is_array($res)) {
			$result = $res[0];			
			return $result; 
		} else {
			return false;
		}		
	}
		
		
	function getNumberOfRows() {
		$query = "SELECT FOUND_ROWS();";
		$res = $this->getResult($query);
		return $res;
	}
		
	function getResult($query) {
		$query = iconv("UTF-8", "windows-1251", $query);	
		$cursor = mysql_query( $query);
						
		if(!$cursor) {
			return false;			
		}
		$ret = null;
		if ($row = mysql_fetch_row($cursor)) {
			$ret = $row[0];
			$ret = iconv("windows-1251", "UTF-8", $ret);
		}		
		mysql_free_result($cursor);
		return $ret; 		
	}
		
		
	function execute($query) {	
		$query = iconv("UTF-8", "windows-1251", $query);
		$res = mysql_query($query);
		if($res) {
			$result['result'] = 'success';
		} else {
			$result['result'] = 'failure';
			$result['code'] = 1;
		}		
		return $result;
	}
		
	function executeListQuery($query) {
	
		$start = $this->getInt('start');
	    $end = $this->getInt('limit');   
	        $end = 10;
	    $field = $this->getString('sort');
	    $dir = $this->getString('dir');  
	    $dir = $dir=='true' ? 'asc': 'desc';
	    if($field && $dir) {
	        $sql = $query . ' ORDER BY ' . $field . ' ' . $dir . ', id desc'; 
	    } else if($dir && !$field) {
	    	$sql = $query . "ORDER BY id $dir";	    	
	    } else {
	       	$sql = $query . "ORDER BY id";
	    }	    	
	   	$sql = $sql . ' LIMIT ' . $start . ', '. $end;      

	    $list = $this->getAssocList($sql);
	   
	    $rows = $this->getNumberOfRows($sql);
		$res = '{"total":"' . $rows . '","results":'. json_encode($list).'}';
		echo $res;
	}
		
	function createRecord($query) {	
		$query = iconv("UTF-8", "windows-1251", $query);
		$res = mysql_query($query);
		if($res) {
			return mysql_insert_id();
		}
		return 0;
	}
		
	function getInt($param) {	
		$arr = array($_REQUEST, $_POST, $GLOBALS['_REQUEST_WITH_JSON']);
		foreach ($arr as $arr_v) {
			if(array_key_exists($param, $arr_v)) {
				return intval($arr_v[$param]);
			} 
		}	
		return 0;
	}
		
	function getString($param) {
		$arr = array($_REQUEST, $_POST, $GLOBALS['_REQUEST_WITH_JSON']);	
		foreach ($arr as $arr_v) {
			if(array_key_exists($param, $arr_v)) {			
				$value = stripslashes($arr_v[$param]); 
				return mysql_real_escape_string($value);
			} 
		}
		return "";
	}

	function getArr($param) {
		$arr = array($_REQUEST, $_POST, $GLOBALS['_REQUEST_WITH_JSON']);	
		foreach ($arr as $arr_v) {
			if(array_key_exists($param, $arr_v)) {			
					return $arr_v[$param]; 
			} 
		}
		return "";
	}

	function  getFloat($param) { 
		$arr = array($_REQUEST, $_POST, $GLOBALS['_REQUEST_WITH_JSON']);
		foreach ($arr as $arr_v) {
			if(array_key_exists($param, $arr_v)) {
				return floatval($arr_v[$param]);
			} 
		}
		return 0;
	}	
	
	
	function concatProps($prop, $allProps) {
		if($prop) {
			if($allProps) {
				$allProps .= ',' . $prop;
			} else {
				$allProps = $prop;
			}
		}
		return $allProps;
	}
	
		
	function formWhere($name, $value, $sign, &$where) {
		if($value) {
			if($where) {
				$where = $where . ' AND ' . $name . $sign . $value;
			} else {
				$where = ' WHERE ' . $name . $sign . $value; 
			}
		}		
	}
		
	function addToLog($message) {
		$filename = "log.txt";		
		$handle = fopen($filename, "a+");
		fwrite($handle, $message . PHP_EOL);
		fclose($handle);			
	}
	
	function convertRow($row) {
		$correctArray = array();
		foreach($row as $key => $value) {
			$correctArray[$key] = iconv("windows-1251", "UTF-8", $value);
		}
		return $correctArray;
	}



function string_begins_with($string, $search) {
    return (strncmp($string, $search, strlen($search)) == 0);
}

function getImageName($folder, $filename) {
	$id = 1;
	$handler = opendir($folder);
		 		
	$path_parts = pathinfo($folder . '/' . $filename);
	 do {
	 	$filename = $path_parts['filename'] . '_' . $id . '.' . $path_parts['extension'];
	 	$id++;
	 } while(file_exists($folder . '/' . $filename)); 
	 	
	 return $filename;	 	 
}




function uploadSingleFile($folder, $url_path, $width) {
	$logo = '';
	if(isset($_FILES['uploadfile']) && $_FILES['uploadfile']['tmp_name'] != '' && $_FILES['uploadfile']['size'] != 0) {
		$file_upload = true;
		$real_name = basename($_FILES['uploadfile']['name']);
		$tmp_name = basename( $_FILES['uploadfile']['tmp_name']);

		$filename = $this->getCorrectFileName($real_name, $tmp_name);
		
		$upload_name = $folder . $tmp_name;
		$correct_name = $this->getImageName($folder, $filename);
		
echo "rename({$_FILES['uploadfile']['tmp_name']}, {$correct_name}";
		if(rename($_FILES['uploadfile']['tmp_name'], $folder . $correct_name)) {
			if($width != -1) {
				$this->makeThumbnail($folder, basename($upload_name), $width, -1, $correct_name);
			}				
		} 	
		$logo = $url_path . $correct_name;
		//unlink($upload_name);							
	}		
	return $logo;
}

function getCorrectFileName($real_name, $tmp_name) {
	$sanitized_name = preg_replace('/[^0-9a-z\.\_\-]/i','',$real_name);
	
	$filename = "";
	if($sanitized_name == $real_name) {
		$filename = $real_name;
	} else {
		$filename = $tmp_name;
	}
	return $filename;
}

function uploadFileGroup($folder, $url_path, $width)  {
	$result = array();
	if(isset($_FILES['uploadfile']) ) {		
		for($i = 0; $i < count($_FILES['uploadfile']['name']); $i++) {			
			$real_name = $_FILES['uploadfile']['name'][$i];
			$tmp_name = $_FILES['uploadfile']['tmp_name'][$i];			
			$size = $_FILES['uploadfile']['size'][$i];
			if($size > 0) {
				$file = $this->moveUploadedFile($real_name, $tmp_name, $folder, $url_path, $width);	
				$result[] = $file;
			}				
		}		
	} 
	return $result;
}

function moveUploadedFile($real, $tmp, $folder, $path, $width) {
		
		$result = '';
		$real_name = basename($real);
		$tmp_name = basename($tmp);
			
		$filename = $this->getCorrectFileName($real_name, $tmp_name);
			
		$path_parts = pathinfo($filename);			
		$file_location =  $folder . $filename;
			
		rename($tmp, $file_location); 
				
		if($path_parts['extension'] != 'flv') {								
			$correct_name = $this->getImageName($folder, $filename);
			$file = $this->makeThumbnail($folder, basename($file_location), $width, -1, $correct_name);
			$result =  $correct_name; 
			unlink($file_location);	
		} else {
			$result =  $filename;
		}
		return $result;
	
}

function makeThumbnail($folder, $fileName, $dest_width, $dest_height, $thumb_name) { 

	$thumbnailPath = $folder . '/' . $thumb_name;
	$imagePath = $folder . '/' . $fileName;
	
	$imageType = array( 1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF');
	$imgInfo = getimagesize($imagePath);

	if ( $imageType[$imgInfo[2]] == 'JPG' ) {
		$sourceImage = imagecreatefromjpeg($imagePath);
	} elseif ($imageType[$imgInfo[2]] == 'PNG'){
		$sourceImage = imagecreatefrompng($imagePath);
	} elseif ($imageType[$imgInfo[2]] == 'BMP'){
		$sourceImage = $this->imageCreateFromBMP($imagePath);
	} else {
		$sourceImage = imagecreatefromgif($imagePath);
	}
	if(!$sourceImage) {
		return false;
	}

	$size = getimagesize($imagePath);
	$src_width = $size[0];
	$src_height = $size[1];

	$x = 0; // shift top
	$y = 0; // shift left
	$calc_height = ceil($src_height * $dest_width / $src_width);

	if($dest_height == -1) {
		// just resize height proportionally
		$dest_height = $calc_height;
	} else if($calc_height > $dest_height) {
		// have to cut top and bottom
		$y = $src_height / 2 / $calc_height * ($calc_height - $dest_height);
	} else {
		// have to cut left and right
		$calc_width = ceil($src_width * $dest_height / $src_height);
		$x = $src_width / 2 / $calc_width * ($calc_width - $dest_width);
	}
	$new_im = ImageCreatetruecolor($dest_width, $dest_height);
	imagecopyresampled($new_im, $sourceImage, 0, 0, $x, $y, 
		$dest_width, $dest_height, $src_width - $x * 2, $src_height - $y * 2);
	imagejpeg($new_im,$thumbnailPath, 70);			
	return true;
}

function imageCreateFromBMP($filename) {

	//Ouverture du fichier en mode binaire
	if (! $f1 = fopen($filename,"rb")) return FALSE;

	//1 : Chargement des ent?tes FICHIER
	$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
	if ($FILE['file_type']	!= 19778) return FALSE;

	//2 : Chargement des ent?tes BMP
	$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
			'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
			'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
	$BMP['colors']	= pow(2,$BMP['bits_per_pixel']);
	if ($BMP['size_bitmap']	== 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
	$BMP['bytes_per_pixel']	= $BMP['bits_per_pixel']/8;
	$BMP['bytes_per_pixel2']= ceil($BMP['bytes_per_pixel']);
	$BMP['decal']	= ($BMP['width']*$BMP['bytes_per_pixel']/4);
	$BMP['decal']	-= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
	$BMP['decal']	= 4-(4*$BMP['decal']);
	if ($BMP['decal'] == 4) $BMP['decal']	= 0;

	//3 : Chargement des couleurs de la palette
	$PALETTE = array();
	if ($BMP['colors'] < 16777216)
	{
		$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
	}

	//4 : Cr?ation de l'image
	$IMG = fread($f1,$BMP['size_bitmap']);
	$VIDE = chr(0);

	$res = imagecreatetruecolor($BMP['width'],$BMP['height']);
	$P = 0;
	$Y = $BMP['height']-1;
	while ($Y >= 0)
	{
		$X=0;
		while ($X < $BMP['width'])
		{
			if ($BMP['bits_per_pixel']== 24)
				$COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
			elseif ($BMP['bits_per_pixel']== 16)
			{		
				$COLOR = unpack("n",substr($IMG,$P,2));
				$COLOR[1]= $PALETTE[$COLOR[1]+1];
			}
			elseif ($BMP['bits_per_pixel']== 8)
			{		
				$COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
				$COLOR[1]= $PALETTE[$COLOR[1]+1];
			}
			elseif ($BMP['bits_per_pixel']== 4)
			{
				$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
				if (($P*2)%2 == 0) 
					$COLOR[1]=($COLOR[1] >> 4); 
				else 
					$COLOR[1]=($COLOR[1] & 0x0F);
				$COLOR[1]= $PALETTE[$COLOR[1]+1];
			}
			elseif ($BMP['bits_per_pixel']	== 1)
			{
				$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
				if(($P*8)%8 == 0) $COLOR[1]= $COLOR[1]				>>7;
				elseif (($P*8)%8 == 1) $COLOR[1]=($COLOR[1]& 0x40)>>6;
				elseif (($P*8)%8 == 2) $COLOR[1]=($COLOR[1]& 0x20)>>5;
				elseif (($P*8)%8 == 3) $COLOR[1]=($COLOR[1]& 0x10)>>4;
				elseif (($P*8)%8 == 4) $COLOR[1]=($COLOR[1]& 0x8)>>3;
				elseif (($P*8)%8 == 5) $COLOR[1]=($COLOR[1]& 0x4)>>2;
				elseif (($P*8)%8 == 6) $COLOR[1]=($COLOR[1]& 0x2)>>1;
				elseif (($P*8)%8 == 7) $COLOR[1]=($COLOR[1]& 0x1);
				$COLOR[1]= $PALETTE[$COLOR[1]+1];
			}
			else
				return FALSE;
			imagesetpixel($res,$X,$Y,$COLOR[1]);
			$X++;
			$P += $BMP['bytes_per_pixel'];
		}
		$Y--;
		$P+=$BMP['decal'];
	}

	//Fermeture du fichier
	fclose($f1);

	return $res;
}

function uploadSingleFileNew($folder, $url_path, $width) {
	$logo = '';
	if(isset($_FILES['uploadfile']) && $_FILES['uploadfile']['tmp_name'] != '' && $_FILES['uploadfile']['size'] != 0) {
		$file_upload = true;
		$real_name = $_FILES['uploadfile']['name'];
		$tmp_name = basename( $_FILES['uploadfile']['tmp_name']);
		$extension_file_name = end(explode(".", $real_name));
		$filename = str_replace ('.'.$extension_file_name,'',$real_name);
		$filename = get_translit_singleline(iconv('utf-8','windows-1251',$filename));
		$filename .='.'.$extension_file_name;
		$upload_name = $folder . $tmp_name;
		$correct_name = $this->getImageName($folder, $filename);
		if(rename($_FILES['uploadfile']['tmp_name'], $upload_name)) {
			copy ($upload_name, $folder.$correct_name);
			if($width != -1) {
				$this->makeThumbnail($folder, basename($upload_name), $width, -1, $correct_name);
			}				
		} 	
		$logo = $url_path . $correct_name;
		unlink($upload_name);							
	}	
	return $correct_name ? $url_path.$correct_name : '';
}

}


?>
