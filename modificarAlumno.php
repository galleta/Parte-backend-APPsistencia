<?php

// Hago que se muestren los errores si los hay
ini_set('display_errors', 1);

require_once('vistas/VistaJson.php');
require_once('controladores/GestorAlumnos.php');
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
	$id = $_REQUEST['id'];
	$nnombre = $_REQUEST['nnombre'];
	$napellidos = $_REQUEST['napellidos'];

	// Me creo un gestor de alumnos
	$gestor = new GestorAlumnos();

	// Saco por pantalla en formato JSON el resultado
	$vista->imprimir($gestor->modificarAlumno($id, $nnombre, $napellidos));
}
else
{
	$vista->imprimir(error);
}