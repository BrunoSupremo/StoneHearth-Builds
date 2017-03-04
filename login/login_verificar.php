<?php
include("../abrir_banco.php");
include("../session.php");

$email = htmlspecialchars($_POST['email'],ENT_QUOTES);
$senha = sha1($_POST['senha']);

$result = mysqli_query($conn,"SELECT id, nome, imagem from usuarios where email = '$email' and senha = '$senha'");
if(mysqli_num_rows($result) > 0){
	$dados = mysqli_fetch_assoc($result);
	$_SESSION['logado'] = true;
	$_SESSION['id'] = $dados['id'];
	$_SESSION['nome'] = $dados['nome'];
	$_SESSION['imagem'] = $dados['imagem'];

	setcookie("login", "{$senha}_{$email}", time()+60*60*24*31, "/");

	if( isset($_POST['veio_do_upload']) ){
		// veio_do_upload, volta pra lá
		header("Location: /upload/");
		exit;
	}else{
		// login neutro, vai pro perfil
		header("Location: /profile/");
		exit;
	}
}
else{
	header("Location: /login/?error=login");
	exit;
}
?>