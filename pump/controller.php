<?php
session_start();
require_once "../../admin/dbconnect.php";
require_once "pump_manager.php";

$pump_manager = new PumpManager();

switch($_SERVER['REQUEST_METHOD']) {
	case "GET":
	if (isset($_REQUEST['id'])){
		$pump_manager->editPump();
	} else {
		$pump_manager->getPump();
	}
	break;
	case "POST":
		$_POST = json_decode(file_get_contents('php://input'), true);	
		$pump_manager->savePump();
	break;
	case "PUT":
		$_POST = json_decode(file_get_contents('php://input'), true);	
		if ($_POST['task'] == "add_new_field") {
			$pump_manager->saveNewCharactPump();				
		} else {
			$pump_manager->savePump();
		}
	break;
	case "DELETE":
		$pump_manager->deletePump();
	break;
}

?>