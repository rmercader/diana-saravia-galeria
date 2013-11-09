<script type="text/javascript">  
  
</script>

<div class="container top">

  <ul class="breadcrumb">
    <li>
      <a href="<?php echo site_url("admin"); ?>">
        <?php echo ucfirst($this->uri->segment(1));?>
      </a> 
      <span class="divider">/</span>
    </li>
    <li class="active">
      Artistas
    </li>
  </ul>

  <div class="page-header users-header">
    <h2>
      Artistas
      <a  href="<?php echo site_url("admin").'/'.$this->uri->segment(2); ?>/add" class="btn btn-success">Agregar nuevo</a>
    </h2>
  </div>
  
  <div class="row">
    <div class="span12 columns">
      <div class="well">
       
        <?php
       
        $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');
       
        //save the columns names in a array that we will use as filter         
        $options_artistas = array('id_artista' => 'Id', 'nombre_artista' => 'Nombre');

        echo form_open('admin/artistas', $attributes);
 
          echo form_label('Buscar:', 'search_string');
          echo form_input('search_string', $search_string_selected);

          echo form_label('Ordenar por:', 'order');
          echo form_dropdown('order', $options_artistas, $order, 'class="span2"');
          echo "&nbsp;";
          $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Ir', 'style' => 'margin-left:5px;');

          $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
          echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1"');

          echo form_submit($data_submit);

        echo form_close();
        ?>

      </div>

      <table class="table table-striped table-bordered table-condensed">
        <thead>
          <tr>
            <th class="header">#</th>
            <th class="yellow header headerSortDown">Nombre</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach($artistas as $row)
          {
            echo '<tr>';
            echo '<td>'.$row['id_artista'].'</td>';
            echo '<td>'.$row['nombre_artista'].'</td>';
            echo '<td class="crud-actions">
              <a href="'.site_url("admin").'/artistas/update/'.$row['id_artista'].'" class="btn btn-info">Ver & editar</a>  
              <a href="'.site_url("admin").'/artistas/delete/'.$row['id_artista'].'" class="btn btn-danger">Eliminar</a>
            </td>';
            echo '</tr>';
          }
          ?>      
        </tbody>
      </table>

      <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>

  </div>
</div>