<script type="text/javascript">
	
	$(window).load(function() {
		$('#menu a[id!="link-artistas"]').each(function(i, e){
			$(e).find(' > span').stop().animate({opacity:'0'},600);
		});
	});

	$(document).ready(function(){
		$("#obras-artista").yoxview({
			lang: 'es',
			autoHideInfo: false,
			autoHideMenu: false
		});
	});

</script>
<!--COMIENZA PAGINA -- DETALLE DE ARTISTAS ---->
<li id="page_Detalle">
	<div class="box1">
		<div class="inner">
			<a href="<?=base_url();?>artistas" class="close" data-type="close"><span></span></a>
			<div class="wrapper tabs">
				<div class="col1">
					<div class="tab-content" id="page_Detalle">
						<h2 style="width:500px"><?=$nombre_artista?></h2>
                        <p class="texto_detalle"><?=nl2br($detalles)?></p>
						<div class="tit_detalle">Obras del artista</div>
						<div class="wrapper ord_imadet yoxview" id="obras-artista">
						<?php foreach($obrasArtista as $obra): ?>
							<figure class="left dis_imadet">
								<a href="<?=base_url();?>uploads/obras/<?=$obra['id_obra'];?>.gal.jpg" class="lightbox-image" data-type="prettyPhoto[group2]" title="<?=$obra['nombre_obra']?>">
									<span></span><img src="<?=base_url();?>obras/preview/<?=$obra['id_obra'];?>" alt="">
								</a>
							</figure>
						<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</li>
<!--FINALIZA PAGINA -- DETALLE DE ARTISTA ---->