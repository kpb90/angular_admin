<?php
require_once "../../admin/dbconnect.php";
require_once "trinity_catalog_manager.php";

session_start();
$trinity_catalog_manager = new TrinityCatalogManager();

switch($_SERVER['REQUEST_METHOD']) {
	case "GET":
		$trinity_catalog_manager->getTrinityCatalog();
	break;
	case "POST":
		$_POST = json_decode(file_get_contents('php://input'), true);	
		$trinity_catalog_manager->saveTrinityCatalog();
	break;
	case "PUT":
		$_POST = json_decode(file_get_contents('php://input'), true);	
		$trinity_catalog_manager->saveTrinityCatalog();
	break;
	case "DELETE":
		$trinity_catalog_manager->deleteTrinityCatalog();
	break;
}

?>