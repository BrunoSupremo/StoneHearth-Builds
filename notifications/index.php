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
		if(!$proprio_perfil){
			//não é o dono da pag. notificação, redireciona pra pagina de perfil
			header("Location: /profile/?user={$_GET['user']}");
			exit;
		}
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
	<title>Notifications</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("../nav.php"); ?>
	<main>
		<?php
		$result_notificacoes=mysqli_query($conn,"
			SELECT usuarios.imagem as usuarios_imagem, usuarios.nome as usuarios_nome, templates.nome as templates_nome, templates.imagem as templates_imagem, templates.id as templates_id
			FROM comentarios
			INNER JOIN usuarios ON comentarios.usuario=usuarios.id
			INNER JOIN templates ON comentarios.template=templates.id
			WHERE comentarios.visto = 0 AND templates.usuario = {$_GET['user']}
			ORDER BY templates.id, comentarios.data
			");
		if(mysqli_num_rows($result_notificacoes) >0){
			?>
			<h1>You have new comments</h1>
			<?php
			while($notificacoes=mysqli_fetch_assoc($result_notificacoes)){
				?>
				<section>
					<img class="imagem_bolinha" style="height: 2em;" src="<?php echo $notificacoes['usuarios_imagem']; ?>"><?php echo $notificacoes['usuarios_nome']; ?> commented at 
					<a class="link_padrao" href="/builds/?id=<?php echo $notificacoes['templates_id']; ?>">
						<img style="height: 2em;" src="<?php echo $notificacoes['templates_imagem']; ?>"><?php echo $notificacoes['templates_nome']; ?>
					</a>
				</section>
				<?php
			}
		}else{
			?>
			<p>No new notifications!</p>
			<?php
		}
		?>
	</main>
	<?php include("../footer.php"); ?>
</body>
</html>