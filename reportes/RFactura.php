<?php
require_once(dirname(__FILE__).'/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RFactura
{
	function generarHtml ($codigo_reporte,$datos) {



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

					</head>';

					$html.='<body style="font-size: 11pt;">';

				$html .= '<center>
					<table style="width:295px;" >
				<thead>
				<tr   >
						<td colspan="2" style=" text-align: center;" align="center" >
							' . $datos['nombre_entidad'] . '<br />
							SUCURSAL ' . $datos['codigo_sucursal'] . '<br />
							' . $datos['nombre_sucursal'] . '<br />
							' . $datos['direccion_sucursal'].'<br />
							' . $datos['zona'] . '<br />
							TELF: ' . $datos['telefono_sucursal'].'<br />
							' . $datos['lugar_sucursal'].'<br />
						<hr/>
						</td>
				</tr>

				<tr>
					<td colspan="2" align="center" style="text-align: center;"><strong>FACTURA</strong><hr/></td>
				</tr>

				<tr>
					<td style="width: 200px;" colspan="2"  align="center">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NIT : ' . $datos['nit_entidad'] . '<br/>
						N° FACTURA : '.$datos['numero_factura'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>
						N° AUTORIZACION : ' . $datos['autorizacion'] . '&nbsp;&nbsp;&nbsp;<hr/>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						'.$datos['actividades'].'
					</td>
				</tr>
				<tr>
					<td colspan="2">
						  FECHA: '.$datos['fecha_venta'].'<br/>
							NIT/CI: '.$datos['nit_cliente'].'<br/>
							SEÑOR(ES):'.trim($datos['cliente']).'
					</td>
				</tr>


					<table style="width: 295px;">
					<thead>
						<tr><th style="width: 11px;">Cant <hr color="#ccc" size=1 width="40"> </th><th style="width:150px;">Concepto <hr color="#ccc" size=1 width="80"></th><th align="center">PU<hr color="#ccc" size=1 width="30"></th><th>SubTotal <hr color="#ccc" size=1 width="80"></th></tr>
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
								<td colspan="2" align="right"><hr/><b>' .$datos['moneda_sucursal'].' '.number_format($datos['total_venta'], 2, '.', ',').'</b><hr/></td>
							</tr>';

					if ($datos['total_venta'] > $datos['sujeto_credito']) {
						$html .= '<tr>
								<td colspan="2" align="left"><b>Sujeto a credito fiscal</b> <hr/></td>
								<td colspan="2" align="right"> <b>' .$datos['moneda_sucursal'].' '.number_format($datos['sujeto_credito'], 2, '.', ',').'</b><hr/></td>
							</tr>';
					}


					$html .=' <tr>
								<td colspan="4">Son: '.$datos['total_venta_literal']. ' '.strtoupper($datos['desc_moneda_sucursal']).' </td>
						</tr>
						<tr>
								<td colspan="4">OBS: '.$datos['observaciones'].' </td>
						</tr>
						<tr>
								<td colspan="4"><b>Código de Control: '.$datos['codigo_control'].'</b></td>
						 </tr>
							<tr>
								<td colspan="4"><b>Fecha Limite de Emisión: '.$datos['fecha_limite_emision'].'</b></td>
							</tr>

							<tr>

								<td colspan="4">
									<div align="center">
										'.$barcodeobj->getBarcodeSVGcode(3, 3, 'black').'
								</div>
							</td>
							</tr>
							<tr>

								<td colspan="4" style=" text-align: center;" align="center">&quot;' . $datos['glosa_impuestos'] . '&quot;<br>
								<br/>
								&quot;' . $datos['glosa_empresa'] . '&quot;
								</td>
							</tr>

							<tr>
								<td colspan="4" style=" text-align: center;" align="center">Cajero: ' . $_SESSION["_LOGIN"] . '  Id: ' . $datos['id'] . '  Hora: ' . $datos['hora'] . '
								<br/>
								'.$datos['leyenda'].'
								<br/>
								' . $datos['pagina_entidad'] .'
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

				// $html.='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
				// 	   "http://www.w3.org/TR/html4/strict.dtd">
				// 	<html>
				// 	<head>
				// 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				// 		<title>sis_ventas_facturacion</title>
				// 		<meta name="author" content="kplian">
				// 	  <link rel="stylesheet" href="../../../sis_ventas_facturacion/control/print.css" type="text/css"  charset="utf-8">
				// 	</head>
        //   <body style="font-size: 11pt;">
				// <!--	<center>
				// 	<table  height=5px;  width="150px" style="margin-top:-20px;">
				// 		<tbody>
				// 			<tr>
				// 				<td><img style=" padding:0;" width="150" src="../../../sis_obingresos/reportes/Logo2.jpg" alt="" ></img></td>
				// 			</tr>
				// 		</tbody>
				// 	</table>
				// 	</center> -->
				//
				// 	<center>
				// 	<table style="width: 295px;">
				// 			<tbody>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">'.$datos['nombre_entidad'] .'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">Sucursal No.'. $datos['codigo_sucursal'] .'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">'.$datos['direccion_sucursal'].'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">'.$datos['zona'].'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">Teléfono '.$datos['telefono_sucursal'].'</td>
				// 				</tr>
				// 				<tr style="height: 23px;">
				// 					<td style="text-align: center; padding:0px;">'.$datos['lugar_sucursal'].'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;"><br>NIT: '.$datos['nit'].'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">Documento Fiscal Nro: '.$datos['numero_factura'].'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px; color:red;">CUF: abcdefghi123456789</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px; color:red;">CUFD: abcdefghi123456789</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;"><br>'.$datos['actividades'].'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;"><br>FACTURA</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">FACTURACIÓN ELECTRÓNICA</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">REPRESENTACIÓN GRÁFICA</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;"><br>Fecha de Emisión: '.$datos['fecha_venta'].'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">NIT/CI: '.$datos['nit_cliente'].'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px;">Nombre/Razón Social: '.$datos['cliente'].'</td>
				// 				</tr>
				// 				<tr>
				// 					<td style="text-align: center; padding:0px; color:red;">Codigo Cliente: 12345</td>
				// 				</tr>
				// 			</tbody>
				// 	</table>
				// 	</center>
				// 	</br>
				//
        // <center>
				// <table cellpadding="2" cellspacing="0" style="width: 295px;">
				// 	<thead>
				// 		<tr>
				// 			<th align="center" style="width:59px; background:#E9E9E9; border-top:1px solid black; border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black;">C&oacute;digo Producto</th>
				// 			<th align="center" style="width:59px; padding-left:5px; background:#E9E9E9; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black;">Cantidad</th>
				// 			<th align="center" style="width:59px; background:#E9E9E9; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black; ">Descripci&oacute;n</th>
				// 			<th align="center" style="width:59px; padding-left:5px; background:#E9E9E9; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black; ">Precio Unitario</th>
				// 			<th align="center" style="width:59px; padding-left:5px; background:#E9E9E9; border-top:1px solid black; border-bottom:1px solid black; border-right:1px solid black; ">Subtotal</th>
				// 		</tr>
				// 	</thead>
				// 	<tbody>';
				//
				// 	foreach ($datos['detalle'] as $item_detalle) {
				// 	    $html .= '<tr>
				// 			<td align="right" style="border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ">'.$item_detalle['cod_producto'].'</td>
				// 			<td align="right" style="border-right:1px solid black; border-bottom:1px solid black;">'.number_format($item_detalle['cantidad'], 2, '.', '').'</td>
				// 			<td style="border-right:1px solid black; border-bottom:1px solid black;"> '.str_replace( "/", " / ", $item_detalle['concepto'] ).'</td>
				// 			<td align="right" style="border-right:1px solid black; border-bottom:1px solid black;">'.number_format($item_detalle['precio_unitario'], 2, '.', '').'</td>
				// 			<td align="right" style="border-right:1px solid black; border-bottom:1px solid black;">'.number_format($item_detalle['precio_total'], 2, '.', '').' </td>
				// 			</tr> ';
				//
				// 	}
				// 	$html.='<tr><td colspan="4"></td></tr>';
				// 	$html.='</tbody>
				// 	    <tfoot>
				// 	    <tr>
				// 	    	<td colspan="4" align="right">Total </td>
				// 	    	<td align="right">'.$datos['moneda_sucursal'].' '.number_format($datos['total_venta'], 2, '.', ',').'</td>
				// 	    </tr>
				// 		 <tr>
				// 			<td colspan="4" align="right" style="border-bottom:1px solid black;">Total a Pagar </td>
				// 			<td align="right" style="border-bottom:1px solid black;">'.$datos['moneda_sucursal'].' '.number_format($datos['total_venta'], 2, '.', ',').'</td>
				// 		</tr>
				//
				// 	';
				//
				// 	if ($datos['total_venta'] > $datos['sujeto_credito']) {
				// 		$html .= '
				// 				<tr>
				// 				 <td colspan="4" align="right">Excento:</td>
				// 				 <td align="right"> <b>'.$datos['moneda_sucursal'].' '.number_format($datos['excento'], 2, '.', ',').'</b></td>
				// 			 </tr>
				// 				<tr>
				// 					<td colspan="4" align="right">Importe Base para Crédito Fiscal:</td>
				// 					<td align="right" style="border-bottom:2px solid black;"> <b>'.$datos['moneda_sucursal'].' '.number_format($datos['sujeto_credito'], 2, '.', ',').'</b></td>
				// 			  </tr>';
				// 	}
				//
				// 	$html .='
				// 	<tr>
				// 	 <td colspan="5" align="right">Son: '.$datos['total_venta_literal']. ' '.strtoupper($datos['desc_moneda_sucursal']).'</td>
				//  </tr>
				// 			<tr>
				// 			 <td colspan="5" align="center"></br>'.$datos['glosa_impuestos'].'</td>
				// 		 </tr>
				// 		 <tr>
				// 			<td  colspan="5" align="center"></br>'.$datos['glosa_empresa'].'</td>
				// 		</tr>
				// 		<tr>
				// 		 <td  colspan="5" align="center"></br>'.$datos['leyenda'].'</td>
				// 	 </tr>
				// 		  <tr>
				// 		    <td colspan="5">
				// 		    </br>	<div align="center">
				// 				    '.$barcodeobj->getBarcodeSVGcode(3, 3, 'black').'
				// 				</div>
				// 			</td>
				// 		  </tr>
				// 	    </tfoot>
				// 	</table>
				//
				// 		<script language="VBScript">
				// 		Sub Print()
				// 		       OLECMDID_PRINT = 6
				// 		       OLECMDEXECOPT_DONTPROMPTUSER = 2
				// 		       OLECMDEXECOPT_PROMPTUSER = 1
				// 		       call WB.ExecWB(OLECMDID_PRINT, OLECMDEXECOPT_DONTPROMPTUSER,1)
				// 		End Sub
				// 		document.write "<object ID="WB" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>"
				// 		</script>
				//
				// 		<script type="text/javascript">
				// 		setTimeout(function(){
				// 			 self.print();
				// 			}, 1000);
				//
				// 		setTimeout(function(){
				// 			 self.close();
				// 			}, 2000);
				// 		</script>
				//
				// </body>
				// </html>';


			return $html;
	}
}
?>
