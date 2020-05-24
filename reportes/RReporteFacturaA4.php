<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RReporteFacturaA4 extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    function Header() {

        $this->SetMargins(11,60,5);
        $this->SetFont('','B',14);
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/LOGOBN.jpg',22,7,40,20);
        $this->SetXY(55,15);
		    $y_cab=$this->GetY();

        $this->SetFont('','B',18);
        $this->MultiCell(90,45,'FACTURA',0,'C',0,1,'50','35');
        $x=$this->GetX();

        //detalle
    		$this->SetFont('','B',9);
    		$this->SetXY($x+115,$y_cab);
    		$this->Cell(31,5,' NIT: ','TL',0,'L');
    		$this->SetXY($x+146,$y_cab);
    		$this->Cell(30,5,'154422029','TR',0,'L');

        $this->SetXY($x+115,$y_cab+3.7);
      	$this->Cell(31,5,' N° FACTURA: ','L',0,'L');
      	$this->SetXY($x+146,$y_cab+3.7);
      	$this->Cell(30,5,$this->datos[0]['numero_factura'],'R',0,'L');

        $this->SetXY($x+115,$y_cab+7.4);
    		$this->Cell(31,5,' N° AUTORIZACIÓN: ','BL',0,'L');
    		$this->SetXY($x+146,$y_cab+7.4);
    		$this->Cell(30,5,$this->datos[0]['autorizacion'],'BR',0,'L');

        $this->SetFont('','B',14);
    		$this->SetXY($x+115, $y_cab+13);
    		$this->Cell(60,5,'ORIGINAL',0,0,'C');

    		$this->SetFont('','B',5);
    		$this->SetXY($x+115, $y_cab+17);
    		$this->Cell(60,5,$this->datos[0]['actividades'],0,0,'C');
    		//LEFT

        $this->SetX(13);
    		$x=$this->GetX();
    		$this->SetFont('','B',6);
    		$this->SetXY($x, $y_cab+12);
    		$_address_box = 60;//60

        if ($this->datos[0]['sucursal'] == 0){
			$this->Cell($_address_box,2,'CASA MATRIZ',0,1,'C');
			//$this->SetXY($x, $y_cab+13);
  		}else{
  			$this->Cell($_address_box,2,'CASA MATRIZ',0,0,'C');
  			$this->SetFont('','',6);
  			$this->SetXY($x, $y_cab+14);
  			$this->Cell($_address_box,2,'AV. SIMON LOPEZ NRO 1582 ZONA CONDEBAMBA',0,1,'C');
  			$this->Cell($_address_box,2,'TELF: 4150000-72201369',0,1,'C');
  			$this->Cell($_address_box,2,'COCHABAMBA - BOLIVIA',0,1,'C');

  			$this->SetFont('','B',6);
  			$this->SetX($x);
  			$this->Cell($_address_box,2,'SUCURSAL '.$this->datos[0]['sucursal'],0,1,'C');
  			$this->SetFont('','',6);
  			$this->SetX($x);
  			$this->Cell($_address_box,2,$this->datos[0]['desc_sucursal'],0,1,'C');
  		}

      $this->SetFont('','',6);
    	//buscar espacio blanco o , o :
    	$position = array(37,38,39,40,41);
    	$address = $this->datos[0]['direccion_sucursal'];
    	$size_addr = strlen($address);
    	$index = -1;

      if ($size_addr > 36 ){

			for($i=0 ; $i<sizeof($position) ; $i++){
				if($size_addr == $position[$i]){
					break;
				}
				if($address[$position[$i]] == ' ' || $address[$position[$i]] == ';' || $address[$position[$i]] == ',' || $address[$position[$i]] == ':'){
					$index = $position[$i];
					break;
				}
			}	//var_dump($index);;exit;
			$this->SetX($x);
			if($index > 0){
				$direccion = str_split($address, $index);
				$this->Cell($_address_box,2,$direccion[0],0,1,'C');
			}else{
				if ($this->datos[0]['sucursal'] == 0){//var_dump($address);exit;
					//$this->SetXY($x, $y_cab+13);
					$this->Cell($_address_box,2,$address,0,0,'C');
				}else{
					$this->Cell($_address_box,2,$address,0,1,'C');
				}
			}

			if($direccion[1]!='' || $direccion[1] != null){
				$this->SetX($x);
				$this->Cell($_address_box,2,$direccion[1],0,1,'C');
				$this->SetX($x);
				$this->Cell($_address_box,2,$this->datos[0]['telefono_sucursal'],0,1,'C');
				$this->SetX($x);
				$this->Cell($_address_box,2,$this->datos[0]['lugar_sucursal'],0,0,'C');
			}else{
				if ($this->datos[0]['sucursal'] == 0){
					$this->SetXY($x, $y_cab+15);
					$this->Cell($_address_box,5,'TELF: '.$this->datos[0]['telefono_sucursal'],0,0,'C');
					$this->SetXY($x, $y_cab+17);
					$this->Cell($_address_box,5,$this->datos[0]['lugar_sucursal'],0,0,'C');
				}else{
					$this->SetX($x);
					$this->Cell($_address_box,2,'TELF: '.$this->datos[0]['telefono_sucursal'],0,1,'C');
					$this->SetX($x);
					$this->Cell($_address_box,2,$this->datos[0]['lugar_sucursal'],0,0,'C');
				}
			}
		}else{
			$this->SetX($x);
			$this->Cell(60,5,$this->datos[0]['direccion_sucursal'],0,0,'C');
			$this->SetXY($x, $y_cab+15);
			$this->Cell(60,5,'TELF: '.$this->datos[0]['telefono_sucursal'],0,0,'C');
			$this->SetXY($x, $y_cab+17);
			$this->Cell(60,5,$this->datos[0]['lugar_sucursal'],0,0,'C');
		}


    }

    function setDatos($datos,$detalle) {

        $this->datos = $datos;
        $this->detalle = $detalle;
        //var_dump( $this->datos);
    }

    function  generarReporte()
    {

      $this->AddPage();
      //$this->AliasNbPages();
      $this->SetAutoPageBreak(true,2);
      $this->SetMargins(11,50,5);
      //$pdf-> AddFont('Arial','','arial.php');
      $this->SetFont('','B',16);

      $textypos = 5;
      $this->setY(30);
      $this->setX(15);

      //$fecha_factura_formateada= substr( $_SESSION['PDF_cabecera_factura'][0][11],8,2)."/".substr( $_SESSION['PDF_cabecera_factura'][0][11],5,2)."/".substr( $_SESSION['PDF_cabecera_factura'][0][11],0,4);

  		if ($this->datos[0]['sucursal'] == 0){
  			$detail_pos = 50;
  		}else{
  			$detail_pos = 55;
  		}

      $size_font = 9;
  		$this->SetFont('','B',$size_font+1);
  		$this->setY($detail_pos);$this->setX(25);
  		$this->Cell(24,$textypos,"Lugar y Fecha:"/*,'LT'*/);

  		$this->setY($detail_pos);$this->setX(125);
  		$this->Cell(15,$textypos,'NIT/CI:'/*,'T'*/);

  		$this->setY($detail_pos+5);$this->setX(25);
  		$this->Cell(5,$textypos,"Señor(es):"/*,'L'*/);

  		$this->setY($detail_pos+10);$this->setX(25);
  		$this->MultiCell(25,$textypos,"Obs.: "/*,'LB'*/);

  		//lugar
      $lugar = ucwords (strtolower ($this->datos[0]['desc_lugar']));

      $this->SetFont('','',$size_font+1);
  		$this->setY($detail_pos);$this->setX(50);
  		$this->Cell(75,$textypos,' '.$lugar.', '.$this->datos[0]['fecha_literal']/*,'T'*/);

  		$this->setY($detail_pos);$this->setX(140);
  		$this->Cell(45,$textypos,$this->datos[0]['nit_cliente']/*,'TR'*/);

      $razon_cliente = wordwrap($this->datos[0]['cliente'], 32, "<br />\n");

  		$this->setY($detail_pos+5);$this->setX(50);
  		//$this->Cell(5,$textypos,utf8_decode($_SESSION['PDF_razon_cliente']));
  		$this->Cell(135,$textypos,' '.$this->datos[0]['cliente']/*,'R'*/);
  		$this->setY($detail_pos+10);$this->setX(50);

      $size_obs = strlen($this->datos[0]['observaciones']);
      //var_dump("el tamaño para este dato es",$size_obs);
  		if ($this->datos[0]['sucursal'] == 0){
  			if($size_obs>70){
  				if ($size_obs>175){
  					$this->SetFont('','',$size_font);
  					$this->MultiCell(135,$textypos,' '.$this->datos[0]['observaciones'],'','L');
  					if ($size_obs>175 && $size_obs<190){
  						$this->Line(22,43,187,43);
  						$this->Line(22,75,187,75);
  						$this->Line(22,75,22,43);
  						$this->Line(187,75,187,43);
  					}else{
  						$this->Line(22,43,187,43);
  						$this->Line(22,88,187,88);
  						$this->Line(22,88,22,43);
  						$this->Line(187,88,187,43);
  					}


  				}else{
  					$this->Line(22,43,187,43);
  					$this->Line(22,70,187,70);
  					$this->Line(22,70,22,43);
  					$this->Line(187,70,187,43);
  					$this->MultiCell(135,$textypos,' '.$this->datos[0]['observaciones'],'','L');
  				}

  			}else{
  				$this->Line(22,45,187,45);
  				$this->Line(22,68,187,68);
  				$this->Line(22,45,22,68);
  				$this->Line(187,45,187,68);
  				$this->Cell(135,$textypos,' '.$this->datos[0]['observaciones']/*,'TR'*/);
  			}
  		}else{
  			if($size_obs>70){
  				$this->Line(22,53,187,53);
  				$this->Line(22,85,187,85);
  				$this->Line(22,85,22,53);
  				$this->Line(187,85,187,53);
  				if ($size_obs>175){
  					$this->SetFont('','',$size_font);
  					$this->MultiCell(135,$textypos,' '.$this->datos[0]['observaciones'],'','L');
  				}else{
  					$this->MultiCell(135,$textypos,' '.$this->datos[0]['observaciones'],'','L');
  				}
  			}else{
  				$this->Line(22,53,187,53);
  				$this->Line(22,72,187,72);
  				$this->Line(22,72,22,53);
  				$this->Line(187,72,187,53);
  				$this->Cell(135,$textypos,' '.$this->datos[0]['observaciones']/*,'TR'*/);
  			}
  		}
      $this->Ln(10);
  		$this->setX(22);
  		$this->SetFont('','B',10);

      /////////////////////////////
  		//// Array de Cabecera
  		$header = array("CANT", "CONCEPTO","PRECIO U.","SUBTOTAL");
  	    // Column widths
  	    $w = array(15, 100, 25, 25);

        for($i=0;$i<count($header);$i++){
  	    	$this->SetFillColor(230 , 230, 230);
  	        $this->Cell($w[$i],7,$header[$i],1,0,'C','1');
  		}

      $this->Ln();
  		$this->setX(22);
  		$this->SetFont('','',9);

      // Data
	    $total = 0;
	    $size_conceptos = sizeof($this->detalle);
      //var_dump("los datos son",$this->datos);
      for ($i=0;$i<$size_conceptos;$i++){
        //var_dump("aqui llega dato",$this->detalle[$i]);
	    	//$detalle_conceptos_format = wordwrap($_SESSION['PDF_detalle_conceptos'][$i][1], 36, "<br />\n");
	    	$detalle_conceptos_format = $this->detalle[$i]['concepto'];
  			if ($i + 1 == $size_conceptos) {
  				$this->setX(22);
  				$this->Cell($w[0],6,'','RL',0,'C');
  				$this->Cell($w[1],6,'','R',0,'C');
  				$this->Cell($w[2],6,'','R',0,'C');
  				$this->Cell($w[3],6,'','R',1,'C');

  				$this->setX(22);

  		        $this->Cell($w[0],6,number_format($this->detalle[$i]['cantidad'], 2),'LR',0,'C');
  		        $this->Cell($w[1],6,'  '.$detalle_conceptos_format,'R',0,'L');
  		        $this->Cell($w[2],6,number_format($this->detalle[$i]['precio_unitario'],2,',','.'),'R',0,'R');
  		        $this->Cell($w[3],6,number_format($this->detalle[$i]['precio_total'],2,',','.'),'R',0,'R');

  				$this->setX(22);
  				$this->Cell($w[0],6,'','RL',0,'C');
  				$this->Cell($w[1],6,'','LR',0,'C');
  				$this->Cell($w[2],6,'','LR',0,'C');
  				$this->Cell($w[3],6,'','LR',1,'C');
  				$this->setX(22);
  				$this->Cell($w[0],6,'','RL',0,'C');
  				$this->Cell($w[1],6,'','LR',0,'C');
  				$this->Cell($w[2],6,'','LR',0,'C');
  				$this->Cell($w[3],6,'','LR',1,'C');

  				$this->setX(22);

  				// $total_general = '50';
  				// $monto_literal = '0';//$this->convertir((integer)$total_general);
  				// //$monto_literal = wordwrap($this->num2letras($_SESSION['PDF_cabecera_factura'][0][14]). "", 36, "<br />\n");
  				// $centimos = explode('.', $total_general);
  				// $centimos = $centimos[1]==''?'00':$centimos[1].'/100 Bolivianos.';

  				$this->setX(22);
  				$this->SetFillColor(230 , 230, 230);
  		        $this->Cell(115,6,'','LTB',0,'L',1);
  				$this->SetFont('','B',10);
  		        $this->Cell(25,6,'TOTAL Bs.','TBR',0,'R',1);
  		        $this->Cell(25,6,number_format($this->datos[0]['total_venta'],2,',','.'),'TBR',1,'R');

  				if($this->datos[0]['excento'] > 0 ){
  					$this->setX(22);
  					$this->SetFillColor(230 , 230, 230);
  			        $this->Cell(115,6,'','LTB',0,'L',1);
  					$this->SetFont('','B',10);
  			        $this->Cell(25,6,'EXENTO Bs.','TBR',0,'R',1);
  			        $this->Cell(25,6,number_format($this->datos[0]['excento'],2,',','.'),'TBR',1,'R');

  					$this->setX(22);
  					$this->SetFillColor(230 , 230, 230);
  			        $this->Cell(115,6,'','LTB',0,'L',1);
  					$this->SetFont('','B',10);
  			        $this->Cell(25,6,'VÁLIDO PARA CRÉDITO FISCAL Bs.','TBR',0,'R',1);
  			        $this->Cell(25,6,number_format($this->datos[0]['sujeto_credito'],2,',','.'),'TBR',1,'R');

  				 }

  				$this->SetFont('','',10);
  				$this->setX(22);
  		        $this->Cell(165,6,' Son: '.$this->datos[0]['total_venta_msuc_literal'].' '.$this->datos[0]['desc_moneda_venta'].'.','LBR',0,'L');

  			}else{
  				$this->setX(22);
  				$this->Cell($w[0],6,'','RL',0,'R');
  				$this->Cell($w[1],6,'','R',0,'R');
  				$this->Cell($w[2],6,'','R',0,'R');
  				$this->Cell($w[3],6,'','R',1,'R');

  				$this->setX(22);
  				    $this->Cell($w[0],6,number_format($this->detalle[$i]['cantidad'], 2),'LR',0,'C');
  		        $this->Cell($w[1],6,'  '.$detalle_conceptos_format,'R',0,'L');
  		        $this->Cell($w[2],6,number_format($this->detalle[$i]['precio_unitario'],2,',','.'),'R',0,'R');
  		        $this->Cell($w[3],6,number_format($this->detalle[$i]['precio_total'],2,',','.'),'R',1,'R');

  			}

  	    }

        //codigo qr
    		$fecha_cambios = mktime(0, 0, 0, 12, 4, 2015);
    		$fecha_qr = mktime(0, 0, 0, 1, 1, 2016);
    		$fecha_factura = mktime(0, 0, 0, substr(19,5,2), substr(5,8,2) , substr(2020,0,4));
    		$importe_sujeto = number_format($this->datos[0]['sujeto_credito'],2,'.','');

        //if($fecha_factura >= $fecha_qr){
        if ($this->datos[0]['fecha_venta'] >= '01/01/2016') {
        //var_dump("los datos son",$this->datos);
  			// $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
  			// $PNG_WEB_DIR = 'tmp/';
  			// $data='154422029|'.$_SESSION['PDF_cabecera_factura'][0][4].'|'.$_SESSION['PDF_cabecera_factura'][0][3].'|'.$fecha_factura_formateada.'|'.number_format($_SESSION['PDF_cabecera_factura'][0][14],2,'.','').'|'.$importe_sujeto.'|'.$_SESSION['PDF_cabecera_factura'][0][16].'|'.$_SESSION['PDF_nit'].'|0.00|0.00|'. $_SESSION['PDF_cabecera_factura'][0][34] .'|0.00';
  			// $nivelCorrecionError='H';
  			// $tamanio=3;
  			// $name_file = 'factA4'.md5($data.'|'.$nivelCorrecionError.'|'.$tamanio).'.jpg';
  			// $filename = $PNG_TEMP_DIR.$name_file;
  			//QRCode::png($data, $filename, $nivelCorrecionError,$tamanio);
  			$this->Image($this->generarImagen('154422029',$this->datos[0]['numero_factura'],$this->datos[0]['autorizacion'],$this->datos[0]['fecha_venta'],$this->datos[0]['total_venta'],$this->datos[0]['sujeto_credito'],$this->datos[0]['codigo_control'],$this->datos[0]['nit_cliente'],$this->datos[0]['excento']),164,$this->getY()+7,20,20);
        //$nitEmpresa,$nroFactura,$nroAutorizacion,$fechaFactura,$montoTotal,$montoFiscal,$codigoControl,$nitCliente,$valorExcento
  		}

      $this->SetFont('','',10);
  		$this->Ln(10);
  		$this->setX(22);
  		$this->Cell(50,6,' Código de Control:','LBT',0,'L');
  		$this->Cell(50,6,$this->datos[0]['codigo_control'],'BTR',0,'L');

  		$fecha_limite_formateada = $this->datos[0]['fecha_limite_emision'];
  		$this->Ln(10);
  		$this->setX(22);
  		$this->Cell(50,6,' Fecha Límite de Emisión:','LBT',0,'L');
  		$this->Cell(50,6,$fecha_limite_formateada,'BTR',0,'L');

  		$ley = $this->datos[0]['glosa_empresa'];//'Ley N° 453: "Los servicios deben suministrarse en condiciones de inocuidad, calidad y seguridad"';
  		$descripcion = '"'.$this->datos[0]['glosa_impuestos'].'"';//'"ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS. EL USO ILÍCITO DE ÉSTA SERÁ SANCIONADO DE ACUERDO A LEY"';

  		$this->Ln(10);

  		$this->SetFont('','B',7);
  		$this->setX(94);
  		$this->Cell(20,3,$descripcion,0,1,'C');

  		$this->SetFont('','',6.5);
  		$this->setX(94);
  		$this->Cell(20,3,$ley,0,1,'C');

  		$this->SetFont('','',6);
  		$this->setX(94);
  		$this->Cell(20,3,'Cajero:  '. $_SESSION["_LOGIN"] .'  Id:  '.$this->datos[0]['id'],0,1,'C');
  		//$this->Cell(10,6,$_SESSION['PDF_cabecera_factura'][0][17],0,0,'L');
  		//$this->Cell(10,6,'Id:',0,0,'L');
  		//$this->Cell(20,6,$_SESSION['PDF_id_factucom'],0,0,'L');

  		$this->setX(94);
  		$this->Cell(20,1,'--------------------------------',0,1,'C');

  		$this->SetFont('','B',6);
  		//$this->Ln();
  		$this->setX(94);
  		$this->Cell(20,4,$this->datos[0]['leyenda'],0,0,'C');
  		$this->Ln(3);
  		$this->setX(94);
  		$this->Cell(20,4,$this->datos[0]['pagina_entidad'],0,0,'C');


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
