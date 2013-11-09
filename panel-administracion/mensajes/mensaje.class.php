<?PHP
// includes
include_once(DIR_BASE.'class/table.class.php');
include_once(DIR_BASE.'seguridad/usuario.class.php');
include_once(DIR_BASE.'class/image-handler.class.php');

class Mensaje extends Table {

	private $Ajax;
	
	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Mensaje($DB, $AJAX=''){
		// Conexion
		$this->Table($DB, 'mensaje');
		$this->AccionesGrid = array(ACC_BAJA, ACC_CONSULTA);
		// Ajax
		$this->Ajax = $AJAX;
	}
	
	function SetSoloLectura(){
		$this->AccionesGrid = array(ACC_CONSULTA);
	}
	
	// ------------------------------------------------
	// Prepara datos para Grid y PDF's
	// ------------------------------------------------
	function _Registros($Regs=0){
		// Creo grid
		$Grid  = new nyiGridDB('MENSAJES', $Regs, 'base_grid.htm');
		
		// Configuro
		$Grid->setParametros(isset($_GET['PVEZ']), 'nombre');
		$Grid->setPaginador('base_navegador.htm');
		$arrCriterios = array(
			'nombre'=>'Nombre', 
			'email'=>'Email',
			'telefono'=>'Teléfono',
			"DATE_FORMAT(m.fecha, '%d/%m/%Y %H:%i')"=>'Fecha'
		);
		$Grid->setFrmCriterio('base_criterios_buscador_fechas.htm', $arrCriterios);
	
		if(isset($_GET['PVEZ'])){
			$_SESSION["data"]["BUSCADOR_MENSAJES"]["CONFIG_FECHAS"] = "00";
			unset($_SESSION["data"]["FECHA_DESDE"]);
			unset($_SESSION["data"]["FECHA_HASTA"]);
		}
	
		// Si viene con post
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			$Grid->setCriterio($_POST['ORDEN_CAMPO'], $_POST['ORDEN_TXT'], $_POST['CBPAGINA']);
			unset($_GET['NROPAG']);
			
			// Fechas
			$configFechas = getConfigFechas("BUSCADOR_MENSAJES", $_POST);
			$_SESSION["data"]["BUSCADOR_MENSAJES"]["CONFIG_FECHAS"] = $configFechas;
		}
		else {
			$Grid->assign('CLASS_DIV_FECHA_DESDE', 'ocultar');
			$Grid->assign('CLASS_DIV_FECHA_HASTA', 'ocultar');
			if(isset($_GET['NROPAG'])){
				// Numero de Pagina
				$Grid->setPaginaAct($_GET['NROPAG']);
			}
		}
		
		$Where = "";
		$fi = $_SESSION["data"]["FECHA_DESDE"];
		$ff = $_SESSION["data"]["FECHA_HASTA"];
		switch($_SESSION["data"]["BUSCADOR_MENSAJES"]["CONFIG_FECHAS"]){
			case '00':
				$Where = "";
				$Grid->assign('CLASS_DIV_FECHA_DESDE', 'ocultar');
				$Grid->assign('CLASS_DIV_FECHA_HASTA', 'ocultar');
				break;
				
			case '01':
				$Where .= "m.fecha <= '".$ff." 23:59:00'";
				$Grid->assign('CLASS_DIV_FECHA_DESDE', 'ocultar');
				$Grid->assign('CLASS_DIV_FECHA_HASTA', 'mostrar');
				$Grid->assign('FECHA_HASTA', $_SESSION["data"]["FECHA_HASTA"]);
				$Grid->assign('FH_SI_CHECKED', 'checked="checked"');
				break;
				
			case '10':
				$Where .= "m.fecha >= '".$fi."'";
				$Grid->assign('FECHA_DESDE', $_SESSION["data"]["FECHA_DESDE"]);
				$Grid->assign('CLASS_DIV_FECHA_DESDE', 'mostrar');
				$Grid->assign('CLASS_DIV_FECHA_HASTA', 'ocultar');
				$Grid->assign('FD_SI_CHECKED', 'checked="checked"');
				break;
				
			case '11':
				$Where .= "m.fecha >= '".$fi."' AND m.fecha <= '".$ff." 23:59:00'";
				$Grid->assign('FECHA_DESDE', $_SESSION["data"]["FECHA_DESDE"]);
				$Grid->assign('FECHA_HASTA', $_SESSION["data"]["FECHA_HASTA"]);
				$Grid->assign('CLASS_DIV_FECHA_DESDE', 'mostrar');
				$Grid->assign('CLASS_DIV_FECHA_HASTA', 'mostrar');
				$Grid->assign('FD_SI_CHECKED', 'checked="checked"');
				$Grid->assign('FH_SI_CHECKED', 'checked="checked"');
				break;
		}
	
