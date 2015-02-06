<?php
if ($viewStatus == 2)
	$viewClass = ' class="error"';
elseif ($viewStatus == 1)
	$viewClass = ' class="success"';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    
	<title>Загрузка XML файла для PXL</title>  
	<link rel="stylesheet" href="assets/style.css">
    
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script src="assets/script.js"></script>
</head>
<body>    
	<form enctype="multipart/form-data" method="POST">
		<div id="dropZone">
			<input type="file" name="file" id="file-input" />
			<input type="hidden" name="notdrop" value="1" /> 
			<button type="upload" name="upload" id="file-submit">Загрузить</button><br>
			или перетащите файл сюда
		</div>
		<div id="resultZone"<?php echo $viewClass; ?>><?php echo $viewMessage; ?></div>
		<div id="historyZone"></div>
		<div id="bodyZone"></div>
	</form>
	</body>
</html>