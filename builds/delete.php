<?php
include("../abrir_banco.php");
include("../session.php");

if( isset($_SESSION['logado']) ){
	if( isset($_GET['build']) && ($_GET['build'] >0) ){
		$build_id = $_GET['build'];
		$result = mysqli_query($conn,"
			SELECT usuario, nome
			FROM templates
			WHERE id={$build_id}
			");
		$template = mysqli_fetch_assoc($result);
		if($template['usuario'] != $_SESSION['id']){
			header("Location: /profile/");
			exit;
		}
	}else{
		header("Location: /search/");
		exit;
	}
}else{
	header("Location: /login/");
	exit;
}
?>
<html>
<head>
	<title>Delete build</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main>
		<form action="deletando.php" enctype="multipart/form-data" method="POST">
			<input type="hidden" name="template_id" value="<?php echo $build_id; ?>">
			<section>
				<p class="mensagem_de_erro">The build, with its data and download file, will be erased from this site. This process can't be reversed.</p>
				<p>Are you sure you want to delete the build "<?php echo $template['nome']; ?>"?</p>
			</section>
			<input type="submit" id="submit">
		</form>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>