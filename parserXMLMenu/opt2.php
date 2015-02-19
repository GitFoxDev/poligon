<?php
$url = 'http://www.wextor.ru/udata/content/menu/';

if (($xml = simplexml_load_file($url)) !== false) {
	foreach ($xml->items->item as $item)
	{
		$menu .= "<li><a href='".$item['link']."' id='".$item['id']."'>".$item['name']."</a></li>";
	}
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>RuPromo</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="opt1.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="menu-wrapper">
			<div id="menu"><?php echo $menu; ?></div>
		</div>
		<div style="width:390px;margin:0 auto;margin-top: 50px;">
			<b><a href="https://github.com/GitFoxDev/poligon/tree/master/parserXMLMenu" target="_blank">Исходные файлы на GitHub:</a></b><br>
			<b>style.css</b> - <i>общий файл стилей</i>
			<hr style="width:320px;height:1px;color:#d8d8d8;margin:5px 0px;">
			<b>opt1.html</b> - <i>загрузка XML меню с помощью JS</i><br>
			<b>opt1.js</b> - <i>JS разбор и прорисовка XML меню</i><br>
			<b>opt1.php</b> - <i>загрузка XML с другого домена</i><br>
			<hr style="width:320px;height:1px;color:#d8d8d8;margin:5px 0px;">
			<b>opt2.php</b> - <i>загрузка XML меню с помощью PHP</i>
			<hr style="width:375px;height:1px;color:#d8d8d8;margin:5px 0px;">
			<b>opt3.php</b> - <i>загрузка XML меню с помощью PHP + XSLT</i>
		</div>
	</body>
</html>