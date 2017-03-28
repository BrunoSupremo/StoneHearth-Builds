<?php
include("../abrir_banco.php");
include("../session.php");

//habilita controles só visiveis para o dono do perfil
$proprio_perfil = false;

if( isset($_GET['user']) && ($_GET['user'] > 0) ){
	$result = mysqli_query($conn,"SELECT * from usuarios where id={$_GET['user']}");
	if( mysqli_num_rows($result) >0 ){
		// mostra o perfil pedido com esse id
		$perfil = mysqli_fetch_assoc($result);
		// verifica se é o próprio dono no perfil
		$proprio_perfil = ( isset($_SESSION['id']) && ($_SESSION['id'] == $_GET['user']) );
	}else{
		// digitaram merda na url, redireciona pra lista de perfis
		header("Location: /builders/");
		exit;
	}
}else{
	if( isset( $_SESSION['logado']) ){
		header("Location: ?user={$_SESSION['id']}");
		exit;
	}else{
		header("Location: /builders/");
		exit;
	}	
}

?>
<html>
<head>
	<title><?php echo $perfil['nome']; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main id="perfil">
		<?php
		if( isset($_GET['new']) && ($_GET['new'] = "true") ){
			?>
			<p>Thanks for joining.</p>
			<p class="mensagem_de_erro">You can change your username to something different than your e-mail and also set a new profile image. It is all optional but highly recommended.</p>
			<?php
		}
		if( $proprio_perfil ){
			?>
			<section>
				<a href="edit.php" class="link_padrao fa fa-edit">Edit profile?</a>
				<a href="/logout.php" class="link_padrao fa fa-logout">Logout?</a>
			</section>
			<?php
		}
		?>
		<section>
			<img id="imagem_perfil" class="imagem_bolinha" src="<?php echo $perfil['imagem']; ?>">
			<h1><?php echo $perfil['nome']; ?></h1>
			<p id="bio"><?php echo nl2br($perfil['bio']); ?></p>
		</section>
		<section>
			<h1>Achievements</h1>
			<?php
			$temConquista=false;
			$result_conquistas=mysqli_query($conn,"
				SELECT COUNT(id) as quantidade FROM templates
				WHERE usuario = {$_GET['user']}
				");
			$conquistas = mysqli_fetch_assoc($result_conquistas);
			if($conquistas['quantidade'] > 2){
				$temConquista=true;
				$nivel = "bronze";
				if($conquistas['quantidade'] > 5){
					$nivel = "prata";
				}
				if($conquistas['quantidade'] > 8){
					$nivel = "ouro";
				}
				?>
				<div class="conquista fa fa-home <?php echo $nivel; ?>">
					<div class="nome">Architect</div>
					<div class="descricao">Uploaded <?php echo $conquistas['quantidade']; ?> Builds!</div>
				</div>
				<?php
			}

			$result_conquistas=mysqli_query($conn,"
				SELECT COUNT(usuario) as quantidade FROM estrelas
				WHERE usuario = {$_GET['user']}
				");
			$conquistas = mysqli_fetch_assoc($result_conquistas);
			if($conquistas['quantidade'] > 0){
				$temConquista=true;
				$nivel = "bronze";
				if($conquistas['quantidade'] > 4){
					$nivel = "prata";
				}
				if($conquistas['quantidade'] > 9){
					$nivel = "ouro";
				}
				?>
				<div class="conquista fa fa-star <?php echo $nivel; ?>">
					<div class="nome">Star giver</div>
					<div class="descricao">Gave <?php echo $conquistas['quantidade']; ?> stars</div>
				</div>
				<?php
			}

			$result_conquistas=mysqli_query($conn,"
				SELECT COUNT(usuario) as quantidade FROM comentarios
				WHERE usuario = {$_GET['user']}
				");
			$conquistas = mysqli_fetch_assoc($result_conquistas);
			if($conquistas['quantidade'] > 0){
				$temConquista=true;
				$nivel = "bronze";
				if($conquistas['quantidade'] > 2){
					$nivel = "prata";
				}
				if($conquistas['quantidade'] > 5){
					$nivel = "ouro";
				}
				?>
				<div class="conquista fa fa-comments <?php echo $nivel; ?>">
					<div class="nome">Talker</div>
					<div class="descricao">Wrote <?php echo $conquistas['quantidade']; ?> comments</div>
				</div>
				<?php
			}
			if(!$temConquista){
				?><p>No achievements yet :(</p><?php
			}
			?>
		</section>
		<section>
			<h1>Builds</h1>
			<?php
			$result_builds=mysqli_query($conn,"
				SELECT templates.id as templates_id, templates.nome, templates.imagem as templates_imagem, templates.contador_downloads, COUNT(distinct estrelas.usuario) as estrelas, COUNT(distinct comentarios.id) as comentarios
				FROM templates
				LEFT JOIN comentarios ON templates.id=comentarios.template
				LEFT JOIN estrelas ON templates.id=estrelas.template
				WHERE templates.usuario={$_GET['user']}
				GROUP BY templates.id
				ORDER BY templates.id DESC
				");
			$temBuilds=false;
			while($builds = mysqli_fetch_assoc($result_builds)){
				$temBuilds=true;
				?>
				<a class="miniatura" href="/builds/?id=<?php echo $builds['templates_id']; ?>">
					<img class="imagem" src="<?php echo $builds['templates_imagem']; ?>">
					<div class="rating">
						<div><?php echo $builds['estrelas']; ?><span>&#xf005</span></div>
						<div><?php echo $builds['comentarios']; ?><span>&#xf086</span></div>
						<div><?php echo $builds['contador_downloads']; ?><span>&#xf019</span></div>
					</div>
					<div class="nome"><?php echo $builds['nome']; ?></div>
				</a>
				<?php
			}
			if(!$temBuilds){
				?><p>No builds yet :(</p><?php
			}
			?>
		</section>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>