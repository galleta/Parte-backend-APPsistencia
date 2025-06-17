<?php

// Hago que se muestren los errores si los hay
ini_set('display_errors', 1);

require_once('utilidades/validacion.php');
require_once('vistas/VistaJson.php');
require_once('controladores/GestorAsistencias.php');
require_once('controladores/GestorAsignaturas.php');
require_once('modelos/Asistencia.php');

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
	$idalumno = $_REQUEST['idalumno'];
	$nombreasignatura = $_REQUEST['nombreasignatura'];
	$cicloasignatura = $_REQUEST['cicloasignatura'];
	$cursoasignatura = $_REQUEST['cursoasignatura'];
	$tipoasistencia = $_REQUEST['tipoasistencia'];
	$fechaasistencia = $_REQUEST['fechaasistencia'];

	// Me creo los gestores
	$gestorasistencia = new GestorAsistencias();
	$gestorasignaturas = new GestorAsignaturas();

	// obtengo el ID de la asignatura
	$idasignatura = $gestorasignaturas->obtenerIDAsignatura($nombreasignatura, $cicloasignatura, $cursoasignatura)[0]['mensaje'][0]['id'];

	$asistencia = new Asistencia($idalumno, $idasignatura, $tipoasistencia, $fechaasistencia);

	// Saco por pantalla en formato JSON el resultado
	$vista->imprimir($gestorasistencia->insertarAsistencia($asistencia));
}
else
{
	$vista->imprimir(error);
}
