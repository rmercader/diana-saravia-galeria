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
      Mensajes
    </li>
  </ul>

  <div class="page-header users-header">
    <h2>Mensajes</h2>
  </div>
  
  <div class="row">
    <div class="span12 columns">
      <div class="well">
       
        <?php
       
        $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');
       
        //save the columns names in a array that we will use as filter         
        $options_mensajes = array('id_mensaje' => 'Id', 'fecha' => 'Fecha', 'nombre' => 'Nombre');

        echo form_open('admin/mensajes', $attributes);
 
          echo form_label('Buscar:', 'search_string');
          echo form_input('search_string', $search_string_selected);          
          echo form_label('Ordenar por:', 'order');
          echo form_dropdown('order', $options_mensajes, $order, 'class="span2"');
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
            <th class="yellow header headerSortDown">Fecha</th>
            <th class="yellow header headerSortDown">Nombre</th>
            <th class="yellow header headerSortDown">Email</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach($mensajes as $row)
          {
            echo '<tr>';
            echo '<td>'.$row['id_mensaje'].'</td>';
            echo '<td>'.date_format(new DateTime($row['fecha']), 'd/m/Y H:i').'</td>';
            echo '<td>'.$row['nombre'].'</td>';
            echo '<td>'.$row['email'].'</td>';
            echo '<td class="crud-actions">
              <a href="'.site_url("admin").'/mensajes/view/'.$row['id_mensaje'].'" class="btn btn-info">Ver detalles</a>
              <a href="'.site_url("admin").'/mensajes/delete/'.$row['id_mensaje'].'" class="btn btn-danger">Eliminar</a>
            </td>';
            echo '</tr>';
          }
          ?>      
        </tbody>
      </table>

      <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>

  </div>
</div>