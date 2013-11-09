<?PHP
// includes
include_once(DIR_BASE.'class/table.class.php');
include_once(DIR_BASE.'seguridad/usuario.class.php');
include_once(DIR_BASE.'class/image-handler.class.php');

class Distribuidor extends Table {
	
	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Distribuidor($DB){
		// Conexion
		$this->Table($DB, 'distribuidor');
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
		$Grid  = new nyiGridDB('DISTRIBUIDORES', $Regs, 'base_grid.htm');
		
		// Configuro
		$Grid->setParametros(isset($_GET['PVEZ']), 'nombre_distribuidor');
		$Grid->setPaginador('base_navegador.htm');
		$arrCriterios = array(
			'id_distribuidor'=>'Identificador',
			'nombre_distribuidor'=>'Nombre', 
			"url"=>"Url",
			"telefono"=>"Telefono"
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
	
		$Campos = "id_distribuidor AS id, nombre_distribuidor, url, telefono";
		$From = "distribuidor";
		
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
		$id = $this->Registro['id_distribuidor'];
		
		// Formulario
		$Form = new nyiHTML('distribuidores/distribuidor-frm.htm');
		$Form->assign('ACC', $Accion);		
		
		// Datos
		$Form->assign('id_distribuidor', $id);
		$Form->assign('nombre_distribuidor', $this->Registro['nombre_distribuidor']);
		$Form->assign('url', $this->Registro['url']);
		$Form->assign('telefono', $this->Registro['telefono']);
		$Form->assign('direccion', $this->Registro['direccion']);
		
		if($Accion != ACC_ALTA && $Accion != ACC_MODIFICACION){
			// Si es una baja o consulta, no dejar editar
			$Form->assign('SOLO_LECTURA', 'readonly');
			$Form->assign('src_imagen', DIR_HTTP_FOTOS_DISTRIBUIDORES . "$id.jpg");
		}
		
		if(isset($_GET["REDIRIGIR"])){
			unset($_GET["REDIRIGIR"]);
			$this->informarSalvarOk();
		}
		
		// Script Post
		$Form->assign('SCRIPT_POST',basename($_SERVER['SCRIPT_NAME']).$Form->fetchParamURL($_GET));
	
		// Cabezal
		$Cab = new nyiHTML('base_cabezal_abm.htm');
		$Cab->assign('NOMFORM', 'DISTRIBUIDORES');
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
	function _GetDB($Cod=-1,$Campo='id_distribuidor'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}
	
	function getNombre($id_distribuidor){
		return $this->DB->getOne("SELECT nombre_distribuidor FROM distribuidor WHERE id_distribuidor = $id_distribuidor");
	}
	
	// ------------------------------------------------
	// Cargo campos desde el formulario
	// ------------------------------------------------
	function _GetFrm(){
		if(trim($_POST['nombre_distribuidor']) == ""){
			$this->Error .= "El nombre es requerido.\n";
		}
		if(trim($_POST['url']) == ""){
			$this->Error .= "La Url es requerida.\n";
		}
		if($this->Error == ""){
			// Cargo desde el formulario
			$this->Registro['id_distribuidor'] = $_POST['id_distribuidor'];
			$this->Registro['nombre_distribuidor'] = addslashes(trim($_POST['nombre_distribuidor']));
			$this->Registro['url'] = addslashes(trim($_POST['url']));
			$this->Registro['telefono'] = addslashes(trim($_POST['telefono']));
			$this->Registro['direccion'] = addslashes(trim($_POST['direccion']));
		}
	}

	function salvarImagen($id){
		if(isset($_FILES["imagen"]) && is_uploaded_file($_FILES["imagen"]["tmp_name"])){
			$nuevoImgFile = DIR_FOTOS_DISTRIBUIDORES . "$id.jpg";
			if(file_exists($nuevoImgFile)){
				@unlink($nuevoImgFile);
			}
			move_uploaded_file( $_FILES["imagen"]["tmp_name"], $nuevoImgFile);

			// Ahora le doy el tamano que lleva
			$ImgHandler = new ImageHandler();
			// Imagen Detalle
			if ($ImgHandler->open_image($nuevoImgFile) == 0){
				// Ajusta la imagen si es necesario
				$ImgHandler->resize_image(LARGO_THUMBNAIL_DISTRIBUIDORES, ANCHO_THUMBNAIL_DISTRIBUIDORES);
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
		
		// devuelvo
		return ($Grid->fetchGrid('distribuidores/distribuidores-grid.htm', 'Listado de distribuidores',
								basename($_SERVER['SCRIPT_NAME']), // Paginador
								"", // PDF
								basename($_SERVER['SCRIPT_NAME']), // Home
								basename($_SERVER['SCRIPT_NAME']), // Mto
								$this->AccionesGrid));
	}
	
	function getLastId(){
		return $this->DB->getOne("SELECT max(id_distribuidor) FROM distribuidor");
	}
	
	function afterDelete($id){
		@unlink(DIR_FOTOS_DISTRIBUIDORES . $id . ".jpg");
	}
	
	function afterInsert($id){
		$this->Registro['id_distribuidor'] = $id;
		$this->salvarImagen($id);
	}
	
	function afterEdit(){
		$this->salvarImagen($this->Registro['id_distribuidor']);
		$this->informarSalvarOk();
	}
	
	function informarSalvarOk(){
		$this->Error = "Los datos del distribuidor se han guardado correctamente.";
	}
}
?>