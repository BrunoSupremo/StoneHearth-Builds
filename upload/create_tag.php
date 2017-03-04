<?php
include("../abrir_banco.php");
include("../session.php");

if( !isset( $_SESSION['logado']) ){
	header("Location: /login/?last_page=upload");
	exit;
}
?>
<html>
<head>
	<title>Create new tags</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
	<style type="text/css">
		input[type="text"]{
			width: auto;
			margin: 5px;
		}
	</style>
</head>
<body>
	<?php include("../nav.php"); ?>
	<main>
		<?php
		if( isset($_GET["tags"]) && $_GET["tags"] == "created"){
			?>
			<section>
				<p class="fa fa-tag efeito_piscadinha">Your new tags were added to the site and are ready to use.</p>
			</section>
			<?php
		}
		?>
		<section>
			<p>Here is a few short rules for creating new tags. Keep them simple, avoid duplicates and plurals (e.g. use "house" instead of "houses").</p>
			<p>Only add tags that other people could also use in their builds later. Don't add a tag with your name or some other kind of (self)promotion. Keep it strictly related to the build features, ok?</p>
		</section>
		<section>
			<form action="salvar_tags.php" enctype="multipart/form-data" method="POST">
				<section>
					<p>Add the new tags below (one per field)</p>
					<div id="lista_de_campos"><input type="text" name="tags[]" required placeholder="New tag here" maxlength="20"></div>
					<span class="link_padrao" onclick="adicionar_mais_um_campo();">Add more fields?</span>
				</section>
				<input type="submit" id="submit">
				<script type="text/javascript">
					function adicionar_mais_um_campo(){
						var text_field = document.createElement("input");
						text_field.setAttribute("type", "text");
						text_field.setAttribute("name", "tags[]");
						text_field.setAttribute("placeholder", "Another tag");
						text_field.setAttribute("maxlength", "20");
						document.getElementById("lista_de_campos").appendChild(text_field);
					}
				</script>
			</form>
		</section>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>