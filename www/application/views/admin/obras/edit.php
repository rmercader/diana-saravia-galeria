<script type="text/javascript">
  var site_url_admin = '<?=site_url("admin")?>';
</script>
<script type="text/javascript" src="<?=base_url();?>assets/js/obras.js"></script>
<div class="container top">
      
  <ul class="breadcrumb">
    <li>
      <a href="<?php echo site_url("admin"); ?>">
        <?php echo ucfirst($this->uri->segment(1));?>
      </a> 
      <span class="divider">/</span>
    </li>
    <li>
      <a href="<?php echo site_url("admin").'/'.$this->uri->segment(2); ?>">
        Obras
      </a> 
      <span class="divider">/</span>
    </li>
    <li class="active">
      Editar
    </li>
  </ul>
  
  <div class="page-header">
    <h2>
      Editando Obra
    </h2>
  </div>


  <?php
  //flash messages
  if($this->session->flashdata('flash_message')){
    if($this->session->flashdata('flash_message') == 'updated')
    {
      echo '<div class="alert alert-success">';
        echo '<a class="close" data-dismiss="alert">×</a>';
        echo 'La obra fue editada correctamente.';
      echo '</div>';       
    }else{
      echo '<div class="alert alert-error">';
        echo '<a class="close" data-dismiss="alert">×</a>';
        echo '<strong>Se ha detectado un problema!</strong> cambiar los datos y tratar de enviar nuevamente.';
      echo '</div>';          
    }
  }
  ?>
  
  <?php
  //form data
  $attributes = array('class' => 'form-horizontal', 'id' => '');

  //form validation
  echo validation_errors();

  echo form_open_multipart('admin/obras/update/'.$this->uri->segment(4).'', $attributes);
  ?>
    <fieldset>
    	<div class="control-group">
	        <label for="inputError" class="control-label">Nombre</label>
	        <div class="controls">
	          <input type="text" id="nombre_obra" name="nombre_obra" value="<?php echo $obra[0]['nombre_obra']; ?>" >
	        </div>
	    </div>

      <div class="control-group">
        <label for="inputError" class="control-label">Información técnica</label>
        <div class="controls">
          <textarea id="info_tecnica" name="info_tecnica" style="margin: 0px;height: 150px;width: 400px;"><?php echo $obra[0]['info_tecnica']; ?></textarea>
        </div>
      </div>

  		<div class="control-group">
  			<label for="inputError" class="control-label">Artista</label>
  			<div class="controls">
  				<input type="text" id="nombre_artista" name="nombre_artista" value="<?php echo $obra[0]['nombre_artista']; ?>" >
  				<input type="hidden" id="id_artista" name="id_artista" value="<?php echo $obra[0]['id_artista']; ?>" >
  			</div>
  		</div>

	    <div class="control-group">
    		<label for="inputError" class="control-label">Categoría</label>
	        <div class="controls">
	        	<input type="text" id="nombre_categoria_obra" name="nombre_categoria_obra" value="<?php echo $obra[0]['nombre_categoria_obra']; ?>" >
	        	<input type="hidden" id="id_categoria_obra" name="id_categoria_obra" value="<?php echo $obra[0]['id_categoria_obra']; ?>" >
	        </div>
	     </div>

       <div class="control-group">
        <label for="inputError" class="control-label">Destacada</label>
          <div class="controls">
            <input type="checkbox" id="destacada" name="destacada" <?php if($obra[0]['destacada']){echo 'checked="checked"';} ?> >
          </div>
       </div>

        <div class="control-group">
	        <label for="inputError" class="control-label">Archivo de imagen</label>
	        <div class="controls">
	        	<input type="file" id="imagen_obra" name="imagen_obra">
	        </div>
        </div>

	    <div class="control-group">
	        <label for="inputError" class="control-label">Vista previa</label>
	        <div class="controls">
	        	<img src="<?php echo site_url("admin").'/obras/preview/' . $obra[0]['id_obra']; ?>" width="<?=$previewWidth?>" height="<?=$previewHeight?>" />
	        </div>
	    </div>

	    <div class="form-actions">
	        <button class="btn btn-primary" type="submit">Salvar cambios</button>
	        <button class="btn" type="reset">Cancelar</button>
	     </div>
    </fieldset>

  <?php echo form_close(); ?>

</div>  