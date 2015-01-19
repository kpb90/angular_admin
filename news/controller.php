<?php
require_once "../../admin/dbconnect.php";
require_once "news_manager.php";

session_start();
$news_manager = new NewsManager();

switch($_SERVER['REQUEST_METHOD']) {
	case "GET":
		$news_manager->getNews();
	break;
	case "POST":
	case "PUT":
		$_POST = json_decode(file_get_contents('php://input'), true);	
		$news_manager->saveNews();
	break;
	case "DELETE":
		$news_manager->deleteNews();
	break;
}

?>