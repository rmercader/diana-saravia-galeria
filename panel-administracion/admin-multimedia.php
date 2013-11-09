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
$Opc = 'imagenes';
$Tpl_Contenido = 'base_contenido.htm';
if(!isset($_GET['MOD'])){
	$_GET['PVEZ'] = _SI;	
}

if(isset($_GET['MOD'])){
	$Opc = $_GET['MOD'];
}

switch($Opc){
	case 'imagenes':
		include("galeria/imagenes.php");
		break;

	case 'videos':
		include("videos/videos.php");
		break;
}

/*--------------------------------------------------------------------------
						G E N E R O   P A G I N A
--------------------------------------------------------------------------*/
// Menu Horizontal
$Menu = new nyiMenuHor('base_menu_horizontal.htm', 180, 22);

// Menu Galeria
$Menu->AddOpcion(1, 'Galeria');
$Menu->AddOpcionLink(1, 1, 'Nueva imagen', $mod_Script, array('MOD'=>'imagenes', 'PVEZ'=>_SI, 'ACC'=>ACC_ALTA));
$Menu->AddOpcionLink(1, 2, 'Galeria de imagenes', $mod_Script, array('MOD'=>'imagenes', 'PVEZ'=>_SI));

$Menu->AddOpcion(2, 'Videos');
$Menu->AddOpcionLink(2, 1, 'Nuevo video', $mod_Script, array('MOD'=>'videos', 'PVEZ'=>_SI, 'ACC'=>ACC_ALTA));
$Menu->AddOpcionLink(2, 2, 'Galeria de videos', $mod_Script, array('MOD'=>'videos', 'PVEZ'=>_SI));

// Genero html
$Contenido = new nyiHTML($Tpl_Contenido);
$Contenido->assign('SOLAPA', $mod_Solapa);
$Contenido->assign('MODCONT', $mod_Contenido);

// Modulo
$Modulo = new nyiModulo('GALERÍA DE IMAGEN Y VIDEO', 'base_modulo.htm');
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