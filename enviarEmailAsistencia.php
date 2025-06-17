<?php

ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('vistas/VistaJson.php');
require_once('controladores/GestorAsistencias.php');
require_once('controladores/GestorAlumnos.php');
require_once('controladores/GestorAsignaturas.php');
require_once('utilidades/validacion.php');
require_once('datos/mensajes.php');

require_once('bibliotecas/PHPMailer/src/Exception.php');
require_once('bibliotecas/PHPMailer/src/PHPMailer.php');
require_once('bibliotecas/PHPMailer/src/SMTP.php');

require_once('bibliotecas/pdfcreator/DocumentoPDF.php');

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
	$idasignatura = $_REQUEST['idasignatura'];

	$cursoescolar = $_REQUEST['cursoescolar'];
	$sistemaoperativo = $_REQUEST['sistemaoperativo'];
	$rutaficheroservidor = $_REQUEST['rutaficheroservidor'];
	$destinatario = $_REQUEST['destinatario'];

	$rutaficheroservidor = "resumenesasistencias/" . $rutaficheroservidor;

	$controladoralum = new GestorAlumnos();
	$controladorasig = new GestorAsignaturas();
	$controladorasis = new GestorAsistencias();

	// ***** PARTE DE OBTENER LOS DATOS NECESARIOS *****

	// Obtengo los datos del alumno
	$datosalumno = $controladoralum->obtenerAlumnoPorID($idalumno)[0]['mensaje'][0];
	// Obtengo los datos de la asignatura
	$datosasignatura = $controladorasig->obtenerAsignaturaPorID($idasignatura)[0]['mensaje'][0];
	// Obtengo todas las faltas, faltas de asistencia y retrasos del alumno en la asignatura con su fecha correspondiente
	$datosfaltas = $controladorasis->obtenerTotalFaltasAlumnoAsignatura($datosalumno['id'], $datosasignatura['id'])[0]['mensaje'];
	// Obtengo la cantidad de asistencias, faltas, faltas justificadas y retrasos del alumno en la asignatura, junto a las horas totales hasta el momento de la asignatura
	$datostotalesasistencias = $controladorasis->obtenerTotalesAsistenciaAlumnoAsignatura($datosalumno['id'], $datosasignatura['id'])[0]['mensaje'][0];

	// *************************************************

	// ***** PARTE DE CREAR EL PDF DE ASISTENCIA *****

	try
	{
		$pdf = new DocumentoPDF($sistemaoperativo, $rutaficheroservidor);
	 
		$pdf->AddPage();

		$pdf->escribirTextoCentrado('Resumen de asistencia','Arial','B',30);
		$pdf->escribirTexto('','Arial','B',16);

		if( $datosasignatura['curso'] == 'PRIMERO' )
		{
			$cursomostrar = "1º";
			$cursomostrar1 = "1&ordm;";
		}
		if( $datosasignatura['curso'] == 'SEGUNDO' )
		{
			$cursomostrar = "2º";
			$cursomostrar1 = "2&ordm;";
		}

		// Obtengo el nombre completo del ciclo
		try 
	    {
	        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

	        // Sentencia SELECT
	        $comando = "SELECT * FROM ciclo WHERE nombre = ?";

	        $sentencia = $pdo->prepare($comando);
	        $sentencia->bindParam(1, $datosasignatura['ciclo']);
	        $sentencia->execute();

	        $array = array();

	        while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) 
	        { 
	            array_push($array, $row);
	        }

	        $nombreciclocompleto = $array[0]['nombrecompleto'];

	        if( $nombreciclocompleto == 'Sistemas Microinformáticos y Redes' )
			{
				$nombreciclocompletoemail = "Sistemas Microinform&aacute;ticos y Redes";
			}
			if( $nombreciclocompleto == 'Desarrollo de Aplicaciones Multiplafatorma' )
			{
				$nombreciclocompletoemail = "Desarrollo de Aplicaciones Multiplafatorma";
			}
	    } 
	    catch (PDOException $e) 
	    {
	        throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
	    }

		$pdf->mostrarDatos($nombreciclocompleto, $cursomostrar, $datosasignatura['nombre'], $cursoescolar, $datosalumno['nombre'] . ' ' . $datosalumno['apellidos']);

		$pdf->dibujarGraficoAsistencias($datostotalesasistencias['totalasiste'], $datostotalesasistencias['totalfalta'], $datostotalesasistencias['totalfaltajustificada'], $datostotalesasistencias['totalretraso'], $datostotalesasistencias['totalhoras']);

		$pdf->escribirTextoCentrado('Listado de faltas, faltas justificadas y retrasos','Arial','B',20);
		$pdf->escribirTextoCentrado(' ', 'Arial', 'B', 5);

		$miCabecera = array('Tipo de asistencia', 'Fecha (día/mes/año)', 'Cantidad');

		$pdf->tablaHorizontal($miCabecera, $datosfaltas);

		$pdf->mostrarPDF(); // Creo el fichero PDF en el servidor
	}
	catch (Exception $e) 
	{
	    $vista->imprimir(error);
	}	
	
	// ***********************************************

	// ***** PARTE DE ENVIAR EL EMAIL *****

	try
	{
		$mail = new PHPMailer(true);

		//Server settings
		$mail->SMTPDebug = 0;                                       // Enable verbose debug output
		$mail->isSMTP();                                            // Set mailer to use SMTP
		$mail->Host       = 'smtp.gmail.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		$mail->Username   = 'francis@itponiente.com';                     // SMTP username
		$mail->Password   = 'formacioN_2015_Francis';                               // SMTP password
		$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
		$mail->Port       = 587;                                    // TCP port to connect to
		$mail->IsHTML(true);

		//Recipients
		$mail->setFrom('francis@itponiente.com', 'App asistencia');
		$mail->addAddress($destinatario, 'App asistencia');     // Add a recipient
		//$mail->addAddress('ellen@example.com');               // Name is optional
		//$mail->addReplyTo('info@example.com', 'Information');
		//$mail->addCC('cc@example.com');
		//$mail->addBCC('bcc@example.com');

		// Attachments
		$mail->addAttachment($rutaficheroservidor);         // Add attachments

		date_default_timezone_set('Europe/Madrid');
		//$script_tz = date_default_timezone_get();

		// Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = 'Informe de asistencia de ' . $datosalumno['nombre'] . ' ' . $datosalumno['apellidos'];
		$mail->Body    = 'Informe de asistencia:<br><ul><li><b><u>Nombre y apellidos del alumno</u>:</b> ' . $datosalumno['nombre'] . ' ' . $datosalumno['apellidos'];
		$mail->Body    = $mail->Body . "</li><li><b><u>Ciclo Formativo</u>:</b> " . $nombreciclocompletoemail . "</li><li><b><u>Curso</u>:</b> " . $cursomostrar1 . "</li><li><b><u>M&oacute;dulo Formativo</u>:</b> " . $datosasignatura['nombre'] . "</li><li><b><u>Curso escolar</u>:</b> " . $cursoescolar . "</li><li><b><u>Asistencia obtenida a d&iacute;a</u>:</b> " . date("d/m/Y") . "</li></ul>";
        $mail->Body    = $mail->Body . "<br>-----------------------------------------------------------------------------------------------------------------------";
        $mail->Body    = $mail->Body . "<br>Este email es autogenerado por la aplicaci&oacute;n de asistencia, por favor no responder.";
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        //$vista->imprimir($mail->Body);

		$mail->send();	
	}
	catch (Exception $e) 
	{
	    $vista->imprimir(error);
	}

	// ************************************

	// ***** Elimino el fichero de asistencia generado *****
	try 
	{
	    unlink($rutaficheroservidor);
	    $vista->imprimir(correcto);
	} 
	catch (Exception $e) 
	{
	    $vista->imprimir(error);
	}
	// *****************************************************
}
else
{
	$vista->imprimir(error);
}

?>
