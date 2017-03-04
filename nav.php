<nav>
	<a class="fa fa-home" href="/">StoneHearth Builds</a>
	<div>
		<a class="fa fa-search" href="/search/">Search</a>
		<a class="fa fa-users" href="/builders/">Builders</a>
		<a class="fa fa-upload" rel="nofollow" href="/upload/">Upload</a>
		<?php
		if( isset($_SESSION['logado']) ){
			?>
			<a class="fa fa-user" href="/profile/?user=<?php echo $_SESSION['id']; ?>"><?php echo $_SESSION['nome']; ?></a>
			<?php
		}else{
			?>
			<a class="fa fa-login" rel="nofollow" href="/login/">Login</a>
			<?php
		}
		?>
	</div>
</nav>
<script type="text/javascript">
	var link = document.querySelectorAll('nav a[href^="/' + location.pathname.split("/")[1] + '"]');
	if(link.length > 0){
		<?php
		$id_perfil = 0;
		if( isset($_SESSION['logado']) ){
			$id_perfil = $_SESSION['id'];
		}
		?>
		if(!(location.pathname == "/profile/") || (location.search == "?user=<?php echo $id_perfil; ?>")){
			link[0].className += " active";
	}
}
</script>