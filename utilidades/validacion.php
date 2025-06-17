<?php

function validar($parametros)
{
	$devolver = false;

	if (!empty($parametros) && isset($parametros['v']))
	{
		$token = $parametros['v'];

		try 
		{
		    $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

		    // Sentencia SELECT
		    $comando = "SELECT COUNT(*) FROM profesor WHERE token = ?";
		    $sentencia = $pdo->prepare($comando);
		    $sentencia->bindParam(1, $token);
		    $sentencia->execute();

		    $row = $sentencia->fetch(PDO::FETCH_ASSOC);
		    
		    if( $row['COUNT(*)'] == 0 )
		    	$devolver = false;
		    else
		    	$devolver = true;
		} 
		catch (PDOException $e) 
		{
		    throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
		}
	}
	else
	{
		$devolver = false;
	}
	return $devolver;
}