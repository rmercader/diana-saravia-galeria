<?PHP

// includes
include_once(DIR_BASE.'class/table.class.php');

class Pedido extends Table {

	var $item__r;

	// ------------------------------------------------
	//  Crea y configura conexion
	// ------------------------------------------------
	function Pedido($DB, $AJAX=''){
		// Conexion
		$this->Table($DB, 'pedido');
		$this->item__r = new Table($DB, 'pedido_item');
		$this->setSoloLectura();
		// Ajax
		$this->Ajax = $AJAX;
	}
	
	function setSoloLectura(){
		$this->AccionesGrid = array(ACC_CONSULTA);
	}
	
	// ------------------------------------------------
	// Prepara datos para Grid y PDF's
	// ------------------------------------------------
	function _Registros($Regs=0){
		// Creo grid
		$Grid  = new nyiGridDB('PEDIDOS', $Regs, 'base_grid.htm');
		
		// Configuro
		//$Grid->setParametros(isset($_GET['PVEZ']), 'v.fecha', 'DESC'); // Parametros de la sesion
		$Grid->setPaginador('base_navegador.htm');
		$filtros = array(
			'v.fecha_registrado'=>'Fecha', 
			'v.id_pedido'=>'Nro. Orden', 
			'v.estado'=>'Estado del pedido',
			'v.nombre'=>'Nombre y apellido',
			'v.email'=>'Email',
			'v.telefono'=>'Telefono',
			'v.ciudad'=>'Ciudad',
			'v.departamento'=>'Departamento',
			'v.total'=>'Total'
		);
		
		$Grid->setFrmCriterio('pedidos/criterios-buscador.htm', $filtros);
	
		$arrWhere = array();
		// Si viene con post
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			if(isset($_POST['ORDEN_CAMPO']) && isset($_POST['ORDEN'])){
				unset($_SESSION['buscador-pedidos']); // Reinicio los filtros
				$_SESSION['buscador-pedidos']['ORDEN_CAMPO'] = $_POST['ORDEN_CAMPO'];
				$_SESSION['buscador-pedidos']['ORDEN'] = $_POST['ORDEN'];
			}
			$Grid->setCriterio($_SESSION['buscador-pedidos']['ORDEN_CAMPO'], "", 1, $_SESSION['buscador-pedidos']['ORDEN']);
			unset($_GET['NROPAG']);
			
			// Criterios
			// Id de pedido
			$critIdPedido = addslashes(trim($_POST['id_pedido']));
			if($critIdPedido != ''){
				array_push($arrWhere, "(v.id_pedido LIKE '%$critIdPedido%')");
				$_SESSION['buscador-pedidos']['id_pedido'] = $critIdPedido;
			}
			// Fechas
			$critFechaDesde = "{$_POST['fecha_desdeYear']}-{$_POST['fecha_desdeMonth']}-{$_POST['fecha_desdeDay']} 00:00:00";
			$critFechaHasta = "{$_POST['fecha_hastaYear']}-{$_POST['fecha_hastaMonth']}-{$_POST['fecha_hastaDay']} 23:59:59";
			array_push($arrWhere, "(v.fecha_registrado BETWEEN '$critFechaDesde' AND '$critFechaHasta')");
			$_SESSION['buscador-pedidos']['fecha_desde'] = $critFechaDesde;
			$_SESSION['buscador-pedidos']['fecha_hasta'] = $critFechaHasta;
			
			// Estado del pedido
			$critEstado = addslashes(trim($_POST['estado']));
			if($critEstado != ''){
				array_push($arrWhere, "(v.estado = '$critEstado')");
				$_SESSION['buscador-pedidos']['estado'] = $critEstado;			
			}

			// Nombre del pedido
			$crit = addslashes(trim($_POST['nombre']));
			if($crit != ''){
				array_push($arrWhere, "(v.nombre LIKE '%$crit%')");
				$_SESSION['buscador-pedidos']['nombre'] = $crit;			
			}
			
			// Email del pedido
			$crit = addslashes(trim($_POST['email']));
			if($crit != ''){
				array_push($arrWhere, "(v.email LIKE '%$crit%')");
				$_SESSION['buscador-pedidos']['email'] = $crit;			
			}

			// Direccion del pedido
			$crit = addslashes(trim($_POST['direccion']));
			if($crit != ''){
				array_push($arrWhere, "(v.direccion LIKE '%$crit%')");
				$_SESSION['buscador-pedidos']['direccion'] = $crit;			
			}

			// Telefono del pedido
			$crit = addslashes(trim($_POST['telefono']));
			if($crit != ''){
				array_push($arrWhere, "(v.telefono LIKE '%$crit%')");
				$_SESSION['buscador-pedidos']['telefono'] = $crit;			
			}

			// Ciudad del pedido
			$crit = addslashes(trim($_POST['ciudad']));
			if($crit != ''){
				array_push($arrWhere, "(v.ciudad LIKE '%$crit%')");
				$_SESSION['buscador-pedidos']['ciudad'] = $crit;			
			}

			// Departamento del pedido
			$crit = addslashes(trim($_POST['departamento']));
			if($crit != ''){
				array_push($arrWhere, "(v.departamento LIKE '%$crit%')");
				$_SESSION['buscador-pedidos']['departamento'] = $crit;			
			}
		}
		else {
			// Esto se hace para comenzar en limpio una nueva busqueda
			if(isset($_GET['PVEZ'])){
				unset($_SESSION['buscador-pedidos']);
				$Grid->setCriterio("v.fecha_registrado", "", 1, "DESC");
				$_SESSION['buscador-pedidos']['ORDEN_CAMPO'] = "v.fecha_registrado";
				$_SESSION['buscador-pedidos']['ORDEN'] = "DESC";
			}
			else{
				// Criterios
				// Id de pedido
				if(isset($_SESSION['buscador-pedidos']['id_pedido'])){
					array_push($arrWhere, "(v.id_pedido LIKE '%{$_SESSION['buscador-pedidos']['id_pedido']}%')");
				}
				
				// Fechas
				if(isset($_SESSION['buscador-pedidos']['fecha_desde']) && isset($_SESSION['buscador-pedidos']['fecha_hasta'])){
					$critFechaDesde = $_SESSION['buscador-pedidos']['fecha_desde'];
					$critFechaHasta = $_SESSION['buscador-pedidos']['fecha_hasta'];
					array_push($arrWhere, "(v.fecha_registrado BETWEEN '$critFechaDesde' AND '$critFechaHasta')");
				}
				
				// Estado del pedido
				if(isset($_SESSION['buscador-pedidos']['estado'])){
					$critEstado = $_SESSION['buscador-pedidos']['estado'];
					array_push($arrWhere, "(v.estado = '$critEstado')");
				}
				
				// Nombre
				if(isset($_SESSION['buscador-pedidos']['nombre'])){
					$crit = $_SESSION['buscador-pedidos']['nombre'];
					array_push($arrWhere, "(v.nombre LIKE '%$crit%')");
				}

				// Email
				if(isset($_SESSION['buscador-pedidos']['email'])){
					$crit = $_SESSION['buscador-pedidos']['email'];
					array_push($arrWhere, "(v.email LIKE '%$crit%')");
				}

				// Telefono
				if(isset($_SESSION['buscador-pedidos']['telefono'])){
					$crit = $_SESSION['buscador-pedidos']['telefono'];
					array_push($arrWhere, "(v.telefono LIKE '%$crit%')");
				}

				// Direccion
				if(isset($_SESSION['buscador-pedidos']['direccion'])){
					$crit = $_SESSION['buscador-pedidos']['direccion'];
					array_push($arrWhere, "(v.direccion LIKE '%$crit%')");
				}

				// Ciudad
				if(isset($_SESSION['buscador-pedidos']['ciudad'])){
					$crit = $_SESSION['buscador-pedidos']['ciudad'];
					array_push($arrWhere, "(v.ciudad LIKE '%$crit%')");
				}

				// Departamento
				if(isset($_SESSION['buscador-pedidos']['departamento'])){
					$crit = $_SESSION['buscador-pedidos']['departamento'];
					array_push($arrWhere, "(v.departamento LIKE '%$crit%')");
				}
				
				if(isset($_SESSION['buscador-pedidos']['ORDEN_CAMPO']) && isset($_SESSION['buscador-pedidos']['ORDEN'])){
					$Grid->setCriterio($_SESSION['buscador-pedidos']['ORDEN_CAMPO'], "", $_GET['NROPAG'], $_SESSION['buscador-pedidos']['ORDEN']);
				}
			}
		}
		
