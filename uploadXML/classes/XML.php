<?php

class XML
{
	private $db;
	private $body;
	
	public function __construct(PDO $db, $id)
	{
		if (!isset($db) OR get_class($db) != 'PDO') {
			$this->message = 'Ошибка подключения к БД';
			return;
		}
		$this->db = $db;
		
		if (!$this->isID($id)){
			$this->message = 'Ошибка загрузки тела файла';
			return;
		}
	}
	
	private function isID($id)
	{
		if (!is_numeric($id))
			return false;
		
		$sql = "SELECT data FROM files WHERE id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$row = $stmt->fetch();
		$this->body = $row['data'];
		
		return true;
	}
	
	public function getBody()
	{
		if ($this->message == null)
		{
			$dom = new DOMDocument;
			$dom->loadXML($this->body);
			$xml = simplexml_import_dom($dom);
			$json = json_encode($xml);
			$array = json_decode($json, true);
			$this->let($array, 1);
			//print_r($array);
			//$this->message = htmlspecialchars($this->body);
			//$this->message = htmlspecialchars($json);
		}
	}
	
	private function let($str, $level)
	{
		$separator = '___';
		foreach ($str as $key => $item)
		{
			if (is_array($item))
			{
				$this->message .= str_repeat($separator, $level++).$key.'<br>';
				$this->let($item, $level);
			}
			else
				$this->message .= str_repeat($separator, $level).$key.'<br>';
				
		}
	}
	
	public function getMessage()
	{
		return $this->message;
	}
}