<?php
include("../abrir_banco.php");
include("../session.php");
?>
<html>
<head>
	<title>Login - Help</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main>
		<section>
			<p>Fill in your e-mail and a link to a new password will be sent to it. It will be a randomly generated password and it will only be active after acessing the link sent, else the old one will not change. You can later change that random password to anything you like in your profile settings.</p>
			<p>As this site uses a free hosting, chances are that this e-mail will end at your junk/spam folder... :( So remember to look there if you don't find the e-mail.</p>
			<form action="reset_password.php" enctype="multipart/form-data" method="POST">
				<input type="email" name="email" required placeholder="The e-mail you used to create your account">
				<input type="hidden" name="resetar" value="sim">
				<input type="submit" id="submit_login">
			</form>
		</section>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>