<?php 
// Ryan Burgess
// 2/17/21
// Attacking Authentication
// 
// I used a database to keep track of the ip of the user sending failed attempts. For every failed attempt, I set a sleep function to stall the code for 1 second per failed attempt. I also set it to record what usernames and passwords the ip is sending to see what was causing the failed attempts and maybe be able to see what kind of brute force attack was being attempted
// With this prevention method, it now takes () to get the correct username and password combination.

$login = False;
$username = "";
$password = "";
require_once 'database.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	try {
		$query = 'SELECT FailedAttempts FROM authentication WHERE IP=:ip ORDER BY ID DESC LIMIT 1;';
		$dbquery = $myDBconnection -> prepare($query);
		$dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
		$dbquery -> execute();
		$result = $dbquery -> fetch();
	} catch (PDOException $e) {
		$error_message = $e->getMessage();
		echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
	}
	if (!empty($result["FailedAttempts"]) || $result["FailedAttempts"] != 0) {
		sleep($result["FailedAttempts"]);
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($_POST['username'] === 'ansible' && $_POST['password'] === 'abc123') {
		$login = True;
		try {
			$query = 'INSERT INTO authentication (IP, Username, Password, Date, FailedAttempts) VALUES (:ip, :user, :pass, NOW(), 0);';
			$dbquery = $myDBconnection -> prepare($query);
			$dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
			$dbquery -> bindValue(':user', $username); 
			$dbquery -> bindValue(':pass', $password);
			$dbquery -> execute();
		} catch (PDOException $e) {
			$error_message = $e->getMessage();
			echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
		}
	} else {
		try {
			$query = 'SELECT FailedAttempts FROM authentication WHERE IP=:ip ORDER BY ID DESC LIMIT 1;';
			$dbquery = $myDBconnection -> prepare($query);
			$dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
			$dbquery -> execute();
			$result = $dbquery -> fetch();
		} catch (PDOException $e) {
			$error_message = $e->getMessage();
			echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
		}
		if (empty($result["FailedAttempts"])) {
			try {
				$query = 'INSERT INTO authentication (IP, Username, Password, Date, FailedAttempts) VALUES (:ip, :user, :pass, NOW(), 1);';
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
				$dbquery -> bindValue(':user', $username); 
				$dbquery -> bindValue(':pass', $password);
				$dbquery -> execute();
			} catch (PDOException $e) {
				$error_message = $e->getMessage();
				echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
			}
		} else {
			$result["FailedAttempts"] = $result["FailedAttempts"] + 1;
			try {
				$query = 'INSERT INTO authentication (IP, Username, Password, Date, FailedAttempts) VALUES (:ip, :user, :pass, NOW(), :result);';
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> bindValue(':ip', $_SERVER['REMOTE_ADDR']);
				$dbquery -> bindValue(':user', $username); 
				$dbquery -> bindValue(':pass', $password);
				$dbquery -> bindValue(':result', $result["FailedAttempts"]);
				$dbquery -> execute();
			} catch (PDOException $e) {
				$error_message = $e->getMessage();
				echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
			}
		}
	} 
}

?>
<!doctype html>
<html lang="en-US">
	<head>
		<title>Login page</title>
		<meta name="description" content="Login page">
		<meta name="author" content="Russell Thackston">
		
		<style>
			body {
			  background-color: linen;
			  padding: 10px;
			}

			fieldset{
				max-width: 300px;
				border-radius: 10px;
			}
			
			label{
				width: 75px;
				display: inline-block;
				padding: 5px;
			}
		</style>
		
	</head>
	<body>
		<?php if ($login) { ?>
			<div>
				Login successful.
			</div>
		<?php } ?>
		<form action="index.php" method="post">
			<fieldset>
				<legend>Login</legend>
				<label for="username">Username</label>
				<input name="username" id="username" type="text" value="<?php echo $username; ?>">
				<br>
				<label for="password">Password</label>
				<input name="password" id="password" type="password" value="<?php echo $password; ?>">
				<br>
				<input type="submit" value="Login">
			</fieldset>
		</form>
	</body>
</html>