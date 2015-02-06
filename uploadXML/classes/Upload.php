<?php

class Upload
{
	//private $db;
	private $status = ERROR_STATUS;
	private $message;
	private $contentDOM = null;
	private $contentXML = null;
			
	public function __construct($file, PDO $db)
	{
		if (!isset($db) OR get_class($db) != 'PDO') {
			$this->message = 'Ошибка подключения к БД';
			return;
		}
		
		if ($this->isXML($file))
			$this->addInDB($file['name'], $db);
	}
	
	private function isXML($file)
	{
		if ($file['error'] == UPLOAD_ERR_OK)
			{
				$file['ext'] = substr(strrchr($file['name'], '.'), 1);
				//echo $file['ext'];
				//print_r($file);
				if($file['ext'] == 'xml' AND $file['type'] == 'text/xml')
				{
					$dom = new DOMDocument;
					$file = file_get_contents($file['name']);
					$dom->loadXML($file);
					if ($dom)
					{
						$this->contentDOM = $dom;
						$this->contentXML = $file;
						return true;
					}
				}
			}
		$this->message = 'Файл не является XML';
		return false;
	}
	
	private function addInDB($fileName, $db)
	{
		$sql = "INSERT INTO files(name,data) VALUES(:name,:data)";
		$stmt = $db->prepare($sql);

		$stmt->bindParam(':name',$fileName);
		$stmt->bindParam(':data',$this->contentXML, PDO::PARAM_LOB);

		try {
			$stmt->execute();
		} catch (PDOException $e) {
			$this->message = 'Не получилось сохранить файл';
			return;
		}
		
		$this->status = SUCCESS_STATUS;
		$this->message = 'Файл успешно загружен';
	}

	public function getStatus()
	{
		return $this->status;
	}
	
	public function getMessage()
	{
		return $this->message;
	}
}
