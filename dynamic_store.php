<?php
include_once "../admin/dbconnect.php";
$GLOBALS['DB_CONNECTION'] = dbconnect_new();
$response = array();
if ($_REQUEST['type']=='composition_of_fluid'){
	$query = "SELECT distinct environment FROM  `chemical_resistance` ";
	$r=$GLOBALS['DB_CONNECTION']->query ($query);
	while ($r&&$row=$r->fetch_assoc()){
		$response[$row['environment']] = $row['environment'];
	}
	echo json_encode($response);
}
?>