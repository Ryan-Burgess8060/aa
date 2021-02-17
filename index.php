<?php 

$login = False;
$username = "";
$password = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($_POST['username'] === 'ansible' && $_POST['password'] === 'abc123') {
		$login = True;
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