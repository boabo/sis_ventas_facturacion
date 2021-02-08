<?php
/**
*@package pXP
*@file gen-ACTComisionistas.php
*@author  (Ismael Valdivia)
*@date 25-01-2020 11:30:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDFFormulario.php';
require_once(dirname(__FILE__).'/../reportes/RComisionistasNaturalesPDF.php');
require_once(dirname(__FILE__).'/../reportes/RComisionistasSimplificadoPDF.php');
require_once(dirname(__FILE__).'/../reportes/RComisionistasNaturalesXLS.php');
require_once(dirname(__FILE__).'/../reportes/RComisionistasSimplificadoXLS.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleVentasNaturalesPDF.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleVentasNaturalesXLS.php');
require_once(dirname(__FILE__).'/../reportes/RResumenVentasNaturalesPDF.php');
require_once(dirname(__FILE__).'/../reportes/RResumenVentasNaturalesXLS.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleVentasRtsPDF.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleVentasRtsXLS.php');
require_once(dirname(__FILE__).'/../reportes/RResumenVentasRtsXLS.php');
require_once(dirname(__FILE__).'/../reportes/RResumenVentasRtsPDF.php');

class ACTComisionistas extends ACTbase{

  function TraerAcumulados(){
        $this->objFunc=$this->create('MODComisionistas');
        $this->res=$this->objFunc->TraerAcumulados($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function reportesComisionistas(){


        if($this->objParam->getParametro('formato_reporte')=='pdf'){

					if ('res_vent_natu' == $this->objParam->getParametro('tipo_reporte')) {
							$nombreArchivo = uniqid(md5(session_id()).'RESUMEN_VENTAS_NATURALES') . '.pdf';

							$this->objFunc = $this->create('MODComisionistas');
							$this->res = $this->objFunc->resumenComisionistas($this->objParam);

              if ($this->res->getTipo() == 'ERROR') {
                throw new \Exception($this->res->getMensaje());
              }

							$this->datos = $this->res->getDatos();

					} elseif ('res_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
            $nombreArchivo = uniqid(md5(session_id()).'RESUMEN_VENTAS_RTS') . '.pdf';

            $this->objFunc = $this->create('MODComisionistas');
            $this->res = $this->objFunc->resumenComisionistas($this->objParam);

            if ($this->res->getTipo() == 'ERROR') {
              throw new \Exception($this->res->getMensaje());
            }

            $this->datos = $this->res->getDatos();
          }

          else {
						if( 'per_natu' == $this->objParam->getParametro('tipo_reporte') ){
								$nombreArchivo = uniqid(md5(session_id()).'NATURALES') . '.pdf';
						}elseif ('regimen_simpli' == $this->objParam->getParametro('tipo_reporte')) {
								$nombreArchivo = uniqid(md5(session_id()).'SIMPLIFICADO') . '.pdf';
						}elseif ('det_vent_natu' == $this->objParam->getParametro('tipo_reporte')) {
								$nombreArchivo = uniqid(md5(session_id()).'DETALLE_VENTAS_NATURALES') . '.pdf';
						}elseif ('det_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
								$nombreArchivo = uniqid(md5(session_id()).'DETALLE_VENTAS_RTS') . '.pdf';
						}

						$this->objFunc = $this->create('MODComisionistas');
						$this->res = $this->objFunc->reporteComisionistas($this->objParam);


            if ($this->res->getTipo() == 'ERROR') {
              throw new \Exception($this->res->getMensaje());
            }


						$this->datos = $this->res->getDatos();
					}

            //parametros basicos
            $tamano = 'A4';
            $orientacion = 'L';

            if( 'per_natu' == $this->objParam->getParametro('tipo_reporte') ){
                $titulo = 'NATURALES';
            }elseif ('regimen_simpli' == $this->objParam->getParametro('tipo_reporte')) {
                $titulo = 'SIMPLIFICADO';
            }elseif ('det_vent_natu' == $this->objParam->getParametro('tipo_reporte')) {
                $titulo = 'DETALLE_VENTAS_NATURALES';
            }elseif ('res_vent_natu' == $this->objParam->getParametro('tipo_reporte')) {
                $titulo = 'RESUMEN_VENTAS_NATURALES';
            }elseif ('det_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
                $titulo = 'DETALLE_VENTAS_RTS';
            }elseif ('res_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
                $titulo = 'RESUMEN_VENTAS_RTS';
            }


            $this->objParam->addParametro('orientacion',$orientacion);
            $this->objParam->addParametro('tamano',$tamano);
            $this->objParam->addParametro('titulo_archivo',$titulo);
            $this->objParam->addParametro('nombre_archivo',$nombreArchivo);


            if( 'per_natu' == $this->objParam->getParametro('tipo_reporte') ){
                $reporte = new RComisionistasNaturalesPDF($this->objParam);
            }elseif ('regimen_simpli' == $this->objParam->getParametro('tipo_reporte')) {
                $reporte = new RComisionistasSimplificadoPDF($this->objParam);
            }elseif ('det_vent_natu' == $this->objParam->getParametro('tipo_reporte')) {
                $reporte = new RDetalleVentasNaturalesPDF($this->objParam);
            }elseif ('res_vent_natu' == $this->objParam->getParametro('tipo_reporte')) {
                $reporte = new RResumenVentasNaturalesPDF($this->objParam);
            }elseif ('det_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
                $reporte = new RDetalleVentasRtsPDF($this->objParam);
            }elseif ('res_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
                $reporte = new RResumenVentasRtsPDF($this->objParam);
            }

            //var_dump("aqui data",$this->datos->getTipo());
            $reporte->setDatos($this->datos);
            $reporte->generarReporte();
            $reporte->output($reporte->url_archivo,'F');

            $this->mensajeExito=new Mensaje();
            $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
            $this->mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
        }

        if($this->objParam->getParametro('formato_reporte') == 'xls'){
					if ('res_vent_natu' == $this->objParam->getParametro('tipo_reporte') ) {

						$nombreArchivo = uniqid(md5(session_id()).'RESUMEN_VENTAS_NATURALES') . '.xls';

						$this->objFunc = $this->create('MODComisionistas');
						$this->res = $this->objFunc->resumenComisionistas($this->objParam);

            if ($this->res->getTipo() == 'ERROR') {
              throw new \Exception($this->res->getMensaje());
            }


						$this->datos = $this->res->getDatos();

					} elseif ('res_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
            $nombreArchivo = uniqid(md5(session_id()).'RESUMEN_VENTAS_RTS') . '.xls';

						$this->objFunc = $this->create('MODComisionistas');
						$this->res = $this->objFunc->resumenComisionistas($this->objParam);

            if ($this->res->getTipo() == 'ERROR') {
              throw new \Exception($this->res->getMensaje());
            }


						$this->datos = $this->res->getDatos();
          } else {

						if( 'per_natu' == $this->objParam->getParametro('tipo_reporte') ){
								$nombreArchivo = uniqid(md5(session_id()).'NATURALES') . '.xls';
						}elseif ('regimen_simpli' == $this->objParam->getParametro('tipo_reporte')) {
								$nombreArchivo = uniqid(md5(session_id()).'SIMPLIFICADO') . '.xls';
						}elseif ('det_vent_natu' == $this->objParam->getParametro('tipo_reporte')) {
								$nombreArchivo = uniqid(md5(session_id()).'DETALLE_VENTAS_NATURALES') . '.xls';
						}elseif ('det_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
								$nombreArchivo = uniqid(md5(session_id()).'DETALLE_VENTAS_RTS') . '.xls';
						}
						$this->objFunc = $this->create('MODComisionistas');
						$this->res = $this->objFunc->reporteComisionistas($this->objParam);

            if ($this->res->getTipo() == 'ERROR') {
              throw new \Exception($this->res->getMensaje());
            }
						$this->datos = $this->res->getDatos();

					}




                $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
                $this->objParam->addParametro('datos',$this->datos);

								if( 'per_natu' == $this->objParam->getParametro('tipo_reporte') ){
		                $this->objReporteFormato=new RComisionistasNaturalesXLS($this->objParam);
		            }elseif ('regimen_simpli' == $this->objParam->getParametro('tipo_reporte')) {
		                $this->objReporteFormato=new RComisionistasSimplificadoXLS($this->objParam);
		            }elseif ('det_vent_natu' == $this->objParam->getParametro('tipo_reporte')) {
		                $this->objReporteFormato=new RDetalleVentasNaturalesXLS($this->objParam);
		            }elseif ('res_vent_natu' == $this->objParam->getParametro('tipo_reporte')) {
		                $this->objReporteFormato=new RResumenVentasNaturalesXLS($this->objParam);
		            }elseif ('det_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
		                $this->objReporteFormato=new RDetalleVentasRtsXLS($this->objParam);
		            }elseif ('res_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {
		                $this->objReporteFormato=new RResumenVentasRtsXLS($this->objParam);
		            }

            // $this->objReporteFormato->generarDatos();
						$this->objReporteFormato->datosHeader($this->datos);
            $this->objReporteFormato->generarReporte();

            $this->mensajeExito=new Mensaje();
            $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado', 'Se generó con éxito el reporte: '.$nombreArchivo,'control');
            $this->mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

        }
        if($this->objParam->getParametro('formato_reporte')!='pdf' && $this->objParam->getParametro('formato_reporte')!='xls'){

          //var_dump("aqui llega para mandar data",$this->objParam->getParametro('tipo_reporte'));
					if ('res_vent_natu' == $this->objParam->getParametro('tipo_reporte') || 'res_vent_rts' == $this->objParam->getParametro('tipo_reporte')) {

						$this->objFunc = $this->create('MODComisionistas');
						$this->res = $this->objFunc->resumenComisionistas($this->objParam);

            if ($this->res->getTipo() == 'ERROR') {
              throw new \Exception($this->res->getMensaje());
            }

						$this->datos = $this->res->getDatos();

					} else {

						$this->objFunc = $this->create('MODComisionistas');
						$this->res = $this->objFunc->reporteComisionistas($this->objParam);

            if ($this->res->getTipo() == 'ERROR') {
              throw new \Exception($this->res->getMensaje());
            }

						$this->datos = $this->res->getDatos();

					}
            $nombreArchivo = $this->crearArchivoTXT_CSV($this->datos, $this->objParam);

            $this->mensajeExito=new Mensaje();
            $this->mensajeExito->setMensaje('EXITO','Reporte.php','Se genero con exito el archivo LCV '.$nombreArchivo, 'Se genero con exito el archivo LCV '.$nombreArchivo,'control');
            $this->mensajeExito->setArchivoGenerado($nombreArchivo);

            $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());


        }
    }

    function crearArchivoTXT_CSV($res, $Obj) {
        $separador = '|';
        if($this->objParam->getParametro('formato_reporte') =='txt'){
            $separador = "|";
            $ext = '.txt';
        }else{
            $separador = ",";
            $ext = '.csv';
        }
        /*******************************
         *  FORMATEA NOMBRE DE ARCHIVO
         * compras_MMAAAA_NIT.txt
         * o
         * ventas_MMAAAA_NIT.txt
         * ********************************/
        $NIT = 	$res[0]['nit_empresa'];

        if ($res[0]['periodo_literal_inicio'] == $res[0]['periodo_literal_fin']) {
          $sufijo = ($res[0]['periodo_num_ini']<10?'0'.$res[0]['periodo_num_ini']:$res[0]['periodo_num_ini']).$res[0]['gestion'];
        } else {
          $sufijo=($res[0]['periodo_num_ini']<10?'0'.$res[0]['periodo_num_ini']:$res[0]['periodo_num_ini']).'_'.($res[0]['periodo_num_fin']<10?'0'.$res[0]['periodo_num_fin']:$res[0]['periodo_num_fin']).'_'.$res[0]['gestion'];
        }

        // if( $this->objParam->getParametro('filtro_sql') == 'periodo' ){
        //     $sufijo = ($res[0]['periodo_num']<10?'0'.$res[0]['periodo_num']:$res[0]['periodo_num']).$res[0]['gestion'];
        // }else{
        //     $sufijo=$this->objParam->getParametro('fecha_ini').'_'.$this->objParam->getParametro('fecha_fin');
        // }

        if($this->objParam->getParametro('tipo_reporte')=='per_natu'){
            $nombre = 'PERSONASNATURALES_'.$sufijo.'_'.$NIT;
        }elseif ($this->objParam->getParametro('tipo_reporte')=='regimen_simpli') {
            $nombre = 'SIMPLIFICADO_'.$sufijo.'_'.$NIT;
        }elseif ($this->objParam->getParametro('tipo_reporte')=='det_vent_natu') {
        	$nombre = 'DETALLE_VENTAS_NATURALES_'.$sufijo;
        }elseif ($this->objParam->getParametro('tipo_reporte')=='det_vent_rts') {
        	$nombre = 'DETALLE_VENTAS_RTS_'.$sufijo;
        }elseif ($this->objParam->getParametro('tipo_reporte')=='res_vent_natu') {
        	$nombre = 'RESUMEN_VENTAS_NATURALES';
        }elseif ($this->objParam->getParametro('tipo_reporte')=='res_vent_rts') {
        	$nombre = 'RESUMEN_VENTAS_RTS';
        }




        $nombre=str_replace("/", "", $nombre);

        $data = $res;
        $fileName = $nombre.$ext;
        //create file
        $file = fopen("../../../reportes_generados/$fileName","w+");
        $ctd = 1;
        /*if($this->objParam->getParametro('formato_reporte') !='txt'){
            //AÑADE EL BOMM PARA NO TENER PROBLEMAS AL LEER DE APLICACIONES EXTERNAS
            fwrite($file, pack("CCC",0xef,0xbb,0xbf));
        }*/
        /******************************
         *  IMPRIME CABECERA PARA CSV
         *****************************/
        /*if($this->objParam->getParametro('formato_reporte') !='txt'){

            if($this->objParam->getParametro('tipo_lcv')=='lcv_compras' || $this->objParam->getParametro('tipo_lcv')=='endesis_erp'){

                if($dataPeriodoArray['gestion']<2017) {
                    fwrite($file, "-" . $separador .
                        'N#' . $separador .
                        'FECHA DE LA FACTURA O DUI' . $separador .
                        'NIT PROVEEDOR' . $separador .
                        'NOMBRE O RAZON SOCIAL' . $separador .
                        'N# de LA FACTURA.' . $separador .
                        'N# de DUI' . $separador .
                        'N# de AUTORIZACION' . $separador .
                        "IMPORTE TOTAL DE LA COMPRA A" . $separador .
                        "IMPORTE NO SUJETO A CREDITO FISCAL B" . $separador .
                        "SUBTOTAL C = A - B" . $separador .
                        "DESCUENTOS BONOS Y REBAJAS  D" . $separador .
                        "IMPORTE SUJETO a CREDITO FISCAL E = C-D" . $separador .
                        "CREDITO FISCAL F = E*13%" . $separador .
                        'CODIGO DE CONTROL' . $separador .
                        'TIPO DE COMPRA' . "\r\n");
                }else{
                    fwrite($file, "-" . $separador .
                        'N#' . $separador .
                        'FECHA DE LA FACTURA O DUI' . $separador .
                        'NIT PROVEEDOR' . $separador .
                        'NOMBRE O RAZON SOCIAL' . $separador .
                        'N# de LA FACTURA.' . $separador .
                        'N# de DUI' . $separador .
                        'N# de AUTORIZACION' . $separador .
                        "IMPORTE TOTAL DE LA COMPRA A" . $separador .
                        "IMPORTE NO SUJETO A CREDITO FISCAL B" . $separador .
                        "SUBTOTAL C = A - B" . $separador .
                        "DESCUENTOS BONOS Y REBAJAS SUJETAS AL IVA D" . $separador .
                        "IMPORTE SUJETO a CREDITO FISCAL E = C-D" . $separador .
                        "CREDITO FISCAL F = E*13%" . $separador .
                        'CODIGO DE CONTROL' . $separador .
                        'TIPO DE COMPRA' . "\r\n");
                }

            }
            else{
                fwrite ($file,  "-".$separador.
                    'N#'.$separador.
                    'FECHA DE LA FACTURA'.$separador.
                    'N# de LA FACTURA'.$separador.
                    'N# de AUTORIZACION'.$separador.
                    'ESTADO'.$separador.
                    'NIT CLIENTE'.$separador.
                    'NOMBRE O RAZON SOCIAL'.$separador.
                    "IMPORTE TOTAL DE LA VENTA A".$separador.
                    "IMPORTE ICE/ IEHD/ TASAS B".$separador.
                    "EXPORTACIO. Y OPERACIONES EXENTAS C".$separador.
                    "VENTAS GRAVADAS TASA CERO D".$separador.
                    "SUBTOTAL E = A-B-C-D".$separador.
                    "DESCUENTOS BONOS Y REBAJAS OTORGADAS F".$separador.
                    "IMPORTE BASE DEBITO FISCAL G = E-F".$separador.
                    "DEBITO FISCAL H = G*13%".$separador.
                    'CODIGO DE CONTROL'."\r\n");
            }
        }*/
        /**************************
         *  IMPRIME CUERPO
         **************************/

        if($this->objParam->getParametro('tipo_reporte')=='per_natu'){
            foreach ($data as $val) {

                //if($this->objParam->getParametro('tipo_lcv')=='lcncd'){
                    fwrite ($file,  $val['nit'].$separador.
                        $val['carnet_ide'].$separador.
                        $val['nit'].$separador.
                        $val['razon_social'].$separador.
                        $val['sistema_origen'].$separador.
                        $val['desc_ruta'].$separador.
                        $val['cantidad'].$separador.
                        number_format($val['precio_unitario'], 2, ',', '.').$separador.
                        number_format($val['precio_total'], 2, ',', '.')."\r\n");

                //}
                /*else{
                    fwrite ($file,  "3".$separador.
                        $ctd.$separador.
                        $newDate.$separador.
                        $val['nro_documento'].$separador.
                        $val['nro_autorizacion'].$separador.
                        $val['tipo_doc'].$separador.
                        $val['nit'].$separador.
                        $val['razon_social'].$separador.
                        $val['importe_doc'].$separador.
                        $val['importe_ice'].$separador.
                        $val['importe_excento'].$separador.
                        $val['venta_gravada_cero'].$separador.
                        $val['subtotal_venta'].$separador.
                        $val['importe_descuento'].$separador.
                        $val['sujeto_df'].$separador.
                        $val['importe_iva'].$separador.
                        $val['codigo_control']."\r\n");
                }*/
                $ctd = $ctd + 1;
            } //end for
        }else if ($this->objParam->getParametro('tipo_reporte')=='regimen_simpli') {

          foreach ($data as $val) {

              //if($this->objParam->getParametro('tipo_lcv')=='lcncd'){
                  fwrite ($file,  $val['nit'].$separador.
                      $val['nit'].$separador.
                      $val['sistema_origen'].$separador.
                      $val['desc_ruta'].$separador.
                      $val['cantidad'].$separador.
                      $val['precio_unitario'].$separador.
                      $val['precio_total']."\r\n");

              //}
              /*else{
                  fwrite ($file,  "3".$separador.
                      $ctd.$separador.
                      $newDate.$separador.
                      $val['nro_documento'].$separador.
                      $val['nro_autorizacion'].$separador.
                      $val['tipo_doc'].$separador.
                      $val['nit'].$separador.
                      $val['razon_social'].$separador.
                      $val['importe_doc'].$separador.
                      $val['importe_ice'].$separador.
                      $val['importe_excento'].$separador.
                      $val['venta_gravada_cero'].$separador.
                      $val['subtotal_venta'].$separador.
                      $val['importe_descuento'].$separador.
                      $val['sujeto_df'].$separador.
                      $val['importe_iva'].$separador.
                      $val['codigo_control']."\r\n");
              }*/
              $ctd = $ctd + 1;
          } //end for


        }else if ($this->objParam->getParametro('tipo_reporte')=='det_vent_natu') {

          // if( $this->objParam->getParametro('filtro_sql') == 'periodo' ){
          //     $sufijo = 'GESTIÓN: '.$res[0]['gestion'].' MES: '.$res[0]['periodo_literal'];
          //     $mes_envio = 'MES DE ENVIO AL SIN: '.$res[0]['periodo_literal'];
          // }else{
          //   $sufijo = 'GESTIÓN: '.$res[0]['gestion'].'DESDE MES: '.$this->objParam->getParametro('fecha_ini').' HASTA MES: '.$this->objParam->getParametro('fecha_fin');
          //   $mes_envio = 'MES DE ENVIO AL SIN DESDE: '.$this->objParam->getParametro('fecha_ini').' HASTA: '.$this->objParam->getParametro('fecha_fin');
          // }

          if ($res[0]['periodo_literal_inicio'] == $res[0]['periodo_literal_fin']) {
            $sufijo = 'GESTIÓN: '.$res[0]['gestion'].' MES: '.$res[0]['periodo_literal_inicio'];
          } else {
            $sufijo = 'GESTIÓN: '.$res[0]['gestion'].' MES DESDE:'.$res[0]['periodo_literal_inicio'].' MES HASTA: '.$res[0]['periodo_literal_fin'];
          }

					fwrite ($file,
									'Boliviana de Aviacion (BoA)          VENTA DE BIENES Y/O SERVICIOS A SUJETOS PASIVOS DEL REGIMEN TRIBUTARIO SIMPLIFICADO A PERSONAS NO INSCRITAS EN EL PADRON NACIONAL DE CONTRIBUYENTES'."\r\n".
								  'Dpto. de FINANZAS                                                                     '.$sufijo."\r\n".
								  'REGIMEN TRIBUTARIO PERSONAS NATURALES                                                    '."\r\n");
					$numero = 1;
					$nit_cliente=array();

          foreach ($data as $val) {
            if ($val['razon_social'] == 'cabecera') {
              fwrite ($file,'NIT: '.$val['nit']."\r\n");
            } else if ($val['razon_social'] != 'cabecera' && $val['razon_social'] != 'total') {
              fwrite ($file,
                  $numero.'|'.
                  date("d/m/Y", strtotime( $val['fecha_factura'])).'|'.
                  $val['nro_factura'].'|'.
                  $val['nit'].'|'.
                  $val['carnet_ide'].'|'.
                  $val['nit'].'|'.
                  $val['razon_social'].'|'.
                  $val['sistema_origen'].'|'.
                  $val['desc_ruta'].'|'.
                  $val['cantidad'].'|'.
                  number_format($val['precio_unitario'], 2, ',', '.').'|'.
                  number_format($val['precio_total'], 2, ',', '.')."\r\n");
                  $numero ++;
            } else if ($val['razon_social'] == 'total') {
              fwrite ($file,'TOTAL NIT: '.number_format($val['precio_total'], 2, ',', '.')."\r\n");
            }

          	}
						fwrite ($file,''."\r\n");

					// foreach($data as $value){
	        //     		$valor=$value['nit'];
	        //       if(!in_array($valor, $nit_cliente)){
	        //          $nit_cliente[]=$valor;
	        //       }
	        // }
          //
					// foreach($nit_cliente as $value ){
					// 		fwrite ($file,'NIT: '.$value."\r\n");
          // foreach ($data as $val) {
					// 				if ($val['nit'] == $value) {
          //         fwrite ($file,
					// 					  $numero.'|'.
          //             date("d/m/Y", strtotime( $val['fecha_factura'])).'|'.
          //             $val['nro_factura'].'|'.
          //             $val['nit'].'|'.
          //             $val['carnet_ide'].'|'.
          //             $val['nit'].'|'.
					// 						$val['razon_social'].'|'.
					// 						$val['sistema_origen'].'|'.
					// 						$val['desc_ruta'].'|'.
					// 						$val['cantidad'].'|'.
          //             number_format($val['precio_unitario'], 2, ',', '.').'|'.
          //             number_format($val['precio_total'], 2, ',', '.')."\r\n");
          //     				$numero ++;
					// 				}
          // 	}
					// 	fwrite ($file,''."\r\n");
          //
					// }


        }else if ($this->objParam->getParametro('tipo_reporte')=='res_vent_natu') {

					fwrite ($file,
									'Boliviana de Aviacion (BoA)          RESUMEN - VENTA DE BIENES Y/O SERVICIOS A SUJETOS PASIVOS'."\r\n".
								  'Dpto. de FINANZAS 	      	    DEL REGIMEN TRIBUTARIO SIMPLIFICADO A PERSONAS NO INSCRITAS'."\r\n".
								  '                                            EN EL PADRON NACIONAL DE CONTRIBUYENTES'."\r\n".
								  '                                      PERIODO: '.$data[0]['gestion'].' Desde MES: '.$data[0]['mes_inicio'].' Hasta MES: '.$data[0]['mes_final']."\r\n");

					// fwrite ($file,
					// 				'NRO       Nro. de doc. de Identificacion       Importe Acumulado Bs.       Mes de Envio al SIN.'."\r\n");

					//fclose($file);
					$numero = 1;
					$nit_cliente=array();


          foreach ($data as $val) {
                  fwrite ($file,
										  '                                            '.
											$numero.'   '.
                      $val['nit'].'   '.
                      number_format($val['total_acumulado'], 2, ',', '.').'   '.
                      $val['mes_envio'].'   '
											."\r\n");
              				$numero ++;

          	}
				}else if ($this->objParam->getParametro('tipo_reporte')=='det_vent_rts') {

          // if( $this->objParam->getParametro('filtro_sql') == 'periodo' ){
          //     $sufijo = 'GESTIÓN: '.$res[0]['gestion'].' MES: '.$res[0]['periodo_literal'];
          //     $mes_envio = 'MES DE ENVIO AL SIN: '.$res[0]['periodo_literal'];
          // }else{
          //   $sufijo = 'GESTIÓN: '.$res[0]['gestion'].'DESDE MES: '.$this->objParam->getParametro('fecha_ini').' HASTA MES: '.$this->objParam->getParametro('fecha_fin');
          //   $mes_envio = 'MES DE ENVIO AL SIN DESDE: '.$this->objParam->getParametro('fecha_ini').' HASTA: '.$this->objParam->getParametro('fecha_fin');
          // }
          if ($res[0]['periodo_literal_inicio'] == $res[0]['periodo_literal_fin']) {
            $sufijo = 'GESTIÓN: '.$res[0]['gestion'].' MES: '.$res[0]['periodo_literal_inicio'];
          } else {
            $sufijo = 'GESTIÓN: '.$res[0]['gestion'].' MES DESDE:'.$res[0]['periodo_literal_inicio'].' MES HASTA: '.$res[0]['periodo_literal_fin'];
          }
          // if ($res[0]['periodo_literal_inicio'] == $res[0]['periodo_literal_fin']) {
          //   $sufijo = ($res[0]['periodo_num_ini']<10?'0'.$res[0]['periodo_num_ini']:$res[0]['periodo_num_ini']).$res[0]['gestion'];
          // } else {
          //   $sufijo=($res[0]['periodo_num_ini']<10?'0'.$res[0]['periodo_num_ini']:$res[0]['periodo_num_ini']).'_'.($res[0]['periodo_num_fin']<10?'0'.$res[0]['periodo_num_fin']:$res[0]['periodo_num_fin']).'_'.$res[0]['gestion'];
          // }

					fwrite ($file,
									'Boliviana de Aviacion (BoA)          VENTA DE BIENES Y/O SERVICIOS A SUJETOS PASIVOS DEL REGIMEN TRIBUTARIO SIMPLIFICADO A PERSONAS NO INSCRITAS EN EL PADRON NACIONAL DE CONTRIBUYENTES'."\r\n".
								  'Dpto. de FINANZAS                                                                     '.$sufijo."\r\n".
								  'REGIMEN TRIBUTARIO SIMPLIFICADO                                                     '."\r\n");

									// fwrite ($file,
									// 				'NRO       FECHA DE EMISIÓN BILLETE O FACTURA       NRO. BILLETE O FACTURA       COD. CLIENTE       TIPO DE DOC.       NRO. DE DOCUMENTO       NOMBRES Y APELLIDOS CLIENTES       COD. DEL SERVICIO       DESC. DEL SERVICIO       CANTIDAD       PRECIO UNITARIO(BS)     TOTAL VENDIDO(BS)'."\r\n");

					//fclose($file);
					$numero = 1;
					$nit_cliente=array();


          foreach ($data as $val) {
            if ($val['razon_social'] == 'cabecera') {
              fwrite ($file,'NIT: '.$val['nit']."\r\n");
            } else if ($val['razon_social'] != 'cabecera' && $val['razon_social'] != 'total') {
              fwrite ($file,
						  $numero.'|'.
              date("d/m/Y", strtotime( $val['fecha_factura'])).'|'.
              $val['nro_factura'].'|'.
              $val['nit'].'|'.
              // $val['carnet_ide'].'|'.
              $val['nit'].'|'.
							// $val['razon_social'].'|'.
							$val['sistema_origen'].'|'.
							$val['desc_ruta'].'|'.
							$val['cantidad'].'|'.
							$val['precio_unitario'].'|'.
              $val['precio_total']."\r\n");
      				$numero ++;
            } else if ($val['razon_social'] == 'total') {
              fwrite ($file,'TOTAL NIT: '.number_format($val['precio_total'], 2, ',', '.')."\r\n");
            }

          	}
						fwrite ($file,''."\r\n");

					// foreach($data as $value){
	        //     		$valor=$value['nit'];
	        //       if(!in_array($valor, $nit_cliente)){
	        //          $nit_cliente[]=$valor;
	        //       }
	        // }

					// foreach($nit_cliente as $value ){
					// 		fwrite ($file,'NIT: '.$value."\r\n");
          // foreach ($data as $val) {
					// 				if ($val['nit'] == $value) {
          //         fwrite ($file,
					// 					  $numero.'|'.
          //             date("d/m/Y", strtotime( $val['fecha_factura'])).'|'.
          //             $val['nro_factura'].'|'.
          //             $val['nit'].'|'.
          //             // $val['carnet_ide'].'|'.
          //             $val['nit'].'|'.
					// 						// $val['razon_social'].'|'.
					// 						$val['sistema_origen'].'|'.
					// 						$val['desc_ruta'].'|'.
					// 						$val['cantidad'].'|'.
					// 						$val['precio_unitario'].'|'.
          //             $val['precio_total']."\r\n");
          //     				$numero ++;
					// 				}
          // 	}
					// 	fwrite ($file,''."\r\n");
          //
					// }


        }else if ($this->objParam->getParametro('tipo_reporte')=='res_vent_rts') {

					fwrite ($file,
									'Boliviana de Aviacion (BoA)          RESUMEN - VENTA DE BIENES Y/O SERVICIOS A SUJETOS PASIVOS'."\r\n".
								  'Dpto. de FINANZAS 	      	    DEL REGIMEN TRIBUTARIO SIMPLIFICADO A PERSONAS NO INSCRITAS'."\r\n".
								  '                                            EN EL PADRON NACIONAL DE CONTRIBUYENTES'."\r\n".
								  '                                      PERIODO: '.$data[0]['gestion'].' Desde MES: '.$data[0]['mes_inicio'].' Hasta MES: '.$data[0]['mes_final']."\r\n");

					// fwrite ($file,
					// 				'NRO       Nro. de doc. de Identificacion       Importe Acumulado Bs.       Mes de Envio al SIN.'."\r\n");

					//fclose($file);
					$numero = 1;
					$nit_cliente=array();


          foreach ($data as $val) {
                  fwrite ($file,
										  '                                            '.
											$numero.'   '.
                      $val['nit'].'   '.
                      number_format($val['total_acumulado'], 2, ',', '.').'   '.
                      $val['mes_envio'].'   '
											."\r\n");
              				$numero ++;

          	}
				}
        fclose($file);
        return $fileName;
    }

    function recuperarMontoNormativa(){
  		 $this->objFunc=$this->create('MODComisionistas');
  		 $this->res=$this->objFunc->recuperarMontoNormativa($this->objParam);
  		 $this->res->imprimirRespuesta($this->res->generarJson());
   }

}

?>
