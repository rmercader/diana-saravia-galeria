<!DOCTYPE html> 
<html lang="en-US">
<head>
	<title>Administración :: Diana Saravia - Galería de Arte</title>
	<meta charset="utf-8">
	<link href="<?php echo base_url(); ?>assets/css/admin/global.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/css/ui-lightness/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.9.1.min.js"></script>	
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/admin.js"></script>	
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.numeric.js"></script>  	
	
</head>
<body>
	
	<script type="text/javascript">
		$(document).ready(function(){

		    $(".btn-danger").click(function(evt){
		      if(!confirm('¿Está seguro de querer eliminar el elemento seleccionado?')){
		        evt.stopPropagation();
		        evt.preventDefault();
		      }      
		    });

		  });
	</script>
	
	<div class="navbar navbar-fixed-top">
	  <div class="navbar-inner">
	    <div class="container">
	      <a class="brand">Diana Saravia - Galería de Arte</a>
	      <ul class="nav">
	      	<li <?php if($this->uri->segment(2) == 'artistas'){echo 'class="active"';}?>>
	        	<a href="<?php echo base_url(); ?>admin/artistas">Artistas</a>
	        </li>
	      	<li <?php if($this->uri->segment(2) == 'categorias_obras'){echo 'class="active"';}?>>
	        	<a href="<?php echo base_url(); ?>admin/categorias_obras">Categorías de Obras</a>
	        </li>
	        <li <?php if($this->uri->segment(2) == 'obras'){echo 'class="active"';}?>>
	          <a href="<?php echo base_url(); ?>admin/obras">Obras</a>
	        </li>
	        <li <?php if($this->uri->segment(2) == 'eventos'){echo 'class="active"';}?>>
	          <a href="<?php echo base_url(); ?>admin/eventos">Eventos</a>
	        </li>
	        <li class="dropdown">
	        	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Sistema <b class="caret"></b></a>
	         	<ul class="dropdown-menu">
		            <li>
		            	<a href="<?php echo base_url(); ?>admin/mensajes">Mensajes de Contacto</a>
		            </li>
		            <li>
		            	<a href="<?php echo base_url(); ?>admin/logout">Salir</a>
		            </li>
	          	</ul>
	        </li>
	      </ul>
	    </div>
	  </div>
	</div>	
