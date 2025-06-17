<?php

require_once('datos/mensajes.php');
require_once('datos/ConexionBD.php');
require_once('utilidades/ExcepcionApi.php');

// Esta clase representa un gestor para los profesores

class GestorProfesores
{
    // Nombres de la tabla y de los atributos
	const NOMBRE_TABLA = "profesor";
    const ID = "id";
    const NOMBRE = "nombre";
    const EMAIL = "email";
    const CONTRASENA = "password";
    const TIPO = "tipo";
    const TOKEN = "token";

    private function encriptarTexto($texto)
    {
        return hash('sha512', $texto);
    }

    /**
     * Descripción: Obtiene las asignaturas de un profesor
     * @param emailprofesor: Email del profesor
     * @param ciclo: Ciclo para buscar las asignaturas
     * @param curso: Curso para buscar las asignaturas
     * @return Asignaturas que imparte el profesor en formato String
     */
    private function obtenerAsignaturasProfesorString($emailprofesor, $ciclo, $curso)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Obtengo sus asignaturas del curso y del ciclo
            $comando2 = "SELECT asignatura.nombreabreviado asignaturadada, asignatura.curso, asignatura.ciclo FROM profesor, asignatura, profesor_asignatura WHERE profesor.id = profesor_asignatura.idprofesor AND asignatura.id = profesor_asignatura.id_asignatura AND asignatura.ciclo = ? AND asignatura.curso = ? AND profesor.email = ?";

            $sentencia2 = $pdo->prepare($comando2);
            $sentencia2->bindParam(1, $ciclo);
            $sentencia2->bindParam(2, $curso);
            $sentencia2->bindParam(3, $emailprofesor);
            $sentencia2->execute();

            $asignaturas = '';
            $entro = 0;

            while($datosasignaturas = $sentencia2->fetch(PDO::FETCH_ASSOC))
            {
                $entro = 1;
                $asignaturas = $asignaturas . $datosasignaturas['asignaturadada'] . ", ";
            }

            if( $entro == 1 )
            {
                $asignaturas = mb_substr($asignaturas, 0, -2);
            }
            else
            {
                $asignaturas = $asignaturas . "Ninguna";
            }

