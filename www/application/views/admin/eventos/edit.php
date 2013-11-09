<script type="text/javascript" src="<?=base_url();?>assets/js/eventos.js"></script>
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
        Eventos
      </a> 
      <span class="divider">/</span>
    </li>
    <li class="active">
      <a href="#">Editar</a>
    </li>
  </ul>
  
  <div class="page-header">
    <h2>
      Editando Evento
    </h2>
  </div>


  <?php
  //flash messages
  if($this->session->flashdata('flash_message')){
    if($this->session->flashdata('flash_message') == 'updated')
    {
      echo '<div class="alert alert-success">';
        echo '<a class="close" data-dismiss="alert">×</a>';
        echo 'El evento fue editado correctamente.';
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

  echo form_open_multipart('admin/eventos/update/'.$this->uri->segment(4).'', $attributes);
  ?>
    <fieldset>
      <div class="control-group">
          <label for="inputError" class="control-label">Nombre</label>
          <div class="controls">
            <input type="text" id="nombre_evento" name="nombre_evento" value="<?php echo $evento[0]['nombre_evento']; ?>" >
          </div>
      </div>

      <div class="control-group">
        <label for="inputError" class="control-label">Fecha</label>
        <div class="controls">
          <input type="text" id="fecha-disp" name="fecha-disp" value="<?php echo $evento[0]['fecha']; ?>" >
          <input type="hidden" id="fecha" name="fecha" value="<?php echo $evento[0]['fecha']; ?>" >
        </div>
      </div>

      <div class="control-group">
        <label for="inputError" class="control-label">Detalles</label>
        <div class="controls">
          <textarea id="detalles" name="detalles" style="margin: 0px;height: 150px;width: 400px;"><?=$evento[0]['detalles'];?></textarea>
        </div>
      </div>

      <div class="control-group">
          <label for="inputError" class="control-label">Archivo de imagen</label>
          <div class="controls">
            <input type="file" id="imagen_evento" name="imagen_evento">
          </div>
      </div>

      <div class="control-group">
          <label for="inputError" class="control-label">Vista previa</label>
          <div class="controls">
            <img src="<?php echo site_url("admin").'/eventos/preview/' . $evento[0]['id_evento']; ?>" width="<?=$previewWidth?>" height="<?=$previewHeight?>" />
          </div>
      </div>

      <div class="form-actions">
          <button class="btn btn-primary" type="submit">Salvar cambios</button>
          <button class="btn" type="reset">Cancelar</button>
       </div>
    </fieldset>

  <?php echo form_close(); ?>

</div>  