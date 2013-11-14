						</ul>
					</article>
<!-- / content -->
				</div>
			</div>
			<div class="block"></div>
		</div>
		<div class="bg1">
			<div class="main">
<!-- footer -->
				<footer>
					<div class="bg_spinner"></div>
					<ul class="pagination">
					<?php
					$i = 1;
					foreach($imgDestacadas as $destacada): ?>
						<li><a href="<?=base_url();?>obras/imagen/<?=$destacada['id_obra']?>"><?=$i?></a></li>
					<?php 
						$i++;
					endforeach ?>
					</ul>
					<div class="pie">
<span class="left">Todos los derechos reservados Diana Saravia<span style="margin-left:50px">Carlos Quijano 1288 bis / Tel. (0598) 29018401 / <a href="mailto:arte@dianasaravia.com.uy">arte@dianasaravia.com.uy</a></span></span>
<div class="bot_marqueria right"><a href="http://www.lamarqueria.com" target="_blank"><cufontext>La Marquería</cufontext></a></div>

						<!-- {%FOOTER_LINK} -->
                        
					
				</footer>
<!-- / footer-->
			</div>
		</div>
		<script type="text/javascript"> Cufon.now(); </script>
		<script>
		$(window).load(function() {
			$('.spinner').fadeOut();
			$('body').css({overflow:'inherit'})
		})
		</script>
	</body>
</html>