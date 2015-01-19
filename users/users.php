<?php
include_once "../dbaccessor.php";
class UserManager extends DBAccessor {
	 
	function __construct() {	
		parent::__construct();				
	}
	
	function __destruct() {		
		parent::__destruct();	
	}	
	 
	function login() {
		$login = trim($this->getString('login'));
		$pwd = trim($this->getString('pwd'));
		$query = "SELECT user_id FROM cms2_users " .
				"WHERE login = '$login' AND password = '$pwd'";						
									
		$userInfo = $this->getAssoc($query);		
		$result = array(); 
		if($userInfo) {			
			$id = $userInfo['user_id']; 
			$_SESSION['user_id'] = $id;
			$result['result'] = 1;	
							
		} else {
			$result['result'] = 0;			
		}

		echo json_encode($result);
	}
	function auntefitication() {
		$result = array('result'=>0); 
		if(array_key_exists('user_id', $_SESSION)) {
			$result['result'] = 1;	
		}
		echo json_encode($result);
	}

	function logout() {
		session_destroy();
		echo json_encode(array('result'=>1));
	}
} 
?>