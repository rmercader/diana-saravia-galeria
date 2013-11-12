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
        Mensajes de Contacto
      </a> 
      <span class="divider">/</span>
    </li>
    <li class="active">
      <a href="#">Editar</a>
    </li>
  </ul>
  
  <div class="page-header">
    <h2>
      Detalles de la consulta
    </h2>
  </div>
  
  <?php
  //form data
  $attributes = array('class' => 'form-horizontal', 'id' => '');

  //form validation
  echo validation_errors();

  echo form_open('admin/mensajes/update/'.$this->uri->segment(4).'', $attributes);
  ?>
    <fieldset>
      <div class="control-group">
        <label for="inputError" class="control-label">Nombre</label>
        <div class="controls">
          <input type="text" id="nombre" name="nombre" value="<?php echo $mensaje[0]['nombre']; ?>" readonly="readonly">
          <!--<span class="help-inline">Woohoo!</span>-->
        </div>
      </div>
      <div class="control-group">
        <label for="inputError" class="control-label">Email</label>
        <div class="controls">
          <input type="text" id="email" name="email" value="<?php echo $mensaje[0]['email']; ?>" readonly="readonly">
          <!--<span class="help-inline">Woohoo!</span>-->
        </div>
      </div>

      <div class="control-group">
        <label for="inputError" class="control-label">Fecha de recibido</label>
        <div class="controls">
          <input type="text" id="fecha" name="fecha" value="<?php echo date_format(new DateTime($mensaje[0]['fecha']), 'd/m/Y H:i'); ?>" readonly="readonly">
          <!--<span class="help-inline">Woohoo!</span>-->
        </div>
      </div>

      <div class="control-group">
        <label for="inputError" class="control-label">Detalles</label>
        <div class="controls">
          <textarea id="mensaje" name="mensaje" style="margin: 0px;height: 150px;width: 400px;" readonly="readonly"><?=$mensaje[0]['mensaje'];?></textarea>
        </div>
      </div>
    </fieldset>

  <?php echo form_close(); ?>

</div>  