		$where = implode(' AND ', $arrWhere);
		
		// Numero de Pagina
		if (isset($_GET['NROPAG'])){
			$Grid->setPaginaAct($_GET['NROPAG']);
		}
			
		$campos = "v.id_pedido AS id, v.*, DATE_FORMAT(v.fecha_registrado, '%e/%c/%Y %H:%i') AS fechaDsc, v.total";
		$from = "pedido v";
			
		if($where != ''){
			$Grid->getDatos($this->DB, $campos, $from, $where);
		}
		else {
			$Grid->getDatos($this->DB, $campos, $from);
		}
		
		// Devuelvo
		return($Grid);
	}
	
	// ------------------------------------------------
	// Devuelve html de la Grid
	// ------------------------------------------------
	function grid($Regs){
		// Datos
		$Grid = $this->_Registros($Regs);
		
		// Id de pedido
		$Grid->assign('id_pedido', isset($_POST['id_pedido']) ? $_POST['id_pedido'] : $_SESSION['buscador-pedidos']['id_pedido']);
		
		// Combo estados
		$arrEstados = array('nuevo', 'enviado', 'completo');
		$Grid->assign('ids_estados', $arrEstados);
		$Grid->assign('dsc_estados', array_map('strtoupper', $arrEstados));
		$Grid->assign('estado', isset($_POST['estado']) ? $_POST['estado'] : $_SESSION['buscador-pedidos']['estado']);
		$Grid->assign('nombre', isset($_POST['nombre']) ? $_POST['nombre'] : $_SESSION['buscador-pedidos']['nombre']);
		$Grid->assign('email', isset($_POST['email']) ? $_POST['email'] : $_SESSION['buscador-pedidos']['email']);
		$Grid->assign('telefono', isset($_POST['telefono']) ? $_POST['telefono'] : $_SESSION['buscador-pedidos']['telefono']);
		$Grid->assign('direccion', isset($_POST['direccion']) ? $_POST['direccion'] : $_SESSION['buscador-pedidos']['direccion']);
		$Grid->assign('ciudad', isset($_POST['ciudad']) ? $_POST['ciudad'] : $_SESSION['buscador-pedidos']['ciudad']);
		$Grid->assign('departamento', isset($_POST['departamento']) ? $_POST['departamento'] : $_SESSION['buscador-pedidos']['departamento']);
		
		// Combo campos ordenacion
		$filtros = array(
			'v.fecha_registrado'=>'Fecha', 
			'v.id_pedido'=>'Nro. Orden', 
			'v.estado'=>'Estado del pedido',
			'v.nombre'=>'Nombre y apellido',
			'v.email'=>'Email',
			'v.telefono'=>'Telefono',
			'v.ciudad'=>'Ciudad',
			'v.departamento'=>'Departamento',
			'v.total'=>'Total'
		);
		$Grid->assign('value_orden_campo', array_keys($filtros));
		$Grid->assign('dsc_orden_campo', array_values($filtros));
		$Grid->assign('value_orden', array('ASC', 'DESC'));
		$Grid->assign('dsc_orden', array('Ascendente', 'Descendente'));
		
		// Fecha desde
		$fechaDesde = "";
		if(isset($_POST['fecha_desdeDay']) && isset($_POST['fecha_desdeMonth']) && isset($_POST['fecha_desdeYear']))
			// La recien posteada
			$fechaDesde = "{$_POST['fecha_desdeYear']}-{$_POST['fecha_desdeMonth']}-{$_POST['fecha_desdeDay']}";
		elseif(isset($_SESSION['buscador-pedidos']['fecha_desde']))
			// La guardada en la session de busqueda
			$fechaDesde = $_SESSION['buscador-pedidos']['fecha_desde'];
		else 
			// La de la primer pedido registrada
			$fechaDesde = $this->DB->getOne("SELECT DATE_FORMAT(fecha_registrado, '%Y-%m-%d') FROM pedido ORDER BY fecha_registrado");
		$Grid->assign('fecha_desde', $fechaDesde);
		
		// Fecha hasta
		$fechaHasta = "";
		if(isset($_POST['fecha_hastaDay']) && isset($_POST['fecha_hastaMonth']) && isset($_POST['fecha_hastaYear']))
			// La recien posteada
			$fechaHasta = "{$_POST['fecha_hastaYear']}-{$_POST['fecha_hastaMonth']}-{$_POST['fecha_hastaDay']}";
		elseif(isset($_SESSION['buscador-pedidos']['fecha_hasta']))
			// La guardada en la session de busqueda
			$fechaHasta = $_SESSION['buscador-pedidos']['fecha_hasta'];
		else 
			// La de la primer pedido registrada
			$fechaHasta = date("Y-m-d");
		$Grid->assign('fecha_hasta', $fechaHasta);
		
		// Criterio de ordenacion
		$critOrd = "";
		if(isset($_POST['ORDEN'])){
			$critOrd = $_POST['ORDEN'];
			$Grid->addVariable('ORDEN', $critOrd);
		}
		elseif(isset($_SESSION['buscador-pedidos']['ORDEN'])){
			$critOrd = $_SESSION['buscador-pedidos']['ORDEN'];
		}
		else {
			$critOrd = "DESC";
		}
		$Grid->assign('ORDEN', $critOrd);
		
		$ordCmp = "";
		if(isset($_POST['ORDEN_CAMPO'])){
			$ordCmp = $_POST['ORDEN_CAMPO'];
		}
		elseif(isset($_SESSION['buscador-pedidos']['ORDEN_CAMPO'])){
			$ordCmp = $_SESSION['buscador-pedidos']['ORDEN_CAMPO'];
		}
		else {
			$ordCmp = "v.fecha_registrado";
		}
		$Grid->assign('ORDEN_CAMPO', $ordCmp);
		
		// devuelvo
		return $Grid->fetchGrid(
			'pedidos/pedidos-grid.htm', 'PEDIDOS', 
			basename($_SERVER['SCRIPT_NAME']), // Paginador
			"", // PDF
			basename($_SERVER['SCRIPT_NAME']), // Home
			basename($_SERVER['SCRIPT_NAME']), // Mto
			$this->AccionesGrid
		);
	}

	// ------------------------------------------------
	// Genera Formulario
	// ------------------------------------------------
	function _Frm($Accion){
		// Conexion
		$Cnx = $this->DB;
		
		// Formulario
		$Form = new nyiHTML('pedidos/pedido-detalle.htm');
		$Form->assign('ACC', $Accion);
		$Form->assign('ERROR',$this->Error);

		// Datos
		$Form->assign('id_pedido', $this->Registro['id_pedido']);
		$Form->assign('fecha_registrado', FormatDateLong($this->Registro['fecha_registrado']));
		$Form->assign('estado', $this->Registro['estado']);
		$Form->assign('nombre', $this->Registro['nombre']);
		$Form->assign('email', $this->Registro['email']);
		$Form->assign('telefono', $this->Registro['telefono']);
		$Form->assign('direccion', $this->Registro['direccion']);
		$Form->assign('ciudad', $this->Registro['ciudad']);
		$Form->assign('departamento', $this->Registro['departamento']);
		$Form->assign('total', $this->Registro['total']);
		
		// Combo estados
		$arrEstados = array('nuevo', 'enviado', 'completo');
		$Form->assign('ids_estados', $arrEstados);
		$Form->assign('dsc_estados', array_map('strtoupper', $arrEstados));
		
		$detalles = $this->obtenerDetalles($this->Registro['id_pedido']);
		$Form->assign('items', $detalles);
		
		if($Accion == ACC_BAJA || $Accion == ACC_CONSULTA){
			// Si es una baja o consulta, no dejar editar
			$Form->assign('SOLO_LECTURA', 'readonly');
		}
		
		// Script Post
		$Form->assign('SCRIPT_POST',basename($_SERVER['SCRIPT_NAME']).$Form->fetchParamURL($_GET));
	
		// Cabezal
		$Cab = new nyiHTML('base_cabezal_abm.htm');
		$Cab->assign('NOMFORM', 'PEDIDOS');
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
		
		$this->Ajax->setRequestURI(DIR_HTTP.'pedidos/ajax-pedidos.php');
		$this->Ajax->registerFunction("modificarEstado");
	
		// Contenido
		return($Form->fetchHTML());
	}
	
	// ------------------------------------------------
	// Cargo campos desde la base de datos
	// ------------------------------------------------
	function _GetDB($Cod=-1, $Campo='id_pedido'){
		// Cargo campos
		$this->Registro[$Campo] = $Cod;
		$this->TablaDB->getRegistro($this->Registro, $Campo);
	}
	
	// ------------------------------------------------
	// Cargo campos desde el formulario
	// ------------------------------------------------
	function _GetFrm(){
		// Cargo desde el formulario
		$id = $_POST['id_pedido'];
		$this->Registro['id_pedido'] = $id;
		$this->Registro['nombre'] = $_POST['nombre'];
		$this->Registro['apellido'] = $_POST['apellido'];
		$this->Registro['email'] = $_POST['email'];
		$this->Registro['direccion'] = $_POST['direccion'];
		$this->Registro['departamento'] = $_POST['departamento'];
		$this->Registro['ciudad'] = $_POST['ciudad'];
		$this->Registro['telefono'] = $_POST['telefono'];
	}
	
	function getLastId(){
		return $this->DB->getOne("SELECT max(id_pedido) FROM pedido");
	}

	// Retorna el combo de identificadores ordenados segun nombre
	function getComboIds($Todos=false, $IdT=0){
		$Aux = $this->DB;
		$Col = $Aux->getCol("SELECT id_pedido FROM pedido ORDER BY CONCAT(CONCAT(nombre, ' '), apellido)");
		
		// Si hay que agregar
		if ($Todos){
			if (is_array($Col))
				$Col = array_merge(array($IdT),$Col);
		}
		return($Col);
	}
	
	// ------------------------------------------------
	// Devuelvo array de depedidos para combo
	// ------------------------------------------------
	function getComboNombres($Todos=false,$NomT='Todos'){
		$Aux = $this->DB;
		$Col = $Aux->getCol("SELECT CONCAT(CONCAT(nombre, ' '), apellido) FROM pedido ORDER BY CONCAT(CONCAT(nombre, ' '), apellido)");
		// Si hay que agregar
		if ($Todos){
			if (is_array($Col))
				$Col = array_merge(array($NomT),$Col);
		}
		return($Col);
	}
	
	function obtenerPedidos(){
		return iterator_to_array($this->DB->execute("SELECT * FROM pedido ORDER BY CONCAT(CONCAT(nombre, ' '), apellido)"));
	}
	
	function insertar(){
		$res = $this->TablaDB->addRegistro($this->Registro);
		if($res == ''){
			// Si el registro fue insertado correctamente, le seteamos el id_pedido
			$this->Registro["id_pedido"] = $this->getLastId();
		}
		return $res;
	}
	
	function asociarComprador($comprador){
		$this->comprador__r->Registro["id_pedido"] = $this->Registro["id_pedido"];
		$this->comprador__r->Registro["id_comprador"] = $comprador;
		return $this->comprador__r->TablaDB->addRegistro($this->comprador__r->Registro);
	}
	
	// $datosItem es del tipo de datos ItemCompra
	function asociarItem($idItem, $datosItem){
		// Inserta el item
		$this->item__r->Registro["id_pedido"] = $this->Registro["id_pedido"];
		$this->item__r->Registro["item"] = $idItem;
		$this->item__r->Registro["id_producto"] = $datosItem["id_producto"];
		$this->item__r->Registro["id_color"] = $datosItem["id_color"];
		$this->item__r->Registro["id_talle"] = $datosItem["id_talle"];
		$this->item__r->Registro["cantidad"] = $datosItem["cantidad"];
		$this->item__r->Registro["precio"] = $datosItem["precio"];
		$this->item__r->Registro["subtotal"] = $datosItem["subtotal"];
		$resAddItem = $this->item__r->TablaDB->addRegistro($this->item__r->Registro);
		if($resAddItem != ""){
			LogArchivo("Error creando item: $resAddItem");
			return $resAddItem;
		}
		else {
			// Actualiza el stock
			$sqlUpdate = "UPDATE producto_stock ";
			$sqlUpdate .= "SET cantidad = (cantidad - {$datosItem["cantidad"]}) ";
			$sqlUpdate .= "WHERE id_producto = {$datosItem["id_producto"]} AND id_talle = {$datosItem["id_talle"]} AND id_color = {$datosItem["id_color"]}";
			$ok = $this->DB->execute($sqlUpdate);
			if($ok === false){
				LogArchivo("Fallo la siguiente consulta tratando de actualizar stock:\n$sqlUpdate");
				return $this->DB->ErrorMsg();
			}
		}
		return "";
	}
	
	function editar(){
		return $this->TablaDB->editRegistro($this->Registro, 'id_pedido');
	}
	
	function obtenerDatosCabezal($idPedido){
		return $this->DB->execute("SELECT * FROM pedido WHERE id_pedido = $idPedido");
	}
	
	function obtenerIdComprador($idPedido){
		return $this->DB->getOne("SELECT id_comprador FROM pedido_comprador WHERE id_pedido = $idPedido");
	}
	
	function obtenerIdInvitado($idPedido){
		return $this->DB->getOne("SELECT id_invitado FROM invitado WHERE id_pedido = $idPedido");
	}
	
	function obtenerDetalles($idPedido){
		$q = "";
		$q .= "SELECT d.cantidad, d.precio, d.subtotal, p.nombre_producto, s.nombre_presentacion "; 
		$q .= "FROM pedido_item d INNER JOIN producto_presentacion s ON s.id_presentacion = d.id_presentacion ";
		$q .= "INNER JOIN producto p ON p.id_producto = s.id_producto ";
		$q .= "WHERE d.id_pedido = $idPedido ORDER BY p.nombre_producto";
		
		return $this->DB->execute($q);
	}
	
	function cancelar($idPedido, $token){
		$sql = "UPDATE pedido SET estado_pedido = 'cancelada' WHERE id_pedido = $idPedido AND codigo_cancelacion = '$token'";
		$ok = $this->DB->execute($sql);
		if($ok === false){
			return "{$this->DB->ErrorMsg()}\nSQL: $sql";
		}
		return "";
	}
	
	// Sea comprador o invitado, se obtiene el identificador del mismo
	function obtenerIdCliente($idPedido){
		$sql  = "SELECT v.invitado, i.id_invitado, c.id_comprador ";
		$sql .= "FROM pedido v LEFT OUTER JOIN invitado i ON i.id_pedido = v.id_pedido LEFT OUTER JOIN pedido_comprador c ON c.id_pedido = v.id_pedido ";
		$sql .= "WHERE v.id_pedido = $idPedido";
		$vectorInfo = iterator_to_array($this->DB->execute($sql));
		$vectorInfo = $vectorInfo[0];
		
		if($vectorInfo['invitado']){
			// Es invitado 
			return (int)$vectorInfo['id_invitado'];
		}
		else {
			// Es comprador 
			return (int)$vectorInfo['id_comprador'];
		}
	}
	
	function confirmarPago($idPedido){
		$sql = "UPDATE pedido SET estado_pago = 'confirmado' WHERE id_pedido = $idPedido";
		$ok = $this->DB->execute($sql);
		if($ok === false){
			return "{$this->DB->ErrorMsg()}\nSQL: $sql";
		}
		return "";
	}
	
	function asociarCobroAbitab($idPedido, $agencia, $subAgente, $fechaCobro){
		$this->cobro_abitab__r->Registro["id_pedido"] = $idPedido;
		$this->cobro_abitab__r->Registro["codigo_agencia"] = $agencia;
		$this->cobro_abitab__r->Registro["codigo_subagente"] = $subAgente;
		$this->cobro_abitab__r->Registro["fecha_cobro"] = $fechaCobro;
		$this->cobro_abitab__r->Registro["fecha_procesado"] = date("Y-m-d H:i");
		return $this->cobro_abitab__r->TablaDB->addRegistro($this->cobro_abitab__r->Registro);
	}
	
	function obtenerCobroAbitab($idPedido){
		$sql = "SELECT * FROM pedido_cobro_abitab WHERE id_pedido = $idPedido";
		$arr = iterator_to_array($this->DB->execute($sql));
		if(count($arr) > 0){
			return $arr[0];
		}
		else {
			return "";
		}
	}
	
	function modificarEstado($idPedido, $estado){
		$sql = "UPDATE pedido SET estado = '$estado' WHERE id_pedido = $idPedido";
		$ok = $this->DB->execute($sql);
		if($ok === false){
			return "{$this->DB->ErrorMsg()}\nSQL: $sql";
		}
		return "";
	}
}
?>