<?php

require_once('datos/mensajes.php');
require_once('utilidades/ExcepcionApi.php');
require_once('datos/ConexionBD.php');

// Esta clase representa un gestor para los alumnos

class GestorAlumnos
{
    // Nombres de la tabla y de los atributos
	const NOMBRE_TABLA = "alumno";
    const ID = "id";
    const NOMBRE = "nombre";
    const APELLIDOS = "apellidos";

    /**
     * Descripción: Inserta un nuevo alumno en el sistema
     * 
     * @param alumno Alumno a insertar
     * @return Devuelve 'ok' si se ha insertado correctamente el alumno y 'no ok' en caso contrario
    */
    public function insertarAlumno($alumno)
    { 
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::NOMBRE . "," .
                self::APELLIDOS . ")" .
                " VALUES(?,?)";

            $sentencia = $pdo->prepare($comando);

            // Pongo los datos en la consulta INSERT
            $sentencia->bindParam(1, $alumno->nombre);
            $sentencia->bindParam(2, $alumno->apellidos);

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
     * Descripción: Obtiene los datos de un alumno a partir de su identificador
     * @param idalumno Identificador del alumno
     * @return Datos del alumno indicado
    */
    public function obtenerAlumnoPorID($idalumno)
    { 
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT * FROM alumno " .
            "WHERE " . self::ID . " = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);
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
     * Descripción: Obtiene todos los alumnos que hay en el sistema de un ciclo y un curso
     * 
     * @param ciclo Ciclo para obtener los alumnos
     * @param curso Curso para obtener los alumnos
     * @return Alumnos registrados en el sistema del ciclo y curso indicados
    */
    public function obtenerTodosAlumnos($ciclo, $curso)
    {
    	try 
		{
		    $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

		    // Sentencia SELECT
		    $comando = "SELECT alumno.nombre, alumno.apellidos FROM alumno " .
		    "INNER JOIN alumno_curso_ciclo ON alumno.id = alumno_curso_ciclo.idalumno " .
		    "WHERE alumno_curso_ciclo.ciclo = ? AND alumno_curso_ciclo.curso = ? " .
            "ORDER BY alumno.apellidos, alumno.nombre";

		    $sentencia = $pdo->prepare($comando);
		    $sentencia->bindParam(1, $ciclo);
		    $sentencia->bindParam(2, $curso);
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
     * Descripción: Obtiene el id de un alumno
     *
     * @param nombre Nombre del alumno
     * @param apellidos Apellidos del alumno
     * @return Identificador del alumno
    */
    public function obtenerIDAlumno($nombre, $apellidos)
    {
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Obtengo el id del profesor
            $comando = "SELECT " . self::ID . " " .
            "FROM " . self::NOMBRE_TABLA . " " .
            "WHERE " . self::NOMBRE . " = ?" .
            "AND " . self::APELLIDOS . " = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $apellidos);
            $sentencia->execute();

            $datosalumno = $sentencia->fetch(PDO::FETCH_ASSOC);

            $array = array(
                "id" => $datosalumno[self::ID]
            );

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
	 * Descripción: Matricula un alumno en un curso de un ciclo
	 * 
	 * @param idAlumno Identificador del alumno
	 * @param ciclo Ciclo en el que se matricula el alumno
	 * @param curso Curso en el que se matricula el alumno
	 * 
	 * @return Devuelve 'ok' si se ha insertado correctamente el alumno y 'no ok' en caso contrario
	*/
	public function matricularAlumno($idAlumno, $ciclo, $curso)
	{
		try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "INSERT INTO alumno_curso_ciclo (idalumno, ciclo, curso) VALUES (?,?,?)";

            $sentencia = $pdo->prepare($comando);

            // Pongo los datos en la consulta INSERT
            $sentencia->bindParam(1, $idAlumno);
            $sentencia->bindParam(2, $ciclo);
            $sentencia->bindParam(3, $curso);

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
     * Descripción: Modifica los datos de un alumno
     * @param id Id del alumno
     * @param nnombre Nombre nuevo del alumno
     * @param napellidos Apellidos nuevos del alumno
     * 
     * @return Si se ha modificado correctamente o no
    */
    public function modificarAlumno($id, $nnombre, $napellidos)
    {   
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "UPDATE " . self::NOMBRE_TABLA . " " .
            "SET " . self::NOMBRE . " = ?, " .
            self::APELLIDOS . " = ? " .
            "WHERE " . self::ID . " = ?";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $nnombre);
            $sentencia->bindParam(2, $napellidos);
            $sentencia->bindParam(3, $id);

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
	 * Descripción: Borra un alumno y todas sus asistencias
	 * @param idalumno Identificador del alumno para borrar
	 * @return Devuelve 'ok' si se ha borrado correctamente y 'no ok' en caso contrario
	*/
	public function eliminarAlumnoAsistencias($idalumno)
	{
		try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // ********************
            // Creo la sentencia DELETE de las matriculaciones de ciclos del alumno
            $comando = "DELETE FROM alumno_curso_ciclo " .
            "WHERE idalumno = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);

            //echo $sentencia;

            // Ejecuto la consulta
            $resultado = $sentencia->execute();

            // ********************
            // Creo la sentencia DELETE de las matriculaciones de asignaturas del alumno
            $comando = "DELETE FROM alumno_asignatura " .
            "WHERE idalumno = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);

            // Ejecuto la consulta
            $resultado = $sentencia->execute();

            // ********************
            // Creo la sentencia DELETE de las asistencias
            $comando = "DELETE FROM asistencia " .
            "WHERE id_alumno = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);

            // Ejecuto la consulta
            $resultado = $sentencia->execute();

            // ********************
            // Creo la sentencia DELETE del alumno
            $comando = "DELETE FROM " . self::NOMBRE_TABLA . " " .
            "WHERE " . self::ID . " = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);

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
	 * Descripción: Matricula un alumno en una asignatura de un curso de un ciclo
	 * 
	 * @param idAlumno Identificacor del alumno
	 * @param asignatura Asignatura
	 * @param ciclo Ciclo
	 * @param curso Curso
	 * 
	 * @return ok si el alumno se ha matriculado correctamente en la asignatura, en caso contrario no ok
	*/
	public function matricularAlumnoAsignatura($idAlumno, $asignatura, $ciclo, $curso)
	{
		try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // ********************
            // Obtengo el id de la asignatura
            $comando = "SELECT id " .
            "FROM asignatura " .
            "WHERE nombreabreviado = ?" .
            "AND ciclo = ? " .
            "AND curso = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $asignatura);
            $sentencia->bindParam(2, $ciclo);
            $sentencia->bindParam(3, $curso);
            $sentencia->execute();

            $datosasignatura = $sentencia->fetch(PDO::FETCH_ASSOC);
            $idasignatura = $datosasignatura['id'];

            // ********************
            // Creo la sentencia INSERT del alumno y asignatura
            $comando = "INSERT INTO alumno_asignatura VALUES(?, ?)";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idAlumno);
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
	 * Descripción: Obtiene los alumnos matriculados de una asignatura
	 * 
	 * @param asignatura Asignatura (nombre abreviado)
	 * @param ciclo Ciclo
	 * @param curso Curso
	 * 
	 * @return Lista con todos los alumnos matriculadores de la asignatura indicada
	*/
	public function obtenerAlumnosMatriculadosAsignatura($asignatura, $ciclo, $curso)
	{
		try 
		{
		    $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

		    // Sentencia SELECT
		    $comando = "SELECT alumno.* FROM alumno " .
				"INNER JOIN alumno_asignatura ON alumno.id = alumno_asignatura.idalumno " .
				"INNER JOIN asignatura ON alumno_asignatura.asignatura = asignatura.id " .
				"WHERE asignatura.nombreabreviado = ? " .
				"AND asignatura.ciclo = ? " .
				"AND asignatura.curso = ? " .
                "ORDER BY alumno.apellidos, alumno.nombre";

		    $sentencia = $pdo->prepare($comando);
		    $sentencia->bindParam(1, $asignatura);
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
     * Descripción: Obtiene los alumnos matriculados de un curso y ciclo
     * 
     * @param ciclo Ciclo
     * @param curso Curso
     * 
     * @return Lista con todos los alumnos matriculadores del curso y ciclo indicados
    */
    public function obtenerAlumnosMatriculadosCicloCurso($ciclo, $curso)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT alumno.id, alumno.nombre, alumno.apellidos FROM alumno " .
                "INNER JOIN alumno_curso_ciclo ON alumno.id = alumno_curso_ciclo.idalumno " .
                "WHERE alumno_curso_ciclo.ciclo = ? " .
                "AND alumno_curso_ciclo.curso = ? " .
                "ORDER BY alumno.apellidos, alumno.nombre";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $ciclo);
            $sentencia->bindParam(2, $curso);
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
     * Descripción: Obtiene los datos de un alumno
     * 
     * @param nombre Nombre del alumno
     * @param apellidos Apellidos del alumno
     * 
     * @return Alumno con los datos indicados
    */
    public function obtenerAlumnoPorNombreApellidos($nombre, $apellidos)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia SELECT
            $comando = "SELECT * FROM alumno " .
                "WHERE nombre = ? " .
                "AND apellidos = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $apellidos);
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
     * Descripción: Desmatricula a un alumno de un ciclo y un curso
     * @param idalumno Identificador del alumno a desmatricular
     * @param ciclo Ciclo para desmatricular al alumno
     * @param curso Curso para desmatricular al alumno
     * @return Verdadero si se ha desmatriculado correctamente, falso en caso contrario
    */
    public function desmatricularAlumnoCicloCurso($idalumno, $ciclo, $curso)
    {
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // ********************
            // Creo la sentencia DELETE de la matriculación del alumno en el curso del ciclo
            $comando = "DELETE FROM alumno_curso_ciclo " .
            "WHERE idalumno = ? " .
            "AND ciclo = ? " .
            "AND curso = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idalumno);
            $sentencia->bindParam(2, $ciclo);
            $sentencia->bindParam(3, $curso);

            // Ejecuto la consulta
            $resultado = $sentencia->execute();

            // ********************
            // Creo la sentencia DELETE de las matriculaciones de asignaturas del alumno de ese curso del ciclo
            // Sentencia SELECT de las asignaturas de ese curso del ciclo
            $comando2 = "SELECT id FROM asignatura " .
            "WHERE ciclo = ? " .
            "AND curso = ?";

            $sentencia2 = $pdo->prepare($comando2);
            $sentencia2->bindParam(1, $ciclo);
            $sentencia2->bindParam(2, $curso);
            $sentencia2->execute();

            while ($row = $sentencia2->fetch(PDO::FETCH_ASSOC)) 
            {
                $comando3 = "DELETE FROM alumno_asignatura " .
                "WHERE idalumno = ? " .
                "AND asignatura = ?";

                $sentencia3 = $pdo->prepare($comando3);
                $sentencia3->bindParam(1, $idalumno);
                $sentencia3->bindParam(2, $row['id']);

                // Ejecuto la consulta
                $resultado = $sentencia3->execute();

                // Creo la sentencia DELETE de las asistencias
                $comando4 = "DELETE FROM asistencia " .
                "WHERE id_alumno = ? " . 
                "AND id_asignatura = ?";

                $sentencia4 = $pdo->prepare($comando4);
                $sentencia4->bindParam(1, $idalumno);
                $sentencia4->bindParam(2, $row['id']);

                // Ejecuto la consulta
                $resultado = $sentencia4->execute();
            }
            
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

}