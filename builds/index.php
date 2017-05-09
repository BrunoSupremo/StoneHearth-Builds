<?php
include("../abrir_banco.php");
include("../session.php");

//habilita controles só visiveis para o dono da build
$dono_da_build = false;

if( isset($_GET['id']) && ($_GET['id'] > 0) ){
	$result = mysqli_query($conn,"
		SELECT templates.id as templates_id, templates.nome as templates_nome, templates.imagem as templates_imagem, templates.contador_downloads, templates.link_download, templates.data, templates.descricao, usuarios.id as usuarios_id, usuarios.nome as usuarios_nome, usuarios.imagem as usuarios_imagem, COUNT(distinct estrelas.usuario) as estrelas, COUNT(distinct comentarios.id) as comentarios, templates.wood, templates.stone, templates.clay_brick
		FROM templates
		INNER JOIN usuarios ON templates.usuario=usuarios.id
		LEFT JOIN comentarios ON templates.id=comentarios.template
		LEFT JOIN estrelas ON templates.id=estrelas.template
		WHERE templates.id={$_GET['id']}
		GROUP BY templates.id
		");
	if( mysqli_num_rows($result) >0 ){
		// mostra a build com esse id
		$build = mysqli_fetch_assoc($result);
		// verifica se é o próprio dono da build
		$dono_da_build = ( isset($_SESSION['id']) && ($_SESSION['id'] == $build['usuarios_id']) );
	}else{
		// digitaram merda na url, redireciona pra lista de builds
		header("Location: /search/?build=notfound");
		exit;
	}
}else{
	header("Location: /search/");
	exit;
}

?>
<html>
<head>
	<title><?php echo $build['templates_nome']; ?> by <?php echo $build['usuarios_nome']; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php
	if($dono_da_build){
		$stmt = $conn->prepare("
			UPDATE comentarios
			set visto=1
			WHERE template=? AND visto=0
			");
		$stmt->bind_param("i", $build['templates_id']);
		$stmt->execute();
	}
	include("../nav.php");
	?>
	<main id="build">
		<span id="modded" class="fa fa-flask"></span>
		<img class="imagem" src="<?php echo $build['templates_imagem']; ?>">
		<section id="materiais">
			<img src="/imagens/wood.png">x<?php echo ceil($build['wood']/60); ?>
			<img src="/imagens/stone.png">x<?php echo ceil($build['stone']/60); ?>
			<img src="/imagens/clay_brick.png">x<?php echo ceil($build['clay_brick']/60); ?>
			<?php
			$aparecer_modded = "false";
			$result_itens = mysqli_query($conn,"
				SELECT * FROM itens_nos_templates
				LEFT JOIN itens ON itens.id=itens_nos_templates.item
				WHERE itens_nos_templates.template={$_GET['id']}
				ORDER BY quantidade DESC
				");
			while($loop=mysqli_fetch_assoc($result_itens)){
				?>
				<!-- <img src="<?php echo $loop['imagem']; ?>"> -->
				<?php
				// echo $loop['nome'];
				// echo $loop['quantidade'];
				$exploded = explode(":", $loop['alias']);
				if( !($exploded[0] == "stonehearth") ){
					$aparecer_modded = "true";
				}
			}
			?>
			<script type="text/javascript">
				if(<?php echo $aparecer_modded; ?>){
					document.querySelector("#modded").style.display = "initial";
				}
			</script>
		</section>
		<section id="dados">
			<div>
				<a href="/profile/?user=<?php echo $build['usuarios_id']; ?>">
					<img class="autor imagem_bolinha" src="<?php echo $build['usuarios_imagem']; ?>">
				</a>
				<p class="nome_do_template">
					<?php echo $build['templates_nome']; ?>
					<span class="icone_estrela">&#xf005</span>
					<span id=contador_estrelas><?php echo $build['estrelas']; ?></span>
				</p>
				<p class="autor">by <a class="link_padrao" href="/profile/?user=<?php echo $build['usuarios_id']; ?>"><?php echo $build['usuarios_nome']; ?></a> on <?php echo date("M d, Y",strtotime($build['data'])); ?></p>
			</div>
			<div>
				<?php
				$add_star_string = "Add Star";
				$onclick_function = "dar_estrela();";
				if( isset($_SESSION['logado']) && ($_SESSION['logado'])=="true" ){
					$result = mysqli_query($conn,"
						SELECT usuario FROM estrelas
						WHERE estrelas.usuario={$_SESSION['id']} and estrelas.template={$_GET['id']}
						");
					if( mysqli_num_rows($result) >0 ){
						$add_star_string="Star Added";
						$onclick_function="";
					}
				}else{
					$onclick_function="pedir_login();";
				}
				?>
				<p><a id="dar_estrela" onclick="<?php echo $onclick_function; ?>" class="link_padrao fa fa-star"><?php echo $add_star_string; ?></a></p>
				<p><a class="link_padrao fa fa-download" onclick="contador_downloads(this);" href="<?php echo $build['link_download']; ?>">Download</a></p>
				<script type="text/javascript">
					function contador_downloads(elemento){
						var xhttp = new XMLHttpRequest();
						xhttp.open("POST", "contador_downloads.php", true);
						xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhttp.send("template=<?php echo $_GET['id']; ?>");
						elemento.onclick = function(event) {event.preventDefault();}
					}
					function pedir_login() {
						document.getElementById("dar_estrela").innerHTML = "Login needed :(";
					}
					function dar_estrela() {
						var xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange = function() {
							if (xhttp.readyState == 4 && xhttp.status == 200) {
								document.getElementById("dar_estrela").innerHTML = "Star Added";
								document.getElementById("contador_estrelas").innerHTML = <?php echo $build['estrelas']; ?>+1;
								document.getElementById("contador_estrelas").className="efeito_piscadinha";
							}
						};
						xhttp.open("POST", "dar_estrela.php", true);
						xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhttp.send("template=<?php echo $_GET['id']; ?>");
					}
				</script>
			</div>
			<?php
			if($dono_da_build){
				?>
				<p>
					<a class="fa fa-edit link_padrao" href="edit.php?build=<?php echo $_GET['id']; ?>">Edit build details?</a>
					<a class="fa fa-trash link_padrao" href="delete.php?build=<?php echo $_GET['id']; ?>">Delete this build?</a>
				</p>
				<?php
			}
			?>
			<p><?php echo $build['descricao']; ?></p>
			<p id="tags">Tags:
				<?php
				$result = mysqli_query($conn,"
					SELECT tags.nome, tags_nos_templates.template, count(contagem.tag) as popularidade
					FROM tags
					LEFT JOIN tags_nos_templates ON tags_nos_templates.tag=tags.id
					LEFT JOIN tags_nos_templates as contagem ON contagem.tag=tags.id
					WHERE tags_nos_templates.template={$_GET['id']}
					GROUP BY tags_nos_templates.tag
					ORDER BY popularidade DESC
					");
				$sem_tags = true;
				if( mysqli_num_rows($result) >0 ){
					$sem_tags = false;
					while($loop_tags=mysqli_fetch_assoc($result)){
						?>
						<a href="/search/?tag=<?php echo $loop_tags['nome']; ?>" class="tag"><?php echo $loop_tags['nome']; ?></a>
						<?php
					}
				}
				if($sem_tags){
					echo "This build has no tags yet.";
				}
				?>
			</p>
		</section>
		<section id="comentar">
			<?php
			if( isset($_SESSION['logado']) && ($_SESSION['logado'])=="true" ){
				?>
				<form action="comentar.php" enctype="multipart/form-data" method="POST">
					<img class="imagem_bolinha" src="<?php echo $_SESSION['imagem']; ?>">
					<textarea onfocus="focou_comentar();" name="texto" placeholder="Add your comment :)" required></textarea>
					<input type="hidden" name="template_id" value="<?php echo $_GET['id']; ?>">
					<script type="text/javascript">
						function focou_comentar(){
							document.getElementById("submit_comentar").className="visivel";
						}
					</script>
					<input type="submit" id="submit_comentar">
				</form>
				<?php
			}else{
				?>
				<p>Comments are only possible after <a class="link_padrao" href="/login/">login</a></p>
				<?php
			}
			?>
		</section>
		<section id="comentarios">
			<?php
			$result=mysqli_query($conn,"
				SELECT comentarios.texto, comentarios.data, comentarios.template, usuarios.id, usuarios.imagem, usuarios.nome
				FROM comentarios
				INNER JOIN usuarios ON comentarios.usuario=usuarios.id
				WHERE comentarios.template = {$_GET['id']}
				ORDER BY comentarios.data
				");
			$temComentarios=false;
			while($loop=mysqli_fetch_assoc($result)){
				$temComentarios=true;
				?>
				<div class="um_dos_comentarios">
					<a href="/profile/?user=<?php echo $loop['id']; ?>"><img class="imagem_bolinha" src="<?php echo $loop['imagem']; ?>"></a>
					<div>
						<p>
							<a class="link_padrao" href="/profile/?user=<?php echo $loop['id']; ?>">
								<?php echo $loop['nome']; ?>
							</a>
							<span><?php echo date("M d, Y",strtotime($loop['data'])); ?></span>
						</p>
						<p><?php echo nl2br($loop['texto']); ?></p>
					</div>
				</div>
				<?php
			}
			if(!$temComentarios){
				?>
				<p>This build has no comments yet.</p>
				<?php
			}
			?>
		</section>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>