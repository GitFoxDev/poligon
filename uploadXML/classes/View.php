<?php

class View
{
	public static function show($viewStatus = null, $viewMessage = null)
	{
		include_once 'views/view.php';
	}
	
	public static function showDrop($viewStatus, $viewMessage)
	{
		echo $viewStatus.';'.$viewMessage;
	}
}
