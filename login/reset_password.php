<?php
include("../abrir_banco.php");
include("../session.php");
if( isset($_SESSION['logado']) ){
	header("Location: /profile/");
	exit;
}
$email = htmlspecialchars($_POST['email'],ENT_QUOTES);

if( isset($_POST["resetar"]) && $_POST["resetar"] == "sim"){
	$result = mysqli_query($conn,"SELECT nome from usuarios where email = '{$email}'");
	if(mysqli_num_rows($result) > 0){
		$dados = mysqli_fetch_assoc($result);
		$recuperar_senha = ''. mt_rand();
		$mensagem=
		"{$dados['nome']}, as requested on the login help page of the StoneHearth Builds site, below is the link that once clicked, will lead you into the reset password page.<br><br>
		<a href='www.stonehearthbuilds.net16.net/login/reset_password.php?email={$email}&code={$recuperar_senha}'>www.stonehearthbuilds.net16.net/login/reset_password.php?email={$email}&code={$recuperar_senha}</a><br>
		Ignoring this e-mail will keep the old password.";

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		$headers .= 'From: StoneHearth Builds<new_password@stonehearthbuilds.net16.net>' . "\r\n";

		if(mail($email, "StoneHearth Builds Password Reset", $mensagem, $headers)){
			$stmt = $conn->prepare("UPDATE usuarios
				set recuperar_senha=?
				where email=?"
				);
			$stmt->bind_param("ss", $recuperar_senha, $email);
			$stmt->execute();
		}
	}
}
?>
<html>
<head>
	<title>Reset Password</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main>
		<section>
			<?php
			if( isset($_GET["email"]) && isset($_GET["code"]) ){
				$email = htmlspecialchars($_GET["email"]);
				$code = htmlspecialchars($_GET["code"]);
				if( mysqli_num_rows(mysqli_query($conn,"SELECT nome from usuarios where email = '{$email}' and recuperar_senha = '{$code}'")) > 0 ){
					?>
					<form action="reset_password.php" enctype="multipart/form-data" method="POST">
						<p>Type your new password:</p>
						<input type="password" name="nova_senha" required placeholder="new password">
						<input type="hidden" name="email" value="<?php echo $email; ?>">
						<input type="submit" id="submit_login">
						<p>You will then be send to the login page where you can enter using your new password.</p>
					</form>
					<?php
				}else{
					?><p>Error</p><?php
				}
			}else if( isset($_POST["nova_senha"]) ){
				$senha = sha1($_POST['nova_senha']);
				$email = $_POST['email'];
				$stmt = $conn->prepare("UPDATE usuarios
					set recuperar_senha=NULL, senha=?
					where email=?"
					);
				$stmt->bind_param("ss", $senha, $email);
				$stmt->execute();
				header("Location: /login/");
				exit;
			}else{
				?>
				<p>An e-mail was send to <span class="mensagem_de_erro"><?php echo $email; ?></span> with a link to reset your password.</p>
				<?php
			}
			?>
		</section>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>