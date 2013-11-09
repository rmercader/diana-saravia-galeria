<?PHP

// Constantes para links en modulos
define('LNK_NADA', 'nada');

// Constantes para acciones
define('ACC_GRID','G');
define('ACC_PDF','F');
define('ACC_ALTA','A');
define('ACC_MODIFICACION','M');
define('ACC_CONSULTA','C');
define('ACC_SELECCIONAR','L');
define('ACC_BAJA','B');
define('ACC_POST','S');
define('ACC_VER','X');
define('ACC_ANULACION', 'N');

// Constantes para las productos
define('PRODUCTOS_POR_PAGINA', 6);
define('DIR_HTTP_FOTOS_PRODUCTOS', DIR_HTTP_PUBLICA.'images/productos/');
define('DIR_FOTOS_PRODUCTOS', "D:\\desarrollo\\Balz Bioenergetic\\www\\images\\productos\\");
define('LARGO_THUMBNAIL_PRODUCTO', 190);
define('ANCHO_THUMBNAIL_PRODUCTO', 190);
define('LARGO_PREVIEW_PRODUCTO', 190);
define('ANCHO_PREVIEW_PRODUCTO', 190);
// Por diagramacion 1
define('WIDTH_FOTO_PRODUCTO_DIAG_1', 700);
define('HEIGHT_FOTO_PRODUCTO_DIAG_1', 300);
// Por diagramacion 2
define('WIDTH_FOTO_PRODUCTO_DIAG_2', 260);
define('HEIGHT_FOTO_PRODUCTO_DIAG_2', 260);
// Por diagramacion 3
define('WIDTH_FOTO_PRODUCTO_DIAG_3', 700);
define('HEIGHT_FOTO_PRODUCTO_DIAG_3', 300);
// Por diagramacion 4
define('WIDTH_FOTO_PRODUCTO_DIAG_4', 260);
define('HEIGHT_FOTO_PRODUCTO_DIAG_4', 260);

// Constantes para las noticias
define('DIR_HTTP_FOTOS_NOTICIAS', DIR_HTTP_PUBLICA.'images/noticias/');
define('DIR_FOTOS_NOTICIAS', "D:\\desarrollo\\Balz Bioenergetic\\www\\images\\noticias\\");
define('LARGO_THUMBNAIL_NOTICIA', 120);
define('ANCHO_THUMBNAIL_NOTICIA', 120);
define('LARGO_PREVIEW_NOTICIA', 190);
define('ANCHO_PREVIEW_NOTICIA', 190);
// Por diagramacion 1
define('WIDTH_FOTO_NOTICIA_DIAG_1', 700);
define('HEIGHT_FOTO_NOTICIA_DIAG_1', 300);
// Por diagramacion 2
define('WIDTH_FOTO_NOTICIA_DIAG_2', 260);
define('HEIGHT_FOTO_NOTICIA_DIAG_2', 260);
// Por diagramacion 3
define('WIDTH_FOTO_NOTICIA_DIAG_3', 700);
define('HEIGHT_FOTO_NOTICIA_DIAG_3', 300);
// Por diagramacion 4
define('WIDTH_FOTO_NOTICIA_DIAG_4', 260);
define('HEIGHT_FOTO_NOTICIA_DIAG_4', 260);

// Constantes para los links de interes
define('DIR_HTTP_FOTOS_LINKS', DIR_HTTP_PUBLICA.'images/links/');
define('DIR_FOTOS_LINKS', "D:\\desarrollo\\Balz Bioenergetic\\www\\images\\links\\");
define('LARGO_THUMBNAIL_LINK', 120);
define('ANCHO_THUMBNAIL_LINK', 120);

// Constantes para los distribuidores
define('DIR_HTTP_FOTOS_DISTRIBUIDORES', DIR_HTTP_PUBLICA.'images/distribuidores/');
define('DIR_FOTOS_DISTRIBUIDORES', "D:\\desarrollo\\Balz Bioenergetic\\www\\images\\distribuidores\\");
define('LARGO_THUMBNAIL_DISTRIBUIDORES', 120);
define('ANCHO_THUMBNAIL_DISTRIBUIDORES', 120);

