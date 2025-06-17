<?php

// Esta clase representa una asistencia

class Asistencia
{
    // Variables de clase
    public $idalumno, $idasignatura, $tipoasistencia, $fechaasistencia;

    // Constructor
    public function __construct($nidalumno, $nidasignatura, $ntipoasistencia, $nfechaasistencia)
    {
        $this->idalumno = $nidalumno;
        $this->idasignatura = $nidasignatura;
        $this->tipoasistencia = $ntipoasistencia;
        $this->fechaasistencia = $nfechaasistencia;
    }

    // Muestra los datos del alumno
    public function toString()
    {
        return
            [
                "id alumno" => utf8_encode($this->idalumno),
                "id asignatura" => utf8_encode($this->idasignatura),
                "tipo de asistencia" => utf8_encode($this->tipoasistencia),
                "fecha de asistencia" => utf8_encode($this->fechaasistencia)
            ];
    }
}