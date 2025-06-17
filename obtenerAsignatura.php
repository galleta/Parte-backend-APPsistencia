<?php

// Hago que se muestren los errores si los hay
ini_set('display_errors', 1);

require_once('vistas/VistaJson.php');
require_once('controladores/GestorAsignaturas.php');
require_once('utilidades/validacion.php');
require_once('datos/mensajes.php');

// Tipo de vista de la salida de datos.
$vista = new VistaJson();

// Con esta función nos aseguramos que cualquier excepción que ocurra se muestre adecuadamente
// en el mismo formato para evitar problemas.
set_exception_handler(function ($exception) use ($vista) 
	{
	    $cuerpo = array(
	    	array(
	        	"estado" => $exception->estado,
	        	"mensaje" => $exception->getMessage()
	    	)
	    );
	    if ($exception->getCode()) 
	    {
	        $vista->estado = $exception->getCode();
	    } 
	    else 
	    {
	        $vista->estado = 500;
	    }

	    $vista->imprimir($cuerpo);
	}
);

if (validar($_REQUEST))
{
	$nombreabreviado = $_REQUEST['nombreabreviado'];
	$ciclo = $_REQUEST['ciclo'];
	$curso = $_REQUEST['curso'];

	// Me creo un gestor de asignaturas
	$gestorasig = new GestorAsignaturas();

	// Saco por pantalla en formato JSON el resultado
	$vista->imprimir($gestorasig->obtenerAsignatura($nombreabreviado, $ciclo, $curso));
}
else
{
	$vista->imprimir(error);
}