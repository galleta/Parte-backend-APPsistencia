<?php

require_once('datos/mensajes.php');
require_once('utilidades/ExcepcionApi.php');
require_once('datos/ConexionBD.php');

// Esta clase representa un gestor para las asistencias

class GestorAsistencias
{
    // Nombres de la tabla y de los atributos
	const NOMBRE_TABLA = "asistencia";
    const ID = "id";
    const ID_ALUMNO = "id_alumno";
    const ID_ASIGNATURA = "id_asignatura";
    const TIPO_ASISTENCIA = "tipo_asistencia";
    const FECHA = "fecha";

    /**
	 * Descripción: Inserta una nueva asistencia
	 * @param asistencia Asistencia a insertar
	 * @return Devuelve 'ok' si se ha insertado correctamente la asistencia y 'no ok' en caso contrario
	*/
	public function insertarAsistencia($asistencia)
    { 
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::ID_ALUMNO . "," .
                self::ID_ASIGNATURA . "," .
                self::TIPO_ASISTENCIA . "," .
                self::FECHA . ")" .
                " VALUES(?,?,?,?)";

            $sentencia = $pdo->prepare($comando);

            // Pongo los datos en la consulta INSERT
            $sentencia->bindParam(1, $asistencia->idalumno);
            $sentencia->bindParam(2, $asistencia->idasignatura);
            $sentencia->bindParam(3, $asistencia->tipoasistencia);
            $sentencia->bindParam(4, $asistencia->fechaasistencia);

            // Ejecuto la consulta
            $resultado = $sentencia->execute();
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }

