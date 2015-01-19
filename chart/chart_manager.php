<?php
require_once "../dbaccessor.php";

	
class ChartManager extends DBAccessor {
	
	function __construct() {	
		parent::__construct();				
	}

	function getChart() {				 
		 $dir = $this->getString('dir');
		 if(!$dir) {
		 	$_REQUEST['dir'] = 'DESC';
		 }
		 $condition = '';
		 if ($_REQUEST['form']) {
			$form = json_decode($_REQUEST['form']);
	 	 	if ($form->title){
	 	 		$condition = " where gtitle like '%{$form->title}%'";
	 	 	}
		 }

		 $query = "SELECT SQL_CALC_FOUND_ROWS id, model_pump, seria_pump, coord, pict, priority FROM chart {$condition}";
		 $this->executeListQuery($query);
	}

	function saveChart() {
		$id = $this->getInt('id');
		$model_pump = $this->getString ('model_pump');
		$seria_pump = $this->getString ('seria_pump');
		$coord = $this->getString ('coord');
		$priority = $this->getString ('priority');
		$folder = $_SERVER['DOCUMENT_ROOT'].'/catalog/upload/images/';
		$pict =  $this->uploadSingleFileNew($folder, '/catalog/upload/images/');

		$query = '';
		if($id) {
			$pict_query = '';
			if($pict) {
				$old_logo_query = "SELECT pict FROM `chart` WHERE id = $id";
				$old_logo = $this->getResult($old_logo_query);
				if($old_logo) {					
					unlink($_SERVER['DOCUMENT_ROOT'].$old_logo);
				}
				$pict_query = ", pict = '$pict'";
			} 
			$query = "UPDATE chart SET priority = '$priority', model_pump = '$model_pump', seria_pump = '$seria_pump', coord = '$coord' {$pict_query} WHERE id=$id";
		} else {
			$query = "INSERT INTO `chart`(`model_pump`, `seria_pump`, `coord`,`pict`,`priority`) 
					       VALUES 
					       				('$model_pump','$seria_pump', '$coord', '$pict','$priority')";
		}

		$result = $this->execute($query);
		if($result['result'] == 'success') {
			echo $id ? 1 : mysql_insert_id();
		} else {
			echo '0';
		}
	}
	
	function editChart() {
		$id = $this->getInt('id');
		$query = "SELECT * FROM chart WHERE id = $id";
		$result = $this->getAssoc($query);
		$query = "SELECT * FROM add_characteristics WHERE chart_id = $id";
		$dynamic_fields = $this->getAssocList($query);
		echo '{"results":'. json_encode($result).'}';
	}
	
	function deleteChart() {
		if ($dynamic_id = $this->getString('dynamic_id')) {
			$query = "DELETE FROM add_characteristics WHERE id = $dynamic_id";
		}
		else {
			$ids = $this->getString('ids');
			$query = "DELETE FROM chart WHERE id IN ($ids)";
		}
		$result = $this->execute($query);
		if($result['result'] == 'success') {
			echo '1';
		} else {
			echo '0';
		}
	}
}
?>
