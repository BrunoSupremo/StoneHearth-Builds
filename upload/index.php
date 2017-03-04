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
	<title>Upload</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main>
		<section>
			<p>Thank you for the interest in contributing to the site and the StoneHearth community with your work! The upload process is done in a few steps.</p>
		</section>
		<section>
			<form action="send_build.php" enctype="multipart/form-data" method="POST">
				<section>
					<?php
					if( isset($_GET["error"]) && $_GET["error"] == "json"){
						?><p class="mensagem_de_erro">Invalid .json file. Something is wrong there.</p><?php
					}
					if( isset($_GET["error"]) && $_GET["error"] == "nameless_json"){
						?><p class="mensagem_de_erro">A name was not found in the .json. Did you edited it?</p><?php
					}
					?>
					<p>Add your .json file here: (Example: cute_house.json)</p>
					<label class="fa fa-upload">
						<span id="span_json">*.json</span>
						<input onchange="document.querySelector('#span_json').innerHTML = this.value.split( '\\' ).pop();" type="file" name="json" accept=".json" required>
					</label>
				</section>
				<section>
					<p>And add the .png file: (Example: cute_house.png)</p>
					<label class="fa fa-upload">
						<span id="span_png">*.png</span>
						<input onchange="document.querySelector('#span_png').innerHTML = this.value.split( '\\' ).pop();" type="file" name="imagem" accept="image/*" required>
					</label>
				</section>
				<section>
					<p>A short description of your building:</p>
					<input type="text" name="descricao" required placeholder="Example: Cute house with big roof and many flowers" maxlength="255">
				</section>
				<section>
					<p>And last, add a few tags:</p>
					<div id="ancora_do_appendChild">
						<select name="tags[]" required onchange="adicionar_outro_select(this)">
							<option disabled selected value="">Select here</option>
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
							elemento.removeAttribute("required");
							var clone = elemento.cloneNode(true);
							clone.firstElementChild.innerHTML = "1 more tag? (Optional)";
							clone.firstElementChild.removeAttribute("value");
							clone.remove(elemento.selectedIndex);
							document.querySelector('#ancora_do_appendChild').appendChild(clone);
							elemento.removeAttribute("onchange");
						}
					</script>
					<p>In case these tags are not enough, you can create new ones through <a class="link_padrao" href="create_tag.php">here</a>.</p>
				</section>
				<input type="submit" id="submit">
			</form>
		</section>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>