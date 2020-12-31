<?php
require_once(dirname(__FILE__).'/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RRecibo
{
	function generarHtml ($codigo_reporte,$datos) {

		/********************************************REPORTE INGLES******************************************************/
		if ($datos['moneda_base'] == 'USD') {
			$cadena_qr = 'Date: '.$datos['fecha_ingles'] . '<br />' .
					'Grand total: '.$datos['total_venta'] . '<br />' .
					'Client: '.$datos['cliente'];

			$barcodeobj = new TCPDF2DBarcode($cadena_qr, 'QRCODE,H');

			//$barcodeobj->getBarcodeSVGcode(3, 3, 'black')
			setlocale(LC_ALL,"es_ES@euro","es_ES","esp");


			$html.='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
					 "http://www.w3.org/TR/html4/strict.dtd">
				<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<title>sis_ventas_facturacion</title>
					<meta name="author" content="kplian">
					<link rel="stylesheet" href="../../../sis_ventas_facturacion/control/print.css" type="text/css"  charset="utf-8">
				</head>

				<body style="font-size: 11pt;">
				<center>
				<table  height=5px;  width="150px" style="margin-top:-20px;">
					<tbody>
						<tr>
							<td><img style=" padding:0;" width="150" src="../../../sis_obingresos/reportes/Logo2.jpg" alt="" ></img></td>
						</tr>
					</tbody>
				</table>
				</center>

			<center>
				<table style="width:295px; margin-top:-30px;">
			<thead >
			<tr >
					<td colspan="2" style=" text-align: center;" align="center" >
					<hr/>
					</td>
			</tr>
			<tr >
					<td colspan="2" style=" text-align: center;" align="center" >
						RECEIPT N°: <strong>'.$datos['numero_factura'] .'</strong><br />
					</td>
			</tr>
			<tr >
					<td colspan="2" style=" text-align: center;" align="center" >
						ISSUING OFFICE: <strong>'. $datos['codigo_iata'].'</strong><br />
					<hr/>
					</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="text-align: center;"><strong>INVOICE</strong><hr/></td>
			</tr>
			<tr>
				<td colspan="2">
					DATE OF ISSUANCE:<strong> '.$datos['fecha_ingles'].'</strong><br/><br/>
					PAX NAME: <strong>'.trim($datos['cliente']).'</strong>
				</td>
			</tr>
				<table style="width: 295px;">
				<thead>
				<tr>
					<td colspan="4" align="center" style="text-align: center;"><hr/></td>
				</tr>
				<tr>
					<td colspan="4" align="center" style="text-align: center;"><strong>SERVICE</strong><hr/></td>
				</tr>
					<tr><th  align="center" style="width: 11px;">QTY <hr color="#ccc" size=1 width="40"> </th><th align="center" style="width:150px;">REASON <hr color="#ccc" size=1 width="80"></th><th align="center">AMT<hr color="#ccc" size=1 width="80	"></th><th align="center">TOTAL <hr color="#ccc" size=1 width="80"></th></tr>
				</thead>
				<tbody>';

				foreach ($datos['detalle'] as $item_detalle) {
						$html .= '<tr>

						<td style="width: 11px;">'.number_format($item_detalle['cantidad'], 2, '.', '').'</td>
						<td style="width:150px;"> '.str_replace( "/", " / ", $item_detalle['concepto'] ).'</td>
						<td align="right">'.number_format($item_detalle['precio_unitario'], 2, '.', '').'</td>
						<td align="right">'.number_format($item_detalle['precio_total'], 2, '.', '').' </td>
						</tr> ';

				}
				$html.='<tr><td colspan="4"></td></tr>';
				$html.='</tbody>
						<tfoot>
						<tr>
							<td colspan="4" align="center" style="text-align: center;"><hr/></td>
						</tr>
						<tr>
							<td colspan="4" align="center" style="text-align: center;"><strong>PAYMENT DETAILS</strong><hr/></td>
						</tr>
						<tr>
							<td colspan="2" align="left">GRAND TOTAL</td>
							<td colspan="2" align="right"><b>' .$datos['codigo_moneda'].' '.number_format($datos['total_venta'], 2, '.', ',').'</b></td>
						</tr>
						<tr>
							<td colspan="2" align="left">FORM OF PAYMENT</td>
							<td colspan="2" align="right"><b>' .$datos['forma_pago'].'</b></td>
						</tr>
						<tr>
							<td colspan="4" align="center" style="text-align: center;"><hr/></td>
						</tr>';


				$html .='
					<tr>
							<td colspan="4">OBS: <strong>'.$datos['observaciones'].'</strong> </td>
					</tr>
						<tr>

							<td colspan="4">
								<div align="center">
									'.$barcodeobj->getBarcodeSVGcode(3, 3, 'black').'
							</div>
						</td>
						</tr>
						<tr>
							<td colspan="4" style=" text-align: center;" align="center">ATM: <strong>' . $_SESSION["_LOGIN"] . '</strong>  Id: <strong>' . $datos['id'] . '</strong>  Hour: <strong>' . $datos['hora'] . '</strong>
							<br/>
							'.$datos['leyenda'].'
							<br/>
							<strong>' . $datos['pagina_entidad'] .'</strong>
							</td>
						</tr>
						</tfoot>
				</table>

					<script language="VBScript">
					Sub Print()
								 OLECMDID_PRINT = 6
								 OLECMDEXECOPT_DONTPROMPTUSER = 2
								 OLECMDEXECOPT_PROMPTUSER = 1
								 call WB.ExecWB(OLECMDID_PRINT, OLECMDEXECOPT_DONTPROMPTUSER,1)
					End Sub
					document.write "<object ID="WB" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>"
					</script>

					<script type="text/javascript">
					setTimeout(function(){
						 self.print();
						}, 1000);

					setTimeout(function(){
						 self.close();
						}, 2000);
					</script>

			</body>
			</html>';
		}
		/***************************************************************************************************************/
		else {

				$cadena_qr = 'Fecha: '.$datos['fecha_venta'] . '<br />' .
						'Total a pagar: '.$datos['total_venta'] . '<br />' .
						'Cliente: '.$datos['cliente'];

				$barcodeobj = new TCPDF2DBarcode($cadena_qr, 'QRCODE,H');

				//$barcodeobj->getBarcodeSVGcode(3, 3, 'black')
				setlocale(LC_ALL,"es_ES@euro","es_ES","esp");


				$html.='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
					   "http://www.w3.org/TR/html4/strict.dtd">
					<html>
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<title>sis_ventas_facturacion</title>
						<meta name="author" content="kplian">
					  <link rel="stylesheet" href="../../../sis_ventas_facturacion/control/print.css" type="text/css"  charset="utf-8">
					</head>
          <body style="font-size: 11pt;">
					<center>
					<table  height=5px;  width="150px" style="margin-top:-20px;">
						<tbody>
							<tr>
								<td><img style=" padding:0;" width="150" src="../../../sis_obingresos/reportes/Logo2.jpg" alt="" ></img></td>
							</tr>
						</tbody>
					</table>
					</center>
        <center>
					<table style="width:295px; margin-top:-30px;">
				<thead >
				<tr >
            <td colspan="2" style=" text-align: center;" align="center" >
              ' . $datos['nombre_entidad'] . '<br />
						  SUCURSAL' . $datos['codigo_sucursal'] . '
							' . $datos['nombre_sucursal'] . '<br />
							' . $datos['direccion_sucursal'].'<br />
							' . $datos['zona'] . '
							TELF: ' . $datos['telefono_sucursal'].'
							' . $datos['lugar_sucursal'].'<br />
						<hr/>
						</td>
				</tr>
				<tr>
					<td colspan="2" align="center" style="text-align: center;"><strong>RECIBO</strong><hr/></td>
				</tr>

			<!--	<tr>
					<td style="width: 200px;" colspan="2"  align="center">
						 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NIT : ' . $datos['nit_entidad'] . ' <br/>
						N° RECIBO : '.$datos['numero_factura'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>
						N° AUTORIZACION : ' . $datos['autorizacion'] . '&nbsp;&nbsp;&nbsp; <hr/>
					</td>
				</tr> -->
				<tr>
					<td colspan="2" align="center">
						'.$datos['actividades'].'
					</td>
				</tr>
				<tr>
					<td colspan="2">
						N° RECIBO :<strong> '.$datos['numero_factura'] . '</strong><br/>
 						FECHA: <strong>'.$datos['fecha_venta'].'</strong><br/>
					   <!-- NIT/CI: '.$datos['nit_cliente'].'<br/>-->
					    SEÑOR(ES): <strong>'.trim($datos['cliente']).'</strong>
					</td>
				</tr>


					<table style="width: 295px;">
					<thead>
						<tr>
						<th style="width: 11px;">Cant</th>
						<th style="width:150px;">Concepto</th>
						<th align="center">PU</th>
						<th>SubTotal</th>
						</tr>
						<tr >
							<td colspan="4">
							<hr tyle="width: 295px;"/>
							</td>
						</tr>
					</thead>
					<tbody>';

					foreach ($datos['detalle'] as $item_detalle) {
					    $html .= '<tr>

							<td style="width: 11px;">'.number_format($item_detalle['cantidad'], 2, '.', '').'</td>
							<td style="width:150px;"> '.str_replace( "/", " / ", $item_detalle['concepto'] ).'</td>
							<td align="right">'.number_format($item_detalle['precio_unitario'], 2, '.', '').'</td>
							<td align="right">'.number_format($item_detalle['precio_total'], 2, '.', '').' </td>
							</tr> ';

					}
					$html.='<tr><td colspan="4"></td></tr>';
					$html.='</tbody>
					    <tfoot>
					    <tr>
					    	<td colspan="2" align="left"><hr/><b>TOTAL A PAGAR</b><hr/></td>
					    	<td colspan="2" align="right"><hr/><b>' .$datos['codigo_moneda_recibo'].' '.number_format($datos['total_venta'], 2, '.', ',').'</b><hr/></td>
					    </tr>';

					if ($datos['total_venta'] > $datos['sujeto_credito']) {
						$html .= '<tr>
					    	<td colspan="2" align="left"><b>Sujeto a credito fiscal</b> <hr/></td>
					    	<td colspan="2" align="right"> <b>' .$datos['moneda_sucursal'].' '.number_format($datos['sujeto_credito'], 2, '.', ',').'</b><hr/></td>
					    </tr>';
					}


					$html .=' <tr>
						    <td colspan="4">Son: <strong>'.$datos['total_venta_literal']. ' '.strtoupper($datos['moneda_literal']).'</strong> </td>
						</tr>
						<tr>
						    <td colspan="4">OBS: <strong>'.$datos['observaciones'].'</strong> </td>
						</tr>
						  <tr>

						    <td colspan="4">
						    	<div align="center">
								    '.$barcodeobj->getBarcodeSVGcode(3, 3, 'black').'
								</div>
							</td>
						  </tr>
						  <tr>
						    <td colspan="4" style=" text-align: center;" align="center">Cajero: <strong>' . $_SESSION["_LOGIN"] . '</strong>  Id: <strong>' . $datos['id'] . '</strong>  Hora: <strong>' . $datos['hora'] . '</strong>
						    <br/>
						    <br/>
						    <strong>' . $datos['pagina_entidad'] .'</strong>
						    </td>
						  </tr>
					    </tfoot>
					</table>

						<script language="VBScript">
						Sub Print()
						       OLECMDID_PRINT = 6
						       OLECMDEXECOPT_DONTPROMPTUSER = 2
						       OLECMDEXECOPT_PROMPTUSER = 1
						       call WB.ExecWB(OLECMDID_PRINT, OLECMDEXECOPT_DONTPROMPTUSER,1)
						End Sub
						document.write "<object ID="WB" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>"
						</script>

						<script type="text/javascript">
						setTimeout(function(){
							 self.print();
							}, 1000);

						setTimeout(function(){
							 self.close();
							}, 2000);
						</script>

				</body>
				</html>';

		}
			return $html;
	}
}
?>
