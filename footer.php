<footer>
	<div>
		<p>2017 StoneHearth Builds - Developed by <a href="https://www.facebook.com/brunomussoi">Bruno Mussoi Mendonça</a></p>
		<p>Stonehearth Builds is an unofficial fan site and is not affiliated with or endorsed by <a href="http://stonehearth.net/">Radiant Entertainment</a>.</p>
	</div>
	<div>
		<p><a href="/credits/">Credits and Mentions</a></p>
		<p><a href="/other_sites/">Other SH related sites</a></p>
	</div>
	<script type="text/javascript">
		function reset_images(){
			var x = document.querySelectorAll(".imagem_bolinha");
			var i;
			for (i = 0; i < x.length; i++) {
				x[i].addEventListener("error", function(){
					if (this.src != '/imagens/profile.png'){
						this.src='/imagens/profile.png';
					}
				});
			}
		}
		reset_images();
	</script>
</footer>