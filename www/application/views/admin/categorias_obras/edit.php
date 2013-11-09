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
            Categorías de Obra
          </a> 
          <span class="divider">/</span>
        </li>
        <li class="active">
          <a href="#">Editar</a>
        </li>
      </ul>
      
      <div class="page-header">
        <h2>
          Editando Categoría de Obra
        </h2>
      </div>

 
      <?php
      //flash messages
      if($this->session->flashdata('flash_message')){
        if($this->session->flashdata('flash_message') == 'updated')
        {
          echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo 'La Categoría fue editada correctamente.';
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

      echo form_open('admin/categorias_obras/update/'.$this->uri->segment(4).'', $attributes);
      ?>
        <fieldset>
          <div class="control-group">
            <label for="inputError" class="control-label">Nombre</label>
            <div class="controls">
              <input type="text" id="" name="nombre_categoria_obra" value="<?php echo $categoria_obra[0]['nombre_categoria_obra']; ?>" >
              <!--<span class="help-inline">Woohoo!</span>-->
            </div>
          </div>
          <div class="control-group">
          <label for="inputError" class="control-label">Orden</label>
            <div class="controls">
              <input type="text" id="orden" name="orden"value="<?php echo $categoria_obra[0]['orden']; ?>" >
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