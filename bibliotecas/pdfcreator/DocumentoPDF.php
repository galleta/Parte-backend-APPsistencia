<?php
require('fpdf181/fpdf.php');
 
class DocumentoPDF extends FPDF
{
	var $B;
	var $I;
	var $U;
	var $HREF;
	var $legends;
    var $wLegend;
    var $sum;
    var $NbVal;
    var $SO;
    var $rutafichero;

    function __construct($sistemaoperativo, $rutafichero)
    {
    	parent::__construct();
    	$this->SO = $sistemaoperativo;
    	$this->rutafichero = $rutafichero;
    }

   	function escribirTexto($texto, $letra, $estilo, $tamanio)
   	{
   		$this->SetFont($letra, $estilo, $tamanio);
		$this->Cell(40,10,utf8_decode($texto));
		$this->Ln(10);
   	}

   	function escribirTextoCentrado($texto, $letra, $estilo, $tamanio)
   	{
   		$this->SetFont($letra, $estilo, $tamanio);
		$this->Cell(0,10,utf8_decode($texto),'',0,'C');
		$this->Ln(10);
   	}

   	function mostrarDatos($ciclo, $curso, $modulo, $cursoescolar, $alumno)
   	{
        $this->SetFont('Arial','I',12);
		$texto = '<b><u>Ciclo Formativo</u>:</b> ' . utf8_decode($ciclo);
		$this->WriteHTML($texto);
		$this->Ln(7);
		$texto2 = '<b><u>Curso</u>:</b> ' . utf8_decode($curso);
		$this->WriteHTML($texto2);
		$this->Ln(7);
		$texto3 = utf8_decode('<b><u>Módulo Formativo</u>:</b> ') . utf8_decode($modulo);
		$this->WriteHTML($texto3);
		$this->Ln(7);
		$texto4 = utf8_decode('<b><u>Curso escolar</u>:</b> ') . utf8_decode($cursoescolar);
		$this->WriteHTML($texto4);
		$this->Ln(7);
		$texto5 = utf8_decode('<b><u>Alumno/a</u>:</b> ') . utf8_decode($alumno);
		$this->WriteHTML($texto5);
   	}

   	function mostrarPDF()
   	{
   		//$this->Output();
   		$this->Output($this->rutafichero,'F');
   	}

	// Pie de página
	function Footer()
	{
		//Establecer la información local en castellano de España
		setlocale(LC_TIME,"es_ES");
        $this->SetY(-15);
        $this->SetFont('Arial','I',10);
        $this->Cell(0,10,utf8_decode('Datos generados automáticamente por la app de asistencia (' . $this->SO . ') a fecha ' . strftime("%d/%m/%Y a las %H:%M") . ', página ') . $this->PageNo(),'T',0,'C');
    }
 
	// Cabecera
    function Header()
    {
    	// Logo
	    $this->Image('datos/logo_ITPv2.png',10,6,30);
	    // Arial bold 15
	    //$this->SetFont('Arial','B',15);
	    // Move to the right
	    //$this->Cell(80);
	    // Title
	    //$this->Cell(30,10,'Title',0,0,'C');
	    // Line break
	    $this->Ln(20);
    }

    function dibujarGraficoAsistencias($asistencias, $faltas, $faltasjustificadas, $retrasos, $horastotales)
    {
		$data = array('Asistencias' => $asistencias, 'Faltas de asistencia' => $faltas, 'Faltas justificadas' => $faltasjustificadas, 'Retrasos' => $retrasos);
		//Pie chart
		$this->escribirTexto('','Arial','B',16);

		$this->SetFont('Arial', '', 10);
		$valX = $this->GetX();
		$valY = $this->GetY();
		$this->Cell(30, 5, 'Asistencias:');
		$this->Cell(15, 5, $data['Asistencias'], 0, 0, 'R');
		$this->Ln();
		$this->Cell(30, 5, 'Faltas de asistencia:');
		$this->Cell(15, 5, $data['Faltas de asistencia'], 0, 0, 'R');
		$this->Ln();
		$this->Cell(30, 5, 'Faltas justificadas:');
		$this->Cell(15, 5, $data['Faltas justificadas'], 0, 0, 'R');
		$this->Ln();
		$this->Cell(30, 5, 'Retrasos:');
		$this->Cell(15, 5, $data['Retrasos'], 0, 0, 'R');
		$this->Ln();
		$this->Cell(30, 5, 'Horas totales impartidas:');
		$this->Cell(15, 5, $horastotales, 0, 0, 'R');
		$this->Ln();
		$this->Ln(8);

		$this->SetXY(90, $valY);
		$col1=array(100,100,255);
		$col2=array(255,100,100);
		$col3=array(255,255,100);
		$col4=array(100,255,255);
		$this->PieChart(100, 35, $data, '%l (%p)', array($col1,$col2,$col3,$col4));
		$this->SetXY($valX, $valY + 40);
    }