            return $asignaturas;
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Descripción: Obtiene los profesores que hay registrados
     * @return Datos de todos los profesores registrados
    */
    public function obtenerTodosProfesores()
    {
    	try 
		{
		    $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

		    // Sentencia SELECT
		    $comando = "SELECT " . self::EMAIL . " " .
            "FROM " . self::NOMBRE_TABLA . " " .
            "ORDER BY " . self::NOMBRE;

		    $sentencia = $pdo->prepare($comando);
		    $sentencia->execute();
		    $arrayfinal = array();

		    while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) 
		    {
                // Obtento los datos del profesor
                $comando = "SELECT " .
                self::NOMBRE . ", " .
                self::EMAIL . ", " .
                self::TIPO . " " .
                "FROM " . self::NOMBRE_TABLA . " " .
                "WHERE " . self::EMAIL . " = ?";

                $sentencia2 = $pdo->prepare($comando);
                $sentencia2->bindParam(1, $row['email']);
                $sentencia2->execute();

                $datosprofesor = $sentencia2->fetch(PDO::FETCH_ASSOC);

                // Obtengo el resultado final
                $array = array(
                    "nombre" => $datosprofesor['nombre'],
                    "email" => $datosprofesor['email'],
                    "tipo" => $datosprofesor['tipo'],
                    "asignaturasSMR1" => 'SMR - 1: ' . self::obtenerAsignaturasProfesorString($row['email'], 'SMR', 'PRIMERO'),
                    "asignaturasSMR2" => 'SMR - 2: ' . self::obtenerAsignaturasProfesorString($row['email'], 'SMR', 'SEGUNDO'),
                    "asignaturasDAM1" => 'DAM - 1: ' . self::obtenerAsignaturasProfesorString($row['email'], 'DAM', 'PRIMERO'),
                    "asignaturasDAM2" => 'DAM - 2: ' . self::obtenerAsignaturasProfesorString($row['email'], 'DAM', 'SEGUNDO')
                );

				array_push($arrayfinal, $array);
			}

            return [
            	[
	                "estado" => ESTADO_CREACION_EXITOSA,
	                "mensaje" => $arrayfinal
            	]
            ];
		} 
		catch (PDOException $e) 
		{
		    throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
		}
    }

    /**
     * Descripción: Obtiene los datos de un profesor sabiendo su email
     * @param email Email del profesor
     * @return Datos y asignaturas impartidas del profesor indicado
    */
    public function obtenerProfesorEmail($email)
    {
        try 
        {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Obtento los datos del profesor
            $comando = "SELECT " .
            self::NOMBRE . ", " .
            self::EMAIL . ", " .
            self::TIPO . " " .
            "FROM " . self::NOMBRE_TABLA . " " .
            "WHERE " . self::EMAIL . " = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $email);
            $sentencia->execute();

            $datosprofesor = $sentencia->fetch(PDO::FETCH_ASSOC);

            // Obtengo el resultado final
            $array = array(
                "nombre" => $datosprofesor['nombre'],
                "email" => $datosprofesor['email'],
                "tipo" => $datosprofesor['tipo'],
                "asignaturasSMR1" => 'SMR - 1: ' . self::obtenerAsignaturasProfesorString($email, 'SMR', 'PRIMERO'),
                "asignaturasSMR2" => 'SMR - 2: ' . self::obtenerAsignaturasProfesorString($email, 'SMR', 'SEGUNDO'),
                "asignaturasDAM1" => 'DAM - 1: ' . self::obtenerAsignaturasProfesorString($email, 'DAM', 'PRIMERO'),
                "asignaturasDAM2" => 'DAM - 2: ' . self::obtenerAsignaturasProfesorString($email, 'DAM', 'SEGUNDO')
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
     * Descripción: Modifica la contraseña de un profesor a la contraseña por defecto
     * 
     * @param email Email del profesor
     * @param npass Nueva contraseña del profesor
     * @return Devuelve si se ha modificado correctamente a no
    */
    public function modificarPasswordProfesor($email, $npass, $ntoken)
    {
        // Encripto la contraseña
        $encrypted = self::encriptarTexto($npass);

        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "UPDATE " . self::NOMBRE_TABLA . " " .
            "SET " . self::CONTRASENA . " = ?, " .
            self::TOKEN . " = ? " .
            "WHERE " . self::EMAIL . " = ?";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $encrypted);
            $sentencia->bindParam(2, $ntoken);
            $sentencia->bindParam(3, $email);

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
     * Descripción: Login de profesor
     * 
     * @param emailprofesor Email del profesor
     * @param password Contraseña del profesor
     * @return Si el login es correcto o no
    */
    public function loguinProfesor($emailprofesor, $password)
    {
        try 
        {
            // Encripto la contraseña
            $encrypted = self::encriptarTexto($password);

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Obtento los datos del profesor
            $comando = "SELECT COUNT(*)" .
            "FROM " . self::NOMBRE_TABLA . " " .
            "WHERE " . self::EMAIL . " = ? " .
            "AND " . self::CONTRASENA . " = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $emailprofesor);
            $sentencia->bindParam(2, $encrypted);
            $sentencia->execute();

            $datosprofesor = $sentencia->fetch(PDO::FETCH_ASSOC);

            $total = $datosprofesor['COUNT(*)'];
            
            switch ($total) 
            {
                case 1:
                    http_response_code(200);
                    return correcto;
                    break;
                case 0:
                    throw new ExcepcionApi(ESTADO_CREACION_FALLIDA, "Error, datos incorrectos.");
                    break;
                default:
                    throw new ExcepcionApi(ESTADO_FALLA_DESCONOCIDA, "Fallo desconocido.", 400);
            }
        } 
        catch (PDOException $e) 
        {
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Descripción: Inserta un nuevo profesor en el sistema
     * 
     * @param profesor Profesor a insertar
     * @return Devuelve 'ok' si se ha insertado correctamente el profesor y 'no ok' en caso contrario
    */
    public function insertarProfesor($profesor)
    { 
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::NOMBRE . "," .
                self::EMAIL . "," .
                self::CONTRASENA . "," .
                self::TIPO . "," .
                self::TOKEN . ")" .
                " VALUES(?,?,?,?,?)";

            $sentencia = $pdo->prepare($comando);

            $contra = self::encriptarTexto($profesor->contrasena);

            // Pongo los datos en la consulta INSERT
            $sentencia->bindParam(1, $profesor->nombre);
            $sentencia->bindParam(2, $profesor->email);
            $sentencia->bindParam(3, $contra);
            $sentencia->bindParam(4, $profesor->tipo);
            $sentencia->bindParam(5, $profesor->token);

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
     * Descripción: Obtiene el id de un profesor
     *
     * @param email Email del profesor
     * @return Identificador del profesor
    */
    public function obtenerIDProfesor($email)
    {
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Obtengo el id del profesor
            $comando = "SELECT " . self::ID . " " .
            "FROM " . self::NOMBRE_TABLA . " " .
            "WHERE " . self::EMAIL . " = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $email);
            $sentencia->execute();

            $datosprofesor = $sentencia->fetch(PDO::FETCH_ASSOC);

            $array = array(
                "id" => $datosprofesor[self::ID]
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
     * Descripción: Elimina a un profesor y los datos de sus sus asignaturas impartidas
     *
     * @param email Email del profesor
     * @return Devuelve si se ha eliminado o no correctamente al profesor
    */
    public function eliminarProfesor($email)
    { 
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Obtengo el id del profesor
            $datos = self::obtenerIDProfesor($email);
            $idprofesor = $datos[0]["mensaje"]["id"];

            // Elimino las asignaturas del profesor
            $comando2 = "DELETE FROM profesor_asignatura " .
                "WHERE idprofesor = ?";

            $sentencia2 = $pdo->prepare($comando2);
            $sentencia2->bindParam(1, $idprofesor);
            $resultado = $sentencia2->execute();

            // Elimino al profesor
            $comando3 = "DELETE FROM " . self::NOMBRE_TABLA . " " .
                "WHERE " . self::ID . " = ?";

            $sentencia3 = $pdo->prepare($comando3);
            $sentencia3->bindParam(1, $idprofesor);
            $resultado = $sentencia3->execute();
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
     * Descripción: Modifica los datos de un profesor
     * @param id Id del profesor
     * @param nemail Nuevo email del profesor
     * @param nnombre Nombre nuevo del profesor
     * @param ntipoprofesor Tipo de profesor
     * 
     * @return Si se ha modificado correctamente o no
    */
    public function modificarProfesor($nemail, $nnombre, $ntipoprofesor)
    {   
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "UPDATE " . self::NOMBRE_TABLA . " " .
            "SET " . self::NOMBRE . " = ?, " .
            self::TIPO . " = ? " .
            "WHERE " . self::EMAIL . " = ?";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $nnombre);
            $sentencia->bindParam(2, $ntipoprofesor);
            $sentencia->bindParam(3, $nemail);

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
     * Descripción: Matricula a un profesor como profesor de una asignatura
     * 
     * @param idprofesor Identificador del profesor
     * @param idasignatura Identificador de la asignatura
     * @return Devuelve si se ha matriculado o no correctamente
    */
    public function matricularProfesorAsignatura($idprofesor, $idasignatura)
    {
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "INSERT INTO profesor_asignatura (idprofesor, id_asignatura) VALUES (?, ?)";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $idprofesor);
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
     * Descripción: Elimina una asignatura que imparte un profesor
     * 
     * @param idprofesor Identificador del profesor
     * @param idasignatura Identificador de la asignatura
     * @return Devuelve si se ha eliminado o no correctamente
    */
    public function quitarAsignaturaProfesor($idprofesor, $idasignatura)
    {
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "DELETE FROM profesor_asignatura WHERE idprofesor = ? AND id_asignatura = ?";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $idprofesor);
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

    public function modificarTokenProfesor($email, $ntoken)
    {
        try 
        {
            // Obtengo una instancia de la base de datos ya conectada
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Creo la sentencia INSERT
            $comando = "UPDATE " . self::NOMBRE_TABLA . " " .
            "SET " . self::TOKEN . " = ? " .
            "WHERE " . self::EMAIL . " = ?";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $ntoken);
            $sentencia->bindParam(2, $email);

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
}