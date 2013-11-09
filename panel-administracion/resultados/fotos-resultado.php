<?PHP

if(!isset($_GET['COD']) || !is_numeric($_GET['COD']) || intval($_GET['COD']) == 0){
	header("Location: admin-resultados.php");	
	exit(0);
}

// Includes
include(DIR_BASE.'resultados/resultado.class.php');

// Objeto
$objResultado = new Resultado($Cnx, $xajax);

$mod_Contenido = '';
$error = "";
$html = new nyiHTML('resultados/fotos-resultado.htm');

// Si viene con POST
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_POST["subefoto"]) && $_POST["subefoto"] == _SI && is_uploaded_file($_FILES["fotonueva"]["tmp_name"])){
		$res = $objResultado->asociarNuevaFoto($_GET['COD'], $_FILES["fotonueva"]["tmp_name"], $_FILES["fotonueva"]["name"]);
		if($res != ""){
			$error = $res;
		}
	}
	elseif(isset($_POST["orden"]) && $_POST["orden"] != ""){
		$orden = explode(",", $_POST["orden"]);
		$nuevoOrden = array();
		foreach($orden as $item){
			array_push($nuevoOrden, trim($item));
		}
		$res = $objResultado->ordenarFotos($_GET['COD'], $nuevoOrden);
		if($res != ""){
			$error = $res;
		}
		else{
			$error = "Las fotos fueron ordenadas correctamente.";
		}
	}
}

$galeria = $objResultado->obtenerGaleriaFotos($_GET['COD']);
while(!$galeria->EOF){
	$extension = $galeria->fields['extension'];
	$nomSinExt = $galeria->fields['nombre_imagen']; 
	$html->append('FOTOS', array('nombre_imagen'=>$nomSinExt, 'url'=>DIR_HTTP_FOTOS_RESULTADOS."{$_GET['COD']}/{$nomSinExt}.{$extension}?c=" . time()));
	$galeria->MoveNext();
}

// Script Post
$html->assign('SCRIPT_POST', basename($_SERVER['SCRIPT_NAME']).$html->fetchParamURL($_GET));

// Cabezal
$Cab = new nyiHTML('base_cabezal_abm.htm');
$Cab->assign('NOMFORM', 'FOTOS DEL RESULTADO: ' . strtoupper($objResultado->getNombre($_GET['COD'])));
$Cab->assign('NOMACCION', "Edición");
$Cab->assign('ACC', ACC_POST);
// Script Salir
$Cab->assign('SCRIPT_SALIR', "admin-catalogo.php");
$html->assign('NAVEGADOR', $Cab->fetchHTML());
$diagramacion = $objResultado->getDiagramacion($_GET['COD']);
switch($diagramacion){
	case 1:
		$width = WIDTH_FOTO_RESULTADO_DIAG_1;
		$height = HEIGHT_FOTO_RESULTADO_DIAG_1;
		break;

	case 2:
		$width = WIDTH_FOTO_RESULTADO_DIAG_2;
		$height = HEIGHT_FOTO_RESULTADO_DIAG_2;
		break;

	case 3:
		$width = WIDTH_FOTO_RESULTADO_DIAG_3;
		$height = HEIGHT_FOTO_RESULTADO_DIAG_3;
		break;

	case 4:
		$width = WIDTH_FOTO_RESULTADO_DIAG_4;
		$height = HEIGHT_FOTO_RESULTADO_DIAG_4;
		break;

	default:
		$width = WIDTH_FOTO_RESULTADO_DIAG_1;
		$height = HEIGHT_FOTO_RESULTADO_DIAG_1;
		break;
}
$html->assign('LARGO_THUMBNAIL_RESULTADO', $width);
$html->assign('ANCHO_THUMBNAIL_RESULTADO', $height);
$html->assign('id_resultado', $_GET['COD']);
$html->assign('ERROR', $error);
$xajax->setRequestURI(DIR_HTTP.'resultados/fotos-resultado-ajax.php');
$xajax->registerFunction("eliminarFoto");

$mod_Contenido = $html->fetchHTML();

?>