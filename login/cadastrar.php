<?php
include("../abrir_banco.php");
include("../session.php");

session_destroy();

$falhou = false;
$error = "";

if(empty($_POST['email']) || empty($_POST['senha'])){
	$falhou=true;
	$error= "empty_fields";
}

$email = htmlspecialchars($_POST['email'],ENT_QUOTES);
$senha = sha1($_POST['senha']);

if(mysqli_num_rows(mysqli_query($conn,"SELECT email from usuarios where email = '{$email}'")) > 0){
	$falhou = true;
	$error = "email_taken";
}

$captcha = false;
if( isset($_POST['g-recaptcha-response']) ){
	$captcha = $_POST['g-recaptcha-response'];
}
if( !$captcha ){
	$falhou = true;
	$error = "empty_captcha";
}

$context = stream_context_create(array(
    'http' => array('ignore_errors' => true),
));//gambiarra pra fazer o file_get_contents() parar de dar warning, sei la porque...
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret = 6LdV-RsTAAAAAGNRTbQhI0XhgoDeG6dWsPpS1ZAG&response = ".$captcha."&remoteip = ".$_SERVER['REMOTE_ADDR'],false, $context);//false, $context); adicionado pra gambiarra acima, antes não tinha isso
if( $response.success == false ){
	$falhou = true;
	$error = "captcha_failed";
}

if( !$falhou ){
	$imagem = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=http%3A%2F%2Fstonehearthbuilds.net16.net%2Fimagens%2Fprofile.png&s=150";
	$exploded=explode("@", $email);
	$nome = $exploded[0];

	$stmt = $conn->prepare("INSERT INTO usuarios 
		(email,senha,nome,imagem,data) VALUES 
		(?, ?, ?, ?, now())"
		);
	$stmt->bind_param("ssss", $email, $senha, $nome, $imagem);
	$stmt->execute();
	$_SESSION['logado'] = true;
	$_SESSION['id'] = mysqli_insert_id($conn);
	$_SESSION['nome'] = $nome;
	$_SESSION['imagem'] = $imagem;

	setcookie("login", "{$senha}_{$email}", time()+60*60*24*31, "/");

	header("Location: /profile?user={$_SESSION['id']}&new=true");
	exit;
}else{
	header("Location: index.php?error={$error}");
	exit;
}
?>