    // Código para escribir HTML

	function WriteHTML($html)
	{
	    // Intérprete de HTML
	    $html = str_replace("\n",' ',$html);
	    $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	    foreach($a as $i=>$e)
	    {
	        if($i%2==0)
	        {
	            // Text
	            if($this->HREF)
	                $this->PutLink($this->HREF,$e);
	            else
	                $this->Write(5,$e);
	        }
	        else
	        {
	            // Etiqueta
	            if($e[0]=='/')
	                $this->CloseTag(strtoupper(substr($e,1)));
	            else
	            {
	                // Extraer atributos
	                $a2 = explode(' ',$e);
	                $tag = strtoupper(array_shift($a2));
	                $attr = array();
	                foreach($a2 as $v)
	                {
	                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
	                        $attr[strtoupper($a3[1])] = $a3[2];
	                }
	                $this->OpenTag($tag,$attr);
	            }
	        }
	    }
	}

	function OpenTag($tag, $attr)
	{
	    // Etiqueta de apertura
	    if($tag=='B' || $tag=='I' || $tag=='U')
	        $this->SetStyle($tag,true);
	    if($tag=='A')
	        $this->HREF = $attr['HREF'];
	    if($tag=='BR')
	        $this->Ln(5);
	}

	function CloseTag($tag)
	{
	    // Etiqueta de cierre
	    if($tag=='B' || $tag=='I' || $tag=='U')
	        $this->SetStyle($tag,false);
	    if($tag=='A')
	        $this->HREF = '';
	}

	function SetStyle($tag, $enable)
	{
	    // Modificar estilo y escoger la fuente correspondiente
	    $this->$tag += ($enable ? 1 : -1);
	    $style = '';
	    foreach(array('B', 'I', 'U') as $s)
	    {
	        if($this->$s>0)
	            $style .= $s;
	    }
	    $this->SetFont('',$style);
	}

	function PutLink($URL, $txt)
	{
	    // Escribir un hiper-enlace
	    $this->SetTextColor(0,0,255);
	    $this->SetStyle('U',true);
	    $this->Write(5,$txt,$URL);
	    $this->SetStyle('U',false);
	    $this->SetTextColor(0);
	}

	function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
    {
        $d0 = $a - $b;
        if($cw){
            $d = $b;
            $b = $o - $a;
            $a = $o - $d;
        }else{
            $b += $o;
            $a += $o;
        }
        while($a<0)
            $a += 360;
        while($a>360)
            $a -= 360;
        while($b<0)
            $b += 360;
        while($b>360)
            $b -= 360;
        if ($a > $b)
            $b += 360;
        $b = $b/360*2*M_PI;
        $a = $a/360*2*M_PI;
        $d = $b - $a;
        if ($d == 0 && $d0 != 0)
            $d = 2*M_PI;
        $k = $this->k;
        $hp = $this->h;
        if (sin($d/2))
            $MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
        else
            $MyArc = 0;
        //first put the center
        $this->_out(sprintf('%.2F %.2F m',($xc)*$k,($hp-$yc)*$k));
        //put the first point
        $this->_out(sprintf('%.2F %.2F l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
        //draw the arc
        if ($d < M_PI/2){
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
        }else{
            $b = $a + $d/4;
            $MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
            $a = $b;
            $b = $a + $d/4;
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
            $a = $b;
            $b = $a + $d/4;
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
            $a = $b;
            $b = $a + $d/4;
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
        }
        //terminate drawing
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='b';
        else
            $op='s';
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1*$this->k,
            ($h-$y1)*$this->k,
            $x2*$this->k,
            ($h-$y2)*$this->k,
            $x3*$this->k,
            ($h-$y3)*$this->k));
    }

    // Código para dibujar un diagrama de tarta