        switch ($resultado) 
        {
            case ESTADO_CREACION_EXITOSA:
                http_response_code(200);
                return correcto;
                break;
            case ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(ESTADO_CREACION_FALLIDA, "Ha ocurrido un error.");
                break;
            default:
                throw new ExcepcionApi(ESTADO_FALLA_DESCONOCIDA, "Fallo desconocido.", 400);
        }
    }

    /**
     * Descripción: Elimina una asistencia
     * @param idasistencia Identificador de la asistencia a eliminar
     * @return Si se ha eliminado la asistencia correctamente o no
    */
	public function borrarAsistencia($idasistencia)
	{
		try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "DELETE FROM " . self::NOMBRE_TABLA . " " .
                "WHERE " . self::ID . " = ?";

            $sentencia = $pdo->prepare($comando);

            // Pongo los datos en la consulta DELETE
            $sentencia->bindParam(1, $idasistencia);

            // Ejecuto la consulta
            $resultado = $sentencia->execute();
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }

        switch ($resultado) 
        {
            case ESTADO_CREACION_EXITOSA:
                http_response_code(200);
                return correcto;
                break;
            case ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(ESTADO_CREACION_FALLIDA, "Ha ocurrido un error.");
                break;
            default:
                throw new ExcepcionApi(ESTADO_FALLA_DESCONOCIDA, "Fallo desconocido.", 400);
        }
	}

    /**
     * Descripción: Elimina todas las asistencias de la base de datos (MUY PELIGROSO)
     * @return Si se ha eliminado la asistencia correctamente o no
    */
    public function eliminarTodasAsistencias()
    {
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "DELETE FROM " . self::NOMBRE_TABLA;

            $sentencia = $pdo->prepare($comando);
            // Ejecuto la consulta
            $resultado = $sentencia->execute();
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }

        switch ($resultado) 
        {
            case ESTADO_CREACION_EXITOSA:
                http_response_code(200);
                return correcto;
                break;
            case ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(ESTADO_CREACION_FALLIDA, "Ha ocurrido un error.");
                break;
            default:
                throw new ExcepcionApi(ESTADO_FALLA_DESCONOCIDA, "Fallo desconocido.", 400);
        }
    }

    /**
     * Descripción: Obtiene el total hasta el momento de asistencias, faltas, faltas justificadas y retrasos para todos los alumnos de una asignatura
     * @param idasignatura Identificador de la asignatura
     * @return El total de asistencias, faltas, faltas justificadas y retrasos que lleva el alumno indicado en la asignatura indicada
    */
    public function obtenerTotalAsistenciaAlumnoAsignatura($idasignatura)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT alumno.nombre, alumno.apellidos, asignatura.nombreabreviado, COUNT(*) totalhoras, " .
            "IF(SUM(IF(tipo_asistencia='ASISTE',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='ASISTE',1,0))) totalasiste, " .
            "IF(SUM(IF(tipo_asistencia='FALTA',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='FALTA',1,0))) totalfalta, " .
            "IF(SUM(IF(tipo_asistencia='FALTAJUSTIFICADA',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='FALTAJUSTIFICADA',1,0))) totalfaltajustificada, " .
            "IF(SUM(IF(tipo_asistencia='RETRASO',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='RETRASO',1,0))) totalretraso " .
            "FROM asistencia " .
            "INNER JOIN alumno ON alumno.id = asistencia.id_alumno " .
            "INNER JOIN asignatura ON asignatura.id = asistencia.id_asignatura " .
            "WHERE asignatura.id = ? " .
            "GROUP BY asistencia.id_alumno, asistencia.id_asignatura " .
            "ORDER BY alumno.apellidos, alumno.nombre";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idasignatura);
            $sentencia->execute();

            $array = array();

            while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) 
            { 
                array_push($array, $row);
            }

            return [
                [
                    "estado" => ESTADO_CREACION_EXITOSA,
                    "mensaje" => $array
                ]
            ];
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Descripción: Obtiene el total hasta el momento de asistencias, faltas, faltas justificadas y retrasos para un alumno de una asignatura
     * @param idasignatura Identificador de la asignatura
     * @return El total de asistencias, faltas, faltas justificadas y retrasos que lleva el alumno indicado en la asignatura indicada
    */
    public function obtenerTotalesAsistenciaAlumnoAsignatura($idalumno, $idasignatura)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT alumno.nombre, alumno.apellidos, asignatura.nombreabreviado, COUNT(*) totalhoras, " .
            "IF(SUM(IF(tipo_asistencia='ASISTE',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='ASISTE',1,0))) totalasiste, " .
            "IF(SUM(IF(tipo_asistencia='FALTA',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='FALTA',1,0))) totalfalta, " .
            "IF(SUM(IF(tipo_asistencia='FALTAJUSTIFICADA',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='FALTAJUSTIFICADA',1,0))) totalfaltajustificada, " .
            "IF(SUM(IF(tipo_asistencia='RETRASO',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='RETRASO',1,0))) totalretraso " .
            "FROM asistencia " .
            "INNER JOIN alumno ON alumno.id = asistencia.id_alumno " .
            "INNER JOIN asignatura ON asignatura.id = asistencia.id_asignatura " .
            "WHERE asignatura.id = ? " .
            "AND alumno.id = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idasignatura);
            $sentencia->bindParam(2, $idalumno);
            $sentencia->execute();

            $array = array();

            while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) 
            { 
                array_push($array, $row);
            }

            return [
                [
                    "estado" => ESTADO_CREACION_EXITOSA,
                    "mensaje" => $array
                ]
            ];
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Descripción: Obtiene la cantidad de asistencias, faltas, faltas justificadas y retrasos de un alumno en una asignatura en una fecha
     * @param idalumno Alumno
     * @param idasignatura Asignatura
     * @param fecha Fecha
     * @return Cantidad de asistencias, faltas, faltas justificadas y retrasos de un alumno en una asignatura en una fecha
    */
    public function obtenerAsistenciasAlumnoAsignaturaDia($idalumno, $idasignatura, $fecha)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT alumno.nombre, alumno.apellidos, asignatura.nombreabreviado, asistencia.fecha, " . 
            "IF(SUM(IF(tipo_asistencia='ASISTE',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='ASISTE',1,0))) totalasiste, " . 
            "IF(SUM(IF(tipo_asistencia='FALTA',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='FALTA',1,0))) totalfalta, " . 
            "IF(SUM(IF(tipo_asistencia='FALTAJUSTIFICADA',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='FALTAJUSTIFICADA',1,0))) totalfaltajustificada, " . 
            "IF(SUM(IF(tipo_asistencia='RETRASO',1,0)) IS NULL, 0, SUM(IF(tipo_asistencia='RETRASO',1,0))) totalretraso " .
            "FROM asistencia " .
            "INNER JOIN alumno ON alumno.id = asistencia.id_alumno " .
            "INNER JOIN asignatura ON asignatura.id = asistencia.id_asignatura " .
            "WHERE asistencia.id_alumno = ? " .
            "AND asistencia.id_asignatura = ? " .
            "AND asistencia.fecha = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);
            $sentencia->bindParam(2, $idasignatura);
            $sentencia->bindParam(3, $fecha);
            $sentencia->execute();

            $array = array();

            while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) 
            { 
                array_push($array, $row);
            }

            return [
                [
                    "estado" => ESTADO_CREACION_EXITOSA,
                    "mensaje" => $array
                ]
            ];
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Descripción: Obtiene la cantidad de faltas, faltas justificadas y retrasos de un alumno en una asignatura hasta el momento por fechas
     * @param idalumno Identificador del alumno
     * @param idasignatura Identificador de la asignatura
     * @return Cantidad total de faltas, faltas justificadas y retrasos del alumno en la asignatura hasta el momento por fechas
    */
    public function obtenerTotalFaltasAlumnoAsignatura($idalumno, $idasignatura)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT alumno.id idalumno, alumno.nombre nombrealumno, alumno.apellidos apellidosalumno, asignatura.id idasignatura, asignatura.nombre nombreasig, asignatura.nombreabreviado nombreabreasig, asignatura.ciclo cicloasig, asignatura.curso cursoasig, asistencia.tipo_asistencia, asistencia.fecha, COUNT(*) cantidad " .
            "FROM asistencia " .
            "INNER JOIN alumno ON alumno.id = asistencia.id_alumno " .
            "INNER JOIN asignatura ON asignatura.id = asistencia.id_asignatura " .
            "WHERE (asistencia.tipo_asistencia = 'FALTA' OR asistencia.tipo_asistencia = 'FALTAJUSTIFICADA' OR asistencia.tipo_asistencia = 'RETRASO') " .
            "AND asistencia.id_alumno = ? " .
            "AND asistencia.id_asignatura = ? " .
            "GROUP BY asistencia.tipo_asistencia, asistencia.fecha " .
            "ORDER BY asistencia.fecha";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);
            $sentencia->bindParam(2, $idasignatura);
            $sentencia->execute();

            $array = array();

            while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) 
            { 
                array_push($array, $row);
            }

            return [
                [
                    "estado" => ESTADO_CREACION_EXITOSA,
                    "mensaje" => $array
                ]
            ];
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Descripción: Obtiene la cantidad de faltas, faltas justificadas y retrasos de un alumno en una asignatura hasta el momento por fechas
     * @param idalumno Identificador del alumno
     * @param idasignatura Identificador de la asignatura
     * @return Cantidad total de faltas, faltas justificadas y retrasos del alumno en la asignatura hasta el momento por fechas
    */
    public function obtenerTotalFaltasAlumnoAsignaturav2($idalumno, $idasignatura)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT asistencia.tipo_asistencia, asistencia.fecha, COUNT(*) cantidad " .
            "FROM asistencia " .
            "INNER JOIN alumno ON alumno.id = asistencia.id_alumno " .
            "INNER JOIN asignatura ON asignatura.id = asistencia.id_asignatura " .
            "WHERE (asistencia.tipo_asistencia = 'FALTA' OR asistencia.tipo_asistencia = 'FALTAJUSTIFICADA' OR asistencia.tipo_asistencia = 'RETRASO') " .
            "AND asistencia.id_alumno = ? " .
            "AND asistencia.id_asignatura = ? " .
            "GROUP BY asistencia.tipo_asistencia, asistencia.fecha " .
            "ORDER BY asistencia.fecha";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);
            $sentencia->bindParam(2, $idasignatura);
            $sentencia->execute();

            $array = array();

            while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) 
            { 
                array_push($array, $row);
            }

            return [
                [
                    "estado" => ESTADO_CREACION_EXITOSA,
                    "mensaje" => $array
                ]
            ];
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Descripción: Obtiene todas las asistencias de un alumno en una asignatura en una fecha concreta
     * @param idalumno Identificador del alumno
     * @param idasignatura Identificador de la asignatura
     * @param fecha Fecha para consultar
     * @return Lista con todas las asistencias del alumno indicado, en la asignatura indicada, en la fecha indicada
    */
    public function obtenerAsistenciasAlumnoAsignaturaFecha($idalumno, $idasignatura, $fecha)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT alumno.id idalumno, alumno.nombre nombrealumno, alumno.apellidos apellidosalumno, asignatura.nombreabreviado, asignatura.ciclo, asignatura.curso, asistencia.id idasistencia, asistencia.tipo_asistencia tipoasistencia, asistencia.fecha fechaasistencia FROM asistencia " .
            "INNER JOIN alumno ON alumno.id = asistencia.id_alumno " .
            "INNER JOIN asignatura ON asignatura.id = asistencia.id_asignatura " .
            "WHERE id_alumno = ? " .
            "AND id_asignatura = ? " .
            "AND fecha = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);
            $sentencia->bindParam(2, $idasignatura);
            $sentencia->bindParam(3, $fecha);
            $sentencia->execute();

            $array = array();

            while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) 
            { 
                array_push($array, $row);
            }

            return [
                [
                    "estado" => ESTADO_CREACION_EXITOSA,
                    "mensaje" => $array
                ]
            ];
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Descripción: Modifica una asistencia
     * @param idasistencia Identificador de la asistencia a modificar
     * @param nuevovalor Nuevo valor de la asistencia. Puede ser ASISTENCIA, FALTA, FALTAJUSTIFICADA o RETRASO
     * @return Si se ha modificado correctamente la asistencia o no
    */
    public function modificarAsistencia($idasistencia, $nuevovalor)
    {   
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia UPDATE
            $comando = "UPDATE " . self::NOMBRE_TABLA . " " .
            "SET " . self::TIPO_ASISTENCIA . " = ? " .
            "WHERE " . self::ID . " = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $nuevovalor);
            $sentencia->bindParam(2, $idasistencia);
            // Ejecuto la consulta
            $resultado = $sentencia->execute();
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }

        switch ($resultado) 
        {
            case ESTADO_CREACION_EXITOSA:
                http_response_code(200);
                return correcto;
                break;
            case ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(ESTADO_CREACION_FALLIDA, "Ha ocurrido un error.");
                break;
            default:
                throw new ExcepcionApi(ESTADO_FALLA_DESCONOCIDA, "Fallo desconocido.", 400);
        }
    }

    /**
     * Descripción: Justifica una asistencia por fecha en bloque
     * @param idalumno Identificador del alumno
     * @param idasignatura Identificador de la asignatura
     * @param fechafalta Fecha de la falta que se va a justificar
     * @return Si se ha modificado correctamente la asistencia o no
    */
    public function justificarAsistencia($idalumno, $idasignatura, $fechafalta)
    {   
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia UPDATE
            $comando = "UPDATE " . self::NOMBRE_TABLA . " " .
            "SET " . self::TIPO_ASISTENCIA . " = 'FALTAJUSTIFICADA' " .
            "WHERE " . self::ID_ALUMNO . " = ? " .
            "AND " . self::ID_ASIGNATURA . " = ? " .
            "AND " . self::FECHA . " = ? " .
            "AND " . self::TIPO_ASISTENCIA . " = 'FALTA'";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);
            $sentencia->bindParam(2, $idasignatura);
            $sentencia->bindParam(3, $fechafalta);
            // Ejecuto la consulta
            $resultado = $sentencia->execute();
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }

        switch ($resultado) 
        {
            case ESTADO_CREACION_EXITOSA:
                http_response_code(200);
                return correcto;
                break;
            case ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(ESTADO_CREACION_FALLIDA, "Ha ocurrido un error.");
                break;
            default:
                throw new ExcepcionApi(ESTADO_FALLA_DESCONOCIDA, "Fallo desconocido.", 400);
        }
    }
};