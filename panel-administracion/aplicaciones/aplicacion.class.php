<?PHP
// includes
include_once(DIR_BASE.'class/table.class.php');
include_once(DIR_BASE.'seguridad/usuario.class.php');
include_once(DIR_BASE.'class/image-handler.class.php');

class Aplicacion extends Table {

	var $TamTextoGrilla = 200;
	var $Ajax;
	var $TablaImg;
	var $ValoresImg;
	
	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Aplicacion($DB, $AJAX=''){
		// Conexion
		$this->Table($DB, 'aplicacion');
		$this->TablaImg   = new Table($DB, 'aplicacion_foto');
		$this->AccionesGrid = array(ACC_BAJA, ACC_MODIFICACION, ACC_CONSULTA);
		// Ajax
		$this->Ajax = $AJAX;
	}
	
	function SetSoloLectura(){
		$this->AccionesGrid = array(ACC_CONSULTA);
	}
	
	function asociarNuevaFoto($idAplicacion, $fileTmpName, $fileName){
		$errores = "";
		if(!file_exists(DIR_FOTOS_APLICACIONES."{$idAplicacion}")){
			mkdir(DIR_FOTOS_APLICACIONES."{$idAplicacion}");
			chmod(DIR_FOTOS_APLICACIONES."{$idAplicacion}", 0755);
		}
		
		$extension = GetExtension($fileName);
		$this->StartTransaction();
		$index = $this->DB->getOne("SELECT MAX(orden) FROM aplicacion_foto WHERE id_aplicacion = $idAplicacion");
		$index++;
		$nuevoNombre = HTMLize(str_ireplace(".{$extension}", "", $fileName));
		
		$ImgHandler = new ImageHandler();
		// Imagen Thumbnail
		if ($ImgHandler->open_image($fileTmpName) == 0){
			// Ajusta la imagen si es necesario
			$ImgHandler->resize_image(LARGO_THUMBNAIL_APLICACION, ANCHO_THUMBNAIL_APLICACION);
			// La guarda
			$rutaImgThu = DIR_FOTOS_APLICACIONES."{$idAplicacion}/{$nuevoNombre}-thu.{$extension}";
			$ImgHandler->image_to_file($rutaImgThu);
		}
		else{
			$errores .= "No se pudo guardar la imagen thumbnail.\n";
		}
		// Imagen Preview
		if ($ImgHandler->open_image($fileTmpName) == 0){
			// Ajusta la imagen si es necesario
			$ImgHandler->resize_image(LARGO_PREVIEW_APLICACION, ANCHO_PREVIEW_APLICACION);
			// La guarda
			$rutaImgPrv = DIR_FOTOS_APLICACIONES."{$idAplicacion}/{$nuevoNombre}-prv.{$extension}";
			$ImgHandler->image_to_file($rutaImgPrv);
		}
		else{
			$errores .= "No se pudo guardar la imagen preview.\n";
		}
		// Imagen Detalle
		if ($ImgHandler->open_image($fileTmpName) == 0){
			// Ajusta la imagen si es necesario
			$ImgHandler->resize_image(LARGO_FOTO_APLICACION, ANCHO_FOTO_APLICACION);
			// La guarda
			$rutaImg = DIR_FOTOS_APLICACIONES."{$idAplicacion}/{$nuevoNombre}.{$extension}";
			$ImgHandler->image_to_file($rutaImg);
		}
		else{
			$errores .= "No se pudo guardar la imagen detalle.\n";
		}
		
		$this->DB->execute("INSERT INTO aplicacion_foto(id_aplicacion, nombre_imagen, extension, orden) VALUES ($idAplicacion, '{$nuevoNombre}', '{$extension}', $index)");
		$this->CompleteTransaction();
		if($this->DB->ErrorMsg() != ""){
			$errores .= "Ocurrio un error al salvar la imagen.\n";
			LogError("Ocurrio un error al salvar la imagen {$nuevoNombre}.{$extension} de aplicacion $idAplicacion a la base de datos.\n" . $this->DB->ErrorMsg(), basename(__FILE__), "asociarNuevaFoto($idAplicacion, $fileTmpName, $fileName)");
		}
		return $errores;
	}
	
	function obtenerGaleriaFotos($idAplicacion){
		return $this->DB->execute("SELECT * FROM aplicacion_foto WHERE id_aplicacion = {$idAplicacion} ORDER BY orden");
	}
	
