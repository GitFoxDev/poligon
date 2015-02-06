<?php

define('SITEPATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
define('SITEURL', 'http://'.$_SERVER['HTTP_HOST'].'/');
define('SUCCESS_STATUS', 1);
define('ERROR_STATUS', 2);

$config = require_once 'config.php';

function __autoload($className)
{
	$file = SITEPATH.'classes'.DIRECTORY_SEPARATOR.$className.'.php';

	if (file_exists($file) === false) {
		return false;
	}
	require_once "$file";
}

try {
	$db = new PDO($config['db']['dsn'], $config['db']['user'], $config['db']['pass']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die('Ошибка подключения к БД, дальнейшие действия не возможны!');
}

if ($_POST['act'] == 'history') {
	$history = new History($db);
	$history->getHistory();
	echo $history->getMessage();
}

if ($_POST['act'] == 'body') {
	$xml = new XML($db, $_POST['id']);
	$xml->getBody();
	echo $xml->getMessage();
}

if(isset($_POST['upload'])) {
	//var_dump($_FILES);
	$file = $_FILES['file'];
	
	$upload = new Upload($file, $db);
	$viewStatus = $upload->getStatus();
	$viewMessage = $upload->getMessage();

	//Вывод соответствующей информации для разного типа загрузки
	if ($_POST['notdrop'] == 1)
		View::show($viewStatus, $viewMessage);
	else
		View::showDrop($viewStatus, $viewMessage);
}

if(!isset($_POST['upload']) AND !isset($_POST['act']))
	View::show();
