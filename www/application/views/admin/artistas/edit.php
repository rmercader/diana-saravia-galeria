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
        Artistas
      </a> 
      <span class="divider">/</span>
    </li>
    <li class="active">Editar</li>
  </ul>
  
  <div class="page-header">
    <h2>
      Editando Artista
    </h2>
  </div>


  <?php
  //flash messages
  if($this->session->flashdata('flash_message')){
    if($this->session->flashdata('flash_message') == 'updated')
    {
      echo '<div class="alert alert-success">';
        echo '<a class="close" data-dismiss="alert">×</a>';
        echo 'El Artista fue editado correctamente.';
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

  echo form_open('admin/artistas/update/'.$this->uri->segment(4).'', $attributes);
  ?>
    <fieldset>
      <div class="control-group">
        <label for="inputError" class="control-label">Nombre</label>
        <div class="controls">
          <input type="text" id="" name="nombre_artista" value="<?php echo $artista[0]['nombre_artista']; ?>" >
          <!--<span class="help-inline">Woohoo!</span>-->
        </div>
      </div>
      <div class="control-group">
        <label for="inputError" class="control-label">Detalles</label>
        <div class="controls">
          <textarea id="detalles" name="detalles" style="margin: 0px;height: 150px;width: 400px;"><?=$artista[0]['detalles'];?></textarea>
        </div>
      </div>      
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Salvar cambios</button>
        <button class="btn" type="reset">Cancelar</button>
      </div>
    </fieldset>

  <?php echo form_close(); ?>

</div>  