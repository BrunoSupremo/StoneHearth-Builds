<?php
include("../abrir_banco.php");
include("../session.php");

$page = $_POST['page'];
$start = ($page-1) * 60;

$WHERE = "";
$tag="";
if( isset($_POST['tag']) && (!empty($_POST['tag'])) ){
	$tag = $_POST['tag'];
	$WHERE = "WHERE templates.id IN
	(SELECT DISTINCT template FROM tags_nos_templates WHERE tag =
	(SELECT id FROM tags WHERE nome = '".mysqli_real_escape_string($conn, $tag)."')
	)";
}
$ORDERBY = "ORDER BY templates.id DESC";
$order="newest";
if( isset($_POST['order']) && (!empty($_POST['order'])) ){
	if($_POST['order'] == "downloads"){
		$order="downloads";
		$ORDERBY = "ORDER BY contador_downloads+COUNT(distinct estrelas.usuario)+COUNT(distinct comentarios.id) DESC,
		contador_downloads DESC,
		COUNT(distinct estrelas.usuario) DESC,
		templates.id DESC";
	}
	if($_POST['order'] == "popular"){
		$order="popular";
		$ORDERBY = "ORDER BY (contador_downloads+COUNT(distinct estrelas.usuario)+COUNT(distinct comentarios.id)) / (DATEDIFF(now(),templates.data)+1) DESC,
		templates.id DESC";
	}
}
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
$result=mysqli_query($conn, $sql . " Limit {$start}, 60");//60 pois é divisivel por 2,3,4,5 e 6
$tem_template = false;
while($loop=mysqli_fetch_assoc($result)){
	$tem_template = true;
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

if (!$tem_template) {
	exit;
}
?>
<div class="link_padrao" onclick="load_more_builds(this,<?php echo($page+1); ?>);">Show More...</div>