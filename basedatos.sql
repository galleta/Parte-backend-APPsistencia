

CREATE TABLE profesor
(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(100) NOT NULL,
	email VARCHAR(100) NOT NULL,
	password VARCHAR(1000) NOT NULL,
	tipo VARCHAR(100) NOT NULL,
	token VARCHAR(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE ciclo
(
	nombre VARCHAR(10) NOT NULL PRIMARY KEY,
	nombrecompleto VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE alumno 
(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE asignatura
(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(100) NOT NULL,
	nombreabreviado VARCHAR(100) NOT NULL,
	ciclo VARCHAR(10) NOT NULL,
	curso VARCHAR(20) NOT NULL,
	CONSTRAINT fk_asignatura_ciclo FOREIGN KEY (ciclo) REFERENCES ciclo(nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE profesor_asignatura
(
	idprofesor INT NOT NULL,
	id_asignatura INT NOT NULL,
	CONSTRAINT fk_profesor_asignatura_idprofesor FOREIGN KEY (idprofesor) REFERENCES profesor(id),
	CONSTRAINT fk_profesor_asignatura_idasignatura FOREIGN KEY (id_asignatura) REFERENCES asignatura(id),
	PRIMARY KEY(idprofesor, id_asignatura)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE alumno_curso_ciclo
(
	idalumno INT NOT NULL,
	ciclo VARCHAR(100) NOT NULL,
	curso VARCHAR(20) NOT NULL,
	CONSTRAINT fk_alumno_curso_ciclo_idalumno FOREIGN KEY (idalumno) REFERENCES alumno(id),
	CONSTRAINT fk_alumno_curso_ciclo_ciclo FOREIGN KEY (ciclo) REFERENCES ciclo(nombre),
	PRIMARY KEY(idalumno, ciclo, curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE alumno_asignatura
(
	idalumno INT NOT NULL,
	asignatura INT NOT NULL,
	CONSTRAINT fk_alumno_asignatura_idalumno FOREIGN KEY (idalumno) REFERENCES alumno(id),
	CONSTRAINT fk_alumno_asignatura_asignatura FOREIGN KEY (asignatura) REFERENCES asignatura(id),
	PRIMARY KEY(idalumno, asignatura)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE asistencia 
(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT NOT NULL,
    id_asignatura INT NOT NULL,
    tipo_asistencia VARCHAR(100) NOT NULL,
    fecha VARCHAR(100) NOT NULL,
    CONSTRAINT fk_asistencia_idalumno FOREIGN KEY (id_alumno) REFERENCES alumno(id),
    CONSTRAINT fk_asistencia_idasignatura FOREIGN KEY (id_asignatura) REFERENCES asignatura(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO ciclo (nombre, nombrecompleto) VALUES('SMR', 'Sistemas Microinformáticos y Redes');
INSERT INTO ciclo (nombre, nombrecompleto) VALUES('DAM', 'Desarrollo de Aplicaciones Multiplataforma');

INSERT INTO profesor (nombre, email, password, tipo, token) VALUES ('Francisco Delgado', 'francis@itponiente.com', '8efe362e8c465464e89464f08dd2191e753a34376fdb6d7317b027b3a38a500d9c0e83c9bc1774501d5b03fb21f74c52a94548cdfdb5acdeeaab05895c9e003f', 'ADMINISTRADOR', 'AF9069950F41E24ECD312E6386399A9BB3F01F65');

INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Montaje y Mantenimiento de Equipos', 'MME','PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Sistemas Operativos Monopuesto', 'SOM','PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Aplicaciones Ofimáticas', 'AO','PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Redes locales', 'RL','PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Formación y Orientación Laboral', 'FOL', 'PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Sistemas Operativos en Red', 'SOR','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Seguridad Informática', 'SI','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Servicios en Red', 'SER','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Aplicaciones Web', 'AW','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Libre configuración', 'LC','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('SMR', 'Empresa e Iniciativa Empresarial', 'EIE','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Sistemas informáticos', 'SI','PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Bases de datos', 'BD','PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Programación', 'PROG', 'PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Lenguajes de marcas', 'LM','PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Entornos de desarrollo', 'ED','PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Formación y orientación laboral', 'FOL','PRIMERO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Acceso a datos', 'AD','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Desarrollo de interfaces', 'DI','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Programación multimedia y dispositivos móviles', 'PMDM', 'SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Sistemas de gestión empresarial', 'SGE','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Empresa e iniciativa emprendedora', 'EIE','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Libre configuración', 'LC','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Programación de servicios y procesos', 'PSP','SEGUNDO');
INSERT INTO asignatura (ciclo, nombre, nombreabreviado, curso) VALUES('DAM', 'Proyecto D.A.M.', 'PROYECTO', 'SEGUNDO');
