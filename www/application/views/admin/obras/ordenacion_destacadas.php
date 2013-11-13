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
      Ordenación de obras destacadas
    </li>
  </ul>
  
  <div class="page-header">
    <h2>
      Creando Obra
    </h2>
  </div>

  <?php
  //flash messages
  if(isset($flash_message)){
    if($flash_message == TRUE)
    {
      echo '<div class="alert alert-success">';
        echo '<a class="close" data-dismiss="alert">×</a>';
        echo 'La obra fue creada correctamente.';
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
  if(isset($error)){
    echo "Errores adicionales: $error";
  }
  
  echo form_open_multipart('admin/obras/add', $attributes);
  ?>
    <fieldset>
      
      <div class="control-group">
        <label for="inputError" class="control-label">Nombre</label>
        <div class="controls">
          <input type="text" id="nombre_obra" name="nombre_obra" value="<?php echo set_value('nombre_obra'); ?>" >
        </div>
      </div>

      <div class="control-group">
        <label for="inputError" class="control-label">Artista</label>
        <div class="controls">
          <input type="text" id="nombre_artista" name="nombre_artista" value="<?php echo set_value('nombre_artista'); ?>" >
          <input type="hidden" id="id_artista" name="id_artista" value="<?php echo set_value('id_artista'); ?>" >
        </div>
      </div>

      <div class="control-group">
        <label for="inputError" class="control-label">Categoría</label>
        <div class="controls">
          <input type="text" id="nombre_categoria_obra" name="nombre_categoria_obra" value="<?php echo set_value('nombre_categoria_obra'); ?>" >
          <input type="hidden" id="id_categoria_obra" name="id_categoria_obra" value="<?php echo set_value('id_categoria_obra'); ?>" >
        </div>
      </div>

      <div class="control-group">
        <label for="inputError" class="control-label">Archivo de imagen</label>
        <div class="controls">
          <input type="file" id="imagen_obra" name="imagen_obra" size="40">
        </div>
      </div>
      
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Salvar</button>
        <button class="btn" type="reset">Cancelar</button>
      </div>
    </fieldset>

  <?php echo form_close(); ?>

</div>     