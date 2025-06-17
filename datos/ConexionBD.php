<?php

/**
 * Esta clase sirve para poder realizar una conexión a la base de datos
 * MySQL de nuestro servidor.
 */

require_once('login_mysql.php');

class ConexionBD
{
    const ESTADO_ERROR_BD = 3;

    /**
     * Atributo para la conexión de la base de datos
     */
    private static $db = null;

    /**
     * Instancia de PDO
     */
    private static $pdo;

    /**
     * Constructor de la clase. Conecta con la base de datos.
     */ 
    final private function __construct()
    {
        try 
        {
            // Crear nueva conexión PDO
            self::obtenerBD();
        } 
        catch (PDOException $e) 
        {
            // Manejo de excepciones
            throw new ExcepcionApi(ESTADO_ERROR_BD, $e->getMessage());
        }

    }

    /**
     * Retorna en la única instancia de la clase
     * @return ConexionBD|null
     */
    public static function obtenerInstancia()
    {
        if (self::$db === null) 
        {
            self::$db = new self();
        }
        return self::$db;
    }

    /**
     * Crear una nueva conexión PDO basada
     * en las constantes de conexión
     * @return PDO Objeto PDO
     */
    public function obtenerBD()
    {
        if (self::$pdo == null) 
        {
            self::$pdo = new PDO(
                'mysql:dbname=' . BASE_DE_DATOS .
                ';host=' . NOMBRE_HOST . ";",
                USUARIO,
                CONTRASENA,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );

            // Habilitar excepciones
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }

    /**
     * Evita la clonación del objeto
     */
    final protected function __clone() {}

    /**
     * Desctructor de la clase. Cierra la conexión a la base de datos.
     */
    function _destructor()
    {
        self::$pdo = null;
    }
}