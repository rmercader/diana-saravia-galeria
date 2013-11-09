<?PHP 

include_once(DIR_BASE.'productos/coleccion.class.php');
include_once(DIR_BASE.'productos/color.class.php');
include_once(DIR_BASE.'productos/talle.class.php');
include_once(DIR_BASE.'class/class.phpmailer.php');
include_once(DIR_BASE.'class/foxycart-helper.class.php');

/* Clase de interfaz de la logica de negocio */
class Interfaz {
	
	private $Cnx; // AdoDBConnection
	
	// Constructor
	function Interfaz(){
		$this->Cnx = nyiCNX();
		$this->Cnx->debug = false;
	}
	
	function obtenerCotizacionMonedaPrincipal(){
		// Include de funcionalidad para consumir Web Services SOAP
		require_once(DIR_LIB . "nusoap/nusoap.php");
		$client = new nusoap_client(URL_WS_COTIZACION, true);
		$err = $client->getError();
		if($err){
			LogError(htmlspecialchars($client->getDebug(), ENT_QUOTES), __FILE__, "obtenerCotizacionMonedaPrincipal()");
			return "No se pudo obtener la cotizacion de la moneda " . MONEDA_TRANSACCIONES;
		}
		else{
			$params = array(
				'FromCurrency'=>MONEDA_BASE,
				'ToCurrency'=>MONEDA_TRANSACCIONES
			);
			$result = $client->call("ConversionRate", $params);
			if ($client->fault) {
				LogError(print_r($result, true), __FILE__, "obtenerCotizacionMonedaPrincipal()");
				return "No se pudo obtener la cotizacion de la moneda " . MONEDA_TRANSACCIONES;
			} 
			else {
				$err = $client->getError();
				if ($err) {
					LogError($err, __FILE__, "obtenerCotizacionMonedaPrincipal()");
					return "No se pudo obtener la cotizacion de la moneda " . MONEDA_TRANSACCIONES;
				} 
				else {
					return $result["ConversionRateResult"];
				}
			}
		}
	}
	
	/**
	 * Raw HTML Signing: Sign all links and form elements in a block of HTML
	 *
	 * Accepts a string of HTML and signs all links and forms.
	 * Requires link 'href' and form 'action' attributes to use 'https' and not 'http'.
	 * Requires a 'code' to be set in every form.
	 *
	 * @return string
	 **/
	function firmarHTML($html){
		return FoxyCartHelper::fc_hash_html($html);
	}
	
	function obtenerCantidadStock($idPrenda, $idColor, $idTalle){
		$obj = new Prenda($this->Cnx);
		return $obj->obtenerCantidadStock($idPrenda, $idColor, $idTalle);
	}
	
	function obtenerColoresPrenda($idPrenda){
		$obj = new Prenda($this->Cnx);
		$objColor = new Color($this->Cnx);
		$lstColores = iterator_to_array($obj->obtenerColores($idPrenda));
		for($i = 0; $i < count($lstColores); $i++){
			$lstColores[$i]["foto"] = $objColor->getUrlImagen($lstColores[$i]['id_color']);
			$lstColores[$i]["thumbnail"] = $objColor->getUrlImagen($lstColores[$i]['id_color'], 1);
		}
		return $lstColores;
	}
	
	function obtenerTallesPrenda($idPrenda){
		$obj = new Prenda($this->Cnx);
		return $obj->obtenerTallesAsociados($idPrenda);
	}
	
	function obtenerDatosPrenda($idPrenda){
		$obj = new Prenda($this->Cnx);
		return $obj->obtenerDatos($idPrenda);
	}
	
	function obtenerFotosPrenda($idPrenda){
		$obj = new Prenda($this->Cnx);
		$lstFotos = iterator_to_array($obj->obtenerGaleriaFotos($idPrenda));
		$lstUrls = array();
		foreach($lstFotos as $foto){
			$extension = $foto['extension'];
			$nomSinExt = $foto['nombre_imagen']; 
			array_push($lstUrls, DIR_HTTP_FOTOS_PRODUCTOS . "$idPrenda/$nomSinExt.$extension");
		}
		return $lstUrls;
	}
	
	function obtenerPrendasDestacadas(){
		$objPrenda = new Prenda($this->Cnx);
		return $objPrenda->obtenerListadoDestacadas();
	}
	
	function obtenerPrendasPorCategoria($idCategoria, $pagina){
		$objPrenda = new Prenda($this->Cnx);
		return $objPrenda->obtenerListadoPorCategoria($idCategoria, $pagina * PRODUCTOS_POR_PAGINA);
	}
	
