<?PHP
// includes
include_once(DIR_BASE.'class/table.class.php');
include_once(DIR_BASE.'seguridad/usuario.class.php');
include_once(DIR_BASE.'class/image-handler.class.php');

class Producto extends Table {

	var $TamTextoGrilla = 200;
	var $Ajax;
	var $TablaImg;
	var $ValoresImg;
	var $TablaPresentacion;
	
	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Producto($DB, $AJAX=''){
		// Conexion
		$this->Table($DB, 'producto');
		$this->TablaImg   = new Table($DB, 'producto_foto');
		$this->TablaPresentacion = new Table($DB, 'producto_presentacion');
		$this->AccionesGrid = array(ACC_BAJA, ACC_MODIFICACION, ACC_CONSULTA);
		// Ajax
		$this->Ajax = $AJAX;
	}
	
	function SetSoloLectura(){
		$this->AccionesGrid = array(ACC_CONSULTA);
	}

	function generarFicha($id){
		$ficha = $id . '-';
		$ficha .= HTMLize($this->Registro['nombre_producto']);
		$this->DB->execute("UPDATE producto SET ficha = '$ficha' WHERE id_producto = $id");
	}
	
	function asociarNuevaFoto($idProducto, $fileTmpName, $fileName){
		$errores = "";
		if(!file_exists(DIR_FOTOS_PRODUCTOS."{$idProducto}")){
			mkdir(DIR_FOTOS_PRODUCTOS."{$idProducto}");
			chmod(DIR_FOTOS_PRODUCTOS."{$idProducto}", 0755);
		}
		
		$extension = GetExtension($fileName);
		$this->StartTransaction();
		$index = $this->DB->getOne("SELECT MAX(orden) FROM producto_foto WHERE id_producto = $idProducto");
		$index++;
		$nuevoNombre = HTMLize(str_ireplace(".{$extension}", "", $fileName));
		
		$ImgHandler = new ImageHandler();
		// Imagen Detalle
		if ($ImgHandler->open_image($fileTmpName) == 0){
			// Obtengo la diagramacion para saber el tamano de la foto
			$diagramacion = $this->getDiagramacion($idProducto);
			switch($diagramacion){
				case 1:
					$width = WIDTH_FOTO_PRODUCTO_DIAG_1;
					$height = HEIGHT_FOTO_PRODUCTO_DIAG_1;
					break;

				case 2:
					$width = WIDTH_FOTO_PRODUCTO_DIAG_2;
					$height = HEIGHT_FOTO_PRODUCTO_DIAG_2;
					break;

				case 3:
					$width = WIDTH_FOTO_PRODUCTO_DIAG_3;
					$height = HEIGHT_FOTO_PRODUCTO_DIAG_3;
					break;

				case 4:
					$width = WIDTH_FOTO_PRODUCTO_DIAG_4;
					$height = HEIGHT_FOTO_PRODUCTO_DIAG_4;
					break;

				default:
					$width = WIDTH_FOTO_PRODUCTO_DIAG_1;
					$height = HEIGHT_FOTO_PRODUCTO_DIAG_1;
					break;
			}
			
			// Ajusta la imagen si es necesario
			$ImgHandler->resize_image($width, $height);
			
			// La guarda
			$rutaImg = DIR_FOTOS_PRODUCTOS."{$idProducto}/{$nuevoNombre}.{$extension}";
			$ImgHandler->image_to_file($rutaImg);
		}
		else{
			$errores .= "No se pudo guardar la imagen detalle.\n";
		}
		
		$this->DB->execute("INSERT INTO producto_foto(id_producto, nombre_imagen, extension, orden) VALUES ($idProducto, '{$nuevoNombre}', '{$extension}', $index)");
		$this->CompleteTransaction();
		if($this->DB->ErrorMsg() != ""){
			$errores .= "Ocurrio un error al salvar la imagen.\n";
			LogError("Ocurrio un error al salvar la imagen {$nuevoNombre}.{$extension} de producto $idProducto a la base de datos.\n" . $this->DB->ErrorMsg(), basename(__FILE__), "asociarNuevaFoto($idProducto, $fileTmpName, $fileName)");
		}
		return $errores;
	}
	
	function obtenerGaleriaFotos($idProducto){
		return $this->DB->execute("SELECT * FROM producto_foto WHERE id_producto = {$idProducto} ORDER BY orden");
	}
	
