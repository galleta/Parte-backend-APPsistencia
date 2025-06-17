<?php

// Esta clase representa un profesor

class Profesor
{
    // Variables de clase
    public $nombre, $email, $contrasena, $tipo, $token;

    // Constructor
    public function __construct($nnombre, $nemail, $ncontrasena, $ntipo, $ntoken)
    {
        $this->nombre = $nnombre;
        $this->email = $nemail;
        $this->contrasena = $ncontrasena;
        $this->tipo = $ntipo;
        $this->token = $ntoken;
    }

    // Muestra los datos del profesor
    public function toString()
    {
        return
            [
                "nombre" => utf8_encode($this->nombre),
                "email" => utf8_encode($this->email),
                "tipo" => utf8_encode($this->tipo)
            ];
    }
}