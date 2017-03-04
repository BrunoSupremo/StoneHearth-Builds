<?php
include("../abrir_banco.php");
include("../session.php");

if( isset($_SESSION['logado']) ){
	if( isset($_GET['build']) && ($_GET['build'] >0) ){
		$build_id = $_GET['build'];
		$result = mysqli_query($conn,"
			SELECT usuario
			FROM templates
			WHERE id={$build_id}
			");
		$usuario = mysqli_fetch_assoc($result);
		if($usuario['usuario'] != $_SESSION['id']){
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
	<title>Editing build details</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main>
		<form action="salvar_edit.php" enctype="multipart/form-data" method="POST">
			<input type="hidden" name="template_id" value="<?php echo $build_id; ?>">
			<section>
				<p>Name:</p>
				<?php
				$result = mysqli_query($conn,"
					SELECT nome, descricao
					FROM templates
					WHERE id={$build_id}
					");
				$resultado = mysqli_fetch_assoc($result);
				?>
				<input type="text" name="nome" required value="<?php echo $resultado['nome']; ?>" maxlength="255">
			</section>
			<section>
				<p>Description:</p>
				<input type="text" name="descricao" required value="<?php echo $resultado['descricao']; ?>" maxlength="255">
			</section>
			<section>
				<p>Tags:</p>
				<div id="ancora_do_appendChild">
					<?php
					$result = mysqli_query($conn,"
						SELECT tags_nos_templates.tag
						FROM tags_nos_templates
						WHERE tags_nos_templates.template={$build_id}
						");
					while($lista_de_ids_usados=mysqli_fetch_assoc($result)){
						?>
						<select name="tags[]">
							<?php
							$result2=mysqli_query($conn,"SELECT id, nome FROM tags ORDER BY LOWER(nome)");
							while($loop=mysqli_fetch_assoc($result2)){
								if($loop['id'] == $lista_de_ids_usados['tag']){
									?>
									<option selected value="<?php echo $loop['id']; ?>"><?php echo $loop['nome']; ?></option>
									<?php
								}else{
									?>
									<option value="<?php echo $loop['id']; ?>"><?php echo $loop['nome']; ?></option>
									<?php
								}
							}
							?>
						</select>
						<?php
					}
					?>
					<select name="tags[]" onchange="adicionar_outro_select(this)">
						<option disabled selected>1 more tag? (Optional)</option>
						<?php
						$result=mysqli_query($conn,"SELECT id, nome FROM tags ORDER BY LOWER(nome)");
						while($loop=mysqli_fetch_assoc($result)){
							?>
							<option value="<?php echo $loop['id']; ?>"><?php echo $loop['nome']; ?></option>
							<?php
						}
						?>
					</select>
				</div>
				<script type="text/javascript">
					function adicionar_outro_select(elemento){
						var clone = elemento.cloneNode(true);
						clone.firstElementChild.innerHTML = "1 more tag? (Optional)";
						clone.firstElementChild.removeAttribute("value");
						clone.remove(elemento.selectedIndex);
						document.querySelector('#ancora_do_appendChild').appendChild(clone);
						elemento.removeAttribute("onchange");
					}
				</script>
				<p>In case these tags are not enough, you can create new ones through <a class="link_padrao" href="/upload/create_tag.php">here</a>.</p>
			</section>
			<input type="submit" id="submit">
		</form>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>