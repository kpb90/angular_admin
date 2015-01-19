<?php
	require_once "header.php";
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Страницы сайта</title>
<?php
	echo $common_links;
	echo $ext_links; 
	echo $after_ext_links;
?>	
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
 	<script type="text/javascript" src="js/extra/ext_ckeditor.js"></script> 	 	
 	<script type="text/javascript" src="js/helper.js"></script> 
 	  	
	<script type="text/javascript" src ="pages/pages.js"></script> 	
</head>
<body>
	<?php echo $common_top; ?>
	<div id="container">		
		<div id="content">		
			<div id="code"></div>		
		</div>		
	</div>	
	<div id="page_editor">&nbsp;</div>
</body>
</html>