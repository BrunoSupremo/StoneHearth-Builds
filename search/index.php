<?php
include("../abrir_banco.php");
include("../session.php");
?>
<html>
<head>
	<title>Search</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Here you will find all types of builds, from small to big, from cute to realistic or useful. All content is created by the users." />
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main>
		<?php
		if( isset($_GET['build']) && ($_GET['build'] = "notfound") ){
			?>
			<section class="mensagem_de_erro">
				<p>"These are not the Builds you are looking for..."</p>
				<p>Sorry, that build is not in our database.</p>
			</section>
			<?php
		}
		?>
		<section id="ordem_e_filtros">
			<script type="text/javascript">
				function remover_parametro_vazio_da_url(){
					if(document.querySelector('#tag').value==""){
						document.querySelector('#tag').removeAttribute("name");
					}
				}
			</script>
			<form enctype="multipart/form-data" method="GET" onsubmit="remover_parametro_vazio_da_url()">
				<?php
				$WHERE = "";
				$tag="";
				if( isset($_GET['tag']) && (!empty($_GET['tag'])) ){
					$tag = $_GET['tag'];
					$WHERE = "WHERE templates.id IN
					(SELECT DISTINCT template FROM tags_nos_templates WHERE tag =
					(SELECT id FROM tags WHERE nome = '".mysqli_real_escape_string($conn, $tag)."')
					)";
				}
				$ORDERBY = "ORDER BY templates.id DESC";
				$order="newest";
				if( isset($_GET['order']) && (!empty($_GET['order'])) ){
					if($_GET['order'] == "downloads"){
						$order="downloads";
						$ORDERBY = "ORDER BY contador_downloads+COUNT(distinct estrelas.usuario)+COUNT(distinct comentarios.id) DESC,
						contador_downloads DESC,
						COUNT(distinct estrelas.usuario) DESC,
						templates.id DESC";
					}
					if($_GET['order'] == "popular"){
						$order="popular";
						$ORDERBY = "ORDER BY (contador_downloads+COUNT(distinct estrelas.usuario)+COUNT(distinct comentarios.id)) / (DATEDIFF(now(),templates.data)+1) DESC,
						templates.id DESC";
					}
				}
				?>
				<p>Sorting order:
					<select style="display:inline;" name="order">
						<?php
						if($order=="popular"){
							echo '<option value="newest">Newest</option>';
							echo '<option value="downloads">Downloads</option>';
							echo '<option selected value="popular">Popular</option>';
						}else{
							if($order=="downloads"){
								echo '<option value="newest">Newest</option>';
								echo '<option selected value="downloads">Downloads</option>';
								echo '<option value="popular">Popular</option>';
							}else{
								echo '<option selected value="newest">Newest</option>';
								echo '<option value="downloads">Downloads</option>';
								echo '<option value="popular">Popular</option>';
							}
						}
						?>
					</select>
				</p>
				<p>Filter using tag:
					<select style="display:inline;" name="tag" id="tag">
						<?php
						if($tag==""){
							echo '<option selected value="">No filter</option>';
						}else{
							echo '<option value="">No filter</option>';
						}
						$result=mysqli_query($conn,"SELECT nome FROM tags ORDER BY LOWER(nome)");
						while($loop=mysqli_fetch_assoc($result)){
							?>
							<option<?php if($loop['nome'] == $tag){echo " selected";} ?>><?php echo $loop['nome']; ?></option>
							<?php
						}
						?>
					</select>
				</p>
				<input type="submit" id="submit" value="Sort and Filter">
			</form>
		</section>
		<?php
		$sql = "
		SELECT templates.id as templates_id, templates.nome, templates.imagem as templates_imagem, templates.contador_downloads, usuarios.imagem as usuarios_imagem, COUNT(distinct estrelas.usuario) as quantidade_estrelas, COUNT(distinct comentarios.id) as quantidade_comentarios
		FROM templates
		INNER JOIN usuarios ON templates.usuario=usuarios.id
		LEFT JOIN comentarios ON templates.id=comentarios.template
		LEFT JOIN estrelas ON templates.id=estrelas.template
		{$WHERE}
		GROUP BY templates.id
		{$ORDERBY}
		";
		$result=mysqli_query($conn, $sql . " Limit 60");//60 pois é divisivel por 2,3,4,5 e 6
		while($loop=mysqli_fetch_assoc($result)){
			?>
			<a class="miniatura" href="/builds/?id=<?php echo $loop['templates_id']; ?>">
				<img class="imagem" src="<?php echo $loop['templates_imagem']; ?>">
				<img class="autor imagem_bolinha" src="<?php echo $loop['usuarios_imagem']; ?>">
				<div class="rating">
					<div><?php echo $loop['quantidade_estrelas']; ?><span>&#xf005</span></div>
					<div><?php echo $loop['quantidade_comentarios']; ?><span>&#xf086</span></div>
					<div><?php echo $loop['contador_downloads']; ?><span>&#xf019</span></div>
				</div>
				<div class="nome"><?php echo $loop['nome']; ?></div>
			</a>
			<?php
		}
		?>
		<script type="text/javascript">
			function load_more_builds(button,load_page) {
				button.style.display = 'none';
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var new_div = document.createElement("div");
						new_div.innerHTML = this.responseText;
						document.querySelectorAll("main")[0].appendChild(new_div);
						reset_images();
					}
				};
				xhttp.open("POST", "load_more_builds.php", true);
				xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhttp.send("tag=<?php echo $tag; ?>&order=<?php echo $order; ?>&page="+load_page);
			}
		</script>
		<div class="link_padrao" onclick="load_more_builds(this,2);">Show More...</div>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>