<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RReporteReciboMiamiA4 extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    function Header() {

      $tabla='<table style="width:650px;" cellpadding="2" cellpadding="0">
                  <tr>
                    <td align="center"><img style=" padding:0;" width="150" src="../../../sis_obingresos/reportes/Logo2.jpg" alt="" ></td>
                  </tr>
                  <tr>
                    <td align="center">BOLIVIANA DE AVIACIÓN - BOA</td>
                  </tr>
                  <tr>
                    <td align="center">'.$this->datos[0]['desc_sucursal'].'</td>
                  </tr>
              </table>
            ';
    //$this->SetMargins(11,60,5);

    $this->writeHTML($tabla);



    }

    function setDatos($datos,$detalle,$casaMatriz) {

        $this->datos = $datos;
        $this->detalle = $detalle;
        $this->casaMatriz = $casaMatriz;
        //var_dump( $this->datos);
    }

    function  generarReporte()
    {


      $this->AddPage();
      //$this->AliasNbPages();
      $this->SetAutoPageBreak(true,2);
      $this->SetMargins(11,50,5);
      $this->SetY(37);
      $tablaContenido = '<table style="width:650px;" cellpadding="0" nobr="true" cellpadding="0">
                              <tr >
                                  <td colspan="2" style=" text-align: center; border-bottom:1px solid black;" >
                                  </td>
                              </tr>
                              <tr >
                                  <td colspan="2" align="center" >
                                    RECEIPT N°: <strong>'.$this->datos[0]['numero_factura'].'</strong>
                                  </td>
                              </tr>
                              <tr >
                                  <td colspan="2" style=" text-align: center; border-bottom:1px solid black;" align="center" >
                                    ISSUING OFFICE: <strong>'. $this->datos[0]['codigo_iata'].'</strong>
                                  </td>
                              </tr>
                              <tr>
                                <td colspan="2" align="center" style="border-bottom:1px solid black;"><strong>INVOICE</strong>
                                </td>
                              </tr>
                              <tr>
                                <td colspan="2">
                                  DATE OF ISSUANCE:<strong> '.$this->datos[0]['fecha_ingles'].'</strong>
                                </td>
                              </tr>
                              <tr>
                                <td colspan="2">
                                  PAX NAME: <strong>'.$this->datos[0]['cliente'].'</strong>
                                </td>
                              </tr>
                                <table style="width: 650px;">
                                <tr>
                                  <td colspan="4" align="center" style="border-bottom:1px solid black;"></td>
                                </tr>
                                <tr>
                                  <td colspan="4" align="center" style="border-bottom:1px solid black;"><strong>SERVICE</strong></td>
                                </tr>
                                  <tr>
                                    <th align="center"><b>QTY</b></th>
                                    <th align="center"><b>REASON</b></th>
                                    <th align="center"><b>AMT</b></th>
                                    <th align="center"><b>TOTAL</b></th>
                                  </tr>

                                ';

        $size_conceptos = sizeof($this->detalle);

        for ($i=0;$i<$size_conceptos;$i++){
          $tablaContenido .= '<tr>
                                      <td align="center">'.number_format($this->detalle[$i]['cantidad'], 2, '.', '').'</td>
                          						<td align="center">'.$this->detalle[$i]['concepto'].'</td>
                                      <td align="center">'.number_format($this->detalle[$i]['precio_unitario'], 2, '.', '').'</td>
                                      <td align="center">'.number_format($this->detalle[$i]['precio_total'], 2, '.', '').'</td>
                      						</tr> ';
        }
          $tablaContenido .= '  <tr>
                                  <td colspan="4" align="center" style="border-bottom:1px solid black;"></td>
                                </tr>
                                <tr>
                                  <td colspan="4" align="center" style="text-align: center;"><strong>PAYMENT DETAILS</strong><hr/></td>
                                </tr>
                                <tr>
                                  <td colspan="2" align="left">GRAND TOTAL</td>
                                  <td colspan="2" align="right"><b>' .$this->datos[0]['moneda_venta'].' '.number_format($this->datos[0]['total_venta'], 2, '.', ',').'</b></td>
                                </tr>
                                <tr>
                                  <td colspan="2" align="left">FORM OF PAYMENT</td>
                                  <td colspan="2" align="right"><b>' .$this->datos[0]['forma_pago'].'</b></td>
                                </tr>
                                <tr>
                                  <td colspan="4" align="center" style="text-align: center;"><hr/></td>
                                </tr>
                                <tr>
                      							<td colspan="4">OBS: <strong>'.$this->datos[0]['observaciones'].'</strong> </td>
                      					</tr>
                                <tr>
                    							<td colspan="4">
                    								<div align="center">
                    									<img  style="width: 110px; height: 110px;" src="'
                                      . $this->generarImagen($this->datos[0]['fecha_ingles'],$this->datos[0]['total_venta'],$this->datos[0]['cliente']).
                                      '" alt="Logo">
                    							  </div>
                    						</td>
                    						</tr>
                                <tr>
                    							<td colspan="4" style=" text-align: center;" align="center">ATM: <strong>' . $_SESSION["_LOGIN"] . '</strong>  Id: <strong>' . $this->datos[0]['id_venta'] . '</strong>  Hour: <strong>' . $this->datos[0]['hora'] . '</strong>
                    							<br/>
                    							'.$this->datos[0]['leyenda'].'
                    							<br/>
                    							<strong>' . $this->datos[0]['pagina_entidad'].'</strong>
                    							</td>
                    						</tr>
                            </table>
                        </table>';


      $this->writeHTML($tablaContenido);


    }

    function Footer(){

	}  //Fin Footer

    function generarImagen($date,$total,$client){
      $cadena_qr = 'Date: '.$date.' ' .
                   'Grand total: '.$total.' ' .
                   'Client: '.$client;
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
