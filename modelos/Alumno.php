<?php

// Esta clase representa un alumno

class Alumno
{
    // Variables de clase
    public $nombre, $apellidos;

    // Constructor
    public function __construct($nnombre, $napellidos)
    {
        $this->nombre = $nnombre;
        $this->apellidos = $napellidos;
    }

    // Muestra los datos del alumno
    public function toString()
    {
        return
            [
                "nombre" => utf8_encode($this->nombre),
                "apellidos" => utf8_encode($this->apellidos)
            ];
    }
}