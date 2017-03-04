<?php
include("../abrir_banco.php");
include("../session.php");

if( !isset( $_SESSION['logado']) ){
	header("Location: /login/");
	exit;
}

$nome = htmlspecialchars($_POST['nome']);
$bio = htmlspecialchars($_POST['bio']);
$imagem = htmlspecialchars($_POST['imagem']);
if( strlen( $_POST['senha'] ) > 0){
	$senha = sha1($_POST['senha']);

	$stmt = $conn->prepare("UPDATE usuarios
		set nome=?, bio=?, imagem=?, senha=?
		where id=?"
		);
	$stmt->bind_param("ssssi", $nome, $bio, $imagem, $senha, $_SESSION['id']);
}else{
	$stmt = $conn->prepare("UPDATE usuarios
		set nome=?, bio=?, imagem=?
		where id=?"
		);
	$stmt->bind_param("sssi", $nome, $bio, $imagem, $_SESSION['id']);
}


if($stmt->execute()){
	$_SESSION['nome'] = $nome;
	$_SESSION['imagem'] = $imagem;
}

header("Location: /profile/");
?>