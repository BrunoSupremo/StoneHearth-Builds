<?php
include("../abrir_banco.php");
include("../session.php");
?>
<html>
<head>
	<title>Builders</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main id="builders">
		<section id="ordem_e_filtros" style="display: none;">

			<!--
			tirar o display none ali de cima quando for implementar isso direito
			tirar o display none ali de cima quando for implementar isso direito
			tirar o display none ali de cima quando for implementar isso direito-->

			<form enctype="multipart/form-data" method="GET">
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
								echo '<option selected value="most_builds">Most builds</option>';
								echo '<option value="alphabetical">Alphabetical</option>';
								echo '<option value="time_joined">Time joined</option>';
								echo '<option value="popular">Popular builds owned</option>';
							}
						}
						?>
					</select>
				</p>
				<input type="submit" id="submit" value="Sort and Filter">
			</form>
		</section>
		<?php
		$result=mysqli_query($conn,"
			SELECT id, nome, imagem FROM usuarios
			WHERE id IN (SELECT usuario FROM templates)
			ORDER BY data DESC
			");
		while($loop=mysqli_fetch_assoc($result)){
			?>
			<a href="/profile/?user=<?php echo $loop['id'] ?>">
				<img class="imagem_bolinha" src="<?php echo $loop['imagem']; ?>">
				<div><?php echo $loop['nome']; ?></div>
			</a>
			<?php
		}
		?>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>