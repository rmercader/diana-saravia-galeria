<script type="text/javascript">

	$(window).load(function() {
		$('#menu a[id!="link-exposiciones"]').each(function(i, e){
			$(e).find(' > span').stop().animate({opacity:'0'},600);
		});

	});

	$(document).ready(function(){
		$("#obras-categoria").yoxview({
			lang: 'es',
			autoHideInfo: false,
			autoHideMenu: false
		});
	});

	// Suscribo click para las categorias
	function obtenerObrasCategoria(idCategoriaObra, nombreCategoriaObra){
		var ajaxUrl = "<?=base_url()?>exposiciones/obras-categoria";
		var settings = {
			url: ajaxUrl,
			data: {
				id_categoria_obra: idCategoriaObra
			},
			dataType: 'json',
			type: 'POST',
			success: function(data, textStatus, jqXHR){
				if(data.success == true && $.isArray(data.obras)){
					var contentObras = '';
					var urlBaseImagenObra = "<?=base_url()?>uploads/obras/";
					var urlBaseImagenObraPreview = "<?=base_url()?>uploads/obras/";
					
					$(".tab-content > h2").html(nombreCategoriaObra);
					
					$.each(data.obras, function(index, obra){
						var dscObra = obra.nombre_obra + ' / ' + obra.nombre_artista;
						contentObras += '<figure class="left dis_imadet"><a href="' + urlBaseImagenObra + obra.id_obra + '.jpg" title="' + dscObra + '" class="lightbox-image" data-type="prettyPhoto[group2]"><span></span><img src="' + urlBaseImagenObraPreview + obra.id_obra + '.prv.jpg" alt="" title="' + dscObra + '"></a></figure>';
					});

					$(".tab-content > #obras-categoria").html(contentObras);

					$('.close span, .button1 span, .tabs .nav li a span, .lightbox-image span ').css({opacity:'0'})
					$('.tabs .nav .selected a span').css({opacity:'1'})
	
					$('.close, .button1').hover(function(){
						$(this).find('span').stop().animate({opacity:'1'})							
					}, function(){
						$(this).find('span').stop().animate({opacity:'0'})							
					})
					
					$('.lightbox-image').hover(function(){
						$(this).find('span').stop().animate({opacity:'0.4'})							
					}, function(){
						$(this).find('span').stop().animate({opacity:'0'})							
					})
					
					$('.tabs .nav li a').hover(function(){
						$(this).find('span').stop().animate({opacity:'1'})							
					}, function(){
						if (!$(this).parent().hasClass('selected')) {
							$(this).find('span').stop().animate({opacity:'0'})							
						}
					})
					
					//tabs
					tabs.init();
					
					// prettyPhoto
					//$("a[data-type^='prettyPhoto']").prettyPhoto({theme:'light_square'});
					$("#obras-categoria").yoxview({
						lang: 'es',
						autoHideInfo: false,
						autoHideMenu: false
					});

					$(".tab-content").css("display", "");
					Cufon.refresh();
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				alert('Ocurrió un error al obtener las obras. Vuelva a intentarlo en unos instantes.');
				console.log(textStatus);
				console.log(errorThrown);
			}
		};
		$.ajax(settings);
	}

</script>
<!--COMIENZA PAGINA -- EXPOSICIONES ---->
<li id="page_Exposiciones">
	<div class="box1">
		<div class="inner">
			<a href="<?=base_url();?>" class="close" data-type="close"><span></span></a>
			<div class="wrapper tabs">
				<div class="col1">
					<h2 class="grisclaro">Categorías</h2>
					<ul class="nav">
					<?php foreach($categorias as $categoria): ?>
						<li class="<?=$categoria['class'];?>">
							<a href="#" onclick="obtenerObrasCategoria(<?=$categoria['id_categoria_obra'];?>, '<?=$categoria['nombre_categoria_obra'];?>');"><span></span><strong><?=$categoria['nombre_categoria_obra'];?></strong></a>
						</li>
					<?php endforeach; ?>
					</ul>
				</div>
				<div class="col2 pad_left1 clearfix">
					<div class="tab-content" id="<?=$nombre_categoria_obra?>">
						<h2><?=$nombre_categoria_obra?></h2>
						<div class="wrapper yoxview" id="obras-categoria">
						<?php foreach($obras as $obra): ?>
						<figure class="left dis_imadet"><a href="<?=base_url();?>uploads/obras/<?=$obra['id_obra'];?>.gal.jpg" title="<?=$obra['nombre_obra']?> / <?=$obra['nombre_artista']?>" class="lightbox-image" data-type="prettyPhoto[group2]"><span></span><img title="<?=$obra['nombre_obra']?> / <?=$obra['nombre_artista']?>" src="<?=base_url();?>uploads/obras/<?=$obra['id_obra'];?>.prv.jpg" alt=""></a></figure>
						<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</li>
<!--FINALIZA PAGINA -- EXPOSICIONES ---->