// Constantes para la galeria de imagen
define('DIR_HTTP_FOTOS_GALERIA', DIR_HTTP_PUBLICA.'images/galeria/');
define('DIR_FOTOS_GALERIA', "D:\\desarrollo\\Balz Bioenergetic\\www\\images\\galeria\\");
define('WIDTH_FOTO_GALERIA', 700);
define('HEIGHT_FOTO_GALERIA', 300);
define('WIDTH_THUMB_GALERIA', 120);
define('HEIGHT_THUMB_GALERIA', 90);

// Constantes para la galeria slider
define('WIDTH_FOTO_SLIDER', 1000);
define('HEIGHT_FOTO_SLIDER', 400);

// Constantes para los resultados
define('DIR_HTTP_FOTOS_RESULTADOS', DIR_HTTP_PUBLICA.'images/resultados/');
define('DIR_FOTOS_RESULTADOS', "D:\\desarrollo\\Balz Bioenergetic\\www\\images\\resultados\\");
define('LARGO_THUMBNAIL_RESULTADO', 190);
define('ANCHO_THUMBNAIL_RESULTADO', 190);
define('LARGO_PREVIEW_RESULTADO', 190);
define('ANCHO_PREVIEW_RESULTADO', 190);
// Por diagramacion 1
define('WIDTH_FOTO_RESULTADO_DIAG_1', 700);
define('HEIGHT_FOTO_RESULTADO_DIAG_1', 300);
// Por diagramacion 2
define('WIDTH_FOTO_RESULTADO_DIAG_2', 260);
define('HEIGHT_FOTO_RESULTADO_DIAG_2', 260);
// Por diagramacion 3
define('WIDTH_FOTO_RESULTADO_DIAG_3', 700);
define('HEIGHT_FOTO_RESULTADO_DIAG_3', 300);
// Por diagramacion 4
define('WIDTH_FOTO_RESULTADO_DIAG_4', 260);
define('HEIGHT_FOTO_RESULTADO_DIAG_4', 260);

// Constantes para las aplicaciones
define('DIR_HTTP_FOTOS_APLICACIONES', DIR_HTTP.'aplicaciones/fotos/');
define('DIR_FOTOS_APLICACIONES', DIR_BASE.'aplicaciones/fotos/');
define('LARGO_THUMBNAIL_APLICACION', 190);
define('ANCHO_THUMBNAIL_APLICACION', 190);
define('LARGO_PREVIEW_APLICACION', 190);
define('ANCHO_PREVIEW_APLICACION', 190);
define('LARGO_FOTO_APLICACION', 300);
define('ANCHO_FOTO_APLICACION', 280);

// Constantes Generales
define('_SI','S');
define('_NO','N');
define('_SIN','Si');
define('_NON','No');
define('ID_SN',_SI.'|'._NO);
define('NOM_SN','Si|No');
define('CANT_DEC', 6);
define('ID_IDIOMA_ADMIN', 2);
define('ALTURA_EDITOR', '300');
define('CREDENCIALES_CLIENTE', "CREDENCIALES_CLIENTE");
define('COOKIE_ID_CLIENTE', "COOKIE_ID_CLIENTE");

// Perfiles de usuario
define('PERFIL_ADMINISTRADOR', 1);
define('PERFIL_CLIENTE', 2);

// Error Level
define('_ERROR','ERROR');
define('_OK','OK');

// Agenda
$DIASEM = array('Domigo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
$HORAS  = array('00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00','24:00');

// Paneles por defecto
$Tpl_Panel      = 'base_panel.htm';
$Tpl_Calendario = 'base_calendario.htm';
$Tpl_Grid       = 'base_grid.htm';
$Tpl_Menu       = 'base_menu.htm';

// Temas HTML
$TEMASHTML_id  = array('estilo01');
$TEMASHTML_nom = array('Tema por defecto');

// Variables por defecto
$ESTILO_HTML = 'estilo01';
$Reg_Pag = 10;
$Reg_Pag_bt = 15;
define('TAM_PAGINA', $Reg_Pag);

?>
