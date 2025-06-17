<?php

if (!defined('ESTADO_CREACION_EXITOSA'))
	define('ESTADO_CREACION_EXITOSA', 1);
if (!defined('ESTADO_CREACION_FALLIDA'))
	define('ESTADO_CREACION_FALLIDA', 2);
if (!defined('ESTADO_ERROR_BD'))
	define('ESTADO_ERROR_BD', 3);
if (!defined('ESTADO_AUSENCIA_CLAVE_API'))
	define('ESTADO_AUSENCIA_CLAVE_API', 4);
if (!defined('ESTADO_CLAVE_NO_AUTORIZADA'))
	define('ESTADO_CLAVE_NO_AUTORIZADA', 5);
if (!defined('ESTADO_URL_INCORRECTA'))
	define('ESTADO_URL_INCORRECTA', 6);
if (!defined('ESTADO_FALLA_DESCONOCIDA'))
	define('ESTADO_FALLA_DESCONOCIDA', 7);
if (!defined('ESTADO_PARAMETROS_INCORRECTOS'))
	define('ESTADO_PARAMETROS_INCORRECTOS', 8);

if (!defined('error'))
	define('error', 
		[
			[
	    		"estado" => ESTADO_FALLA_DESCONOCIDA,
				"mensaje" => utf8_encode("Error desconocido.")
			]
		]
	);

if (!defined('correcto'))
	define('correcto', 
		[
			[
	    		"estado" => ESTADO_CREACION_EXITOSA,
				"mensaje" => utf8_encode("OK")
			]
		]
	);



