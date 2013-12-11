<script type="text/javascript">
	$(window).load(function() {
		$('#menu a[id!="link-artistas"]').each(function(i, e){
			$(e).find(' > span').stop().animate({opacity:'0'},600);
		});
	});
</script>
<!--COMIENZA PAGINA -- ARTISTAS ---->
<li id="page_Artistas">
	<div class="box1">
		<div class="inner">
			<a href="<?=base_url();?>" class="close" data-type="close"><span></span></a>
			<div class="wrapper pad_bot3">
				<h2>Artistas</h2>
				<div class="col1 esp_artistas">
					<ul class="list2">
					<?php foreach ($artistas as $artista): ?>
						<li><a class="entrellos" href="artistas/detalle/<?=$artista['id_artista']?>"><?=$artista['nombre_artista']?></a></li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</li>
<!--FINALIZA PAGINA -- ARTISTAS ---->