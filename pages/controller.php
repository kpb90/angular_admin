<?php
require_once "../../admin/dbconnect.php";
require_once "page_manager.php";

$_JSON = json_decode(file_get_contents('php://input'), true);	
$GLOBALS['_REQUEST_WITH_JSON'] = array_merge ((array)$_REQUEST,(array)$_JSON);
$task = $GLOBALS['_REQUEST_WITH_JSON']['task'];
session_start();

$page_manager = new PageManager();

switch($task) {
	case "getTree":
		$page_manager->getPages();
		break;
	case "getPageInfo":
		$page_manager->getPageInfo();
		break;	
	case "savePageInfo":
		$page_manager->savePageInfo();
	break;
	case "addPage":
		$page_manager->addPage();
		break;
	case "deletePage":
		$page_manager->deletePage();
		break;

}

?>