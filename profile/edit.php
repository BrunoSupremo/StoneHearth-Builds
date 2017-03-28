<?php
include("../abrir_banco.php");
include("../session.php");

if( isset( $_SESSION['logado']) ){
	$result = mysqli_query($conn,"SELECT * from usuarios where id={$_SESSION['id']}");
	$perfil = mysqli_fetch_assoc($result);
}else{
	header("Location: /login/");
	exit;
}
?>
<html>
<head>
	<title>Editing profile</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main id="perfil">
		<form action="salvar_edit.php" enctype="multipart/form-data" method="POST">
			<section>
				<p>Username:</p>
				<input type="text" name="nome" required value="<?php echo $perfil['nome']; ?>">
			</section>
			<section>
				<p>About me:</p>
				<textarea name=bio placeholder="Optional. A little about you in less than 1000 characters. You can add contacts or links to anything about you."><?php echo $perfil['bio']; ?></textarea>
			</section>
			<section class="flex">
				<img src="<?php echo $perfil['imagem']; ?>" id="preview" class="imagem_bolinha" style="height: 90px; width: 90px; flex: 0; margin: 5px;">
				<div style="flex: 1; min-width: 200px;">
					<p>Profile image (url):</p>
					<input oninput="update_imagem()" type="text" id="imagem" name="imagem" required value="<?php echo $perfil['imagem']; ?>">
				</div>
				<script type="text/javascript">
					function update_imagem() {
						document.getElementById("preview").src=document.getElementById("imagem").value;
					}
				</script>
			</section>
			<section>
				<p>New password:</p>
				<input type="password" name="senha" placeholder="Leave it empty to not change">
			</section>
			<section>
				<p>Save all changes?</p>
				<input type="submit" id="submit">
			</section>
		</form>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>