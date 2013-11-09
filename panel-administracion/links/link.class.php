<?PHP
// includes
include_once(DIR_BASE.'class/table.class.php');
include_once(DIR_BASE.'seguridad/usuario.class.php');
include_once(DIR_BASE.'class/image-handler.class.php');

class Link extends Table {

	var $TamTextoGrilla = 200;
	var $Ajax;
	var $TablaImg;
	var $ValoresImg;
	
	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Link($DB){
		// Conexion
		$this->Table($DB, 'link');
		$this->AccionesGrid = array(ACC_BAJA, ACC_MODIFICACION, ACC_CONSULTA);
	}
	
	function SetSoloLectura(){
		$this->AccionesGrid = array(ACC_CONSULTA);
	}
	
	// ------------------------------------------------
	// Prepara datos para Grid y PDF's
	// ------------------------------------------------
	function _Registros($Regs=0){
		// Creo grid
		$Grid  = new nyiGridDB('LINKS DE INTERÉS', $Regs, 'base_grid.htm');
		
		// Configuro
		$Grid->setParametros(isset($_GET['PVEZ']), 'nombre_link');
		$Grid->setPaginador('base_navegador.htm');
		$arrCriterios = array(
			'id_link'=>'Identificador',
			'nombre_link'=>'Nombre', 
			"url"=>"Url"
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
	
		$Campos = "id_link AS id, nombre_link, url";
		$From = "link";
		
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
		$id = $this->Registro['id_link'];
		
		// Formulario
		$Form = new nyiHTML('links/link-frm.htm');
		$Form->assign('ACC', $Accion);		
		
		// Datos
		$Form->assign('id_link', $id);
		$Form->assign('nombre_link', $this->Registro['nombre_link']);
		$Form->assign('url', $this->Registro['url']);
		
		if($Accion != ACC_ALTA && $Accion != ACC_MODIFICACION){
			// Si es una baja o consulta, no dejar editar
			$Form->assign('SOLO_LECTURA', 'readonly');
			$Form->assign('src_imagen', DIR_HTTP_FOTOS_LINKS . "$id.jpg");
		}
		
		if(isset($_GET["REDIRIGIR"])){
			unset($_GET["REDIRIGIR"]);
			$this->informarSalvarOk();
		}
		
		// Script Post
		$Form->assign('SCRIPT_POST',basename($_SERVER['SCRIPT_NAME']).$Form->fetchParamURL($_GET));
	
		// Cabezal
		$Cab = new nyiHTML('base_cabezal_abm.htm');
		$Cab->assign('NOMFORM', 'LINKS DE INTERÉS');
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
	
	// ------------------------------------------------
	// Cargo campos desde la base de datos
	// ------------------------------------------------
	function _GetDB($Cod=-1,$Campo='id_link'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}
	
	function getNombre($id_link){
		return $this->DB->getOne("SELECT nombre_link FROM link WHERE id_link = $id_link");
	}
	
	// ------------------------------------------------
	// Cargo campos desde el formulario
	// ------------------------------------------------
	function _GetFrm(){
		if(trim($_POST['nombre_link']) == ""){
			$this->Error .= "El nombre es requerido.\n";
		}
		if(trim($_POST['url']) == ""){
			$this->Error .= "La Url es requerida.\n";
		}
		if($this->Error == ""){
			// Cargo desde el formulario
			$this->Registro['id_link'] = $_POST['id_link'];
			$this->Registro['nombre_link'] = trim($_POST['nombre_link']);
			$this->Registro['url'] = trim($_POST['url']);
		}
	}

	function salvarImagen($id){
		if(isset($_FILES["imagen"]) && is_uploaded_file($_FILES["imagen"]["tmp_name"])){
			$nuevoImgFile = DIR_FOTOS_LINKS . "$id.jpg";
			if(file_exists($nuevoImgFile)){
				@unlink($nuevoImgFile);
			}
			move_uploaded_file( $_FILES["imagen"]["tmp_name"], $nuevoImgFile);

			// Ahora le doy el tamano que lleva
			$ImgHandler = new ImageHandler();
			// Imagen Detalle
			if ($ImgHandler->open_image($nuevoImgFile) == 0){
				// Ajusta la imagen si es necesario
				$ImgHandler->resize_image(LARGO_THUMBNAIL_LINK, ANCHO_THUMBNAIL_LINK);
				$ImgHandler->image_to_file($nuevoImgFile);
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
		
		// devuelvo
		return ($Grid->fetchGrid('links/links-grid.htm', 'Listado de links',
								basename($_SERVER['SCRIPT_NAME']), // Paginador
								"", // PDF
								basename($_SERVER['SCRIPT_NAME']), // Home
								basename($_SERVER['SCRIPT_NAME']), // Mto
								$this->AccionesGrid));
	}
	
	function getLastId(){
		return $this->DB->getOne("SELECT max(id_link) FROM link");
	}
	
	function afterDelete($id){
		@unlink(DIR_FOTOS_LINKS . $id . ".jpg");
	}
	
	function afterInsert($id){
		$this->Registro['id_link'] = $id;
		$this->salvarImagen($id);
	}
	
	function afterEdit(){
		$this->salvarImagen($this->Registro['id_link']);
		$this->informarSalvarOk();
	}
	
	function informarSalvarOk(){
		$this->Error = "Los datos del link se han guardado correctamente.";
	}
}
?>