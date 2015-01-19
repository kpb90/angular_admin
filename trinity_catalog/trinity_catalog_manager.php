<?php
require_once "../dbaccessor.php";

	
class TrinityCatalogManager extends DBAccessor {
	
	function __construct() {	
		parent::__construct();				
	}
		
	function getTrinityCatalog() {				 
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

		 $query = "SELECT SQL_CALC_FOUND_ROWS id, category, gtitle, article, color, descr, concat ('/images/items/',pict) as pict, price, opt1, val1, opt2, val2, opt3, val3, size FROM trinity_catalog {$condition}";
		 $this->executeListQuery($query);
	}
	
	function saveTrinityCatalog() {
		$id = $this->getInt('id');
		$category = $this->getString('category');
		$gtitle = $this->getString('gtitle');
		$article = $this->getString('article');
		$color = $this->getString('color');
		$descr = $this->getString('descr');
		$pict = $this->getString('pict');
		$price = $this->getString('price');
		$opt1 = $this->getString('opt1');
		$val1 = $this->getString('val1');
		$opt2 = $this->getString('opt2');
		$val2 = $this->getString('val2');
		$opt3 = $this->getString('opt3');
		$val3 = $this->getString('val3');
		$size = $this->getString('size');
		$query = '';
		if($id) {
			$query = "UPDATE trinity_catalog SET category = '$category', gtitle = '$gtitle', article = '$article', color = '$color', descr = '$descr', pict = '$pict', price = '$price', opt1 = '$opt1', val1 = '$val1', opt2 = '$opt2', val2 = '$val2', opt3 = '$opt3', val3 = '$val3', size = '$size' WHERE id=$id";
		} else {
			$query = "INSERT INTO trinity_catalog (category, gtitle, article, color, descr, pict, price, opt1, val1, opt2, val2, opt3, val3, size) VALUES('$category', '$gtitle', '$article', '$color', '$descr', '$pict', '$price', '$opt1', '$val1', '$opt2', '$val2', '$opt3', '$val3', '$size')";
		}
		$result = $this->execute($query);
		if($result['result'] == 'success') {
			if ($id){
				echo '1';
			}
			else {
				echo mysql_insert_id();
			}
		} else {
			echo '0';
		}
	}
	
	function editTrinityCatalog() {
		$id = $this->getInt('id');
		$query = "SELECT * FROM trinity_catalog WHERE id = $id";
		$result = $this->getAssoc($query);
		echo json_encode($result);
	}
	
	function deleteTrinityCatalog() {
		$ids = $this->getString('ids');
		$query = "DELETE FROM trinity_catalog WHERE id IN ($ids)";
		$result = $this->execute($query);
		if($result['result'] == 'success') {
			echo '1';
		} else {
			echo '0';
		}
	}
}
?>
