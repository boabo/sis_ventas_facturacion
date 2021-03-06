<?php
// Extend the TCPDF class to create custom MultiRow
class RResumenVentasRtsPDF extends  ReportePDF {
    var $datos_titulo;
    var $datos_detalle;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    var $categoria;
    var $modalidad;
    var $lugar;

    var $bandera_header;

    function setDatos($datos) {
        $this->datos = $datos;
    }

    function Header() {

          $fecha_actual = date("d/m/y");

          $hora_actual = date('H:i:s');


          if( $this->objParam->getParametro('filtro_sql') == 'periodo' ){
              $sufijo = ($this->datos[0]['periodo_num']<10?'0'.$this->datos[0]['periodo_num']:$this->datos[0]['periodo_num']).$this->datos[0]['gestion'];
          }else{
              $sufijo=$this->objParam->getParametro('fecha_ini').'_'.$this->objParam->getParametro('fecha_fin');
          }

          $NIT = 	$this->datos[0]['nit_empresa'];
        //$this->SetMargins(3, 40, 2);
        $this->Image(dirname(__FILE__) . '/../../lib/imagenes/logos/logo.jpg', 16, 5, 30, 25);
        $this->SetFont('', '', 10);
        //$this->MultiCell(200, 0, 'RESUMEN - VENTA DE BIENES Y/O SERVICIOS A SUJETOS PASIVOS',0,'C',0,1,'60','');
        $this->Cell(0, 5, 'RESUMEN - VENTA DE BIENES Y/O SERVICIOS A SUJETOS PASIVOS', 0, 1, 'C');
        $this->Cell(0, 5, 'DEL REGIMEN TRIBUTARIO SIMPLIFICADO A PERSONAS NO INSCRITAS', 0, 1, 'C');
        $this->Cell(0, 5, 'EN EL PADRON NACIONAL DE CONTRIBUYENTES', 0, 1, 'C');


        $this->SetFont('', '', 10);

        $this->Cell(0, 5, 'GESTIÓN: '.$this->datos[0]['gestion'].'          DESDE MES: '.$this->datos[0]['mes_inicio'].'   HASTA MES: '.$this->datos[0]['mes_final'], 0, 1, 'C');
        $this->Cell(0, 5, 'REGIMEN TRIBUTARIO SIMPLIFICADO', 0, 1, 'C');


        // if ($this->objParam->getParametro('filtro_sql') == 'periodo') {
        //   $this->Cell(0, 5, 'GESTIÓN: '.$this->datos[0]['gestion'].'       MES: '.$this->datos[0]['mes_envio'], 0, 1, 'C');
        //   //$this->Cell(0, 5, 'MES DE ENVIO AL SIN: '.$this->datos[0]['periodo_literal'], 0, 1, 'C');
        // } else {
        //   $this->Cell(0, 5, 'GESTIÓN: '.$this->datos[0]['gestion'].'          DESDE MES: '.$this->objParam->getParametro('fecha_ini').'   HASTA MES: '.$this->objParam->getParametro('fecha_fin'), 0, 1, 'C');
        //   //$this->Cell(0, 5, 'ENVIO AL SIN DESDE: '.$this->objParam->getParametro('fecha_ini').' HASTA: '.$this->objParam->getParametro('fecha_fin'), 0, 1, 'C');
        // }


        $this->SetFont('', '', 10);

        //
        // $this->MultiCell(220, 0, '(DEPTO. DE FINANZAS)             REGIMEN TRIBUTARIO PERSONAS NATURALES           FECHA: '.$fecha_actual.'   HORA: '.$hora_actual,0,'L',0,0,'60','');

        // $this->Line(15,32,270,32);
        $this->Line(15,38,285,38);
        // $this->Line(15,38,15,32);


        $this->SetFont('', 'B', 9);
        //$this->Cell(0, 3, "", 0, 1, 'C');


        $this->SetFont('', 'B', 7);
        $count = 0;

        $cabecera = array(10,30,30,30,19,27,45,35,15,15,15);
           //$array = array(15,16,14,55,30,40,20,28,25);
        $this->ln(1);
        //$this->Cell($cabecera[0],3,'Nro','',0,'R');
        $this->MultiCell($cabecera[0], 6, 'Nro.',0,'R',0,0,'80','');
        $this->MultiCell($cabecera[1], 6, 'Nro. de doc. de Identificación',0,'R',0,0,'100','');
        $this->MultiCell($cabecera[2], 6, 'Importe Acumulado Bs.',0,'R',0,0,'150','');
        $this->MultiCell($cabecera[3], 6, 'Mes de Envio al SIN.',0,'R',0,0,'200','');
        // $this->Cell($cabecera[1],3,'NRO DE BOL. O FACT.','',0,'R');
        // $this->Cell($cabecera[2],3,'CODIGO CLIENTE','',0,'R');
        // $this->Cell($cabecera[3],3,'TIPO DE DOC.','',0,'R');
        // $this->Cell($cabecera[4],3,'NRO DE DOC.','',0,'R');
        // $this->Cell($cabecera[5],3,'CLIENTE','',0,'R');
        // $this->Cell($cabecera[6],3,'CÓD. DEL PRODUCTO','',0,'R');
        // $this->Cell($cabecera[7],3,'DESC. DEL PRODUCTO','',0,'R');
        // $this->Cell($cabecera[8],3,'CANTIDAD','',0,'R');
        // $this->Cell($cabecera[9],3,'P/U.','',0,'R');
        // $this->Cell($cabecera[10],3,'TOTAL.','',0,'R');
        // $this->Cell($cabecera[9],3,'CANT. DEL PRODUCTO','',0,'C');
        // $this->Cell($cabecera[10],3,'P/U','',0,'C');
        $this->Ln();
    }

