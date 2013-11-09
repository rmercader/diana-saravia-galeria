<?PHP
// includes
include_once(DIR_BASE.'class/table.class.php');
include_once(DIR_BASE.'seguridad/usuario.class.php');
include_once(DIR_BASE.'class/image-handler.class.php');

class Resultado extends Table {

	var $TamTextoGrilla = 200;
	var $Ajax;
	var $TablaImg;
	var $ValoresImg;
	
	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Resultado($DB, $AJAX=''){
		// Conexion
		$this->Table($DB, 'resultado');
		$this->TablaImg   = new Table($DB, 'resultado_foto');
		$this->AccionesGrid = array(ACC_BAJA, ACC_MODIFICACION, ACC_CONSULTA);
		// Ajax
		$this->Ajax = $AJAX;
	}
	
	function SetSoloLectura(){
		$this->AccionesGrid = array(ACC_CONSULTA);
	}
	
	function asociarNuevaFoto($idResultado, $fileTmpName, $fileName){
		$errores = "";
		if(!file_exists(DIR_FOTOS_RESULTADOS."{$idResultado}")){
			mkdir(DIR_FOTOS_RESULTADOS."{$idResultado}");
			chmod(DIR_FOTOS_RESULTADOS."{$idResultado}", 0755);
		}
		
		$extension = GetExtension($fileName);
		$this->StartTransaction();
		$index = $this->DB->getOne("SELECT MAX(orden) FROM resultado_foto WHERE id_resultado = $idResultado");
		$index++;
		$nuevoNombre = HTMLize(str_ireplace(".{$extension}", "", $fileName));
		
		$ImgHandler = new ImageHandler();
		// Imagen Detalle
		if ($ImgHandler->open_image($fileTmpName) == 0){
			// Obtengo la diagramacion para saber el tamano de la foto
			$diagramacion = $this->getDiagramacion($idResultado);
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

			// Ajusta la imagen si es necesario
			$ImgHandler->resize_image($width, $height);
			
			// La guarda
			$rutaImg = DIR_FOTOS_RESULTADOS."{$idResultado}/{$nuevoNombre}.{$extension}";
			$ImgHandler->image_to_file($rutaImg);
		}
		else{
			$errores .= "No se pudo guardar la imagen detalle.\n";
		}
		
		$this->DB->execute("INSERT INTO resultado_foto(id_resultado, nombre_imagen, extension, orden) VALUES ($idResultado, '{$nuevoNombre}', '{$extension}', $index)");
		$this->CompleteTransaction();
		if($this->DB->ErrorMsg() != ""){
			$errores .= "Ocurrio un error al salvar la imagen.\n";
			LogError("Ocurrio un error al salvar la imagen {$nuevoNombre}.{$extension} de resultado $idResultado a la base de datos.\n" . $this->DB->ErrorMsg(), basename(__FILE__), "asociarNuevaFoto($idResultado, $fileTmpName, $fileName)");
		}
		return $errores;
	}
	
	function obtenerGaleriaFotos($idResultado){
		return $this->DB->execute("SELECT * FROM resultado_foto WHERE id_resultado = {$idResultado} ORDER BY orden");
	}
	
	function eliminarFoto($idResultado, $nombre){
		$ext = $this->DB->getOne("SELECT extension FROM resultado_foto WHERE id_resultado = {$idResultado} AND nombre_imagen = '{$nombre}'");
		
		$rutaImgThu = DIR_FOTOS_RESULTADOS."{$idResultado}/{$nombre}-thu.{$ext}";
		if(file_exists($rutaImgThu)){
			@unlink($rutaImgThu);
		}
		
		$rutaImgPrv = DIR_FOTOS_RESULTADOS."{$idResultado}/{$nombre}-prv.{$ext}";
		if(file_exists($rutaImgPrv)){
			@unlink($rutaImgPrv);
		}
		
		$rutaImg = DIR_FOTOS_RESULTADOS."{$idResultado}/{$nombre}.{$ext}";
		if(file_exists($rutaImg)){
			@unlink($rutaImg);
		}
		
		$this->StartTransaction();
		$orden = $this->DB->getOne("SELECT orden FROM resultado_foto WHERE id_resultado = $idResultado AND nombre_imagen = '{$nombre}'");
		$this->DB->execute("DELETE FROM resultado_foto WHERE id_resultado = {$idResultado} AND nombre_imagen = '{$nombre}'");
		$this->DB->execute("UPDATE resultado_foto SET orden = (orden-1) WHERE orden > {$orden} AND id_resultado = $idResultado");
		$this->CompleteTransaction();
	}
	
	function ordenarFotos($idResultado, $nuevoOrden){
		$errores = "";
		$i = 1;
		$this->StartTransaction();
		foreach($nuevoOrden as $nombre_imagen){
			$this->DB->execute("UPDATE resultado_foto SET orden = {$i} WHERE nombre_imagen = '{$nombre_imagen}' AND id_resultado = $idResultado");
			$i++;
		}
		$this->CompleteTransaction();
		if($this->DB->ErrorMsg() != ""){
			$errores .= "Ocurrio un error al ordenar las fotos.\n";
			LogError("Ocurrio un error al ordenar las imagenes de resultado $idResultado.\n" . $this->DB->ErrorMsg(), basename(__FILE__), "ordenarFotos($idResultado, $nuevoOrden)");
		}
		return $errores;
	}
	
