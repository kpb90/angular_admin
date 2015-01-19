<?php
require_once "../dbaccessor.php";

	
class NewsManager extends DBAccessor {
	
	function __construct() {	
		parent::__construct();				
	}
	
	
	function getNews() {				 
		 $dir = $this->getString('dir');
		 if(!$dir) {
		 	$_REQUEST['dir'] = 'DESC';
		 }
		 		 
		 $condition = '';
		 if ($_REQUEST['form']) {
			$form = json_decode($_REQUEST['form']);
	 	 	if ($form->title){
	 	 		$condition = " where text_small like '%{$form->title}%'";
	 	 	}
		 }
		//(array)json_decode($_REQUEST['required']);
		 $query = "SELECT SQL_CALC_FOUND_ROWS id,text_small, text, archive, main, DATE_FORMAT(date, '%d.%m.%Y') AS date, priority FROM news {$condition}";
		 $this->executeListQuery($query);
	}
	
	function saveNews() {
		$id = $this->getInt('id');
		$text_small = $this->getString('text_small');
		$date = $this->getString('date');
		$date = preg_replace("/(\d\d).(\d\d).(\d\d\d\d)/","$3$2$1",$date);	
		$descr = $this->getString('text');
		$archive = $this->getInt('archive'); 
		$active = $this->getInt('main');
		$priority = $this->getInt('priority');
		$query = '';
		if($id) {
			$query = "UPDATE news SET text_small='$text_small', date='$date', text='$descr', archive=$archive, main=$active, priority=$priority WHERE id=$id";
		} else {
			$query = "INSERT INTO news (id, text_small, date, text, archive, main, priority) VALUES(DEFAULT, '$text_small', '$date', '$descr', $archive, $active, $priority)";
		}
		$result = $this->execute($query);
		if($result['result'] == 'success') {
			echo '1';
		} else {
			echo '0';
		}
	}
	
	function editNews() {
		$id = $this->getInt('id');
		$query = "SELECT * FROM news WHERE id = $id";
		$result = $this->getAssoc($query);
		echo json_encode($result);
	}
	
	function deleteNews() {
		$ids = $this->getString('ids');
		$query = "DELETE FROM news WHERE id IN ($ids)";
		$result = $this->execute($query);
		if($result['result'] == 'success') {
			echo '1';
		} else {
			echo '0';
		}
	}
	function getText() {
		$id = $this->getInt('id');
		$query = "SELECT text FROM news WHERE id=$id";			
		$text = $this->getResult($query);					
		echo $text;
	}
	
	function saveText() {
		$id = $this->getInt('id');
		$text = $this->getString('text');
		$query = "UPDATE news SET text='$text' WHERE id=$id";
		$result = $this->execute($query);
		if($result['result'] == 'success') {
			echo '1';
		} else {
			echo '0';
		}			
	}
}
?>