		$Campos = "m.id_mensaje AS id, m.nombre, m.email, m.telefono, DATE_FORMAT(m.fecha, '%d/%m/%Y %H:%i') AS fecha_dsc, m.fecha";
		$From = "mensaje m";
		
		if($Where != ""){
			$Grid->getDatos($this->DB, $Campos, $From, $Where);
		}
		else {
			$Grid->getDatos($this->DB, $Campos, $From);
		}
		
		// Devuelvo
		return($Grid);
	}

	// ------------------------------------------------
	// Genera Formulario
	// ------------------------------------------------
	function _Frm($Accion){
		// Conexion
		$Cnx = $this->DB;
		$id = $this->Registro['id_mensaje'];
		$id_aux = $id == "" ? 0 : $id;
		
		// Formulario
		$Form = new nyiHTML('mensajes/mensaje-frm.htm');
		$Form->assign('ACC', $Accion);		
		
		// Datos
		$Form->assign('id_mensaje', $id);
		$Form->assign('nombre', $this->Registro['nombre']);
		$Form->assign('email', $this->Registro['email']);
		$Form->assign('telefono', $this->Registro['telefono']);
		$Form->assign('mensaje', $this->Registro['mensaje']);
		$Form->assign('fecha', FormatDateLong($this->Registro['fecha'], false, "/", true));
		
		if($Accion != ACC_ALTA && $Accion != ACC_MODIFICACION){
			// Si es una baja o consulta, no dejar editar
			$Form->assign('SOLO_LECTURA', 'readonly');
		}
		
		// Script Post
		$Form->assign('SCRIPT_POST',basename($_SERVER['SCRIPT_NAME']).$Form->fetchParamURL($_GET));
	
		// Cabezal
		$Cab = new nyiHTML('base_cabezal_abm.htm');
		$Cab->assign('NOMFORM', 'MENSAJES');
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
	function _GetDB($Cod=-1,$Campo='id_mensaje'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}
	
	function getNombre($id_mensaje){
		return $this->DB->getOne("SELECT nombre FROM mensaje WHERE id_mensaje = $id_mensaje");
	}
	
	// ------------------------------------------------
	// Cargo campos desde el formulario
	// ------------------------------------------------
	function _GetFrm(){
		// Cargo desde el formulario
		$this->Registro['id_mensaje'] = $_POST['id_mensaje'];
		$this->Registro['nombre'] = mysql_real_escape_string($_POST['nombre']);
		$this->Registro['email'] = mysql_real_escape_string($_POST['email']);
		$this->Registro['telefono'] = mysql_real_escape_string($_POST['telefono']);
		$this->Registro['mensaje'] = mysql_real_escape_string($_POST['mensaje']);
		
		if($this->Registro['id_mensaje'] == ""){
			$this->Registro['fecha'] = date('Y')."-".date('m')."-".date('d')." ".date('H').":".date('i').":00";
		}
	}
	
	// ------------------------------------------------
	// Devuelve html de la Grid
	// ------------------------------------------------
	function grid($Regs){
		// Datos
		$Grid = $this->_Registros($Regs);
		
		// devuelvo
		return ($Grid->fetchGrid('mensajes/mensaje-grid.htm', 'Listado de mensajes',
								basename($_SERVER['SCRIPT_NAME']), // Paginador
								"", // PDF
								basename($_SERVER['SCRIPT_NAME']), // Home
								basename($_SERVER['SCRIPT_NAME']), // Mto
								$this->AccionesGrid));
	}
	
	function getLastId(){
		return $this->DB->getOne("SELECT max(id_mensaje) FROM mensaje");
	}
	
	function afterInsert($id){
		$this->Registro['id_mensaje'] = $id;
	}
}
?>