    function generarReporte(){


        $this->AddPage();
        $this->SetMargins(15, 40, 2);
        $this->Ln(10);

        $this->SetFont('', '', 7);
        $array = array(75,40,50,50,19,27,45,35,15,15,15);

        $this->SetFont('', '', 7);

        /*Aqui almacenamos el Nit Cliente para separarlos*/
        $nit_cliente=array();
        $totales = 0;

        $numero = 1;
        $this->Ln(4);



        foreach ($this->datos as $key => $value2) {

                    // $this->MultiCell($array[0], 4, $numero,0,'R',0,0,'80','');
                    // $this->MultiCell($array[1], 4, $value2['nit'],0,'R',0,0,'100','');
                    // $this->MultiCell($array[2], 4, $value2['total_acumulado'],0,'R',0,0,'150','');
                    // $this->MultiCell($array[3], 4, $value2['mes_envio'],0,'R',0,0,'200','');

                    $this->Cell($array[0],3,$numero,'',0,'R');
                    $this->Cell($array[1],3,$value2['nit'],'',0,'R');
                    $this->Cell($array[2],3,number_format($value2['total_acumulado'], 2, ',', '.'),'',0,'R');
                    $this->Cell($array[3],3,$value2['mes_envio'],'',0,'R');


                    $this->Ln();


                    $numero ++;
                    //
                    // $this->Cell($array[0],6,date("d/m/Y", strtotime($value2['fecha_factura'])),'',0,'R');
                    // $this->Cell($array[1],6,$value2['nro_factura'],'',0,'R');
                    // $this->Cell($array[2],6,$value2['nit'],'',0,'R');
                    // $this->Cell($array[3],6,$value2['carnet_ide'],'',0,'R');
                    // $this->Cell($array[4],6,$value2['nit'],'',0,'R');
                    // $this->MultiCell($array[5], 6, $value2['razon_social'],0,'C',0,0,'130','');
                    // $this->MultiCell($array[6], 6, $value2['sistema_origen'],0,'C',0,0,'170','');
                    // $this->MultiCell($array[7], 6, $value2['desc_ruta'],0,'J',0,0,'210','');
                    // $this->Cell($array[8],6,$value2['cantidad'],'',0,'R');
                    // $this->Cell($array[9],6,$value2['precio_unitario'],'',0,'R');
                    // $this->Cell($array[10],6,$value2['precio_total'],'',0,'R');
            }





      //   foreach($this->datos as $value){
      //           $valor=$value['nit'];
      //         if(!in_array($valor, $nit_cliente)){
      //            $nit_cliente[]=$valor;
      //         }
      //   }
      //   /************************************************/
      //
      //
      //
      //   foreach($nit_cliente as $value ){
      //     $this->SetFont('', 'B', 8);
      //     $this->Cell($array[0],3,'NIT: '.$value,'',1,'C');
      //     $this->SetFont('', '', 7);
      //     foreach ($this->datos as $key => $value2) {
      //       if ($value2['nit'] == $value) {
      //             $this->Cell($array[0],6,date("d/m/Y", strtotime($value2['fecha_factura'])),'',0,'R');
      //             $this->Cell($array[1],6,$value2['nro_factura'],'',0,'R');
      //             $this->Cell($array[2],6,$value2['nit'],'',0,'R');
      //             $this->Cell($array[3],6,$value2['carnet_ide'],'',0,'R');
      //             $this->Cell($array[4],6,$value2['nit'],'',0,'R');
      //             $this->MultiCell($array[5], 6, $value2['razon_social'],0,'C',0,0,'130','');
      //             $this->MultiCell($array[6], 6, $value2['sistema_origen'],0,'C',0,0,'170','');
      //             $this->MultiCell($array[7], 6, $value2['desc_ruta'],0,'J',0,0,'210','');
      //             $this->Cell($array[8],6,$value2['cantidad'],'',0,'R');
      //             $this->Cell($array[9],6,$value2['precio_unitario'],'',0,'R');
      //             $this->Cell($array[10],6,$value2['precio_total'],'',0,'R');
      //
      //             //$this->Cell($array[5],3,$value2['razon_social'],'',0,'R');
      //             // $this->Cell($array[6],3,$value2['cantidad'],'',0,'R');
      //             // $this->Cell($array[7],3,$value2['precio_unitario'],'',0,'R');
      //             // $this->Cell($array[8],3,number_format($value2['precio_total'], 2, ',', '.'),'',0,'R');
      //             $this->Ln();
      //             $totales += $value2['precio_total'];
      //        }
      //
      //     }
      //
      //     $this->SetFont('', 'B', 7);
      //     $this->SetTextColor(0, 66, 66, 0);
      //     $this->Cell('250',3,'TOTALES','B',0,'R');
      //     $this->SetTextColor(100, 35, 0, 31);
      //     $this->Cell('17',3,number_format($totales, 2, ',', '.'),'B',0,'R');
      //     $this->SetTextColor();
      //     $totales = 0;
      //     $this->Ln();
      // }

    }
  //   function Footer(){
  //
	// }
}
?>