	// ------------------------------------------------
	// Prepara datos para Grid y PDF's
	// ------------------------------------------------
	function _Registros($Regs=0){
		// Creo grid
		$Grid  = new nyiGridDB('RESULTADOS', $Regs, 'base_grid.htm');
		
		// Configuro
		$Grid->setParametros(isset($_GET['PVEZ']), 'nombre_resultado');
		$Grid->setPaginador('base_navegador.htm');
		$arrCriterios = array(
			'p.id_resultado'=>'Identificador',
			'p.marca'=>'Marca',
			'nombre_resultado'=>'Nombre', 
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
	
		$Campos = "p.id_resultado AS id, p.nombre_resultado, UPPER(p.marca) AS marca, pf.nombre_imagen, pf.extension, IF(p.visible, 'Si', 'No') AS visible";
		$From = "resultado p LEFT OUTER JOIN resultado_foto pf ON pf.id_resultado = p.id_resultado AND pf.orden = 1";
		
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
		$id = $this->Registro['id_resultado'];
		$id_aux = $id == "" ? 0 : $id;
		
		// Formulario
		$Form = new nyiHTML('resultados/resultado-frm.htm');
		$Form->assign('ACC', $Accion);		
		
		// Datos
		$Form->assign('id_resultado', $id);
		$Form->assign('marca', $this->Registro['marca']);
		$Form->assign('diagramacion', $this->Registro['diagramacion']);
		$Form->assign('nombre_resultado', $this->Registro['nombre_resultado']);
		$Form->assign('descripcion', $this->Registro['descripcion']);
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
		$Cab->assign('NOMFORM', 'RESULTADOS');
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
		return $this->DB->getOne("SELECT extension_imagen FROM resultado_foto WHERE id_resultado = $id AND numero_imagen = $i");
	}

	// ------------------------------------------------
	// Cargo campos desde la base de datos
	// ------------------------------------------------
	function _GetDB($Cod=-1,$Campo='id_resultado'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}

	function getDiagramacion($id_resultado){
		return $this->DB->getOne("SELECT diagramacion FROM resultado WHERE id_resultado = $id_resultado");
	}
	
	function getDescripcion($id_resultado){
		return $this->DB->getOne("SELECT descripcion FROM resultado WHERE id_resultado = $id_resultado");
	}
	
	function getNombre($id_resultado){
		return $this->DB->getOne("SELECT nombre_resultado FROM resultado WHERE id_resultado = $id_resultado");
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
		if(trim($_POST['nombre_resultado']) == ""){
			$this->Error .= "Es requerido especificar un nombre de resultado.\n";
		}
		if($this->Error == ""){
			// Cargo desde el formulario
			$this->Registro['id_resultado'] = $_POST['id_resultado'];
			$this->Registro['marca'] = $_POST['marca'];
			$this->Registro['diagramacion'] = $_POST['diagramacion'];
			$this->Registro['nombre_resultado'] = addslashes($_POST['nombre_resultado']);
			$this->Registro['descripcion'] = stripslashes($_POST['descripcion']);
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
		
		// devuelvo
		return ($Grid->fetchGrid('resultados/resultado-grid.htm', 'Listado de resultados',
								basename($_SERVER['SCRIPT_NAME']), // Paginador
								"", // PDF
								basename($_SERVER['SCRIPT_NAME']), // Home
								basename($_SERVER['SCRIPT_NAME']), // Mto
								$this->AccionesGrid));
	}
	
	function getLastId(){
		return $this->DB->getOne("SELECT max(id_resultado) FROM resultado");
	}
	
	function afterDelete($id){
		$dir = DIR_FOTOS_RESULTADOS.$id;
		BorrarDirectorio($dir);
	}
	
	function afterInsert($id){
		$this->Registro['id_resultado'] = $id;
		$this->generarFicha($id);
	}
	
	function afterEdit(){
		$this->generarFicha($this->Registro['id_resultado']);
		$this->informarSalvarOk();
	}
	
	function informarSalvarOk(){
		$this->Error = "Los datos del resultado se han guardado correctamente.";
	}
	
	function SetOrdinal($id, $valOrdinal){
		$sql = "UPDATE resultado SET ordinal = $valOrdinal WHERE id_resultado = $id";
		$OK = $this->DB->execute($sql);
		if($OK === false){
			$this->Error = $this->DB->ErrorMsg();
		}
	}
	
	function obtenerDatos($idResultado){
		$q =  "SELECT p.id_resultado, p.nombre_resultado, p.descripcion, FORMAT(p.precio, 1) AS precio, ";
		$q .= "CONCAT('" . DIR_HTTP_FOTOS_RESULTADOS . "', CONCAT(p.id_resultado, CONCAT('/', CONCAT(pf.nombre_imagen, CONCAT('-thu.', pf.extension))))) AS url_thumbnail, ";
		$q .= "CONCAT('" . DIR_HTTP_FOTOS_RESULTADOS . "', CONCAT(p.id_resultado, CONCAT('/', CONCAT(pf.nombre_imagen, CONCAT('.', pf.extension))))) AS url_imagen ";
		$q .= "FROM resultado p INNER JOIN resultado_foto pf ON pf.id_resultado = p.id_resultado AND pf.orden = 1 WHERE p.id_resultado = $idResultado";
		
		return iterator_to_array($this->DB->execute($q));
	}

	function generarFicha($id){
		$ficha = $id . '-';
		$ficha .= HTMLize($this->Registro['nombre_resultado']);
		$this->DB->execute("UPDATE resultado SET ficha = '$ficha' WHERE id_resultado = $id");
	}
}
?>