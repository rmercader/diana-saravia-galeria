<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Constantes para imagenes
|--------------------------------------------------------------------------
*/

define("OBRA_GALLERY_WIDTH", 800);
define("OBRA_GALLERY_HEIGHT", 600);
define("OBRA_PREVIEW_WIDTH", 186);
define("OBRA_PREVIEW_HEIGHT", 140);
define("OBRA_THUMB_WIDTH", 101);
define("OBRA_THUMB_HEIGHT", 64);
define("OBRA_IMAGE_QUALITY", 100);
define("OBRA_IMAGE_GALLERY_MARKER", ".gal");
define("OBRA_IMAGE_PREVIEW_MARKER", ".prv");
define("OBRA_IMAGE_THUMB_MARKER", ".thu");

define("EVENTO_PREVIEW_WIDTH", 186);
define("EVENTO_PREVIEW_HEIGHT", 140);
define("EVENTO_THUMB_WIDTH", 110);
define("EVENTO_THUMB_HEIGHT", 69);
define("EVENTO_IMAGE_QUALITY", 100);
define("EVENTO_IMAGE_PREVIEW_MARKER", ".prv");
define("EVENTO_IMAGE_THUMB_MARKER", ".thu");
/* End of file constants.php */
/* Location: ./application/config/constants.php */