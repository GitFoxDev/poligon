<?php

class History
{
	private $message = null;
	private $db;
	
	public function __construct(PDO $db)
	{
		if (!isset($db) OR get_class($db) != 'PDO') {
			$this->message = 'Ошибка подключения к БД';
			return;
		}
		
		$this->db = $db;
	}
	
	public function getHistory()
	{
		if ($this->message == null)
		{
			$sql = "SELECT id,name FROM files ORDER BY id DESC LIMIT 5";
			$stmt = $this->db->query($sql);

			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			while ($row = $stmt->fetch()) {
				$export .= '<div class="id">ID: '.$row['id'].'</div>';
				$export .= '<div class="name"><a href="#" onClick="getBody('.$row['id'].');">'.$row['name'].'</a></div>';
			}

			$this->message = $export;
		}
	}
	
	public function getMessage()
	{
		return $this->message;
	}
}