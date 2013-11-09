<?php

// includes
include_once(DIR_BASE.'class/table.class.php');

class Video extends Table {

	private $Ajax;
	
	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Video($DB, $AJAX=''){
		// Conexion
		$this->Table($DB, 'video');
		// Ajax
		$this->Ajax = $AJAX;
		$this->AccionesGrid = array(ACC_BAJA, ACC_MODIFICACION, ACC_CONSULTA);
	}

	function SetSoloLectura(){
		$this->AccionesGrid = array(ACC_CONSULTA);
	}

	// ------------------------------------------------
	// Devuelve html de la Grid
	// ------------------------------------------------
	function grid($Regs){
		// Datos
		$Grid = $this->_Registros($Regs);
		
		// devuelvo
		return ($Grid->fetchGrid('videos/videos-grid.htm', 'Listado de videos',
								basename($_SERVER['SCRIPT_NAME']), // Paginador
								"", // PDF
								basename($_SERVER['SCRIPT_NAME']), // Home
								basename($_SERVER['SCRIPT_NAME']), // Mto
								$this->AccionesGrid));
	}

	// ------------------------------------------------
	// Prepara datos para Grid y PDF's
	// ------------------------------------------------
	function _Registros($Regs=0){
		// Creo grid
		$Grid  = new nyiGridDB('VIDEOS', $Regs, 'base_grid.htm');
		
		// Configuro
		$Grid->setParametros(isset($_GET['PVEZ']), 'titulo');
		$Grid->setPaginador('base_navegador.htm');
		$arrCriterios = array(
			'id_video'=>'Identificador',
			'titulo'=>'Título',
			'marca'=>'Marca'
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
	
		$Campos = "id_video AS id, titulo, UPPER(marca) AS marca, ficha, codigo";
		$From = "video";
		
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
		$id = $this->Registro['id_video'];
		
		// Formulario
		$Form = new nyiHTML('videos/video-frm.htm');
		$Form->assign('ACC', $Accion);
		
		// Datos
		$Form->assign('id_video', $id);
		$Form->assign('titulo', $this->Registro['titulo']);
		$Form->assign('codigo', $this->Registro['codigo']);
		$Form->assign('marca', $this->Registro['marca']);
		
		if($Accion != ACC_ALTA && $Accion != ACC_MODIFICACION){
			// Si es una baja o consulta, no dejar editar
			$Form->assign('SOLO_LECTURA', 'readonly');
			//$Form->assign('src_video', DIR_HTTP_FOTOS_GALERIA . $this->Registro['ficha'] . ".jpg");
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
		$Form->assign('SCRIPT_POST', basename($_SERVER['SCRIPT_NAME']).$Form->fetchParamURL($_GET));
	
		// Cabezal
		$Cab = new nyiHTML('base_cabezal_abm.htm');
		$Cab->assign('NOMFORM', 'VIDEOS');
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
	function _GetDB($Cod=-1,$Campo='id_video'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}
	
	function getTitulo($id_video){
		return $this->DB->getOne("SELECT titulo FROM video WHERE id_video = $id_video");
	}
	
	// ------------------------------------------------
	// Cargo campos desde el formulario
	// ------------------------------------------------
	function _GetFrm(){
		if(trim($_POST['titulo']) == ""){
			$this->Error .= "El título es requerido.\n";
		}
		if(trim($_POST['codigo']) == ""){
			$this->Error .= "El codigo es requerido.\n";
		}
		if($this->Error == ""){
			// Cargo desde el formulario
			$this->Registro['id_video'] = $_POST['id_video'];
			$this->Registro['titulo'] = addslashes(trim($_POST['titulo']));
			$this->Registro['marca'] = addslashes(trim($_POST['marca']));
			$this->Registro['codigo'] = addslashes(trim($_POST['codigo']));
		}
	}

	function getLastId(){
		return $this->DB->getOne("SELECT max(id_video) FROM video");
	}
	
	function afterInsert($id){
		$this->Registro['id_video'] = $id;
		$ficha = $this->generarFicha($id);
	}
	
	function afterEdit(){
		$id = $this->Registro['id_video'];
		$ficha = $this->generarFicha($id);
		$this->informarSalvarOk();
	}
	
	function informarSalvarOk(){
		$this->Error = "Los datos del video se han guardado correctamente.";
	}

	function generarFicha($id){
		$ficha = $id . '-';
		$ficha .= HTMLize($this->Registro['titulo']);
		$this->DB->execute("UPDATE video SET ficha = '$ficha' WHERE id_video = $id");
		return $ficha;
	}
}

?>