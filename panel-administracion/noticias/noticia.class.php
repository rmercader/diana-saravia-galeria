<?PHP
// includes
include_once(DIR_BASE.'class/table.class.php');
include_once(DIR_BASE.'seguridad/usuario.class.php');
include_once(DIR_BASE.'class/image-handler.class.php');

class Noticia extends Table {

	var $TamTextoGrilla = 200;
	var $Ajax;
	var $TablaImg;
	var $ValoresImg;
	
	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Noticia($DB, $AJAX=''){
		// Conexion
		$this->Table($DB, 'noticia');
		$this->TablaImg   = new Table($DB, 'noticia_foto');
		$this->AccionesGrid = array(ACC_BAJA, ACC_MODIFICACION, ACC_CONSULTA);
		// Ajax
		$this->Ajax = $AJAX;
	}
	
	function SetSoloLectura(){
		$this->AccionesGrid = array(ACC_CONSULTA);
	}
	
	function asociarNuevaFoto($idNoticia, $fileTmpName, $fileName){
		$errores = "";
		if(!file_exists(DIR_FOTOS_NOTICIAS."{$idNoticia}")){
			mkdir(DIR_FOTOS_NOTICIAS."{$idNoticia}");
			chmod(DIR_FOTOS_NOTICIAS."{$idNoticia}", 0755);
		}
		
		$extension = GetExtension($fileName);
		$this->StartTransaction();
		$index = $this->DB->getOne("SELECT MAX(orden) FROM noticia_foto WHERE id_noticia = $idNoticia");
		$index++;
		$nuevoNombre = HTMLize(str_ireplace(".{$extension}", "", $fileName));
		
		$ImgHandler = new ImageHandler();
		// Imagen Detalle
		if ($ImgHandler->open_image($fileTmpName) == 0){
			// Obtengo la diagramacion para saber el tamano de la foto
			$diagramacion = $this->getDiagramacion($idNoticia);
			switch($diagramacion){
				case 1:
					$width = WIDTH_FOTO_NOTICIA_DIAG_1;
					$height = HEIGHT_FOTO_NOTICIA_DIAG_1;
					break;

				case 2:
					$width = WIDTH_FOTO_NOTICIA_DIAG_2;
					$height = HEIGHT_FOTO_NOTICIA_DIAG_2;
					break;

				case 3:
					$width = WIDTH_FOTO_NOTICIA_DIAG_3;
					$height = HEIGHT_FOTO_NOTICIA_DIAG_3;
					break;

				case 4:
					$width = WIDTH_FOTO_NOTICIA_DIAG_4;
					$height = HEIGHT_FOTO_NOTICIA_DIAG_4;
					break;

				default:
					$width = WIDTH_FOTO_NOTICIA_DIAG_1;
					$height = HEIGHT_FOTO_NOTICIA_DIAG_1;
					break;
			}
			// Ajusta la imagen si es necesario
			$ImgHandler->resize_image($width, $height);
			
			// La guarda
			$rutaImg = DIR_FOTOS_NOTICIAS."{$idNoticia}/{$nuevoNombre}.{$extension}";
			$ImgHandler->image_to_file($rutaImg);
		}
		else{
			$errores .= "No se pudo guardar la imagen detalle.\n";
		}
		
		$this->DB->execute("INSERT INTO noticia_foto(id_noticia, nombre_imagen, extension, orden) VALUES ($idNoticia, '{$nuevoNombre}', '{$extension}', $index)");
		$this->CompleteTransaction();
		if($this->DB->ErrorMsg() != ""){
			$errores .= "Ocurrio un error al salvar la imagen.\n";
			LogError("Ocurrio un error al salvar la imagen {$nuevoNombre}.{$extension} de noticia $idNoticia a la base de datos.\n" . $this->DB->ErrorMsg(), basename(__FILE__), "asociarNuevaFoto($idNoticia, $fileTmpName, $fileName)");
		}
		return $errores;
	}
	
	function obtenerGaleriaFotos($idNoticia){
		return $this->DB->execute("SELECT * FROM noticia_foto WHERE id_noticia = {$idNoticia} ORDER BY orden");
	}
	
	function eliminarFoto($idNoticia, $nombre){
		$ext = $this->DB->getOne("SELECT extension FROM noticia_foto WHERE id_noticia = {$idNoticia} AND nombre_imagen = '{$nombre}'");
		
		$rutaImgThu = DIR_FOTOS_NOTICIAS."{$idNoticia}/{$nombre}-thu.{$ext}";
		if(file_exists($rutaImgThu)){
			@unlink($rutaImgThu);
		}
		
		$rutaImgPrv = DIR_FOTOS_NOTICIAS."{$idNoticia}/{$nombre}-prv.{$ext}";
		if(file_exists($rutaImgPrv)){
			@unlink($rutaImgPrv);
		}
		
		$rutaImg = DIR_FOTOS_NOTICIAS."{$idNoticia}/{$nombre}.{$ext}";
		if(file_exists($rutaImg)){
			@unlink($rutaImg);
		}
		
		$this->StartTransaction();
		$orden = $this->DB->getOne("SELECT orden FROM noticia_foto WHERE id_noticia = $idNoticia AND nombre_imagen = '{$nombre}'");
		$this->DB->execute("DELETE FROM noticia_foto WHERE id_noticia = {$idNoticia} AND nombre_imagen = '{$nombre}'");
		$this->DB->execute("UPDATE noticia_foto SET orden = (orden-1) WHERE orden > {$orden} AND id_noticia = $idNoticia");
		$this->CompleteTransaction();
	}
	