	function eliminarFoto($idAplicacion, $nombre){
		$ext = $this->DB->getOne("SELECT extension FROM aplicacion_foto WHERE id_aplicacion = {$idAplicacion} AND nombre_imagen = '{$nombre}'");
		
		$rutaImgThu = DIR_FOTOS_APLICACIONES."{$idAplicacion}/{$nombre}-thu.{$ext}";
		if(file_exists($rutaImgThu)){
			@unlink($rutaImgThu);
		}
		
		$rutaImgPrv = DIR_FOTOS_APLICACIONES."{$idAplicacion}/{$nombre}-prv.{$ext}";
		if(file_exists($rutaImgPrv)){
			@unlink($rutaImgPrv);
		}
		
		$rutaImg = DIR_FOTOS_APLICACIONES."{$idAplicacion}/{$nombre}.{$ext}";
		if(file_exists($rutaImg)){
			@unlink($rutaImg);
		}
		
		$this->StartTransaction();
		$orden = $this->DB->getOne("SELECT orden FROM aplicacion_foto WHERE id_aplicacion = $idAplicacion AND nombre_imagen = '{$nombre}'");
		$this->DB->execute("DELETE FROM aplicacion_foto WHERE id_aplicacion = {$idAplicacion} AND nombre_imagen = '{$nombre}'");
		$this->DB->execute("UPDATE aplicacion_foto SET orden = (orden-1) WHERE orden > {$orden} AND id_aplicacion = $idAplicacion");
		$this->CompleteTransaction();
	}
	
	function ordenarFotos($idAplicacion, $nuevoOrden){
		$errores = "";
		$i = 1;
		$this->StartTransaction();
		foreach($nuevoOrden as $nombre_imagen){
			$this->DB->execute("UPDATE aplicacion_foto SET orden = {$i} WHERE nombre_imagen = '{$nombre_imagen}' AND id_aplicacion = $idAplicacion");
			$i++;
		}
		$this->CompleteTransaction();
		if($this->DB->ErrorMsg() != ""){
			$errores .= "Ocurrio un error al ordenar las fotos.\n";
			LogError("Ocurrio un error al ordenar las imagenes de aplicacion $idAplicacion.\n" . $this->DB->ErrorMsg(), basename(__FILE__), "ordenarFotos($idAplicacion, $nuevoOrden)");
		}
		return $errores;
	}
	
