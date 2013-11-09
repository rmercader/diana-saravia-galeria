<?PHP

if(!isset($_GET['COD']) || !is_numeric($_GET['COD']) || intval($_GET['COD']) == 0){
	header("Location: admin-catalogo.php");	
	exit(0);
}

// Includes
include(DIR_BASE.'productos/producto.class.php');

// Objeto
$objProducto = new Producto($Cnx, $xajax);

$mod_Contenido = '';
$error = "";
$html = new nyiHTML('productos/presentaciones-producto.htm');

// Si viene con POST
if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Extraigo
	$presentacion = $_POST["nombre_presentacion"];
	$precio = $_POST["precio"];
	$id_producto = $_POST["id_producto"];

	// Valido
	if(trim($presentacion) == ""){
		$error .= "El campo Nombre de presentación es requerido.\n";
	}

	if(!is_numeric($precio)){
		$error .= "El campo Precio requiere un número válido.\n";
	}
	elseif($precio <= 0){
		$error .= "El campo Precio requiere un valor mayor que cero.\n";
	}

	if($error == ""){
		$res = $objProducto->asociarPresentacion($presentacion, $precio, $id_producto);
		if($res != ""){
			$error = $res;
		}
		else{
			$error = "La presentación fue asociada correctamente.";
		}
	}
}

// Extraigo presentaciones
$html->assign('htmlPresentaciones', $objProducto->htmlPresentaciones($_GET['COD']));

// Script Post
$html->assign('SCRIPT_POST', basename($_SERVER['SCRIPT_NAME']).$html->fetchParamURL($_GET));

// Cabezal
$Cab = new nyiHTML('base_cabezal_abm.htm');
$Cab->assign('NOMFORM', 'PRESENTACIONES DEL PRODUCTO: ' . strtoupper($objProducto->getNombre($_GET['COD'])));
$Cab->assign('NOMACCION', "Edición");
$Cab->assign('ACC', ACC_VER);
// Script Salir
$Cab->assign('SCRIPT_SALIR', "admin-catalogo.php");
$html->assign('NAVEGADOR', $Cab->fetchHTML());

$html->assign('id_producto', $_GET['COD']);
$html->assign('ERROR', $error);
$xajax->setRequestURI(DIR_HTTP.'productos/presentaciones-producto-ajax.php');
$xajax->registerFunction("eliminarPresentacion");

$mod_Contenido = $html->fetchHTML();

?>