	function eliminarFoto($idProducto, $nombre){
		$ext = $this->DB->getOne("SELECT extension FROM producto_foto WHERE id_producto = {$idProducto} AND nombre_imagen = '{$nombre}'");
		
		$rutaImgThu = DIR_FOTOS_PRODUCTOS."{$idProducto}/{$nombre}-thu.{$ext}";
		if(file_exists($rutaImgThu)){
			@unlink($rutaImgThu);
		}
		
		$rutaImgPrv = DIR_FOTOS_PRODUCTOS."{$idProducto}/{$nombre}-prv.{$ext}";
		if(file_exists($rutaImgPrv)){
			@unlink($rutaImgPrv);
		}
		
		$rutaImg = DIR_FOTOS_PRODUCTOS."{$idProducto}/{$nombre}.{$ext}";
		if(file_exists($rutaImg)){
			@unlink($rutaImg);
		}
		
		$this->StartTransaction();
		$orden = $this->DB->getOne("SELECT orden FROM producto_foto WHERE id_producto = $idProducto AND nombre_imagen = '{$nombre}'");
		$this->DB->execute("DELETE FROM producto_foto WHERE id_producto = {$idProducto} AND nombre_imagen = '{$nombre}'");
		$this->DB->execute("UPDATE producto_foto SET orden = (orden-1) WHERE orden > {$orden} AND id_producto = $idProducto");
		$this->CompleteTransaction();
	}
	
	function ordenarFotos($idProducto, $nuevoOrden){
		$errores = "";
		$i = 1;
		$this->StartTransaction();
		foreach($nuevoOrden as $nombre_imagen){
			$this->DB->execute("UPDATE producto_foto SET orden = {$i} WHERE nombre_imagen = '{$nombre_imagen}' AND id_producto = $idProducto");
			$i++;
		}
		$this->CompleteTransaction();
		if($this->DB->ErrorMsg() != ""){
			$errores .= "Ocurrio un error al ordenar las fotos.\n";
			LogError("Ocurrio un error al ordenar las imagenes de producto $idProducto.\n" . $this->DB->ErrorMsg(), basename(__FILE__), "ordenarFotos($idProducto, $nuevoOrden)");
		}
		return $errores;
	}
	
	// ------------------------------------------------
	// Prepara datos para Grid y PDF's
	// ------------------------------------------------
	function _Registros($Regs=0){
		// Creo grid
		$Grid  = new nyiGridDB('PRODUCTOS', $Regs, 'base_grid.htm');
		
		// Configuro
		$Grid->setParametros(isset($_GET['PVEZ']), 'nombre_producto');
		$Grid->setPaginador('base_navegador.htm');
		$arrCriterios = array(
			'p.id_producto'=>'Identificador',
			'p.marca'=>'Marca',
			'nombre_producto'=>'Nombre', 
			"IF(p.visible, 'Si', 'No')"=>"Visible"
		);
		$Grid->setFrmCriterio('base_criterios_buscador.htm', $arrCriterios);
	
		// Si viene con post
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$Grid->setCriterio($_POST['ORDEN_CAMPO'], $_POST['ORDEN_TXT'], $_POST['CBPAGINA']);
			unset($_GET['NROPAG']);
		}
		else if(isset($_GET['NROPAG'])){
			// Numero de Pagina
			$Grid->setPaginaAct($_GET['NROPAG']);
		}		
	
		$Campos = "p.id_producto AS id, p.nombre_producto, UPPER(p.marca) AS marca, pf.nombre_imagen, pf.extension, IF(p.visible, 'Si', 'No') AS visible";
		$From = "producto p LEFT OUTER JOIN producto_foto pf ON pf.id_producto = p.id_producto AND pf.orden = 1";
		
		$Grid->getDatos($this->DB, $Campos, $From);
		