	function ordenarFotos($idNoticia, $nuevoOrden){
		$errores = "";
		$i = 1;
		$this->StartTransaction();
		foreach($nuevoOrden as $nombre_imagen){
			$this->DB->execute("UPDATE noticia_foto SET orden = {$i} WHERE nombre_imagen = '{$nombre_imagen}' AND id_noticia = $idNoticia");
			$i++;
		}
		$this->CompleteTransaction();
		if($this->DB->ErrorMsg() != ""){
			$errores .= "Ocurrio un error al ordenar las fotos.\n";
			LogError("Ocurrio un error al ordenar las imagenes de noticia $idNoticia.\n" . $this->DB->ErrorMsg(), basename(__FILE__), "ordenarFotos($idNoticia, $nuevoOrden)");
		}
		return $errores;
	}
	
	// ------------------------------------------------
	// Prepara datos para Grid y PDF's
	// ------------------------------------------------
	function _Registros($Regs=0){
		// Creo grid
		$Grid  = new nyiGridDB('NOTICIAS', $Regs, 'base_grid.htm');
		
		// Configuro
		$Grid->setParametros(isset($_GET['PVEZ']), 'titulo');
		$Grid->setPaginador('base_navegador.htm');
		$arrCriterios = array(
			'id'=>'Identificador',
			'titulo'=>'T&iacute;tulo', 
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
	
		$Campos = "p.id_noticia AS id, p.titulo, pf.nombre_imagen, pf.extension, IF(p.visible, 'Si', 'No') AS visible, p.copete";
		$From = "noticia p LEFT OUTER JOIN noticia_foto pf ON pf.id_noticia = p.id_noticia AND pf.orden = 1";
		
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
		$id = $this->Registro['id_noticia'];
		$id_aux = $id == "" ? 0 : $id;
		
		// Formulario
		$Form = new nyiHTML('noticias/noticia-frm.htm');
		$Form->assign('ACC', $Accion);		
		
		// Datos
		$Form->assign('id_noticia', $id);
		$Form->assign('titulo', $this->Registro['titulo']);
		$Form->assign('copete', $this->Registro['copete']);
		$Form->assign('cuerpo', $this->Registro['cuerpo']);
		$Form->assign('diagramacion', $this->Registro['diagramacion']);
		$Form->assign('visible', $this->Registro['visible'] == 1 ? 'checked="checked"' : '');
		
		if($Accion != ACC_ALTA && $Accion != ACC_MODIFICACION){
			// Si es una baja o consulta, no dejar editar
			$Form->assign('SOLO_LECTURA', 'readonly');
			$Form->assign('src_imagen', DIR_HTTP_FOTOS_NOTICIAS . "$id.jpg");
		}
		
		if(isset($_GET["REDIRIGIR"])){
			unset($_GET["REDIRIGIR"]);
			$this->informarSalvarOk();
		}
		
		// Script Post
		$Form->assign('SCRIPT_POST',basename($_SERVER['SCRIPT_NAME']).$Form->fetchParamURL($_GET));
	
		// Cabezal
		$Cab = new nyiHTML('base_cabezal_abm.htm');
		$Cab->assign('NOMFORM', 'NOTICIAS');
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
		return $this->DB->getOne("SELECT extension_imagen FROM noticia_foto WHERE id_noticia = $id AND numero_imagen = $i");
	}

	// ------------------------------------------------
	// Cargo campos desde la base de datos
	// ------------------------------------------------
	function _GetDB($Cod=-1,$Campo='id_noticia'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}
	
	function getDescripcion($id_noticia){
		return $this->DB->getOne("SELECT descripcion FROM noticia WHERE id_noticia = $id_noticia");
	}
	
	function getInfoTecnica($id_noticia){
		return $this->DB->getOne("SELECT info_tecnica FROM noticia WHERE id_noticia = $id_noticia");
	}
	
	function getNombre($id_noticia){
		return $this->DB->getOne("SELECT titulo FROM noticia WHERE id_noticia = $id_noticia");
	}

	function getDiagramacion($id_noticia){
		return $this->DB->getOne("SELECT diagramacion FROM noticia WHERE id_noticia = $id_noticia");
	}

	// ------------------------------------------------
	// Cargo campos desde el formulario
	// ------------------------------------------------
	function _GetFrm(){
		// Valido
		if(!isset($_POST['diagramacion']) || $_POST['diagramacion'] == 0){
			$this->Error .= "Es requerido seleccionar una diagramación.\n";
		}
		if(trim($_POST['titulo']) == ""){
			$this->Error .= "Es requerido especificar un título de noticia.\n";
		}
		if(trim($_POST['copete']) == ""){
			$this->Error .= "Es requerido especificar un texto inicial de noticia.\n";
		}
		if(trim($_POST['cuerpo']) == ""){
			$this->Error .= "Es requerido especificar un texto principal de noticia.\n";
		}
		if($this->Error == ""){
			// Cargo desde el formulario
			$this->Registro['id_noticia'] = $_POST['id_noticia'];
			$this->Registro['diagramacion'] = addslashes($_POST['diagramacion']);
			$this->Registro['titulo'] = addslashes($_POST['titulo']);
			$this->Registro['copete'] = addslashes($_POST['copete']);
			$this->Registro['cuerpo'] = stripslashes($_POST['cuerpo']);
			$this->Registro['visible'] = $_POST['visible'] ? 1 : 0;
			if($this->Registro['id_noticia'] == ""){
				$this->Registro['fecha_creacion'] = date('Y')."-".date('m')."-".date('d')." ".date('H').":".date('i').":00";
			}
		}
	}
	
	// ------------------------------------------------
	// Devuelve html de la Grid
	// ------------------------------------------------
	function grid($Regs){
		// Datos
		$Grid = $this->_Registros($Regs);
		$Grid->addVariable('TAM_TXT', $this->TamTextoGrilla);
		//$Grid->addVariable('DIR_HTTP_FOTOS_NOTICIAS', DIR_HTTP_FOTOS_NOTICIAS);
		
		// devuelvo
		return ($Grid->fetchGrid('noticias/noticia-grid.htm', 'Listado de noticias',
								basename($_SERVER['SCRIPT_NAME']), // Paginador
								"", // PDF
								basename($_SERVER['SCRIPT_NAME']), // Home
								basename($_SERVER['SCRIPT_NAME']), // Mto
								$this->AccionesGrid));
	}
	
	function getLastId(){
		return $this->DB->getOne("SELECT max(id_noticia) FROM noticia");
	}
	
	function afterDelete($id){
		$dir = DIR_FOTOS_NOTICIAS.$id;
		BorrarDirectorio($dir);
	}
	
	function afterInsert($id){
		$this->Registro['id_noticia'] = $id;
		$this->salvarImagenMiniatura($id);
		$this->generarFicha($id);
	}
	
	function afterEdit(){
		$this->generarFicha($this->Registro['id_noticia']);
		$this->salvarImagenMiniatura($this->Registro['id_noticia']);
		$this->informarSalvarOk();
	}
	
	function informarSalvarOk(){
		$this->Error = "Los datos de la noticia se han guardado correctamente.";
	}

	function generarFicha($id){
		$ficha = $id . '-';
		$ficha .= HTMLize($this->Registro['titulo']);
		$this->DB->execute("UPDATE noticia SET ficha = '$ficha' WHERE id_noticia = $id");
	}
	
	function SetOrdinal($id, $valOrdinal){
		$sql = "UPDATE noticia SET ordinal = $valOrdinal WHERE id_noticia = $id";
		$OK = $this->DB->execute($sql);
		if($OK === false){
			$this->Error = $this->DB->ErrorMsg();
		}
	}
	
	function obtenerDatos($idNoticia){
		$q =  "SELECT p.id_noticia, p.titulo, p.copete, p.cuerpo, ";
		$q .= "CONCAT('" . DIR_HTTP_FOTOS_NOTICIAS . "', CONCAT(p.id_noticia, CONCAT('/', CONCAT(pf.nombre_imagen, CONCAT('-thu.', pf.extension))))) AS url_thumbnail, ";
		$q .= "CONCAT('" . DIR_HTTP_FOTOS_NOTICIAS . "', CONCAT(p.id_noticia, CONCAT('/', CONCAT(pf.nombre_imagen, CONCAT('.', pf.extension))))) AS url_imagen ";
		$q .= "FROM noticia p INNER JOIN noticia_foto pf ON pf.id_noticia = p.id_noticia AND pf.orden = 1 WHERE p.id_noticia = $idNoticia";
		
		return iterator_to_array($this->DB->execute($q));
	}

	function salvarImagenMiniatura($id){
		if(isset($_FILES["miniatura"]) && is_uploaded_file($_FILES["miniatura"]["tmp_name"])){
			$nuevoImgFile = DIR_FOTOS_NOTICIAS . "$id.jpg";
			if(file_exists($nuevoImgFile)){
				@unlink($nuevoImgFile);
			}
			move_uploaded_file( $_FILES["miniatura"]["tmp_name"], $nuevoImgFile);

			// Ahora le doy el tamano que lleva
			$ImgHandler = new ImageHandler();
			// Imagen Detalle
			if ($ImgHandler->open_image($nuevoImgFile) == 0){
				// Ajusta la imagen si es necesario
				$ImgHandler->resize_image(LARGO_THUMBNAIL_NOTICIA, ANCHO_THUMBNAIL_NOTICIA);
				$ImgHandler->image_to_file($nuevoImgFile);
			}				
		}
	}
}
?>