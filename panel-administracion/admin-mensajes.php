<?php

include('app.config.php');
include('./admin.config.php');
include(DIR_BASE.'configuracion-inicial.php');
include_once(DIR_BASE.'seguridad/seguridad.class.php');

/*--------------------------------------------------------------------------
                             P E R M I S O S
  --------------------------------------------------------------------------*/

$Security = new Seguridad($Cnx);

/*--------------------------------------------------------------------------
                             M O D U L O S
  --------------------------------------------------------------------------*/
$mod_Contenido = '';
$mod_Solapa    = '';
$mod_Script    = basename($_SERVER['SCRIPT_NAME']);
$Opc = 'mensajes';
$Tpl_Contenido = 'base_contenido.htm';
if(!isset($_GET['MOD'])){
	$_GET['PVEZ'] = _SI;	
}

if ( ValidateModuleParameters($_GET['MOD'], 'mensajes') ){
	$Opc = $_GET['MOD'];
}

$file = 'mensajes/'.$Opc.'.php';
include($file);

/*--------------------------------------------------------------------------
						G E N E R O   P A G I N A
--------------------------------------------------------------------------*/
// Menu Horizontal
$Menu = new nyiMenuHor('base_menu_horizontal.htm', 150, 22);

// Menu Productos
$Menu->AddOpcion(1, 'Mensajes');
$Menu->AddOpcionLink(1, 1, 'Lista Mensajes',$mod_Script, array('MOD'=>'mensajes','PVEZ'=>_SI));

// Genero html
$Contenido = new nyiHTML($Tpl_Contenido);
$Contenido->assign('SOLAPA', $mod_Solapa);
$Contenido->assign('MODCONT', $mod_Contenido);

// Modulo
$Modulo = new nyiModulo('MENSAJES RECIBIDOS DESDE EL SITIO', 'base_modulo.htm');
$Modulo->assign('NOMSCRIPT', $mod_Script);
$Modulo->SetUsuario('Usuario: '.$_SESSION["cfgusu"]["nombre_usuario_admin"]);
$Modulo->assign('MENUES', $Menu->fetchMenu());
$Modulo->SetContenido($Contenido->fetchHTML());

// Imagen
$Perfil = $Security->GetIdPerfilUsuario($_SESSION["cfgusu"]["id_usuario_admin"]);
switch($Perfil){
	case PERFIL_ADMINISTRADOR:
		$Modulo->assign('IMAGEN_PERFIL', 'pg_sup_esq_izq.gif');
		break;
}

// Ajax
$xajax->processRequest();
$Modulo->assign('AJAX_JAVASCRIPT', $xajax->getJavascript(DIR_XAJAX_PARA_ADMIN));
$Modulo->printHTML();

?>