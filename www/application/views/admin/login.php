<!DOCTYPE html> 
<html lang="en-US">
  <head>
    <title>Administración :: Diana Saravia - Galería de Arte</title>
    <meta charset="utf-8">
    <link href="<?php echo base_url(); ?>assets/css/admin/global.css" rel="stylesheet" type="text/css">
  </head>
  <body>
    <div class="container login">
      <?php 
      $attributes = array('class' => 'form-signin');
      echo form_open('admin/login/validate_credentials', $attributes);
      echo '<h2 class="form-signin-heading">Administración - Inicio de Sesión</h2>';
      echo form_input('user_name', '', 'placeholder="Nombre de usuario"');
      echo form_password('password', '', 'placeholder="Contraseña"');
      if(isset($message_error) && $message_error){
          echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Falla en la autenticación.</strong> Nombre de usuario y/o contraseña incorrectos.';
          echo '</div>';             
      }
      echo "<br />";
      echo form_submit('submit', 'Ingresar', 'class="btn btn-large btn-primary"');
      echo form_close();
      ?>      
    </div><!--container-->
    <script src="<?php echo base_url(); ?>assets/js/jquery-1.9.1.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>
  </body>
</html>    
    