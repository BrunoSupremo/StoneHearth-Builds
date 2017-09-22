<?php
include("../abrir_banco.php");
include("../session.php");

$temErro = "no";
$imagem=$_FILES['imagem'];
if($imagem['name']==''){
	$temErro = "You need to upload an image.";
	echo $temErro;
	exit;
}else{
	$filename = $imagem['tmp_name'];
	$client_id="f32959d70d854cc";
	$handle = fopen($filename, "r");
	$data = fread($handle, filesize($filename));
	$pvars   = array('image' => base64_encode($data));
	$timeout = 30;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
	$out = curl_exec($curl);
	curl_close ($curl);
	$pms = json_decode($out,true);
	$url_imagem=$pms['data']['link'];
	if($url_imagem!=""){

	}else{
		$temErro = $pms['data']['error'];
		echo "Your image has an error";
		exit;
	} 
}

$json = $_FILES['json'];
if($json['name']==''){
	header("Location: /upload/?error=json");
	exit;
}else{
	$filename2 = $json['tmp_name'];
}
// copy file content into a string var
$json_file = file_get_contents($filename2);
// convert the string to a json object
$jfo = json_decode($json_file);
// read the name value
if(!isset($jfo->header->name)){
	header("Location: /upload/?error=nameless_json");
	exit;
}else{
	$name = $jfo->header->name;
}

if($jfo->header->{"preview_image"} != strtolower("/r/saved_objects/stonehearth/building_templates/{$imagem['name']}")){
	$jfo->header->{"preview_image"} = strtolower("/r/saved_objects/stonehearth/building_templates/{$imagem['name']}");
}
$newJsonString = json_encode($jfo);


$desc = htmlspecialchars($_POST['descricao']);

$stmt = $conn->prepare("INSERT INTO templates 
	(nome,descricao,imagem,usuario,data) VALUES 
	(?, ?, ?, ?, now())"
);
$stmt->bind_param("sssi", $name, $desc, $url_imagem, $_SESSION['id']);
$stmt->execute();
$build_number = mysqli_insert_id($conn);

//criar o zip com o numero do template que acabou de registrar, por isso que vem depois do sql
$zip = new ZipArchive();
$zip->open('../builds/build'.$build_number.'.zip', ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
$zip->addFromString("{$json['name']}", $newJsonString);
$zip->addFile("{$imagem['tmp_name']}", "{$imagem['name']}");
$zip->close();
$link_download = '/builds/build'.$build_number.'.zip';

//atualiza o link do template com o numero correto
$stmt = $conn->prepare("UPDATE templates 
	SET link_download = ? 
	WHERE id = ?"
);
$stmt->bind_param("si", $link_download, $build_number);
$stmt->execute();

$lista_de_tags = $_POST["tags"];
$stmt = $conn->prepare("INSERT INTO tags_nos_templates 
	(tag, template) VALUES 
	(?, ?)"
);
foreach( $lista_de_tags as $tag ) {
	$stmt->bind_param("ii", $tag, $build_number);
	$stmt->execute();
}

//adiciona no banco itens que ainda não existiam
$stmt = $conn->prepare("INSERT INTO itens 
	(alias, nome) VALUES 
	(?, ?)"
);
foreach ($jfo->header->cost->items as $key => $value) {
	$result = mysqli_query($conn,"SELECT id from itens where alias='{$key}'");
	if( !(mysqli_num_rows($result) >0) ){
		$stmt->bind_param("ss", $key, $key);
		$stmt->execute();
	}
}
//adiciona os resoures (wood, stone, clay_brick)
$stmt = $conn->prepare("UPDATE templates 
	SET wood=?, stone=?, clay_brick=?
	WHERE id={$build_number}"
);
$wood = 0;
$stone = 0;
$clay_brick = 0;
foreach ($jfo->header->cost->resources as $key => $value) {
	if($key == "wood resource"){
		$wood = $value;
	}
	if($key == "stone resource"){
		$stone = $value;
	}
	if($key == "clay_brick resource"){
		$clay_brick = $value;
	}
}
$stmt->bind_param("iii", $wood, $stone, $clay_brick);
$stmt->execute();
//adiciona os itens (cadeiras, camas, etc.)
$stmt = $conn->prepare("INSERT INTO itens_nos_templates 
	(item, template, quantidade) VALUES 
	(?, ?, ?)"
);
$tem_itens_de_mod = false;
foreach ($jfo->header->cost->items as $key => $value) {
	if( isset($value->count) ){
		$value = $value->count;
	}
	$result = mysqli_query($conn,"SELECT id from itens where alias='{$key}'");
	$result_item = mysqli_fetch_assoc($result);
	$stmt->bind_param("iii", $result_item['id'], $build_number, $value);
	$stmt->execute();

	$exploded = explode(":", $key);
	if( !($exploded[0] == "stonehearth") ){
		$tem_itens_de_mod = true;
	}
}
//marca o template como modded
if($tem_itens_de_mod){
	$stmt = $conn->prepare("UPDATE templates 
		SET modded = 'true'
		WHERE id = ?"
	);
	$stmt->bind_param("i", $build_number);
	$stmt->execute();
}
header("Location: /search/");
?>