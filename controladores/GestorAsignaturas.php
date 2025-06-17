<?php

require_once('datos/mensajes.php');
require_once('datos/ConexionBD.php');
require_once('utilidades/ExcepcionApi.php');

// Esta clase representa un gestor para las asignaturas

class GestorAsignaturas
{
    // Nombres de la tabla y de los atributos
	const NOMBRE_TABLA = "asignatura";
    const ID = "id";
    const NOMBRE = "nombre";
    const NOMBREABREVIADO = "nombreabreviado";
    const CICLO = "ciclo";
    const CURSO = "curso";

    /**
     * Descripción: Obtiene las asignaturas
     * @return Asignaturas
    */
    public function obtenerTodasAsignaturas()
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA;

            $sentencia = $pdo->prepare($comando);
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
     * Descripción: Obtiene el id de una asignatura
     * @param nombre Nombre abreviado de la asignatura
     * @param ciclo Ciclo de la asignatura
     * @param curso Curso de la asignatura
     * @return Identificador de la asignatura
    */
    public function obtenerIDAsignatura($nombre, $ciclo, $curso)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT id FROM " . self::NOMBRE_TABLA . " " .
            "WHERE " . self::NOMBREABREVIADO . " = ? " .
            "AND " . self::CICLO . " = ? " .
            "AND " . self::CURSO . " = ? ";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $ciclo);
            $sentencia->bindParam(3, $curso);
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
     * Descripción: Obtiene todos los datos de una asignatura
     * @param nombre Nombre abreviado de la asignatura
     * @param ciclo Ciclo de la asignatura
     * @param curso Curso de la asignatura
     * @return Identificador de la asignatura
    */
    public function obtenerAsignatura($nombre, $ciclo, $curso)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA . " " .
            "WHERE " . self::NOMBREABREVIADO . " = ? " .
            "AND " . self::CICLO . " = ? " .
            "AND " . self::CURSO . " = ? ";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $ciclo);
            $sentencia->bindParam(3, $curso);
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
     * Descripción: Obtiene todos los datos de una asignatura mediante su identificador
     * @param idasignatura Identificador de la asignatura
     * @return Datos de la asignatura
    */
    public function obtenerAsignaturaPorID($idasignatura)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA . " " .
            "WHERE " . self::ID . " = ? ";

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
     * Descripción: Obtiene todas las asignaturas en las que está matriculado un alumno
     * @param: idalumno Identificador del alumno
     * @return Asignaturas en las que está matriculado en alumno
    */
    public function obtenerTodasAsignaturasMatriculadasAlumno($idalumno)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT * FROM " . self::NOMBRE_TABLA . " " .
            "INNER JOIN alumno_asignatura ON alumno_asignatura.asignatura = asignatura.id " .
            "WHERE alumno_asignatura.idalumno = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);
            $sentencia->execute();

            $array = array();

            while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) 
            { 
                array_push($array, $row);
            }

            return 
            [
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
     * Descripción: Desmatricula a un alumno de una asignatura
     * @param idalumno Alumno para desmatricular
     * @param idasignatura Asignatura que se le va a quitar al alumno
     * @return Verdadero si se ha eliminado correctamente, falso en caso contrario
    */
    public function eliminarAsignaturaMatriculadaAlumno($idalumno, $idasignatura)
    {
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "DELETE FROM alumno_asignatura " .
            "WHERE idalumno = ? " .
            "AND asignatura = ?";

            $sentencia = $pdo->prepare($comando);

            // Pongo los datos en la consulta INSERT
            $sentencia->bindParam(1, $idalumno);
            $sentencia->bindParam(2, $idasignatura);

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
     * Descripción: Obtiene todas las asignaturas impartidas por un profesor
     * @param emailprofesor Email del profesor
     * @param curso Curso
     * @param ciclo Ciclo
     * @return Asignaturas que imparte el profesor en ese ciclo y curso
    */
    public function obtenerAsignaturasImpartidasProfesor($emailprofesor, $ciclo, $curso)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT asignatura.* FROM profesor " .
            "INNER JOIN profesor_asignatura ON profesor.id = profesor_asignatura.idprofesor " .
            "INNER JOIN asignatura ON profesor_asignatura.id_asignatura = asignatura.id " .
            "WHERE profesor.email = ? " .
            "AND asignatura.ciclo = ? " .
            "AND asignatura.curso = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $emailprofesor);
            $sentencia->bindParam(2, $ciclo);
            $sentencia->bindParam(3, $curso);
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
}