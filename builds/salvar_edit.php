<?php
include("../abrir_banco.php");
include("../session.php");

$template_id = $_POST["template_id"];
$nome = htmlspecialchars(trim( $_POST["nome"] ));
$descricao = htmlspecialchars(trim( $_POST["descricao"] ));
$lista_de_tags = $_POST["tags"];

$stmt = $conn->prepare("DELETE FROM tags_nos_templates 
	WHERE template = ?"
	);
$stmt->bind_param("i", $template_id);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO tags_nos_templates 
	(tag, template) VALUES 
	(?, ?)"
	);
foreach( $lista_de_tags as $tag ) {
	$stmt->bind_param("ii", $tag, $template_id);
	$stmt->execute();
}

$stmt = $conn->prepare("UPDATE templates 
	SET nome = ?, descricao = ?
	WHERE id = ?"
	);
$stmt->bind_param("ssi", $nome, $descricao, $template_id);
$stmt->execute();

header("Location: index.php?id={$template_id}");
?>