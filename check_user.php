<?php
addToLog('СЕССИЯ: '.print_r($_SERVER,'true'));
$logged  = false;
if(array_key_exists('user_id', $_SESSION)) {
	$user_id = $_SESSION['user_id'];
	if($user_id) {
		$logged = true;
	}
} 
if(!$logged) {
	if ($_SERVER['REQUEST_URI'] != '/catalog/users/controller.php')
	{
		header('Location:/catalog/');
	}
}

function addToLog($message,$file='log333.txt') 
{
        if (!$message)
	{
		file_put_contents($file, '');
		return;
	}
	$handle = fopen($file, "a+");
	fwrite($handle, date("Y-m-d H:i:s"). ': ' . $message . PHP_EOL);
	fclose($handle);			
}
?>