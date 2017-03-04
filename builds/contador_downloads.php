<?php
include("../abrir_banco.php");
include("../session.php");

if( isset($_POST['template']) && ($_POST['template'] > 0) ){
	$template = $_POST['template'];

	$stmt = $conn->prepare("UPDATE templates
		set contador_downloads=contador_downloads+1
		where id=?"
		);
	$stmt->bind_param("i", $template);
	$stmt->execute();
}else{
	header("Location: /search/");
	exit;
}
?>