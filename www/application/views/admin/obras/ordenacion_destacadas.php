<script type="text/javascript">
  var site_url_admin = '<?=site_url("admin")?>';
</script>
<script type="text/javascript" src="<?=base_url();?>assets/js/obras.js"></script>
<style>
  #sortable { list-style-type: none; margin: 0; padding: 0; width: 900px; }
  #sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: <?=$previewWidth?>px; height: <?=$previewHeight?>px; font-size: 4em; text-align: center; cursor: pointer; }
</style>
<script>

  $(function() {
    
    $("#sortable").sortable({
      cursor: "move",
      opacity: 0.5,
      revert: true
    });

    $("#sortable").disableSelection();
  });

  function rescatarOrdenacion(){
    // Recupero los id en el orden que fueron seteados
    var sortedIDs = $( "#sortable" ).sortable( "toArray" );
    $("#ids_ordenados").val(JSON.stringify(sortedIDs));
  }

  $(document).ready(function(){

    $("#btn-cancelar").click(function(){
      if(confirm("Los cambios que haya hecho se perderán. ¿Continuar de todos modos?")){
        $("#sortable").sortable("cancel");
      }
      return false;
    });

  });

</script>
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
      Ordenación
    </li>
  </ul>
  
  <div class="page-header">
    <h2>
      Ordenación de obras destacadas
    </h2>
  </div>

  <?php
  //flash messages
  if(isset($flash_message)){
    if($flash_message == TRUE)
    {
      echo '<div class="alert alert-success">';
        echo '<a class="close" data-dismiss="alert">×</a>';
        echo 'Las obra destacadas fueron ordenadas correctamente.';
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
  $attributes = array('class' => 'form-horizontal', 'id' => '', 'onsubmit' => 'rescatarOrdenacion();');

  //form validation
  echo validation_errors();
  if(isset($error)){
    echo "Errores adicionales: $error";
  }
  
  echo form_open_multipart('admin/obras/destacadas', $attributes);
  ?>

  <fieldset>

    <div class="control-group">
      <div class="controls" style="margin-left: 5px;">
        <ul id="sortable">
        <?php foreach($destacadas as $row): ?>
          <li class="ui-state-default" id="<?=$row['id_obra']?>">
            <img title="<?=$row['nombre_obra'].'/'.$row['nombre_artista']?>" alt="<?=$row['nombre_obra'].'/'.$row['nombre_artista']?>" src="<?php echo site_url("admin").'/obras/preview/'.$row['id_obra']; ?>" width="<?=$previewWidth?>" height="<?=$previewHeight?>" />
          </li>
        <?php endforeach ?>
        </ul>
      </div>
    </div>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Salvar</button>
      <button class="btn" id="btn-cancelar">Cancelar</button>
    </div>

    <input type="hidden" name="ids_ordenados" id="ids_ordenados" />

  </fieldset>

  <?php echo form_close(); ?>

</div>     