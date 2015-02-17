<?php
$dsn = "mysql:host=localhost;dbname=todo";
$user = "root";
$pass = "";

function ConnectDB($dsn, $user, $pass)
{
	try {
		$db = new PDO($dsn, $user, $pass);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		$message = 'Ошибка подключения к БД!';
		return false;
	}
	return $db;
}

function validatePhone($phone)
{
	$reg = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
	return (preg_match($reg, $phone) ? true : false);
}

function validateEmail($email)
{
	if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
		return true;
	return false;
}

function mCode($text)
{
	return $text ^ str_pad( '', strlen($text), md5('CoDeEmPhOne'));
}

function mDeCode($text)
{
	return $text ^ str_pad( '', strlen($text), md5('CoDeEmPhOne'));
}

if (isset($_POST['add'])) {
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	if (validatePhone($phone) AND validateEmail($email)) {
		if (($db = ConnectDB($dsn, $user, $pass)) !== false) {
		
			$sql = "INSERT INTO base(phone,email) VALUES(:phone,:email)";
			$stmt = $db->prepare($sql);

			$stmt->bindParam(':phone', mCode($phone));
			$stmt->bindParam(':email', hash('md5', $email.'emailTwow'));

			if (!$stmt->execute())
				$message = 'Не удалось добавить телефон';
			else
				$message = 'Телефон добавлен успешно';
		}
	}
	else
		$message = 'Не верно указаны телефон или электронная почта.';
}

if (isset($_POST['retreive'])) {
	$email = $_POST['email'];
	if (validateEmail($email)) {
		if (($db = ConnectDB($dsn, $user, $pass)) !== false) {
			$sql = "SELECT phone, email FROM base WHERE email = :email";
			$stmt = $db->prepare($sql);
			$stmt->execute(array('email' => hash('md5', $email.'emailTwow')));
			
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			while ($row = $stmt->fetch()) {
				$message = 'Your phone: '.mDeCode($row['phone']);
			}		
		}
	}
	else
		$message = 'Не верно указана электронная почта.';
}

?>

<html>
	<head>
		<style>body { width: 600px; } .form { float:left; width:300px; } .status { text-align: center; font-size: 18px; }</style>
	</head>
	<body>
		<div class="status"><?php echo $message; ?></div>
		<div class="form">
			<form method="POST">
			<fieldset>
				<legend>Add your phone number</legend>
				<b>Option 1. Add your phone number</b><br><br>
				
				Enter you phone:<br>
				<input name="phone" type="text" required="required" placeholder="555 555 5555"><br><br>
				
				Enter you e-mail:<br>
				<input name="email" type="email" required="required"><br><br>
				
				You will be able to retrieve your phone number later on using your e-mail.<br><br>
				
				<button type="submit" name="add">Submit</button>
			</fieldset>
			</form>
		</div>
			
		<div class="form">
			<form method="POST">
			<fieldset>
				<legend>Retreive your phone number</legend>
				<b>Option 2. Retrieve your phone number</b><br><br>
				
				Enter you e-mail:<br>
				<input name="email" type="email" required="required"><br><br>
				
				The phone number will be e-mailed to you.<br><br>
				
				<button type="submit" name="retreive">Submit</button>
			</fieldset>
			</form>
		</div>
	</body>
</html>