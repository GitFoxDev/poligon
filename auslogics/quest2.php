<?php

function searchSpy($array)
{
	$arrayCount = array_count_values($array);
	foreach ($arrayCount as $key => $value)
	{
		if ($value > 1) {
			echo $key;
			break;
		}
	}
}