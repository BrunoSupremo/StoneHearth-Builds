<?php
include("abrir_banco.php");
include("session.php");
?>
<html>
<head>
	<title>StoneHearth Builds</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css.css" type="text/css">
</head>
<body>
	<?php include("nav.php"); ?>
	<main id="home">
		<section>
			<h1>What is this site about?</h1>
			<p>This is a database of StoneHearth building templates. You can send builds that you like or search for one to use in your game.</p>
		</section>
		<section>
			<h1>Wait, what's a building template?</h1>
			<p>A build template is a blueprint of a building. In game, you can select it from the build menu, and place it on a desired location. After ordering your settlers to finish it, they will gather the materials needed and start building it!</p>
		</section>
		<section>
			<h1>Cool! But how can I create one?</h1>
			<p>In your world, click on your desired building. On its menu, there will be a button "save" which when clicked will save it as a template.</p>
		</section>
		<section>
			<h1>And how can I use a template that I found here?</h1>
			<p>All custom templates are placed in the folder "saved_objects\stonehearth\building_templates", located inside your stonehearth game folder. After you download one file from here, all you need to do is place your unzipped files in that building_templates folder!</p>
			<p>Tip: If your game doesn't have this folder, you can manually create it and it will work the same way. </p>
		</section>
	</main>
	<?php include("footer.php"); ?>
</body>
</html>