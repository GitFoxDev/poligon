<?php

class Downloads
{
	private $filePath = 'files';
	private $fileName;
	private $isDirExist = false;
	private $isFileExist = false;
	
	public function __construct($filepath)
	{
		$temp = explode('/', $filepath);
		if ((count($temp) == 2) AND ($temp[0] == $this->filePath)) {
			$this->fileName = $temp[1];
			$this->isDirExist = true;
			if (file_exists($this->fileName)) {
				$this->isFileExist = true;
			}
		}
	}
	
	private function setCookies()
	{
		$url = parse_url($_SERVER['HTTP_REFERER']);
		if ($url !== false) {
			setcookie('referrer', $url['host']);
		}
	}

	public function giveFile()
	{
		if ($this->isDirExist AND $this->isFileExist) {
			$this->setCookies();
			if (ob_get_level()) {
				ob_end_clean();
			}
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
		} else {
			echo 'Ooops!';
		}
	}
}

if (isset($_GET['download'])) {
	$downloads = new Downloads($_GET['download']);
	$downloads->giveFile();
}