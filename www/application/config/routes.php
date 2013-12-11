<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
$route['default_controller'] = 'sitio/index';
$route['404_override'] = '';

/* Sitio publico */
$route['la-galeria'] = 'sitio/la_galeria';
$route['diana'] = 'sitio/diana';
$route['contacto'] = 'sitio/contacto';
$route['artistas/detalle/(:any)'] = 'artistas/detalle/$1';
$route['eventos/preview/(:any)'] = 'eventos/preview/$1';
$route['obras/preview/(:any)'] = 'obras/preview/$1';
$route['obras/imagen/(:any)'] = 'obras/imagen/$1';
$route['obras/imagengaleria/(:any)'] = 'obras/imagenGaleria/$1';
$route['exposiciones'] = 'obras/index';
$route['exposiciones/obras-categoria'] = 'obras/obrasPorCategoria';

/*admin*/
$route['admin'] = 'user/index';
//$route['admin/signup'] = 'user/signup';
//$route['admin/create_member'] = 'user/create_member';
$route['admin/login'] = 'user/index';
$route['admin/logout'] = 'user/logout';
$route['admin/login/validate_credentials'] = 'user/validate_credentials';

$route['admin/categorias_obras'] = 'admin_categorias_obras/index';
$route['admin/categorias_obras/add'] = 'admin_categorias_obras/add';
$route['admin/categorias_obras/update'] = 'admin_categorias_obras/update';
$route['admin/categorias_obras/update/(:any)'] = 'admin_categorias_obras/update/$1';
$route['admin/categorias_obras/delete/(:any)'] = 'admin_categorias_obras/delete/$1';
$route['admin/categorias_obras/pornombre'] = 'admin_categorias_obras/pornombre';
$route['admin/categorias_obras/pornombre/(:any)'] = 'admin_categorias_obras/pornombre/$1';
$route['admin/categorias_obras/(:any)'] = 'admin_categorias_obras/index/$1'; //$1 = page number

$route['admin/artistas'] = 'admin_artistas/index';
$route['admin/artistas/add'] = 'admin_artistas/add';
$route['admin/artistas/update'] = 'admin_artistas/update';
$route['admin/artistas/update/(:any)'] = 'admin_artistas/update/$1';
$route['admin/artistas/delete/(:any)'] = 'admin_artistas/delete/$1';
$route['admin/artistas/pornombre'] = 'admin_artistas/pornombre';
$route['admin/artistas/pornombre/(:any)'] = 'admin_artistas/pornombre/$1';
$route['admin/artistas/(:any)'] = 'admin_artistas/index/$1'; //$1 = page number

$route['admin/obras'] = 'admin_obras/index';
$route['admin/obras/add'] = 'admin_obras/add';
$route['admin/obras/update'] = 'admin_obras/update';
$route['admin/obras/update/(:any)'] = 'admin_obras/update/$1';
$route['admin/obras/delete/(:any)'] = 'admin_obras/delete/$1';
$route['admin/obras/destacadas'] = 'admin_obras/destacadas';
$route['admin/obras/thumbnail'] = 'admin_obras/thumbnail';
$route['admin/obras/thumbnail/(:any)'] = 'admin_obras/thumbnail/$1';
$route['admin/obras/preview'] = 'admin_obras/preview';
$route['admin/obras/preview/(:any)'] = 'admin_obras/preview/$1';
$route['admin/obras/(:any)'] = 'admin_obras/index/$1'; //$1 = page number

$route['admin/eventos'] = 'admin_eventos/index';
$route['admin/eventos/add'] = 'admin_eventos/add';
$route['admin/eventos/update'] = 'admin_eventos/update';
$route['admin/eventos/update/(:any)'] = 'admin_eventos/update/$1';
$route['admin/eventos/delete/(:any)'] = 'admin_eventos/delete/$1';
$route['admin/eventos/thumbnail'] = 'admin_eventos/thumbnail';
$route['admin/eventos/thumbnail/(:any)'] = 'admin_eventos/thumbnail/$1';
$route['admin/eventos/preview'] = 'admin_eventos/preview';
$route['admin/eventos/preview/(:any)'] = 'admin_eventos/preview/$1';
$route['admin/eventos/(:any)'] = 'admin_eventos/index/$1'; //$1 = page number

$route['admin/mensajes'] = 'admin_mensajes/index';
$route['admin/mensajes/delete/(:any)'] = 'admin_mensajes/delete/$1';
$route['admin/mensajes/view/(:any)'] = 'admin_mensajes/view/$1';
$route['admin/mensajes/(:any)'] = 'admin_mensajes/index/$1'; //$1 = page number

/* End of file routes.php */
/* Location: ./application/config/routes.php */