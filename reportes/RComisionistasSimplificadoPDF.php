<?php
// Extend the TCPDF class to create custom MultiRow
class RComisionistasSimplificadoPDF extends  ReportePDF {
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
          if( $this->objParam->getParametro('filtro_sql') == 'periodo' ){
              $sufijo = ($this->datos[0]['periodo_num']<10?'0'.$this->datos[0]['periodo_num']:$this->datos[0]['periodo_num']).$this->datos[0]['gestion'];
          }else{
              $sufijo=$this->objParam->getParametro('fecha_ini').'_'.$this->objParam->getParametro('fecha_fin');
          }

          $NIT = 	$this->datos[0]['nit_empresa'];
        //$this->SetMargins(3, 40, 2);
        $this->Image(dirname(__FILE__) . '/../../lib/imagenes/logos/logo.jpg', 16, 5, 30, 25);
        $this->ln(7);
        $this->SetFont('', 'B', 10);
        $this->Cell(0, 5, "REGIMEN SIMPLIFICADO", 0, 1, 'C');
        $this->Cell(0, 5, "BOLIVIANA DE AVIACIÓN (BoA) NIT ".$NIT, 0, 1, 'C');
        $this->SetFont('', 'B', 10);


        if ($this->datos[0]['periodo_literal_inicio'] == $this->datos[0]['periodo_literal_fin']) {
          $this->Cell(0, 5, 'GESTIÓN: '.$this->datos[0]['gestion'].'       MES: '.$this->datos[0]['periodo_literal_inicio'], 0, 1, 'C');
          //$this->Cell(0, 5, 'MES DE ENVIO AL SIN: '.$this->datos[0]['periodo_literal'], 0, 1, 'C');
        } else {
          $this->Cell(0, 5, 'GESTIÓN: '.$this->datos[0]['gestion'].'          DESDE: '.$this->datos[0]['periodo_literal_inicio'].'   HASTA: '.$this->datos[0]['periodo_literal_fin'], 0, 1, 'C');
        }
        // if ($this->objParam->getParametro('filtro_sql') == 'periodo') {
        //   $this->Cell(0, 5, 'GESTIÓN: '.$this->datos[0]['gestion'].'       MES: '.$this->datos[0]['periodo_literal'], 0, 1, 'C');
        //   //$this->Cell(0, 5, 'MES DE ENVIO AL SIN: '.$this->datos[0]['periodo_literal'], 0, 1, 'C');
        // } else {
        //   $this->Cell(0, 5, 'GESTIÓN: '.$this->datos[0]['gestion'].'          DESDE: '.$this->objParam->getParametro('fecha_ini').'   HASTA: '.$this->objParam->getParametro('fecha_fin'), 0, 1, 'C');
        //   //$this->Cell(0, 5, 'ENVIO AL SIN DESDE: '.$this->objParam->getParametro('fecha_ini').' HASTA: '.$this->objParam->getParametro('fecha_fin'), 0, 1, 'C');
        // }



        $this->SetFont('', 'B', 7);
        $count = 0;

        $cabecera = array(15,16,20,35,45,25,25);
           //$array = array(15,16,14,55,30,40,20,28,25);
        $this->ln(6);
        $this->Cell($cabecera[0],3,'Cod. Cliente','',0,'R');
        $this->Cell($cabecera[1],3,'NIT. Cliente','',0,'R');
        $this->Cell($cabecera[2],3,'Cod. Producto','',0,'R');
        $this->Cell($cabecera[3],3,'Desc. Producto','',0,'R');
        $this->Cell($cabecera[4],3,'Cant. Producto','',0,'R');
        $this->Cell($cabecera[5],3,'P/U (Bs)','',0,'R');
        $this->Cell($cabecera[6],3,'Total (Bs)','',0,'R');
        $this->Ln();


        // $tbl_head = '<table border="0" style="font-size: 9pt;">
        //                 <tr><td width="5%" style="text-align: center"></td><td width="40%" style="text-align: left">Periodo: '.$this->datos[0]['periodo_num'].'</td> <td width="40%" style="text-align: center"></td></tr>
        //                 <tr><td width="5%" style="text-align: center"></td><td width="40%" style="text-align: left">Gestión: '.$this->datos[0]['gestion'].'</td> <td width="40%" style="text-align: center"></td></tr>
        //                 <tr><td width="5%" style="text-align: center"></td><td width="40%" style="text-align: left">Empresa: '.$this->datos[0]['razon_empresa'].'</td><td width="40%" style="text-align: left"> NIT: ' .$this->datos[0]['nit_empresa'] . '</td></tr>
        //              </table>
        //                 ';
        // $this->writeHTML($tbl_head);
        // $this->SetX(12);
        // $cabecera = '<font size="8">
        //           <table border = "1" cellspacing="0" cellpadding="2" >
        //             <tbody>
        //               <tr>
        //                 <td style="width:60px;">Código Cliente</td>
        //                 <td style="width:65px;">Tipo de documento</td>
        //                 <td style="width:65px;">Nro. de documento</td>
        //                 <td style="width:100px;">Cliente</td>
        //                 <td style="width:100px;">Cod. Servicio</td>
        //                 <td style="width:100px;">Desc. Servicio</td>
        //                 <td style="width:60px;">Cant. Servicio</td>
        //                 <td style="width:60px;">Precio Unitario</td>
        //                 <td style="width:60px;">Importe Total</td>
        //               </tr>
        //             </tbody>
        //           </table>
        //         </font>
        //         ';
        //
        //         $this->writeHTML($cabecera, true, 0, true, 0);


    }

    function generarReporte(){

      //$this->setFontSubsetting(false);

      $this->SetMargins(15, 40, 2);
      $this->AddPage();
      //$this->SetAutoPageBreak(true,2);

        //$this->SetY(55);

        $this->SetFont('', '', 7);
        $array = array(17,17,30,50,10,32,25);

        $this->SetFont('', '', 7);
        foreach ($this->datos as $key => $value) {

                $this->Cell($array[0],3,$value['nit'],'',0,'L');
                $this->Cell($array[1],3,$value['nit'],'',0,'L');
                $this->Cell($array[2],3,$value['sistema_origen'],'',0,'L');
                $this->Cell($array[3],3,$value['desc_ruta'],'',0,'L');
                $this->Cell($array[4],3,$value['cantidad'],'',0,'R');
                $this->Cell($array[5],3,number_format($value['precio_unitario'], 2, ',', '.'),'',0,'R');
                $this->Cell($array[6],3,number_format($value['precio_total'], 2, ',', '.'),'',0,'R');
                $this->Ln();

        }

    }
  //   function Footer(){
  //
	// }
}
?>
