<?php
include("../abrir_banco.php");
include("../session.php");

if( isset($_POST['texto']) && (trim($_POST['texto'])!="") && isset($_POST['template_id']) && ($_POST['template_id']>0) ){

	$texto = htmlspecialchars(trim($_POST['texto']));
	$template_id = $_POST['template_id'];

	$result = mysqli_query($conn,"
		SELECT id, usuario, texto
		FROM comentarios
		WHERE template = {$template_id}
		ORDER BY data DESC
		LIMIT 1
		");
	$ultimo_comentario = mysqli_fetch_assoc($result);
	if( $ultimo_comentario['usuario'] == $_SESSION['id'] ){
		$stmt = $conn->prepare("UPDATE comentarios
			set texto=?, data=now()
			where id=?"
			);
		$texto = $ultimo_comentario['texto'] . "\n" . $texto;
		$stmt->bind_param("si", $texto, $ultimo_comentario['id']);
		$stmt->execute();
	}else{
		$result_dono_da_build = mysqli_query($conn,"
			SELECT usuario
			FROM templates
			WHERE id = {$template_id}
			");
		$dono_da_build = mysqli_fetch_assoc($result_dono_da_build);
		$stmt = $conn->prepare("INSERT INTO comentarios 
			(texto,usuario,data,template) VALUES 
			(?, ?, now(), ?)"
			);
		$stmt->bind_param("sii", $texto, $_SESSION['id'], $template_id);
		$stmt->execute();
	}

	header("Location: /builds/?id={$template_id}");
	exit;
}else{
	header("Location: /search/");
	exit;
}
?>