<!--COMIENZA PAGINA -- CONTACTO ---->
<script type="text/javascript">
	$(window).load(function() {
		$('#menu a[id!="link-contacto"]').each(function(i, e){
			$(e).find(' > span').stop().animate({opacity:'0'},600);
		});
	});
</script>

<? if($error != ""): ?>
<script type="text/javascript">
	alert('<?=$error?>');
</script>
<? endif ?>

<li id="page_Contacto">
	<div class="box1">
		<div class="inner">
			<a href="<?=base_url();?>" class="close" data-type="close"><span></span></a>
			<div class="wrapper">
				<div class="col1">
					<h2>Contacto</h2>
					<span style="color:#FFF; font-size:18px">Carlos Quijano 1288 bis</span><br><br>
					<p class="cols">Teléfono:<br>
							Email:</p>
					<p>(0598) 2901 8401<br>
							<a href="mailto:" class="link1">arte@dianasaravia.com.uy</a></p>
				</div>
				<div class="col1 pad_left1">
					<h2>Ubicación</h2>
					<figure><iframe width="280" height="210" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com.uy/maps?f=q&amp;source=s_q&amp;hl=es-419&amp;geocode=&amp;q=Carlos+Quijano,+Montevideo&amp;aq=0&amp;oq=carlos+&amp;sll=-32.620508,-55.776511&amp;sspn=5.328767,10.821533&amp;ie=UTF8&amp;hq=&amp;hnear=Carlos+Quijano,+Montevideo,+Departamento+de+Montevideo&amp;ll=-34.908507,-56.188821&amp;spn=0.010136,0.021136&amp;t=m&amp;z=14&amp;output=embed"></iframe></figure>
				</div>
			</div>
			<h2>Formulario de contacto</h2>
			<form id="ContactForm" action="" method="post">
				<div>
					<div class="wrapper">
						<span>Nombre:</span>
						<input type="text" class="input" name="nombre" id="nombre" value="<?=$nombre?>" />
					</div>
					<div  class="wrapper">
						<span>E-mail:</span>
						<input type="text" class="input" name="email" id="email" value="<?=$email?>" />
					</div>
					<div  class="textarea_box">
						<span>Mensaje:</span>
						<textarea name="mensaje" id="mensaje" cols="1" rows="1"><?=$mensaje?></textarea>
					</div>
					<div class="wrapper">
						<span>&nbsp;</span>
						<a href="#" class="button1" onClick="document.getElementById('ContactForm').reset()"><span></span><strong>Borrar</strong></a>
						<a href="#" class="button1" onClick="document.getElementById('ContactForm').submit()"><span></span><strong>Enviar</strong></a>
					</div>
				</div>
			</form>
		</div>
	</div>
</li>
                            <!--FINALIZA PAGINA -- CONTACTO ---->