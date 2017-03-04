<?php
include("../abrir_banco.php");
include("../session.php");

$lista_de_tags = $_POST["tags"];

$stmt = $conn->prepare("INSERT INTO tags (nome) VALUES (?)");

foreach( $lista_de_tags as $tag ) {
	$tag = htmlspecialchars(trim( $tag ));

	if(!empty($tag)){
		$stmt->bind_param("s", $tag);
		$stmt->execute();
	}
}

header("Location: create_tag.php?tags=created");
?>