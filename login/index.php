<?php
include("../abrir_banco.php");
include("../session.php");
if( isset($_SESSION['logado']) ){
	header("Location: /profile/");
	exit;
}
?>
<html>
<head>
	<title>Login</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
	<?php include("../nav.php"); ?>
	<main id="login">
		<section>
			<h1>Login</h1>
			<?php
			if( isset($_GET["error"]) && $_GET["error"] == "login"){
				?>
				<p class="mensagem_de_erro">E-mail or password incorrect.</p>
				<p>Forgot your password? <a href="/login/help.php" class="link_padrao">Click here</a>.</p>
				<?php
			}

			if( isset($_GET['last_page']) && ($_GET['last_page'] == "upload") ){
				?>
				<p class="mensagem_de_erro">Sorry, you need to login before uploading.</p>
				<?php
			}
			?>
			<form action="login_verificar.php" enctype="multipart/form-data" method="POST">
				<p>E-mail:</p>
				<input type="email" name="email" required placeholder="your_email@domain.com">
				<p>Password:</p>
				<input type="password" name="senha" required placeholder="password">
				<?php
				// Se chegou pela pagina de upload, manda de volta pra lá depois do login
				if( isset($_GET['last_page']) && ($_GET['last_page'] == "upload") ){
					?>
					<input type="hidden" name="veio_do_upload" value="sim">
					<?php
				}
				?>
				<input type="submit" id="submit_login">
			</form>
		</section>
		<section>
			<h1>Not a user yet?</h1>
			<?php
			if( isset($_GET["error"]) ){
				if( $_GET["error"] == "empty_fields" ){
					?>
					<p class="mensagem_de_erro">Empty fields, please fill then correctly.</p>
					<?php
				}else if( $_GET["error"] == "email_taken" ){
					?>
					<p class="mensagem_de_erro">This e-mail is already in use. If it is your e-mail, try to login with it, and if it doesn't work, try to recover the password.</p>
					<?php
				}else if( $_GET["error"] == "empty_captcha" ){
					?>
					<p class="mensagem_de_erro">You forgot the reCaptcha. Try again after doing it.</p>
					<?php
				}else if( $_GET["error"] == "captcha_failed" ){
					?>
					<p class="mensagem_de_erro">reCaptcha failed. Are you a robot? Try again.</p>
					<?php
				}
			}
			?>
			<p>Fill these basics fields and you will be ready. (You can add more details later)</p>
			<form action="cadastrar.php" enctype="multipart/form-data" method="POST">
				<p>E-mail:</p>
				<input type="email" name="email" required placeholder="your_email@domain.com">
				<p>Password:</p>
				<input type="password" name="senha" required placeholder="password">

				<div id="div_recaptcha" class="g-recaptcha" data-sitekey="6LdV-RsTAAAAAKITaJVQoLkPiyA0vjntM-PlgHVt"></div>

				<input type="submit" id="submit_register">
			</form>
		</section>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>