    function PieChart($w, $h, $data, $format, $colors=null)
    {
        $this->SetFont('Courier', '', 10);
        $this->SetLegends($data,$format);

        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $hLegend = 5;
        $radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
        $radius = floor($radius / 2);
        $XDiag = $XPage + $margin + $radius;
        $YDiag = $YPage + $margin + $radius;
        if($colors == null) {
            for($i = 0; $i < $this->NbVal; $i++) {
                $gray = $i * intval(255 / $this->NbVal);
                $colors[$i] = array($gray,$gray,$gray);
            }
        }

        //Sectors
        $this->SetLineWidth(0.2);
        $angleStart = 0;
        $angleEnd = 0;
        $i = 0;
        foreach($data as $val) {
            $angle = ($val * 360) / doubleval($this->sum);
            if ($angle != 0) {
                $angleEnd = $angleStart + $angle;
                $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
                $this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
                $angleStart += $angle;
            }
            $i++;
        }

        //Legends
        $this->SetFont('Courier', '', 10);
        $x1 = $XPage + 2 * $radius + 4 * $margin;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
        for($i=0; $i<$this->NbVal; $i++) {
            $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
            $this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
            $this->SetXY($x2,$y1);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }

    function SetLegends($data, $format)
    {
        $this->legends=array();
        $this->wLegend=0;
        $this->sum=array_sum($data);
        $this->NbVal=count($data);
        foreach($data as $l=>$val)
        {
            $p=sprintf('%.2f',$val/$this->sum*100).'%';
            $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
            $this->legends[]=$legend;
            $this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
        }
    }

    // Código para dibujar una tabla

    function cabeceraHorizontal($cabecera)
    {
        //$this->SetXY(10, 10);
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(255,255,255);//Fondo blanco de la primera celda
        $this->CellFitSpace(35, 7, ' ', 0, 0,'C', true);
        $this->SetFillColor(2,157,116);//Fondo verde de celda
        $this->SetTextColor(240, 255, 240); //Letra color blanco
        foreach($cabecera as $fila)
        {
            $this->CellFitSpace(35, 7, utf8_decode($fila), 1, 0,'C', true);
        }
    }
 
    function datosHorizontal($datos)
    {
        //$this->SetXY(70,150);
        $this->SetFont('Arial','',10);
        $this->SetFillColor(229, 229, 229); //Gris tenue de cada fila
        $this->SetTextColor(3, 3, 3); //Color del texto: Negro
        $bandera = false; //Para alternar el relleno
        foreach($datos as $fila)
        {
        	$this->SetFillColor(255,255,255);//Fondo blanco de la primera celda
        	$this->CellFitSpace(35,7, ' ', 0, 0, 'C', $bandera);
            //Usaremos CellFitSpace en lugar de Cell
            $this->SetFillColor(229, 229, 229); //Gris tenue de cada fila
        	$this->SetTextColor(3, 3, 3); //Color del texto: Negro
            if(utf8_decode($fila['tipo_asistencia']) == 'FALTA')
                $asistenciamostrar = 'Falta de asistencia';
            else
                if(utf8_decode($fila['tipo_asistencia']) == 'FALTAJUSTIFICADA')
                    $asistenciamostrar = 'Falta justificada';
                else
                    if(utf8_decode($fila['tipo_asistencia']) == 'RETRASO')
                        $asistenciamostrar = 'Retraso';
            $this->CellFitSpace(35, 7, $asistenciamostrar, 1, 0, 'C', $bandera );
            $this->CellFitSpace(35, 7, utf8_decode($fila['fecha']), 1, 0, 'C',$bandera);
            $this->CellFitSpace(35, 7, utf8_decode($fila['cantidad']), 1, 0, 'C', $bandera);
            $this->Ln();//Salto de línea para generar otra fila
            $bandera = !$bandera;//Alterna el valor de la bandera
        }
    }
 
    function tablaHorizontal($cabeceraHorizontal, $datosHorizontal)
    {        
        if( count($datosHorizontal) == 0)
        {
            $this->escribirTextoCentrado('No hay registro de faltas de asistencia, faltas justificadas o retrasos', 'Arial', 'B', 12);
        }
        else
        {
            $this->cabeceraHorizontal($cabeceraHorizontal);
            $this->Ln();
            $this->datosHorizontal($datosHorizontal);
        }
    }
 
    //***** Aquí comienza código para ajustar texto *************
    //***********************************************************
    function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
    {
        //Get string width
        $str_width=$this->GetStringWidth($txt);
 
        //Calculate ratio to fit cell
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $ratio = ($w-$this->cMargin*2)/$str_width;
 
        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit)
        {
            if ($scale)
            {
                //Calculate horizontal scaling
                $horiz_scale=$ratio*100.0;
                //Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
            }
            else
            {
                //Calculate character spacing in points
                $char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
                //Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET',$char_space));
            }
            //Override user alignment (since text will fill up cell)
            $align='';
        }
 
        //Pass on to Cell method
        $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
 
        //Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
    }
 
    function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
    }
 
    //Patch to also work with CJK double-byte text
    function MBGetStringLength($s)
    {
        if($this->CurrentFont['type']=='Type0')
        {
            $len = 0;
            $nbbytes = strlen($s);
            for ($i = 0; $i < $nbbytes; $i++)
            {
                if (ord($s[$i])<128)
                    $len++;
                else
                {
                    $len++;
                    $i++;
                }
            }
            return $len;
        }
        else
            return strlen($s);
    }

} // FIN Class PDF


?>