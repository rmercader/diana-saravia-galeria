<script type="text/javascript">
	$(window).load(function() {
		$('#menu a[id!="link-la-galeria"]').each(function(i, e){
			$(e).find(' > span').stop().animate({opacity:'0'},600);
		});
	});
</script>
<!--COMIENZA PAGINA -- LA GALERIA ---->
<li id="page_Home">
	<div class="box1">
		<div class="inner">
			<a href="<?=base_url();?>" class="close" data-type="close"><span></span></a>
			<div class="wrapper pad_bot1">
				<div class="col1">
					<h2>La Galería</h2>
					<figure><img src="images/galeria.jpg" alt="" class="pad_bot1"></figure>
                    <p class="color1 pad_bot2 tit_galeria"><strong>Bienvenidos a nuestra galería de arte !!</strong></p>
					<p class="pad_bot1 angaleria">
Desde 1999 trabajamos día a día para ofrecerles lo mejor, somos una galería dedicada a la difusión de artistas nacionales. Las obras de arte son una presencia que hace mas grato los ámbitos donde el hombre desarrolla sus quehaceres y su vida en general.<br><br>
Contemplar las producciones artísticas y su posible adquisición se ha transformado ya en una costumbre para el publico uruguayo. Ni las crisis económicas, ni las financieras, han podido echar por tierra nuestro esfuerzo, evidenciando que el arte sigue siendo un excelente refugio para el espíritu y también para las inversiones. los esperamos en nuestro local: Carlos Quijano 1288 bis entre Soriano y San Jose, pleno centro de Montevideo. Nuestro teléfono: 2901 8401<br><br>
<span class="grisclaro">Diana Saravia.</span></p>
				</div>
			</div>
		</div>
	</div>
</li>
<!--FINALIZA PAGINA -- LA GALERIA ---->