		// Devuelvo
		return($Grid);
	}

	// ------------------------------------------------
	// Genera Formulario
	// ------------------------------------------------
	function _Frm($Accion){
		// Conexion
		$Cnx = $this->DB;
		$id = $this->Registro['id_producto'];
		$id_aux = $id == "" ? 0 : $id;
		
		// Formulario
		$Form = new nyiHTML('productos/producto-frm.htm');
		$Form->assign('ACC', $Accion);		
		
		// Datos
		$Form->assign('id_producto', $id);
		$Form->assign('nombre_producto', $this->Registro['nombre_producto']);
		$Form->assign('descripcion', $this->Registro['descripcion']);
		$Form->assign('marca', $this->Registro['marca']);
		$Form->assign('diagramacion', $this->Registro['diagramacion']);
		$Form->assign('precio', $this->Registro['precio']);
		$Form->assign('visible', $this->Registro['visible'] == 1 ? 'checked="checked"' : '');
		
		if($Accion != ACC_ALTA && $Accion != ACC_MODIFICACION){
			// Si es una baja o consulta, no dejar editar
			$Form->assign('SOLO_LECTURA', 'readonly');
			$Form->assign('marca_nom', strtoupper($this->Registro['marca']));
		} else {
			$arrMarcas = array('penergetic', 'sagal');
			$Form->assign('marca_ids', $arrMarcas);
			$Form->assign('marca_dsc', array_map("ucfirst", $arrMarcas));
		}
		
		if(isset($_GET["REDIRIGIR"])){
			unset($_GET["REDIRIGIR"]);
			$this->informarSalvarOk();
		}
		
		// Script Post
		$Form->assign('SCRIPT_POST',basename($_SERVER['SCRIPT_NAME']).$Form->fetchParamURL($_GET));
	
		// Cabezal
		$Cab = new nyiHTML('base_cabezal_abm.htm');
		$Cab->assign('NOMFORM', 'PRODUCTOS');
		$Cab->assign('NOMACCION', getNomAccion($Accion));
		$Cab->assign('ACC', $Accion);
		
		// Script Listado
		$Parametros = $_GET;
		unset($Parametros['ACC']);
		unset($Parametros['COD']);
		$Cab->assign('SCRIPT_LIS', basename($_SERVER['SCRIPT_NAME']).$Cab->fetchParamURL($Parametros));
		// Script Salir
		$Cab->assign('SCRIPT_SALIR', basename($_SERVER['SCRIPT_NAME']).$Cab->fetchParamURL($Parametros));
		$Form->assign('NAVEGADOR', $Cab->fetchHTML());
		$Form->assign('ERROR', $this->Error);
	
		// Contenido
		return($Form->fetchHTML());
	}
	
	function getExtensionImagen($id, $i){
		return $this->DB->getOne("SELECT extension_imagen FROM producto_foto WHERE id_producto = $id AND numero_imagen = $i");
	}

	// ------------------------------------------------
	// Cargo campos desde la base de datos
	// ------------------------------------------------
	function _GetDB($Cod=-1,$Campo='id_producto'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}
	
	function getDescripcion($id_producto){
		return $this->DB->getOne("SELECT descripcion FROM producto WHERE id_producto = $id_producto");
	}
	
	function getInfoTecnica($id_producto){
		return $this->DB->getOne("SELECT info_tecnica FROM producto WHERE id_producto = $id_producto");
	}
	
	function getNombre($id_producto){
		return $this->DB->getOne("SELECT nombre_producto FROM producto WHERE id_producto = $id_producto");
	}

	function getDiagramacion($id_producto){
		return $this->DB->getOne("SELECT diagramacion FROM producto WHERE id_producto = $id_producto");
	}

	// ------------------------------------------------
	// Cargo campos desde el formulario
	// ------------------------------------------------
	function _GetFrm(){
		// Valido
		if(!isset($_POST['diagramacion']) || $_POST['diagramacion'] == 0){
			$this->Error .= "Es requerido seleccionar una diagramación.\n";
		}
		if(trim($_POST['marca']) == ""){
			$this->Error .= "Es requerido seleccionar una marca.\n";
		}
		if(trim($_POST['nombre_producto']) == ""){
			$this->Error .= "Es requerido especificar un nombre de producto.\n";
		}
		if(trim($_POST['descripcion']) == ""){
			$this->Error .= "Es requerido especificar una descripción de producto.\n";
		}
		
		if($this->Error == ""){
			// Cargo desde el formulario
			$this->Registro['id_producto'] = $_POST['id_producto'];
			$this->Registro['diagramacion'] = $_POST['diagramacion'];
			$this->Registro['marca'] = addslashes($_POST['marca']);
			$this->Registro['nombre_producto'] = addslashes($_POST['nombre_producto']);
			$this->Registro['descripcion'] =  stripslashes($_POST['descripcion']);
			$this->Registro['visible'] = $_POST['visible'] ? 1 : 0;
		}
	}
	
	// ------------------------------------------------
	// Devuelve html de la Grid
	// ------------------------------------------------
	function grid($Regs){
		// Datos
		$Grid = $this->_Registros($Regs);
		$Grid->addVariable('TAM_TXT', $this->TamTextoGrilla);
		//$Grid->addVariable('DIR_HTTP_FOTOS_PRODUCTOS', DIR_HTTP_FOTOS_PRODUCTOS);
		
		// devuelvo
		return ($Grid->fetchGrid('productos/producto-grid.htm', 'Listado de productos',
								basename($_SERVER['SCRIPT_NAME']), // Paginador
								"", // PDF
								basename($_SERVER['SCRIPT_NAME']), // Home
								basename($_SERVER['SCRIPT_NAME']), // Mto
								$this->AccionesGrid));
	}
	
	function getLastId(){
		return $this->DB->getOne("SELECT max(id_producto) FROM producto");
	}
	
	function afterDelete($id){
		$dir = DIR_FOTOS_PRODUCTOS.$id;
		BorrarDirectorio($dir);
	}
	
	function afterInsert($id){
		$this->Registro['id_producto'] = $id;
		$this->generarFicha($id);
	}
	
	function afterEdit(){
		$this->generarFicha($this->Registro['id_producto']);
		$this->informarSalvarOk();
	}
	
	function informarSalvarOk(){
		$this->Error = "Los datos del producto se han guardado correctamente.";
	}
	
	function SetOrdinal($id, $valOrdinal){
		$sql = "UPDATE producto SET ordinal = $valOrdinal WHERE id_producto = $id";
		$OK = $this->DB->execute($sql);
		if($OK === false){
			$this->Error = $this->DB->ErrorMsg();
		}
	}
	
	function obtenerDatos($idProducto){
		$q =  "SELECT p.id_producto, p.nombre_producto, p.descripcion, ";
		$q .= "CONCAT('" . DIR_HTTP_FOTOS_PRODUCTOS . "', CONCAT(p.id_producto, CONCAT('/', CONCAT(pf.nombre_imagen, CONCAT('-thu.', pf.extension))))) AS url_thumbnail, ";
		$q .= "CONCAT('" . DIR_HTTP_FOTOS_PRODUCTOS . "', CONCAT(p.id_producto, CONCAT('/', CONCAT(pf.nombre_imagen, CONCAT('.', pf.extension))))) AS url_imagen ";
		$q .= "FROM producto p INNER JOIN producto_foto pf ON pf.id_producto = p.id_producto AND pf.orden = 1 WHERE p.id_producto = $idProducto";
		
		return iterator_to_array($this->DB->execute($q));
	}

	function asociarPresentacion($presentacion, $precio, $idProducto){
		$this->TablaPresentacion->Registro["nombre_presentacion"] = $presentacion;
		$this->TablaPresentacion->Registro["precio"] = $precio;
		$this->TablaPresentacion->Registro["id_producto"] = $idProducto;
		$res = $this->TablaPresentacion->TablaDB->addRegistro($this->TablaPresentacion->Registro);
		if($res != ""){
			LogError("Error al asociar presentacion:\n$res", "producto.class.php", "asociarPresentacion($presentacion, $precio, $idProducto)");
			$res = "Ocurrio un problema y no se pudo asociar la presentacion.";
		}
		return $res;
	}

	function obtenerPresentaciones($idProducto){
		$rows = $this->DB->execute("SELECT id_presentacion, nombre_presentacion, precio FROM producto_presentacion WHERE id_producto = $idProducto ORDER BY nombre_presentacion");
		return iterator_to_array($rows);
	}

	function htmlPresentaciones($idProducto){
		$html = new nyiHTML("productos/tabla-presentaciones-producto.htm");
		$html->assign("presentaciones", $this->obtenerPresentaciones($idProducto));
		return $html->fetchHTML();
	}

	function obtenerIdProductoPorPresentacion($idPresentacion){
		$val = $this->DB->getOne("SELECT id_producto FROM producto_presentacion WHERE id_presentacion = $idPresentacion");
		LogArchivo("SELECT id_producto FROM producto_presentacion WHERE id_presentacion = $idPresentacion");
		return $val;
	}

	function eliminarPresentacion($idPresentacion){
		$Ok = $this->DB->execute("DELETE FROM producto_presentacion WHERE id_presentacion = $idPresentacion");
		if($OK === false){
			$error = $this->DB-ErrorMsg();
			LogError("Error al eliminar presentacion:\n$error", "producto.class.php", "eliminarPresentacion($idPresentacion)");
			return "Ocurrio un problema y no se pudo eliminar la presentacion.";
		}
		return "";
	}
}
?>