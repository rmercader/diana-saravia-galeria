<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/categorias-obras.js"></script>

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
            Categorías de Obras
          </a> 
          <span class="divider">/</span>
        </li>
        <li class="active">
          <a href="#">Nueva</a>
        </li>
      </ul>
      
      <div class="page-header">
        <h2>
          Creando Categoría de Obra
        </h2>
      </div>

      <?php
      //flash messages
      if(isset($flash_message)){
        if($flash_message == TRUE)
        {
          echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo 'La Categoría fue creada correctamente.';
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
      
      echo form_open('admin/categorias_obras/add', $attributes);
      ?>
        <fieldset>
          <div class="control-group">
            <label for="inputError" class="control-label">Nombre</label>
            <div class="controls">
              <input type="text" id="nombre_categoria_obra" name="nombre_categoria_obra" value="<?php echo set_value('nombre_categoria_obra'); ?>" >
              <!--<span class="help-inline">Woohoo!</span>-->
            </div>
          </div>
          <div class="control-group">
            <label for="inputError" class="control-label">Orden</label>
            <div class="controls">
              <input type="text" id="orden" name="orden" value="<?php echo set_value('orden'); ?>" class="numeric" >
              <!--<span class="help-inline">Woohoo!</span>-->
            </div>
          </div>
          <div class="form-actions">
            <button class="btn btn-primary" type="submit">Salvar cambios</button>
            <button class="btn" type="reset">Cancelar</button>
          </div>
        </fieldset>

      <?php echo form_close(); ?>

    </div>
<script type="text/javascript">
  $("#orden").numeric(false);
</script>     