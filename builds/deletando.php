<?php
include("../abrir_banco.php");
include("../session.php");

if( isset($_SESSION['logado']) ){
	$template_id = $_POST["template_id"];
	$result = mysqli_query($conn,"
		SELECT usuario
		FROM templates
		WHERE id={$template_id}
		");
	$template = mysqli_fetch_assoc($result);
	if($template['usuario'] != $_SESSION['id']){
		header("Location: /profile/");
		exit;
	}

	$stmt = $conn->prepare("DELETE FROM templates 
		WHERE id = ?"
		);
	$stmt->bind_param("i", $template_id);
	$stmt->execute();

	$stmt = $conn->prepare("DELETE FROM tags_nos_templates 
		WHERE template = ?"
		);
	$stmt->bind_param("i", $template_id);
	$stmt->execute();

	$stmt = $conn->prepare("DELETE FROM estrelas 
		WHERE template = ?"
		);
	$stmt->bind_param("i", $template_id);
	$stmt->execute();

	$stmt = $conn->prepare("DELETE FROM comentarios 
		WHERE template = ?"
		);
	$stmt->bind_param("i", $template_id);
	$stmt->execute();

	header("Location: /profile/");
	exit;
}else{
	header("Location: /login/");
	exit;
}
?>