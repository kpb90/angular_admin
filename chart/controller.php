<?php
require_once "../../admin/dbconnect.php";
require_once "chart_manager.php";

session_start();
$chart_manager = new ChartManager();

switch($_SERVER['REQUEST_METHOD']) {
	case "GET":
	if (isset($_REQUEST['id'])){
		$chart_manager->editChart();
	} else {
		$chart_manager->getChart();
	}
	break;
	case "POST":
		$_POST = json_decode(file_get_contents('php://input'), true);	
		$chart_manager->saveChart();
	break;
	case "DELETE":
		$chart_manager->deleteChart();
	break;
}

?>