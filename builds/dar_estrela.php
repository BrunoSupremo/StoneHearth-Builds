<?php
include("../abrir_banco.php");
include("../session.php");

if( isset($_SESSION['logado']) && ($_SESSION['logado']=="true") && isset($_POST['template']) && ($_POST['template'] > 0) ){
	$template = $_POST['template'];

	$stmt = $conn->prepare("INSERT INTO estrelas 
		(usuario,template,data) VALUES 
		(?, ?, now())"
		);
	$stmt->bind_param("ii", $_SESSION['id'], $template);
	$stmt->execute();
}else{
	header("Location: /search/");
	exit;
}
?>