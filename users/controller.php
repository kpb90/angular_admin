<?php
session_start();
$_POST = json_decode(file_get_contents('php://input'), true);	
include_once "../../admin/dbconnect.php";
include_once "users.php";

$task = $_POST['task'];


$userManager = new UserManager();

switch($task) {
	case "login":		
		$userManager->login();
		break;
		case "auntefitication":		
		$userManager->auntefitication();
		break;
		case "logout":		
		$userManager->logout();
		break;
}	
?>
