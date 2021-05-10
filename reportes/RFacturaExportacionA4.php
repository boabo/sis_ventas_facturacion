<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RFacturaExportacionA4 extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    public $y_cabecera;

    function Header() {
        //var_dump("cabecera llega",$this->datos);
        $this->SetMargins(11,60,5);
        $this->SetFont('','B',14);
        $this->Image($this->datos['logo'],15,6,40,20);



    		$this->SetFont('','B',9);
    		$this->SetXY(126,6);
    		$this->Cell(67,5,' NIT: ','TL',0,'L');
    		$this->SetXY(161,6);
        $this->Cell(32,5,$this->datos['nit'],'TR',0,'L');

        $this->SetXY(126,9.7);
        $this->Cell(67,5,' N° FACTURA: ','L',0,'L');
        $this->SetXY(161,9.7);
        $this->Cell(32,5,$this->cabecera_factura['nro_factura'],'R',0,'L');

        $this->SetXY(126,13.4);
    		$this->Cell(67,5,' N° AUTORIZACIÓN: ','BL',0,'L');
    		$this->SetXY(161,13.4);
    		$this->Cell(32,5,$this->cabecera_factura['nro_autorizacion'],'BR',0,'L');

        $this->SetFont('','B',14);
    		$this->SetXY(130, 19);
    		$this->Cell(60,5,'ORIGINAL',0,1,'C');

        $this->SetFont('','B',7);
    		//$this->Cell(60,14,'VENTA AL POR MENOR DE OTROS PRODUCTOS EN ALMACENES NO ESPECIALIZADOS',0,1,'C');
        $this->MultiCell(65,45,$this->cabecera_factura['actividad_economica'],0,'C',0,1,'127','25');

        // if ($this->cabecera_factura['codigo_sucursal'] == 0) {
        //   $this->SetFont('','B',7);
        //   $this->SetXY(29,27);
        //   $this->Cell(10,2,$this->cabecera_factura['nombre_sucursal'],0,1,'C');
        //   $this->SetFont('','',6);
        //   $this->SetXY(29,30);
        //   $this->Cell(10,2,$this->cabecera_factura['direccion_sucursal'],0,1,'C');
        //   $this->SetXY(29,33);
        //   $this->Cell(10,2,'TELF: '.$this->cabecera_factura['telefono_sucursal'],0,1,'C');
        //   $this->SetXY(29,36);
        //   $this->Cell(10,2,$this->cabecera_factura['lugar_sucursal'],0,1,'C');
        //
        //   $x=52;
        //   $y=39;
        // } else {


          $this->SetFont('','B',7);
          $this->MultiCell(40,2,$this->cabecera_factura['nombre_sucursal'],0,'C',0,1,15,27);
          $this->SetFont('','',6);
          $this->MultiCell(40,2,$this->cabecera_factura['direccion_sucursal'],0,'C',0,1,'15','');
          $this->SetX(29);
          $this->Cell(10,2,'TELF: '.$this->cabecera_factura['telefono_sucursal'],0,1,'C');
          $this->SetX(29);
          $this->Cell(10,2,$this->cabecera_factura['lugar_sucursal'],0,1,'C');

          $x=40;
          $y=39;

        // }

        //var_dump("aqui llega total",strlen($this->cabecera_factura['direccion_sucursal']));
        $this->SetFont('','B',18);
        $this->MultiCell(150,4,'FACTURA COMERCIAL DE EXPORTACIÓN',0,'C',0,1,$x,'');

        $this->SetFont('','B',15);
        $this->MultiCell(130,4,'SIN DERECHO A CRÉDITO FISCAL',0,'C',0,1,($x+13),'');

        $y_final = $this->getY();

        $this->recuperarY($y_final);

    }

    function recuperarY($dato) {
        $this->y_cabecera = $dato;
    }


    function setDatos($datos, $cabecera_factura, $datosDetalleFactura, $totalesFactura) {

        $this->datos = $datos;
        $this->cabecera_factura = $cabecera_factura;
        $this->detalle = $datosDetalleFactura;
        $this->totales = $totalesFactura;

    }

    function  generarReporte()
    {
      //$this->cabecera_factura

      $this->AddPage();
      //$this->AliasNbPages();
      $this->SetAutoPageBreak(true,2);
      $this->SetMargins(11,2,5);
      //$pdf-> AddFont('Arial','','arial.php');

      $this->setY($this->y_cabecera);

      $this->ln(6);

      $y_linea = $this->getY();

      $this->ln(2);

      $this->SetFont('','B',16);

      $textypos = 5;
      // $this->setY(30);
      // $this->setX(15);

      $detail_pos = 52;

      $size_font = 9;
  		$this->SetFont('','B',$size_font+1);
  		//$this->setY($detail_pos);
      $this->setX(18);
      $this->Cell(24,$textypos,"Lugar y Fecha:"/*,'LT'*/);

      $this->SetFont('','',$size_font+1);
      $this->setX(93);
      $this->Cell(15,$textypos,$this->cabecera_factura['lugar'].', '.$this->cabecera_factura['fecha_literal'],0,1/*,'T'*/);

      $this->SetFont('','B',$size_font+1);
      $this->setX(18);
      $this->Cell(5,$textypos,"Nombre:"/*,'L'*/);

      $this->SetFont('','',$size_font+1);
      $this->setX(93);
    	$this->Cell(15,$textypos,$this->cabecera_factura['nombre_factura'],0,1);

      $this->SetFont('','B',$size_font+1);
      $this->setX(18);
      $this->MultiCell(50,$textypos,"Dirección del Importador: ",0,'L',0,0,'','');

      $this->SetFont('','',$size_font+1);
      $this->setX(93);
    //$this->Cell(15,$textypos,'2nd Floor, Block 5, Irish Life Centre, Abbey Street Lower,Dublin 1, Ireland,'/*,'T'*/);
      $this->MultiCell(80,$textypos,$this->cabecera_factura['direccion_cliente'],0,'L',0,1,'','');

      $this->SetFont('','B',$size_font+1);
      $this->setX(18);
      $this->Cell(25,$textypos,"NIT: "/*,'LB'*/);

      $this->SetFont('','',$size_font+1);
      $this->setX(93);
      $this->Cell(15,$textypos,$this->cabecera_factura['nit'],0,1);


      $this->SetFont('','B',$size_font+1);
      $this->setX(18);
      $this->MultiCell(60,$textypos,"INCOTERM y Puerto Destino: ",0,'L',0,0,'','');

      $this->SetFont('','',$size_font+1);
      $this->setX(93);
      $this->Cell(15,$textypos,$this->cabecera_factura['incoterm'],0,1/*,'T'*/);

      $this->SetFont('','B',$size_font+1);
      $this->setX(18);
      $this->MultiCell(70,$textypos,"Moneda de la Transacción Comercial: ",0,'L',0,0,'','');

      $this->SetFont('','',$size_font+1);
      $this->setX(93);
      $this->Cell(15,$textypos,$this->cabecera_factura['moneda_venta']/*,'T'*/);

      $this->SetFont('','B',$size_font+1);
      $this->setX(135);
      $this->Cell(15,$textypos,'Tipo de Cambio:'/*,'T'*/);

      if ($this->cabecera_factura['moneda_venta'] == 'Dólares Americanos') {
        $this->SetFont('','',$size_font+1);
        $this->setX(165);
        $this->Cell(15,$textypos,'1'.$this->totales['codigo_moneda_extranjera'].': '.$this->cabecera_factura['tipo_cambio_venta'].$this->totales['codigo_moneda_local'],0,1/*,'T'*/);

      } else {
        $this->SetFont('','',$size_font+1);
        $this->setX(165);
        $this->Cell(15,$textypos,'1'.$this->totales['codigo_moneda_local'].': '.$this->cabecera_factura['tipo_cambio_venta'].$this->totales['codigo_moneda_local'],0,1/*,'T'*/);

      }


      $this->ln();

      $y_linea_final = $this->getY();

      $this->Line(16,$y_linea,193,$y_linea);
      $this->Line(16,$y_linea_final,193,$y_linea_final);
      $this->Line(16,$y_linea,16,$y_linea_final);
      $this->Line(193,$y_linea,193,$y_linea_final);

      $this->Ln(4);
  		$this->setX(16);
  		$this->SetFont('','B',8);

      /////////////////////////////
  		//// Array de Cabecera
  		$header = array("ITEM", "NANDINA","DESCRIPCIÓN","CANTIDAD","UNIDAD DE MEDIDA","PRECIO UNITARIO","SUBTOTAL");
  	    // Column widths
  	    $w = array(10, 20, 70, 17, 20, 20, 20);

        for($i=0;$i<count($header);$i++){
  	    	$this->SetFillColor(230 , 230, 230);
          $this->MultiCell($w[$i],8,$header[$i],'LRBT','C',1,0,'','');
  		}

      $this->Ln();
  		$this->setX(16);
  		$this->SetFont('','',7);

      // Data
	    $total = 0;
	    $size_conceptos = sizeof($this->detalle);

      $item = 1;

      //var_dump("aqui llega el detalle",$size_conceptos);

      for ($i=0;$i<$size_conceptos;$i++){
        $this->setX(16);
        $this->Cell($w[0],8,$item,'LRB',0,'C');
        $this->Cell($w[1],8,$this->detalle[$i]['nandina'],'LRB',0,'C');
        $this->Cell($w[2],8,$this->detalle[$i]['descripcion_reporte'],'RB',0,'L');
        $this->Cell($w[3],8,$this->detalle[$i]['cantidad'],'LRB',0,'C');
        $this->Cell($w[4],8,$this->detalle[$i]['unidad_medida'],'RB',0,'C');
        $this->Cell($w[5],8,number_format($this->detalle[$i]['precio'], 2),'RB',0,'C');
        $this->Cell($w[6],8,number_format($this->detalle[$i]['subtotal'], 2),'RB',1,'C');

        $item ++;


  	    }

        //var_dump("aqui llega los totales",$this->totales);




      $this->setX(16);
      $this->SetFillColor(230 , 230, 230);
      $this->Cell(115,6,'','LTB',0,'L',1);
      $this->SetFont('','B',9);
      $this->Cell(37,6,'TOTAL '.$this->totales['codigo_moneda_extranjera'].'.','TBR',0,'R',1);
      $this->Cell(25,6,number_format($this->totales['total_extranjera'],2),'TBR',1,'R');

      $this->setX(16);
      $this->SetFillColor(230 , 230, 230);
      $this->Cell(115,6,'','LTB',0,'L',1);
      $this->SetFont('','B',9);
      $this->Cell(37,6,'TOTAL '.$this->totales['codigo_moneda_local'].'.','TBR',0,'R',1);
      $this->Cell(25,6,number_format($this->totales['total_local'],2),'TBR',1,'R');

      $this->SetFont('','B',8);
      $this->setX(16);
      $this->Cell(177,6,'Son:','LB',0,'L');
      $this->SetFont('','',8);
      $this->setX(23);
      $this->Cell(170,6,$this->totales['total_literal_extranjera'].' '.$this->totales['moneda_extranjera'].'.','R',1,'L');

      $this->SetFont('','B',8);
      $this->setX(16);
      $this->Cell(177,6,'Son:','LB',0,'L');
      $this->SetFont('','',8);
      $this->setX(23);
      $this->Cell(170,6,$this->totales['total_literal_local'].' '.$this->totales['moneda_local'].'.','R',0,'L');


      //var_dump("aqui llega el dato ",$this->cabecera_factura);
      $this->SetFont('','',8);
  		$this->Ln(8);
  		$this->setX(16);
  		$this->Cell(70,6,'PRECIO VALOR BRUTO','',0,'L');
  		$this->Cell(30,6,number_format($this->cabecera_factura['valor_bruto'],2).' '.$this->cabecera_factura['codigo'],'',1,'R');

  		$this->setX(16);
  		$this->Cell(70,6,'GASTOS DE TRANSPORTE HASTA FRONTERA','',0,'L');
  		$this->Cell(30,6,number_format($this->cabecera_factura['transporte_fob'],2).' '.$this->cabecera_factura['codigo'],'',1,'R');

      $this->setX(16);
  		$this->Cell(70,6,'GASTOS DE SEGURO HASTA FRONTERA','',0,'L');
  		$this->Cell(30,6,number_format($this->cabecera_factura['seguros_fob'],2).' '.$this->cabecera_factura['codigo'],'',1,'R');

      $this->setX(16);
  		$this->Cell(70,6,'OTROS','',0,'L');
  		$this->Cell(30,6,number_format($this->cabecera_factura['otros_fob'],2).' '.$this->cabecera_factura['codigo'],'B',1,'R');


      $this->setX(16);
      $this->SetFont('','B',8);
      $this->Cell(70,6,'TOTAL F.O.B. - FRONTERA','',0,'L');
      $this->Cell(30,6,number_format($this->cabecera_factura['totales_fob'],2).' '.$this->cabecera_factura['codigo'],'',1,'R');

      $this->SetFont('','',8);
      $this->setX(16);
      $this->Cell(70,6,'TRANSPORTE INTERNACIONAL','',0,'L');
      $this->Cell(30,6,number_format($this->cabecera_factura['transporte_cif'],2).' '.$this->cabecera_factura['codigo'],'',1,'R');

      $this->setX(16);
      $this->Cell(70,6,'SEGURO INTERNACIONAL','',0,'L');
      $this->Cell(30,6,number_format($this->cabecera_factura['seguros_cif'],2).' '.$this->cabecera_factura['codigo'],'',1,'R');

      $this->setX(16);
      $this->Cell(70,6,'OTROS','',0,'L');
      $this->Cell(30,6,number_format($this->cabecera_factura['otros_cif'],2).' '.$this->cabecera_factura['codigo'],'B',0,'R');

      $this->setX(138);
      $this->Cell(55,6,'Código de Control:            '.$this->cabecera_factura['cod_control'],'LBTR',1,'L');
      $this->SetFont('','B',8);
      $this->setX(16);
      $this->Cell(70,6,'TOTAL '.$this->cabecera_factura['incoterm'],'',0,'L');
      $this->Cell(30,6,number_format(($this->cabecera_factura['totales_fob']+$this->cabecera_factura['totales_cif']),2).' '.$this->cabecera_factura['codigo'],'',0,'R');

      $this->SetFont('','',8);
      $this->setX(138);
      $this->Cell(55,6,'Fecha Límite de Emisión: '.$this->cabecera_factura['fecha_limite_dosificacion'],'LBTR',1,'L');


  		$this->Ln(1);
      $this->setX(16);
      $this->SetFont('','B',8);
      $this->Cell(55,6,'"'.$this->cabecera_factura['glosa_impuestos'].'"','',1,'L');


      $this->Ln(1);
      $this->setX(47);
      $this->SetFont('','',8);
      $this->Cell(55,6,'"'.$this->cabecera_factura['glosa_empresa'].'"','',1,'L');



        //
        // //codigo qr
    		// $fecha_cambios = mktime(0, 0, 0, 12, 4, 2015);
    		// $fecha_qr = mktime(0, 0, 0, 1, 1, 2016);
    		// $fecha_factura = mktime(0, 0, 0, substr(19,5,2), substr(5,8,2) , substr(2020,0,4));
    		// $importe_sujeto = number_format($this->datos[0]['sujeto_credito'],2,'.','');
        //
        // if ($this->datos[0]['fecha_venta'] >= '01/01/2016') {
        // 	$this->Image($this->generarImagen($this->datos[0]['nit'],$this->datos[0]['numero_factura'],$this->datos[0]['autorizacion'],$this->datos[0]['fecha_venta'],$this->datos[0]['total_venta'],$this->datos[0]['sujeto_credito'],$this->datos[0]['codigo_control'],$this->datos[0]['nit_cliente'],$this->datos[0]['excento']),164,$this->getY()+7,20,20);
        // }

      $this->ln(3);
  		$this->SetFont('','',8);
  		$this->setX(94);
  		$this->Cell(20,3,'Cajero: '.$this->cabecera_factura['cuenta_cajero'].' Hora: '.$this->cabecera_factura['hora_estimada_entrega'],0,1,'C');
      $this->setX(94);
      $this->Cell(20,3,'Id: '.$this->cabecera_factura['id_venta'],0,1,'C');

      $this->setX(94);
  		$this->Cell(20,1,'--------------------------------',0,1,'C');

  		$this->setX(94);
  		$this->Cell(20,4,'www.boa.bo',0,1,'C');

      $this->setX(94);
  		$this->Cell(20,4,$this->cabecera_factura['leyenda'],0,0,'C');


    }

    function Footer(){

	}  //Fin Footer

    function generarImagen($nitEmpresa,$nroFactura,$nroAutorizacion,$fechaFactura,$montoTotal,$montoFiscal,$codigoControl,$nitCliente,$valorExcento){
        $cadena_qr = $nitEmpresa.'|'.$nroFactura.'|'.$nroAutorizacion.'|'.$fechaFactura.'|'.$montoTotal.'|'.$montoFiscal.'|'.$codigoControl.'|'.$nitCliente.'|0.00|0.00|'.$valorExcento.'|0.00';
        $barcodeobj = new TCPDF2DBarcode($cadena_qr, 'QRCODE,M');
        $nombreFactura = 'FactA4'.$nroFactura;
        $png = $barcodeobj->getBarcodePngData($w = 6, $h = 6, $color = array(0, 0, 0));
        $im = imagecreatefromstring($png);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im, dirname(__FILE__) . "/../../reportes_generados/" . $nombreFactura . ".png");
            imagedestroy($im);

        } else {
            echo 'A ocurrido un Error.';
        }
        $url_archivo = dirname(__FILE__) . "/../../reportes_generados/" . $nombreFactura . ".png";

        return $url_archivo;
    }

}
?>