	function obtenerPrendasPorColeccion($idColeccion, $pagina){
		$objPrenda = new Prenda($this->Cnx);
		return $objPrenda->obtenerListadoPorColeccion($idColeccion, $pagina * PRODUCTOS_POR_PAGINA);
	}
	
	function obtenerDatosCategoria($idCategoria, $campos=null){
		$objCat = new CategoriaPrenda($this->Cnx);
		return $objCat->obtenerDatos($idCategoria, $campos);
	}
	
	function obtenerUrlFotoCategoriaPrendas($idCategoria){
		$objCat = new CategoriaPrenda($this->Cnx);
		return $objCat->getUrlFoto($idCategoria);
	}
	
	function obtenerUrlFotoColeccionPrendas($idColeccion){
		$objCol = new Coleccion($this->Cnx);
		return $objCol->getUrlFoto($idColeccion);
	}
	
	function obtenerSubcategorias($idCategoria, $campos=null){
		$objCat = new CategoriaPrenda($this->Cnx);
		return $objCat->obtenerSubcategorias($idCategoria, $campos=null);
	}
	
	function obtenerCategoriasPrincipalesPorLinea($idLinea, $campos=null){
		$objCat = new CategoriaPrenda($this->Cnx);
		return $objCat->categoriasPrincipalesPorLinea($idLinea, $campos);
	}
	
	function obtenerCategoriasConPrendasPorLinea($idLinea, $campos=null){
		$objCat = new CategoriaPrenda($this->Cnx);
		return $objCat->categoriasConPrendasPorLinea($idLinea, $campos);
	}
	
	function obtenerColeccionesConPrendasPorLinea($idLinea, $campos=null){
		$objCol = new Coleccion($this->Cnx);
		return $objCol->coleccionesConPrendasPorLinea($idLinea, $campos);
	}
	
	function obtenerDatosColeccion($idColeccion, $campos=null){
		$objCol = new Coleccion($this->Cnx);
		return $objCol->obtenerDatos($idColeccion, $campos);
	}
	
	function obtenerNombreLinea($idLinea){
		$datos = "";
		switch($idLinea){
			case LINEA_DAMA:
				$datos = "Dama";
				break;
			case LINEA_HOMBRE:
				$datos = "Hombre";
				break;
			case LINEA_INFANTIL:
				$datos = "Infantil";
				break;
		}
		return $datos;
	}
	
	function obtenerTalles(){
		$obj = new Talle($this->Cnx);
		return $obj->obtenerTalles();
	}
	
	function obtenerIdTallePorCodigo($codTalle){
		$obj = new Talle($this->Cnx);
		return $obj->obtenerIdPorCodigo($codTalle);
	}
	
	function obtenerColores(){
		$obj = new Color($this->Cnx);
		$lstColores = $obj->obtenerColores();
		for($i = 0; $i < count($lstColores); $i++){
			$lstColores[$i]["foto"] = $obj->getUrlImagen($lstColores[$i]['id_color']);
			$lstColores[$i]["thumbnail"] = $obj->getUrlImagen($lstColores[$i]['id_color'], 1);
		}
		return $lstColores;
	}
	
	function esEmailValido($email){
		if(preg_match('/^[_\x20-\x2D\x2F-\x7E-]+(\.[_\x20-\x2D\x2F-\x7E-]+)*@(([_a-z0-9-]([_a-z0-9-]*[_a-z0-9-]+)?){1,63}\.)+[a-z0-9]{2,6}$/i', $email)){
			return TRUE;
		}
		return FALSE;
	}
	
	function loguearError($mensaje, $operacion='', $adicional=''){
		$logFilePtr = fopen(LOG_ERRORES, "a+");
		$log = sprintf("ERROR: %s\nFECHA: %s\nOPERACIÓN: %s\nINF. ADICIONAL: %s\n\n", $mensaje, date("d-m-Y H:i"), $operacion, $adicional);
		fwrite($logFilePtr, $log);
		fflush($logFilePtr);
		fclose($logFilePtr);
	}
	
	function armarMensajeError($mensaje){
		$contenido = new nyiHTML('base-error.htm');	
		$contenido->assign('error', $mensaje);
		return $contenido->fetchHTML();
	}
	
	function armarMensajeExito($mensaje){
		$contenido = new nyiHTML('base-exito.htm');	
		$contenido->assign('mensaje', $mensaje);
		return $contenido->fetchHTML();
	}
}
?>