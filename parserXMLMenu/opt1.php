<?php

header("Content-Type: text/xml");
$url = $_GET['url'];

if ($curl = curl_init()) {
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8","Expect: 100-continue"));
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	$data = curl_exec($curl);
	curl_close($curl);
	if ($data !== false) {
		echo $data;
	}
}