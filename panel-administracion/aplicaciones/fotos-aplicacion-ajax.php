<?PHP

// Includes
include('../app.config.php');
include('../admin.config.php');

// Incluyo funcionalidades comunes
require_once("../xajax/xajax_core/xajax.inc.php");
include(DIR_LIB.'nyiLIB.php');
include(DIR_LIB.'nyiHTML.php');
include(DIR_LIB.'nyiDATA.php');
include(DIR_LIB.'nyiPDF.php');
include(DIR_BASE.'funciones-auxiliares.php');

// Conexion con la base de datos
$Cnx = nyiCNX();
$Cnx->debug = false;
$xajax = new xajax();

include_once(DIR_BASE.'seguridad/seguridad.class.php');
include(DIR_BASE.'aplicaciones/aplicacion.class.php');

function eliminarFoto($idAplicacion, $nombre){
	$objResponse = new xajaxResponse(); // Creo objeto Response
	$Cnx = nyiCNX(); // Creo la conexion
	$app = new aplicacion($Cnx);
	$app->eliminarFoto($idAplicacion, $nombre);
	$galeria = $app->obtenerGaleriaFotos($idAplicacion);
	$html = "";
	while(!$galeria->EOF){
		$extension = $galeria->fields['extension'];
		$nomSinExt = $galeria->fields['nombre_imagen']; 
		$url = DIR_HTTP_FOTOS_APLICACIONES."{$idAplicacion}/{$nomSinExt}-thu.{$extension}";
		$html .= '<div class="image" id="' . $nomSinExt . '" style="background-image:url(' . $url . ');">';
		$html .= '<a href="#" class="delete">';
        $html .= '<img src="templates/img/ico-lst-eliminar.gif" />';
        $html .= '</a>';
        $html .= '</div>';
		$galeria->MoveNext();
	}
	$objResponse->assign("container", "innerHTML", $html);
	$objResponse->call("prepararBorrados");
	return $objResponse;
}

// Ajax
$xajax->registerFunction("eliminarFoto");
$xajax->processRequest();
$xajax->printJavascript(DIR_XAJAX_PARA_ADMIN);

?>