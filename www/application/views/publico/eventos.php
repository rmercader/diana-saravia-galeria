<?php

function mesEsp($mes){
	$mesEnEsp = $mes;
	switch (strtolower($mes)) {
		case 'jan':
			$mesEnEsp = 'Ene';
			break;
		case 'apr':
			$mesEnEsp = 'Abr';
			break;
		case 'aug':
			$mesEnEsp = 'Ago';
			break;
		case 'sep':
			$mesEnEsp = 'Set';
			break;
		case 'dec':
			$mesEnEsp = 'Dic';
			break;
		default:
			break;
	}

	return $mesEnEsp;
}

?>
<script type="text/javascript">
	$(window).load(function() {
		$('#menu a[id!="link-eventos"]').each(function(i, e){
			$(e).find(' > span').stop().animate({opacity:'0'},600);
		});
	});
</script>
<!--COMIENZA PAGINA -- EVENTOS ---->
<li id="page_Eventos">
	<div class="box1">
		<div class="inner">
			<a href="<?=base_url();?>" class="close" data-type="close"><span></span></a>
			<div class="wrapper pad_bot1">
				
				<div class="col1 pad_left1 aneventos">
					<h2>Eventos</h2>
					<?php foreach($eventos as $evento): ?>
					<div class="wrapper bloque_evento">
						<span class="date"><strong><?=date_format(new DateTime($evento['fecha']), 'd');?></strong><span><?=strtolower(mesEsp(date_format(new DateTime($evento['fecha']), 'M')));?></span></span>
                        <figure class="left marg_right1"><img style="border: 8px solid #FFF;" src="<?=base_url() . 'eventos/preview/' . $evento['id_evento']; ?>" alt="No disponible"></figure>
						<div class="col2">
							<p class="pad_bot2 color1 tit_evento"><strong><?=$evento['nombre_evento'];?></strong></p>
							<p class="det_evento"><?=$evento['detalles'];?></p>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			
		</div>
	</div>
</li>
<!--FINALIZA PAGINA -- EVENTOS ---->