<?php

// includes
include_once(DIR_BASE.'class/table.class.php');
include_once(DIR_BASE.'class/image-handler.class.php');

class Imagen extends Table {

	private $Ajax;
	private $fichaAnterior;
	
	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Imagen($DB, $AJAX=''){
		// Conexion
		$this->Table($DB, 'imagen');
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
		$Grid->addVariable('w', WIDTH_THUMB_GALERIA);
		$Grid->addVariable('h', HEIGHT_THUMB_GALERIA);
		$Grid->addVariable('url_galeria', DIR_HTTP_FOTOS_GALERIA);
		
		// devuelvo
		return ($Grid->fetchGrid('galeria/imagenes-grid.htm', 'Listado de imágenes',
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
		$Grid  = new nyiGridDB('IMAGENES', $Regs, 'base_grid.htm');
		
		// Configuro
		$Grid->setParametros(isset($_GET['PVEZ']), 'titulo');
		$Grid->setPaginador('base_navegador.htm');
		$arrCriterios = array(
			'id_imagen'=>'Identificador',
			'titulo'=>'Título',
			'marca'=>'Marca',
			"IF(portada, 'Si', 'No')"=>"Portada"
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
	
		$Campos = "id_imagen AS id, titulo, UPPER(marca) AS marca, ficha, IF(portada, 'Si', 'No') AS portada";
		$From = "imagen";
		
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
		$id = $this->Registro['id_imagen'];
		
		// Formulario
		$Form = new nyiHTML('galeria/imagen-frm.htm');
		$Form->assign('ACC', $Accion);
		
		// Datos
		$Form->assign('id_imagen', $id);
		$Form->assign('titulo', $this->Registro['titulo']);
		$Form->assign('marca', $this->Registro['marca']);
		$Form->assign('portada', $this->Registro['portada'] == 1 ? 'checked="checked"' : '');
		
		if($Accion != ACC_ALTA && $Accion != ACC_MODIFICACION){
			// Si es una baja o consulta, no dejar editar
			$Form->assign('SOLO_LECTURA', 'readonly');
			$Form->assign('src_imagen', DIR_HTTP_FOTOS_GALERIA . $this->Registro['ficha'] . ".jpg");
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
		$Cab->assign('NOMFORM', 'IMAGENES');
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
	function _GetDB($Cod=-1,$Campo='id_imagen'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}
	
	function getTitulo($id_imagen){
		return $this->DB->getOne("SELECT titulo FROM imagen WHERE id_imagen = $id_imagen");
	}

	function getFicha($id_imagen){
		return $this->DB->getOne("SELECT ficha FROM imagen WHERE id_imagen = " . $id_imagen);
	}
	
	// ------------------------------------------------
	// Cargo campos desde el formulario
	// ------------------------------------------------
	function _GetFrm(){
		if(trim($_POST['titulo']) == ""){
			$this->Error .= "El título es requerido.\n";
		}
		if($this->Error == ""){
			// Cargo desde el formulario
			$this->Registro['id_imagen'] = $_POST['id_imagen'];
			$this->Registro['titulo'] = addslashes(trim($_POST['titulo']));
			$this->Registro['marca'] = addslashes(trim($_POST['marca']));
			$this->Registro['portada'] = $_POST['portada'] ? 1 : 0;
		}
	}

	function salvarImagen($id, $ficha){
		// Si hay upload, salvo imagen
		if(isset($_FILES["imagen"]) && is_uploaded_file($_FILES["imagen"]["tmp_name"])){
			$nuevoImgFile = DIR_FOTOS_GALERIA . $ficha . '.jpg';
			$nuevoImgThuFile = DIR_FOTOS_GALERIA . $ficha . '-thu.jpg';
			if(file_exists($nuevoImgFile)){
				@unlink($nuevoImgFile);
			}
			if(file_exists($nuevoImgThuFile)){
				@unlink($nuevoImgThuFile);
			}
			move_uploaded_file( $_FILES["imagen"]["tmp_name"], $nuevoImgFile);

			$ImgHandler = new ImageHandler();
			if($this->Registro['portada'] == 1){
				// Hay que resizear la imagen para el slider
				if ($ImgHandler->open_image($nuevoImgFile) == 0){
					// Ajusta la imagen si es necesario
					$ImgHandler->resize_image(WIDTH_FOTO_SLIDER, HEIGHT_FOTO_SLIDER);
					$ImgHandler->image_to_file($nuevoImgFile);
				}
			}
			// Ahora le doy el tamano que lleva el thumb
			if ($ImgHandler->open_image($nuevoImgFile) == 0){
				// Ajusta la imagen si es necesario
				$ImgHandler->resize_image(WIDTH_THUMB_GALERIA, HEIGHT_THUMB_GALERIA);
				$ImgHandler->image_to_file($nuevoImgThuFile);
			}	
		}
	}

	function getLastId(){
		return $this->DB->getOne("SELECT max(id_imagen) FROM imagen");
	}

	function beforeEdit(){
		$this->fichaAnterior = $this->getFicha($this->Registro["id_imagen"]);
	}
	
	function afterDelete($id){
		@unlink(DIR_FOTOS_GALERIA . $this->Registro['ficha'] . ".jpg");
		@unlink(DIR_FOTOS_GALERIA . $this->Registro['ficha'] . "-thu.jpg");
	}
	
	function afterInsert($id){
		$this->Registro['id_imagen'] = $id;
		$ficha = $this->generarFicha($id);
		$this->salvarImagen($id, $ficha);
	}
	
	function afterEdit(){
		$id = $this->Registro['id_imagen'];
		$ficha = $this->generarFicha($id);
		$this->salvarImagen($id, $ficha);
		// Si el titulo fue cambiado
		if($ficha != $this->fichaAnterior){
			$nuevoImgFile = DIR_FOTOS_GALERIA . $ficha . '.jpg';
			$nuevoImgThuFile = DIR_FOTOS_GALERIA . $ficha . '-thu.jpg';	
			// Si no fueron generados los nuevos archivos
			if(!(file_exists($nuevoImgFile) && file_exists($nuevoImgThuFile))){
				$viejaImgFile = DIR_FOTOS_GALERIA . $this->fichaAnterior . '.jpg';
				$viejaImgThuFile = DIR_FOTOS_GALERIA . $this->fichaAnterior . '-thu.jpg';
				rename($viejaImgFile, $nuevoImgFile);
				rename($viejaImgThuFile, $nuevoImgThuFile);
			}
		}
		$this->informarSalvarOk();
	}
	
	function informarSalvarOk(){
		$this->Error = "Los datos de la imagen se han guardado correctamente.";
	}

	function generarFicha($id){
		$ficha = $id . '-';
		$ficha .= HTMLize($this->Registro['titulo']);
		$this->DB->execute("UPDATE imagen SET ficha = '$ficha' WHERE id_imagen = $id");
		return $ficha;
	}
}

?>