	// ------------------------------------------------
	// Prepara datos para Grid y PDF's
	// ------------------------------------------------
	function _Registros($Regs=0){
		// Creo grid
		$Grid  = new nyiGridDB('APLICACIONES', $Regs, 'base_grid.htm');
		
		// Configuro
		$Grid->setParametros(isset($_GET['PVEZ']), 'nombre_aplicacion');
		$Grid->setPaginador('base_navegador.htm');
		$arrCriterios = array(
			'p.id_aplicacion'=>'Identificador',
			'nombre_aplicacion'=>'Nombre', 
			"IF(p.visible, 'Si', 'No')"=>"Visible",
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
	
		$Campos = "p.id_aplicacion AS id, p.nombre_aplicacion, pf.nombre_imagen, pf.extension, IF(p.visible, 'Si', 'No') AS visible";
		$From = "aplicacion p LEFT OUTER JOIN aplicacion_foto pf ON pf.id_aplicacion = p.id_aplicacion AND pf.orden = 1";
		
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
		$id = $this->Registro['id_aplicacion'];
		$id_aux = $id == "" ? 0 : $id;
		
		// Formulario
		$Form = new nyiHTML('aplicaciones/aplicacion-frm.htm');
		$Form->assign('ACC', $Accion);		
		
		// Datos
		$Form->assign('id_aplicacion', $id);
		$Form->assign('nombre_aplicacion', $this->Registro['nombre_aplicacion']);
		$Form->assign('visible', $this->Registro['visible'] == 1 ? 'checked="checked"' : '');
		
		// Tengo que meterlo como caja de texto enriquecido
		$editor = new FCKeditor('DESCRIPCION') ;
		$editor->BasePath = 'fckeditor/' ;
		$editor->Height = ALTURA_EDITOR;
		$editor->Config['EnterMode'] = 'br';
		$editor->Value = $this->Registro['descripcion'];
		$contenido = $editor->CreateHtml();
		$Form->assign('DESCRIPCION', $contenido);
		
		if($Accion != ACC_ALTA && $Accion != ACC_MODIFICACION){
			// Si es una baja o consulta, no dejar editar
			$Form->assign('SOLO_LECTURA', 'readonly');
		}
		
		if(isset($_GET["REDIRIGIR"])){
			unset($_GET["REDIRIGIR"]);
			$this->informarSalvarOk();
		}
		
		// Script Post
		$Form->assign('SCRIPT_POST',basename($_SERVER['SCRIPT_NAME']).$Form->fetchParamURL($_GET));
	
		// Cabezal
		$Cab = new nyiHTML('base_cabezal_abm.htm');
		$Cab->assign('NOMFORM', 'APLICACIONES');
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
		return $this->DB->getOne("SELECT extension_imagen FROM aplicacion_foto WHERE id_aplicacion = $id AND numero_imagen = $i");
	}

	// ------------------------------------------------
	// Cargo campos desde la base de datos
	// ------------------------------------------------
	function _GetDB($Cod=-1,$Campo='id_aplicacion'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}
	
	function getDescripcion($id_aplicacion){
		return $this->DB->getOne("SELECT descripcion FROM aplicacion WHERE id_aplicacion = $id_aplicacion");
	}
	
	function getNombre($id_aplicacion){
		return $this->DB->getOne("SELECT nombre_aplicacion FROM aplicacion WHERE id_aplicacion = $id_aplicacion");
	}
	
	// ------------------------------------------------
	// Cargo campos desde el formulario
	// ------------------------------------------------
	function _GetFrm(){
		// Cargo desde el formulario
		$this->Registro['id_aplicacion'] = $_POST['id_aplicacion'];
		$this->Registro['nombre_aplicacion'] = $_POST['nombre_aplicacion'];
		$this->Registro['descripcion'] = stripslashes($_POST['DESCRIPCION']);
		$this->Registro['visible'] = $_POST['visible'] ? 1 : 0;
	}
	
	// ------------------------------------------------
	// Devuelve html de la Grid
	// ------------------------------------------------
	function grid($Regs){
		// Datos
		$Grid = $this->_Registros($Regs);
		$Grid->addVariable('TAM_TXT', $this->TamTextoGrilla);
		
		// devuelvo
		return ($Grid->fetchGrid('aplicaciones/aplicacion-grid.htm', 'Listado de aplicacions',
								basename($_SERVER['SCRIPT_NAME']), // Paginador
								"", // PDF
								basename($_SERVER['SCRIPT_NAME']), // Home
								basename($_SERVER['SCRIPT_NAME']), // Mto
								$this->AccionesGrid));
	}
	
	function getLastId(){
		return $this->DB->getOne("SELECT max(id_aplicacion) FROM aplicacion");
	}
	
	function afterDelete($id){
		$dir = DIR_FOTOS_APLICACIONES.$id;
		BorrarDirectorio($dir);
	}
	
	function afterInsert($id){
		$this->Registro['id_aplicacion'] = $id;
	}
	
	function afterEdit(){
		$this->informarSalvarOk();
	}
	
	function informarSalvarOk(){
		$this->Error = "Los datos de la aplicación se han guardado correctamente.";
	}
	
	function SetOrdinal($id, $valOrdinal){
		$sql = "UPDATE aplicacion SET ordinal = $valOrdinal WHERE id_aplicacion = $id";
		$OK = $this->DB->execute($sql);
		if($OK === false){
			$this->Error = $this->DB->ErrorMsg();
		}
	}
	
	function obtenerDatos($idAplicacion){
		$q =  "SELECT p.id_aplicacion, p.nombre_aplicacion, p.descripcion, FORMAT(p.precio, 1) AS precio, ";
		$q .= "CONCAT('" . DIR_HTTP_FOTOS_APLICACIONES . "', CONCAT(p.id_aplicacion, CONCAT('/', CONCAT(pf.nombre_imagen, CONCAT('-thu.', pf.extension))))) AS url_thumbnail, ";
		$q .= "CONCAT('" . DIR_HTTP_FOTOS_APLICACIONES . "', CONCAT(p.id_aplicacion, CONCAT('/', CONCAT(pf.nombre_imagen, CONCAT('.', pf.extension))))) AS url_imagen ";
		$q .= "FROM aplicacion p INNER JOIN aplicacion_foto pf ON pf.id_aplicacion = p.id_aplicacion AND pf.orden = 1 WHERE p.id_aplicacion = $idAplicacion";
		
		return iterator_to_array($this->DB->execute($q));
	}
}
?>