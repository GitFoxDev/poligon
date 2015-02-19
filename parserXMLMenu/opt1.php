<?php

header("Content-Type: text/xml");
$filename = $_GET['url'];
$handle = fopen($filename, "rb");
$contents = '';
while (!feof($handle))
{
	$contents .= fread($handle, 8192);
}
fclose($handle);
echo $contents;